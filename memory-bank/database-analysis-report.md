# Database Analysis Report: RMS QR Meter Reading System

## Executive Summary

This report provides a comprehensive analysis of the RMS database structure for the QR Meter Reading System implementation. The system uses the existing `t_tenant_reading` table with proper joins to `m_tenant` and `m_real_property` tables.

## RMS Database Schema for QR Meter Reading

### Key Tables and Their Relationships

#### 1. **t_tenant_reading** (Primary readings table)
```sql
CREATE TABLE [dbo].[t_tenant_reading](
    [reading_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
    [tenant_code] [varchar](20) NULL,
    [date_from] [datetime] NULL,
    [date_to] [datetime] NULL,
    [prev_reading] [decimal](18, 0) NULL,
    [current_reading] [decimal](18, 0) NULL,
    [remarks] [varchar](100) NULL,
    [billing_date_from] [datetime] NULL,
    [billing_date_to] [datetime] NULL,
    [created_by] [varchar](50) NULL,
    [date_created] [datetime] NULL,
    [updated_by] [varchar](50) NULL,
    [date_updated] [datetime] NULL
)
```

**Key Fields for QR System:**
- `reading_id`: Primary key (auto-increment)
- `tenant_code`: Links to m_tenant table
- `date_from`: Reading start date
- `date_to`: Reading end date
- `prev_reading`: Previous meter reading
- `current_reading`: Current meter reading
- `remarks`: Notes (includes QR System identifier)
- `created_by`: User who created the reading
- `date_created`: Creation timestamp

#### 2. **m_tenant** (Tenant information)
```sql
CREATE TABLE [dbo].[m_tenant](
    [tenant_code] [char](10) NOT NULL,
    [tenant_name] [varchar](100) NOT NULL,
    [real_property_code] [char](5) NULL,
    [building_code] [char](10) NULL,
    [unit_no] [char](10) NULL,
    [bill_to] [char](10) NULL,
    [contact_no1] [varchar](20) NULL,
    [contact_no2] [varchar](20) NULL,
    [address1] [varchar](50) NULL,
    [address2] [varchar](50) NULL,
    [contract_eff_date] [datetime] NULL,
    [contract_expiry_date] [datetime] NULL,
    [sap_code] [varchar](20) NULL,
    [terminated] [char](1) NULL,
    [date_terminated] [datetime] NULL,
    [actual_move_in_date] [datetime] NULL,
    [email_add] [varchar](256) NULL,
    [is_affiliate_employee] [char](1) NULL,
    [employer] [varchar](100) NULL,
    [tenant_type] [char](2) NOT NULL,
    [is_sap_affiliate] [char](1) NULL,
    [new_code] [varchar](50) NULL,
    [business_area] [varchar](50) NULL,
    [company_code] [varchar](5) NULL,
    [last_meter_reading] [varchar](20) NULL,
    [security_deposit_amount] [decimal](18, 2) NULL,
    [tenant_remarks] [varchar](500) NULL,
    [is_employee_benefit] [char](1) NULL,
    [employee_benefit_cc] [varchar](7) NULL,
    [is_notifications] [char](1) NULL,
    [date_created] [datetime] NULL,
    [created_by] [char](10) NULL,
    [date_updated] [datetime] NULL,
    [updated_by] [char](10) NULL
)
```

**Key Fields for QR System:**
- `tenant_code`: Primary key (links to t_tenant_reading)
- `tenant_name`: Tenant name
- `real_property_code`: Links to m_real_property
- `building_code`: Building identifier
- `unit_no`: Unit number
- `last_meter_reading`: Last recorded meter reading

#### 3. **m_real_property** (Property information)
```sql
CREATE TABLE [dbo].[m_real_property](
    [real_property_code] [char](5) NOT NULL,
    [real_property_name] [varchar](100) NULL,
    [real_property_company_name] [varchar](100) NULL,
    [real_property_dba_name] [varchar](100) NULL,
    [address1] [varchar](50) NULL,
    [address2] [varchar](50) NULL,
    [address3] [varchar](50) NULL,
    [contact_no1] [varchar](20) NULL,
    [contact_no2] [varchar](20) NULL,
    [tot_no_of_units] [int] NULL,
    [lot_space] [varchar](20) NULL,
    [space_type] [char](1) NULL,
    [remarks] [varchar](100) NULL,
    [cost_center] [varchar](20) NULL,
    [company_code] [varchar](5) NULL
)
```

**Key Fields for QR System:**
- `real_property_code`: Primary key (links to m_tenant)
- `real_property_name`: Property name
- `space_type`: Property type classification

## QR System Database Relationships

### Primary Relationship Chain
```
QR Code Data → m_tenant (real_property_code + unit_no) → t_tenant_reading (tenant_code)
```

### Query Structure for Recent Readings
```sql
SELECT TOP 10 
    r.reading_id,
    r.tenant_code,
    r.date_from as readingDate,
    r.date_to as readingDateTo,
    r.prev_reading,
    r.current_reading as meterReading,
    r.remarks,
    r.created_by as createdBy,
    r.date_created as createdAt,
    r.updated_by as updatedBy,
    r.date_updated as updatedAt,
    t.tenant_name,
    t.real_property_code as propertyId,
    t.unit_no as unitNumber,
    p.real_property_name as propertyName
FROM t_tenant_reading r
INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
LEFT JOIN m_real_property p ON t.real_property_code = p.real_property_code
WHERE r.remarks LIKE '%QR System%' OR r.remarks LIKE '%QR Reading%'
ORDER BY r.date_created DESC
```

### Insert Structure for New Readings
```sql
INSERT INTO t_tenant_reading 
(tenant_code, date_from, date_to, prev_reading, current_reading, 
 billing_date_from, billing_date_to, remarks, created_by, date_created) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
```

