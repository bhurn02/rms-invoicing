# Utility Rate & QR Meter Reading Implementation v1.1

## Executive Summary
This document reflects current implementation status, recent changes, and the remaining scope for the RMS enhancements across two tracks: Utility Rate Management and Mobile QR Code Meter Reading. It focuses in particular on the Reading Persistence Plan and implementation approach that leverages the existing schema with minimal or no changes.

## Status Overview

### Completed (This iteration)
- Batch QR print: eliminated extra blank trailing pages; print layout stabilized
- Batch selection UX: table rows are clickable with visual feedback; select-all synced
- Removed redundant Test Scanner tab from generator; dedicated `camera-test.html` retained
- Database configuration confirmed via `config.php` (localhost mirrors live)
- Documentation: memory-bank active context, tasks, and progress updated with new priorities

### In Progress / Next Priorities
- **User Access Rights Implementation**: Implement proper user group validation for QR Meter Reading modules
- End-to-End Testing: Test complete QR reading flow with real data
- Tenant Readings Management Page: Implement comprehensive reading management interface
- Documentation Updates: Update user and technical documentation
- Production Deployment: Deploy to production environment

## Reading Persistence Plan (Using Existing Schema)

### Goal
Save meter readings captured via the QR Meter Reading module directly into existing RMS tables with minimal schema changes, preserving compatibility and auditability.

### Target Tables (Existing + New)
- `t_tenant_reading` (primary storage for readings) - **REQUIRES SCHEMA UPDATE**
- `m_tenant` (lookup of `tenant_code`, `real_property_code`, `unit_no`)
- `m_real_property` (optional property metadata for UI)
- `s_tenant_reading_default` (default values for charge codes) - **NEW REQUIREMENT**
- `t_tenant_reading_ext` (extended properties for audit) - **NEW TABLE**

### Required Schema Changes

#### 1. Update t_tenant_reading Table
```sql
-- Add new tracking columns to existing table
ALTER TABLE t_tenant_reading 
ADD reading_date datetime NULL,           -- Actual date/time reading taken
    reading_by nvarchar(32) NULL;        -- Technician who took reading
```

#### 2. Create Extended Properties Table
```sql
-- New table for audit trail and metadata
CREATE TABLE t_tenant_reading_ext (
    id int IDENTITY(1,1) PRIMARY KEY,
    reading_id decimal(18,0) NOT NULL,   -- Foreign key to t_tenant_reading
    ip_address varchar(45) NULL,         -- IP address for audit trail
    user_agent nvarchar(500) NULL,       -- User agent for audit trail
    device_info nvarchar(200) NULL,      -- Device information
    location_data nvarchar(500) NULL,    -- GPS/location if available
    created_date datetime DEFAULT GETDATE(),
    
    CONSTRAINT FK_reading_ext_reading 
    FOREIGN KEY (reading_id) REFERENCES t_tenant_reading(reading_id)
);

-- Performance indexes for audit queries
CREATE INDEX IX_reading_ext_reading_id ON t_tenant_reading_ext(reading_id);
CREATE INDEX IX_reading_ext_audit ON t_tenant_reading_ext(ip_address, created_date);
```

### Data Flow (QR → Save Reading)
1) Technician scans QR → UI extracts `propertyCode`, `unitNo` (and optionally meterId)
2) **Primary**: UI fetches `tenant_code` by `real_property_code = propertyCode` AND `unit_no = unitNo` AND `ISNULL(terminated,'N') = 'N'`
3) **Fallback**: If no active tenant found, system retrieves last active tenant from `vw_TenantReading` for the same property/unit
4) Technician enters `current_reading` (+ reading date)
5) **NEW**: System checks `s_tenant_reading_default` for CUCF/CUCNF default values
6) **NEW**: System calculates reading period and billing period dates using business logic
7) API composes payload and writes to `t_tenant_reading` + `t_tenant_reading_ext`

### Business Logic Implementation

#### 1. Default Values Lookup
```sql
-- Check for default charge code values
SELECT trd_charge_code, trd_default_value 
FROM s_tenant_reading_default 
WHERE trd_charge_code IN ('CUCF', 'CUCNF')
  AND trd_is_active = 1;
```

#### 2. Date Calculation Logic
**Reading Period**: Covers the month when reading was taken
- `date_from`: 1st day of reading month
- `date_to`: Last day of reading month

