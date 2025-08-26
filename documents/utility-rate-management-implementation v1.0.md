# Utility Rate & QR Meter Reading Implementation v1.0

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
- Fix post-login incorrect redirection (auth flow)
- Resolve double logout dialog alerts
- Implement SweetAlert for alerts/confirmations (replace Bootstrap alert toasts)
- Reading Persistence: finalize plan and implement API/UI to save readings using existing schema
- Backlog: Batch downloads (PDF/ZIP), performance runs with 100+ tenants

## Reading Persistence Plan (Using Existing Schema)

### Goal
Save meter readings captured via the QR Meter Reading module directly into existing RMS tables with minimal to no schema changes, preserving compatibility and auditability.

### Target Tables (Existing)
- `t_tenant_reading` (primary storage for readings)
- `m_tenant` (lookup of `tenant_code`, `real_property_code`, `unit_no`)
- `m_real_property` (optional property metadata for UI)

### Data Flow (QR → Save Reading)
1) Technician scans QR or selects tenant → UI extracts `propertyId`, `unitNumber` (and optionally meterId)
2) UI fetches `tenant_code` by `real_property_code = propertyId` AND `unit_no = unitNumber`
3) Technician enters `current_reading` (+ reading date)
4) API composes payload and writes to `t_tenant_reading`

### Field Mapping (t_tenant_reading)
- `tenant_code`: resolved via lookup from `m_tenant`
- `date_from`, `date_to`: reading date (both set to same date unless a range is used)
- `prev_reading`: fetched from the most recent prior reading for tenant (nullable if none)
- `current_reading`: technician input
- `remarks`: include identifier like "QR System Reading" (+ optional meta: device/user)
- `billing_date_from`, `billing_date_to`: optional (set by billing cycle logic or left null for now)
- `created_by`/`date_created`: current user and server timestamp
- `updated_by`/`date_updated`: populated on edits only

### Validation Rules
- Tenant must exist for given `propertyId` + `unitNumber`
- `current_reading` must be numeric and ≥ 0
- If previous reading exists and the meter is non-rolling, enforce `current_reading ≥ prev_reading`
- Valid date (YYYY-MM-DD); default to today if not provided

### Minimal or No Schema Changes
- No new columns required for MVP
- Optional indexes (if needed for performance):
  - Composite index on `t_tenant_reading(tenant_code, date_created DESC)` for fast last-reading lookup

### API Endpoints (Proposed)
1) `POST /api/save-reading.php`
   - Request (JSON):
     - `propertyId` (string), `unitNumber` (string), `readingDate` (YYYY-MM-DD), `currentReading` (number), `notes` (optional)
   - Process:
     - Lookup tenant_code
     - Resolve previous reading
     - Insert into `t_tenant_reading`
   - Response (JSON): `{ success, message, data: { tenantCode, prevReading, currentReading, readingDate, readingId } }`

2) `GET /api/get-last-reading.php?propertyId=...&unitNumber=...`
   - Returns the most recent saved reading for display/validation

3) `GET /api/get-tenant-by-unit.php?propertyId=...&unitNumber=...`
   - Resolves `tenant_code`, `tenant_name`, optional meter info

### UI Integration (High Level)
- Scanner/Entry page:
  - QR parse → populate property and unit fields
  - Fetch and display last reading (if any)
  - Input: current reading, date, notes
  - Submit → show SweetAlert success/error; reset form or move to next unit

### Error Handling & Messaging (SweetAlert)
- Success: "Reading saved" with summary (tenant, previous, current, date)
- Errors: tenant not found, invalid data, DB failure; offer retry guidance

### Security & Audit
- Require valid session/auth for all reading writes
- Log `created_by` as the current authenticated user
- Remarks include "QR System Reading" for traceability
- Server-side validation and prepared statements across all queries

### Testing Plan
- Unit: endpoint validation (missing fields, invalid formats)
- Integration: end-to-end save with known tenant; confirm row in `t_tenant_reading`
- Regression: ensure existing billing processes read new rows without issues

## Utility Rate Management (Context)
- Remains on the roadmap for the administrative interface with single-point rate entry
- No schema changes needed beyond potential service layer for transactional bulk updates
- Will integrate with classification via existing tables (e.g., `m_space_type` or property/space type flags)

## Implementation Strategy (Remaining Work)

1) Authentication UX fixes
   - Correct post-login redirect route
   - Remove duplicate logout dialogs; single confirmation with SweetAlert

2) SweetAlert integration
   - Replace Bootstrap alerts/toasts in generator and related pages
   - Align visuals with project style guide

3) Reading persistence build
   - Implement proposed endpoints (`save-reading`, `get-last-reading`, `get-tenant-by-unit`)
   - Wire UI form submission and success flows with SweetAlert
   - Add last reading preview for technician confidence

4) Optional/Backlog
   - Batch downloads (PDF/ZIP) for QR generator
   - Performance pass with 100+ tenants; minor optimizations

## Timeline (Estimate)
- Auth fixes + SweetAlert: 1–2 days
- Reading persistence API + UI wiring: 2–4 days
- QA and polish: 1 day

## Risks & Mitigations
- Risk: Tenant lookup ambiguity (multiple matches). Mitigation: query by exact `real_property_code + unit_no`; if multiple, surface a clear choice or rule.
- Risk: Meter rollover scenarios. Mitigation: allow current < previous with explicit confirmation; flag in remarks.
- Risk: Performance on large datasets. Mitigation: indexes and efficient queries; lazy loading where appropriate.

## Conclusion
The QR Meter Reading module is functionally ready for scanning, QR generation, and printing. The next critical step is implementing reading persistence using the existing `t_tenant_reading` table with minimal change, alongside UX fixes and SweetAlert adoption. Utility Rate Management remains on the roadmap with a service-layer approach for safe, auditable bulk updates.