## QR System Implementation Notes

### 1. Tenant Lookup Process (Corrected)
- QR codes contain `real_property_code` and `unit_no` (required)
- QR codes may contain `meter_id` (optional)
- System looks up `tenant_code` using: `WHERE real_property_code = ? AND unit_no = ? AND ISNULL(terminated,'N') = 'N'`
- Only active tenants (not terminated) are considered for readings
- If tenant not found, returns error

### 2. QR Code Data Structure
- **Default Format**: `real_property_code|unit_no`
- **Enhanced Format**: `real_property_code|unit_no|meter_id`
- **Security**: No tenant information stored in QR codes
- **Flexibility**: Allows tenant changes without reprinting QR codes

### 3. Reading Storage
- Readings stored in `t_tenant_reading` table
- `tenant_code` links to `m_tenant` table
- `date_from` and `date_to` set to reading date
- `remarks` field includes "QR System Reading" identifier

### 4. Data Validation
- Validates tenant exists before saving
- Checks for existing readings on same date
- Updates existing readings if found
- Logs all activities with user information

### 5. Tenant Move-In/Out Transition Rules

#### A. Move-Out Reading (Between Tenants)
- Attribute the reading to the outgoing tenant (last active)
- Periods:
  - `date_from` = 1st day of reading month
  - `date_to` = `move_out_date`
  - `billing_date_from` = 1st day of reading month
  - `billing_date_to` = `move_out_date + 1 day`

#### B. Next Tenant Regular Reading (Same Month)
- `date_from` = previous reading `date_to + 1 day`
- `date_to` = last day of month
- `billing_date_from` = 1st day of next month
- `billing_date_to` = last day of next month

#### C. Last Reading Source (Unit-Level)
- `prev_reading` is based on the last recorded reading for the unit using `real_property_code + unit_no`
- Do not limit to tenant when determining `prev_reading`
- Suggest using view `vw_TenantReading` for efficient lookup across tenant transitions

```sql
-- Suggested unit-level last reading lookup
SELECT TOP 1 r.reading_id, r.current_reading, r.date_to
FROM t_tenant_reading r
JOIN m_tenant t ON r.tenant_code = t.tenant_code
WHERE t.real_property_code = @propertyCode
  AND t.unit_no = @unitNumber
ORDER BY r.date_created DESC;
```

### 5. Error Handling
- Tenant not found: Returns specific error message
- Invalid data: Validates meter reading values
- Database errors: Logs and returns user-friendly messages

## API Endpoints

### 1. Save Reading (`/api/save-reading.php`)
**Method:** POST
**Required Fields:**
- `propertyCode`: Real property code (char(5))
- `unitNo`: Unit number (char(10))
- `readingDate`: Reading date (YYYY-MM-DD)
- `currentReading`: Current meter reading (decimal)

**Optional Fields:**
- `meter_id`: Meter identifier if available (varchar(20))

**Response:**
```json
{
    "success": true,
    "message": "Reading saved successfully",
    "data": {
        "tenantCode": "TENANT001",
        "propertyCode": "PROP1",
        "unitNo": "UNIT001",
        "meter_id": "METER123",
        "currentReading": 1234.56,
        "readingDate": "2025-01-15",
        "timestamp": "2025-01-15 10:30:00",
        "userId": "USER001"
    }
}
```

### 2. Get Recent Readings (`/api/get-recent-readings.php`)
**Method:** GET
**Response:**
```json
{
    "success": true,
    "data": [
        {
            "readingId": 123,
            "tenantCode": "TENANT001",
            "tenantName": "John Doe",
            "propertyCode": "PROP1",
            "propertyName": "Sample Property",
            "unitNo": "UNIT001",
            "readingDate": "2025-01-15",
            "readingDateTo": "2025-01-15",
            "prevReading": 1200.00,
            "currentReading": 1234.56,
            "usage": 34.56,
            "remarks": "QR System Reading: 1234.56",
            "createdBy": "USER001",
            "createdAt": "2025-01-15 10:30:00"
        }
    ],
    "count": 1
}
```

### API Guidance: Using vw_TenantReading for Last Reading

- All API logic that requires the most recent prior reading (prev_reading) for a unit MUST query `vw_TenantReading` keyed by `propertyCode + unitNo` (unit-level), not strictly by tenant.

```sql
-- Example: Get last reading for unit via vw_TenantReading
SELECT TOP 1 reading_id, tenant_code, date_from, date_to, prev_reading, current_reading
FROM vw_TenantReading
WHERE real_property_code = @propertyCode
  AND unit_no = @unitNo
ORDER BY date_created DESC;
```

- Endpoints affected:
  - `POST /api/save-reading.php`: Resolve `prev_reading` from view
  - `GET /api/get-last-reading.php`: Return payload from view

## Security Considerations

1. **Authentication Required**: All API endpoints require valid session
2. **Input Validation**: All inputs sanitized and validated
3. **SQL Injection Protection**: Prepared statements used throughout
4. **Error Logging**: All errors logged with user context
5. **Data Integrity**: Foreign key relationships maintained

## Performance Considerations

1. **Indexing**: Ensure indexes on `tenant_code`, `date_created`, `real_property_code`, `unit_no`
2. **Query Optimization**: Use proper joins and WHERE clauses
3. **Caching**: Consider caching for frequently accessed data
4. **Batch Operations**: For bulk readings, consider batch inserts

## Future Enhancements

1. **Reading History**: Implement reading history tracking
2. **Usage Calculations**: Automatic usage calculation based on previous readings
3. **Billing Integration**: Direct integration with billing system
4. **Reporting**: Enhanced reporting capabilities
5. **Mobile Optimization**: Further mobile-specific optimizations 