**Billing Period**: Covers the following month for invoice generation
- `billing_date_from`: 1st day of month AFTER reading month
- `billing_date_to`: Last day of month AFTER reading month

**Example Implementation**:
```php
// Given reading date: 08/29/2025 14:28
$readingDate = new DateTime('2025-08-29 14:28:00');

// Reading period: August 1-31, 2025
$dateFrom = new DateTime('2025-08-01 00:00:00');
$dateTo = new DateTime('2025-08-31 23:59:59');

// Billing period: September 1-30, 2025
$billingDateFrom = new DateTime('2025-09-01 00:00:00');
$billingDateTo = new DateTime('2025-09-30 23:59:59');
```

### Tenant Move-In/Out Edge Cases (Overrides to Defaults)

In cases where a reading is taken during a tenant transition (move-out/move-in), the default period rules are overridden to align readings and billing with actual occupancy.

#### Scenario A: Move-Out Reading (Between Tenants)
- Example:
  - real_property_code: GC A
  - unit_no: 101
  - Tenant 001 (AARON) move-out date: 2025-08-15
  - Technician takes reading on: 2025-08-16 (between tenants)
  - Readings: prev_reading = 10374, current_reading = 10510

- Save to `t_tenant_reading` with `tenant_code = 001` (last active tenant):
  - `date_from`: 2025-08-01 (1st day of reading month)
  - `date_to`: 2025-08-15 (move-out date)
  - `billing_date_from`: 2025-08-01
  - `billing_date_to`: 2025-08-16 (move-out date + 1 day)
  - `reading_date`: 2025-08-16 (actual reading timestamp)
  - `prev_reading`: 10374
  - `current_reading`: 10510

- Rationale: Final reading attributed to the outgoing tenant, billing period capped to move-out (+1 day) to align with business rules.

#### Scenario B: Next Regular Monthly Reading (New Tenant)
- Example:
  - Tenant 002 (ALDRICH) move-in date: 2025-08-16
  - Scheduled monthly reading date: 2025-08-26
  - Obtain `prev_reading` from last known reading for the unit (property_code + unit_no), not strictly by tenant

- Save to `t_tenant_reading` with `tenant_code = 002` (current active tenant):
  - `date_from`: 2025-08-16 (day after previous `date_to`; i.e., last reading `date_to` + 1 day)
  - `date_to`: 2025-08-31 (end of month)
  - `billing_date_from`: 2025-09-01 (start of next month)
  - `billing_date_to`: 2025-09-30 (end of next month)
  - `reading_date`: 2025-08-26
  - `prev_reading`: 10510 (from prior unit-level reading)
  - `current_reading`: 10585

#### Implementation Notes
- Active tenant resolution uses: `m_tenant` where `real_property_code = ? AND unit_no = ? AND ISNULL(terminated,'N') = 'N'`
- `prev_reading` source: most recent reading for the unit keyed by `real_property_code + unit_no` (regardless of tenant). Recommended to use/view: `vw_TenantReading` for efficient retrieval.
- When move-out/move-in occurs mid-month:
  - Outgoing tenant’s billing period ends at `move_out_date + 1 day`.
  - Next tenant’s `date_from` begins the day after the previous `date_to`.
- Defaults from `s_tenant_reading_default` apply unless overridden by these transition rules.

### Field Mapping (Updated t_tenant_reading)
- `tenant_code`: resolved via lookup from `m_tenant`
- `date_from`, `date_to`: calculated reading period (1st to last day of reading month)
- `prev_reading`: fetched from the most recent prior reading for tenant (nullable if none)
- `current_reading`: technician input
- `reading_date`: server-side timestamp at save time (UI field is read-only)
- `reading_by`: **NEW** - technician username/ID who took reading
- `billing_date_from`, `billing_date_to`: calculated billing period (1st to last day of next month)
- `remarks`: free-text note from the meter reading form (e.g., "QR System Reading", anomalies, access notes)
- `created_by`/`date_created`: current user and server timestamp
- `updated_by`/`date_updated`: populated on edits only

### Extended Properties (t_tenant_reading_ext)
- `reading_id`: foreign key to t_tenant_reading
- `ip_address`: technician's IP address for audit trail
- `user_agent`: browser/device information for audit trail
- `device_info`: mobile device details if available
- `location_data`: GPS coordinates if technician allows location access
- `created_date`: timestamp when extended data was recorded

