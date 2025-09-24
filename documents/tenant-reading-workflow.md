# Tenant Reading Workflow - QR Meter Reading

## Overview
End-to-end flow from QR scan to database writes, including tenant lookup, default value resolution, date calculations, and audit trail persistence.

---

## 1) High-Level Process
```mermaid
flowchart TD
    A[Scan QR] --> B[Parse Data]
    B --> C{Resolve Tenant}
    C -->|Active Tenant Found| D[Fetch Last Reading via vw_TenantReading]
    C -->|Not Found| E[Fallback: Get Last Active Tenant from vw_TenantReading]
    E --> F2{Last Tenant Found?}
    F2 -->|Yes| D
    F2 -->|No| E2[Error: No tenant history for unit]
    D --> G[Calculate Periods]
    G --> H[Compose Payload]
    H --> I[Save t_tenant_reading]
    I --> J[Save t_tenant_reading_ext]
    J --> K[Response + SweetAlert Success]
```

Notes:
- QR content: `propertyCode|unitNo` (+ optional `meter_id`)
- **Primary tenant resolution**: `m_tenant` by `real_property_code = propertyCode AND unit_no = unitNo AND ISNULL(terminated,'N') = 'N'`
- **Fallback tenant resolution**: If no active tenant, get last active tenant from `vw_TenantReading` for same property/unit
- Last reading: `vw_TenantReading` by unit (not tenant)

---

## 2) Detailed Backend Workflow
```mermaid
sequenceDiagram
    participant UI as Mobile UI
    participant API as save-reading.php
    participant DB as MSSQL

    UI->>API: POST { propertyCode, unitNo, currentReading, remarks }
    API->>DB: SELECT tenant_code FROM m_tenant WHERE real_property_code=@propertyCode AND unit_no=@unitNo AND ISNULL(terminated,'N')='N'
    DB-->>API: tenant_code (or no rows)
    alt Active tenant found
        API->>DB: SELECT TOP 1 * FROM vw_TenantReading WHERE real_property_code=@propertyCode AND unit_no=@unitNo ORDER BY date_created DESC
    else No active tenant - fallback
        API->>DB: SELECT TOP 1 tenant_code FROM vw_TenantReading WHERE real_property_code=@propertyCode AND unit_no=@unitNo ORDER BY date_created DESC
        DB-->>API: last active tenant_code (or no rows)
        alt Fallback tenant found
            API->>DB: SELECT TOP 1 * FROM vw_TenantReading WHERE real_property_code=@propertyCode AND unit_no=@unitNo ORDER BY date_created DESC
        else No tenant history
            API-->>UI: Error: No tenant history for unit
        end
    end
    DB-->>API: last reading row (if any)
    API->>API: calculate reading/billing periods (move-in/out overrides if needed)
    API->>DB: INSERT INTO t_tenant_reading (..., reading_date=GETDATE(), remarks, ...)
    DB-->>API: reading_id
    API->>DB: INSERT INTO t_tenant_reading_ext (reading_id, ip_address, user_agent, ...)
    DB-->>API: ok
    API-->>UI: { success, data: { readingId, prevReading, currentReading, readingDate, periods } }
```

---

## 3) Move-In/Out Rules (Overrides)
- Move-out reading (between tenants):
  - Save under last active tenant; `date_to = move_out_date`, `billing_date_to = move_out_date + 1 day`
- Next tenant in same month:
  - `date_from = previous date_to + 1 day`, month-end rules for `date_to`; billing = next month
- `prev_reading`: unit-level (via `vw_TenantReading`), not tenant-limited

---

## 4) Data Mapping Summary
- Request: `propertyCode`, `unitNo`, `currentReading`, `remarks`
- Derived: `readingDate` (server time), `prevReading` (view), `date_from/date_to`, `billing_date_from/billing_date_to`
- Writes:
  - `t_tenant_reading`: tenant_code, periods, readings, reading_date, reading_by, remarks
  - `t_tenant_reading_ext`: ip_address, user_agent, device_info, location_data

---

## 5) UI Behavior
- Reading Date field: read-only in UI; server sets actual timestamp
- After successful QR scan: autofocus Current Meter Reading input
- SweetAlert on success/error with concise summary

---

## 6) Reporting Linkage
- Report endpoint queries `t_tenant_reading` (+ join `t_tenant_reading_ext`) by date range/property/technician
- Usage = `current_reading - prev_reading`
- Exports: PDF, Excel, CSV