### Validation Rules
- Tenant must exist for given `propertyId` + `unitNumber`
- `current_reading` must be numeric and ≥ 0
- If previous reading exists and the meter is non-rolling, enforce `current_reading ≥ prev_reading`
- Valid date (YYYY-MM-DD); default to today if not provided
- **NEW**: Reading period dates must be valid calendar dates
- **NEW**: Billing period dates must be valid calendar dates

### API Endpoints (Updated)
1) `POST /api/save-reading.php`
   - Request (JSON):
     - `propertyCode` (string), `unitNo` (string), `currentReading` (number), `remarks` (optional), `notes` (optional)
   - Server derives:
     - `readingDate` (server-side NOW), `prevReading` (from `vw_TenantReading`), periods and billing dates
   - Response (JSON): `{ success, message, data: { tenantCode, prevReading, currentReading, readingDate, readingId, readingPeriod, billingPeriod, remarks } }`

2) `GET /api/get-last-reading.php?propertyCode=...&unitNo=...`
   - Returns the most recent saved reading for display/validation

3) `GET /api/get-tenant-by-unit.php?propertyCode=...&unitNo=...`
   - Resolves `tenant_code`, `tenant_name`, optional meter info

4) **NEW**: `GET /api/meter-reading-report.php`
   - Parameters: date range, property filter, technician filter, status filter
   - Returns: Comprehensive reading report for audit and validation
   - Supports pagination for large datasets
   - Export functionality for PDF, Excel, and CSV formats

### Meter Reading Report Features
- **Filtering Options**: Date range, property filter, technician filter, status filter
- **Report Columns**: Property, Unit, Tenant, Reading Date, Reading Period, Billing Period, Previous/Current Reading, Usage, Technician, Validation Status
- **Export Options**: PDF, Excel, CSV
- **Audit Features**: Reading validation workflow, error flagging, technician performance tracking

### Report Implementation Query
```sql
-- Comprehensive reading report for audit and validation
SELECT 
    p.real_property_name,
    t.unit_no,
    t.tenant_name,
    r.reading_date,
    r.date_from as reading_period_start,
    r.date_to as reading_period_end,
    r.billing_date_from,
    r.billing_date_to,
    r.prev_reading,
    r.current_reading,
    (r.current_reading - r.prev_reading) as usage,
    r.reading_by,
    r.remarks,
    r.date_created,
    ext.ip_address,
    ext.user_agent
FROM t_tenant_reading r
INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
LEFT JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
WHERE r.reading_date BETWEEN @startDate AND @endDate
  AND (@propertyFilter IS NULL OR t.real_property_code = @propertyFilter)
  AND (@technicianFilter IS NULL OR r.reading_by = @technicianFilter)
ORDER BY r.reading_date DESC, p.real_property_name, t.unit_no
```

### UI Integration (Enhanced)
- Scanner/Entry page:
  - QR parse → populate property and unit fields
  - Fetch and display last reading (if any)
  - Input: current reading, remarks
  - Reading Date: displayed for confirmation but read-only; actual `reading_date` set server-side on save
  - Auto-focus: After successful QR scan, focus the Current Meter Reading input for immediate entry
  - Display calculated reading period and billing period for technician verification
  - Submit → show SweetAlert success/error; reset form or move to next unit

### Error Handling & Messaging (SweetAlert)
- Success: "Reading saved" with summary (tenant, previous, current, date, periods)
- Errors: tenant not found, invalid data, DB failure; offer retry guidance
- **NEW**: Validation errors for date calculations and business rules

### Security & Audit
- Require valid session/auth for all reading writes
- Log `created_by` as the current authenticated user
- **NEW**: Log `reading_by` as the technician who took the reading
- **NEW**: Capture IP address and user agent for complete audit trail
- **NEW**: Store extended properties for forensic analysis
- Remarks include "QR System Reading" for traceability
- Server-side validation and prepared statements across all queries

### Testing Plan
- Unit: endpoint validation (missing fields, invalid formats)
- Integration: end-to-end save with known tenant; confirm row in `t_tenant_reading` + `t_tenant_reading_ext`
- **NEW**: Date calculation testing (edge cases: year boundaries, leap years, month length variations)
- **NEW**: Default values lookup testing
- **NEW**: Report generation and export testing
- Regression: ensure existing billing processes read new rows without issues

### Implementation Priority
1. **High**: Schema updates (new columns and table)
2. **High**: Date calculation logic implementation
3. **High**: Default values lookup integration
4. **Medium**: Extended properties capture and storage
5. **Medium**: Meter reading report with filtering and export
6. **Low**: Performance optimization and advanced reporting features

## Utility Rate Management (Context)
- Remains on the roadmap for the administrative interface with single-point rate entry
- No schema changes needed beyond potential service layer for transactional bulk updates
- Will integrate with classification via existing tables (e.g., `m_space_type` or property/space type flags)

## User Access Rights Implementation (NEW REQUIREMENT)

### Goal
Implement proper user group validation for QR Meter Reading modules to ensure only authorized users can access the system.

### Database Setup
Execute the provided SQL script to create the necessary user access structure:

```sql
-- From database/qr-meter-reading-user-access.sql
-- 1. Add QR Meter Reading Module
INSERT INTO s_modules (module_id, module_name)
VALUES (25, 'QR METER READING');

-- 2. Add Field Technician User Group
INSERT INTO s_user_group (group_code, group_desc)
VALUES (12, 'FIELD TECHNICIAN');

-- 3. Grant Field Technician access to QR Meter Reading module
INSERT INTO s_user_group_modules (group_code, module_id)
VALUES (12, 25);
```

### Authentication Enhancement Requirements

#### 1. User Permission Validation
- **Login Validation**: After successful login, check if user has access to QR Meter Reading module
- **Module Access Check**: Validate user group membership for module_id = 25
- **API Protection**: All API endpoints must validate user permissions

#### 2. Access Control Implementation
```php
// Enhanced authentication function
function hasQRMeterReadingAccess($userId) {
    $sql = "SELECT COUNT(*) as access_count
            FROM s_user_group_users ugu
            INNER JOIN s_user_group_modules ugm ON ugu.group_code = ugm.group_code
            WHERE ugu.user_id = ? AND ugm.module_id = 25";
    
    $result = $database->query($sql, [$userId]);
    return $result[0]['access_count'] > 0;
}
```

#### 3. User Experience Requirements

**Access Denied Page** (`pages/qr-meter-reading/access-denied.php`):
- Professional design matching RMS style guide
- Clear explanation of insufficient permissions
- Instructions for requesting access from administrator
- Contact information for access requests
- Link back to main RMS system

**Failed Login Message**:
- SweetAlert dialog for users without QR Meter Reading permissions
- Clear message: "You do not have permission to access QR Meter Reading. Please contact your administrator to request access."
- Professional, non-technical language
- Option to contact administrator or return to main system

#### 4. Implementation Files to Update

**Authentication Files**:
- `pages/qr-meter-reading/auth/auth.php` - Add permission validation
- `pages/qr-meter-reading/auth/login.php` - Add permission check after login
- `pages/qr-meter-reading/index.php` - Add permission validation on page load

**New Files to Create**:
- `pages/qr-meter-reading/access-denied.php` - Access denied page
- `pages/qr-meter-reading/includes/permission-check.php` - Permission validation functions

**API Files to Update**:
- All API endpoints in `pages/qr-meter-reading/api/` - Add permission validation

### User Assignment Process
To assign QR Meter Reading access to a user:

1. **Find User**: Query s_users table for user_id
2. **Assign Group**: Insert into s_user_group_users table
   ```sql
   INSERT INTO s_user_group_users (user_id, group_code) 
   VALUES ('USER_ID_HERE', 12);
   ```
3. **Verify Assignment**: Confirm user has access to QR Meter Reading module

### Security Considerations
- **Session Validation**: Ensure user permissions are checked on every request
- **API Protection**: All API endpoints must validate permissions
- **Audit Trail**: Log access attempts and permission denials
- **Graceful Degradation**: Provide helpful error messages for unauthorized access

## Implementation Strategy (Remaining Work)

1) **User Access Rights Implementation** (NEW PRIORITY)
   - Execute database script to create module and user group
   - Update authentication system to check user permissions
   - Create access denied pages and failed login messages
   - Test with different user permission levels

2) Authentication UX fixes
   - Correct post-login redirect route
   - Remove duplicate logout dialogs; single confirmation with SweetAlert

3) SweetAlert integration
   - Replace Bootstrap alerts/toasts in generator and related pages
   - Align visuals with project style guide

4) Reading persistence build
   - Implement proposed endpoints (`save-reading`, `