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

## 7) Complete QR Scanning Flow with Meter Replacement Validation

### **Comprehensive QR Meter Reading Flow**
```mermaid
flowchart TD
    A[User Opens QR Scanner App] --> B[Check Authentication]
    B -->|Not Authenticated| C[Show Login Form]
    B -->|Authenticated| D[Check QR Access Permissions]
    C --> E[Real-time Login Validation]
    E -->|Valid| D
    E -->|Invalid| F[Show Inline Error Messages]
    F --> C
    D -->|Has Access| G[Show QR Scanner Interface]
    D -->|No Access| H[Show Access Denied Page]
    
    G --> I[User Clicks Start Scanner]
    I --> J[Camera Activates]
    J --> K[User Scans QR Code]
    K --> L[Parse QR Data]
    L --> M[Auto-populate Form Fields]
    M --> N[Focus Current Reading Input]
    N --> O[User Enters Current Reading]
    O --> P[Validate Current Reading]
    P -->|Valid| Q[Check if Current < Previous]
    P -->|Invalid| R[Show Inline Validation Error]
    R --> O
    
    Q -->|Current >= Previous| S[Normal Reading Process]
    Q -->|Current < Previous| T[Meter Replacement Validation]
    
    T --> U[Show SweetAlert: Is this a new meter?]
    U -->|Yes| V[Add Meter Replacement Remark]
    U -->|No| W[Block Submission - Show Error]
    W --> X[Inform User: Provide Valid Reading]
    X --> O
    
    V --> Y[Set Previous Reading to 0]
    Y --> S
    
    S --> Z[Calculate Reading Periods]
    Z --> AA[Save to Database]
    AA --> BB[Show Success Toast Notification]
    BB --> CC[Auto-reset Form]
    CC --> DD[Focus Scanner for Next Reading]
    DD --> K
    
    H --> II[User Clicks Login Button]
    II --> C
    
    style T fill:#ffeb3b,stroke:#f57f17,color:black
    style U fill:#ffeb3b,stroke:#f57f17,color:black
    style V fill:#4caf50,stroke:#2e7d32,color:white
    style W fill:#f44336,stroke:#c62828,color:white
    style Y fill:#4caf50,stroke:#2e7d32,color:white
```

### **Meter Replacement Validation Logic**
```mermaid
flowchart TD
    A[Current Reading Entered] --> B{Current < Previous?}
    B -->|No| C[Proceed with Normal Flow]
    B -->|Yes| D[Trigger Meter Replacement Validation]
    
    D --> E[Show SweetAlert Dialog]
    E --> F[Title: Meter Replacement Detected]
    F --> G[Message: Current reading is less than previous reading. Is this a new meter?]
    G --> H[Options: Yes / No]
    
    H -->|User Selects: No| I[Block Form Submission]
    I --> J[Show Error Message]
    J --> K[Message: Please provide a valid current reading]
    K --> L[Return to Form Input]
    
    H -->|User Selects: Yes| M[Proceed with Meter Replacement]
    M --> N["Add Remark: New meter installed on date"]
    N --> O[Set Previous Reading to 0]
    O --> P[Allow Form Submission]
    P --> Q[Save with Adjusted Data]
    
    style D fill:#ff9800,stroke:#e65100,color:white
    style E fill:#ff9800,stroke:#e65100,color:white
    style M fill:#4caf50,stroke:#2e7d32,color:white
    style I fill:#f44336,stroke:#c62828,color:white
    style N fill:#4caf50,stroke:#2e7d32,color:white
    style O fill:#4caf50,stroke:#2e7d32,color:white
```

### **Database Integration for Meter Replacement**
```mermaid
sequenceDiagram
    participant UI as Frontend
    participant API as save-reading.php
    participant DB as MSSQL Database
    
    UI->>API: POST { currentReading, remarks, meterReplacement: true }
    API->>API: Validate meter replacement flag
    API->>DB: UPDATE previous_reading = 0 WHERE meter_replacement = true
    API->>DB: INSERT INTO t_tenant_reading (prev_reading = 0, remarks = 'New meter installed on date')
    DB-->>API: reading_id
    API->>DB: INSERT INTO t_tenant_reading_ext (reading_id, meter_replacement_flag = true)
    DB-->>API: success
    API-->>UI: { success: true, message: 'Meter replacement reading saved successfully' }
```

---

## 8) Meter Replacement Validation Specification

### **Business Requirements**
- **Trigger Condition**: Current reading < Previous reading
- **User Prompt**: SweetAlert dialog asking "Is this a new meter?"
- **User Options**: 
  - **Yes**: Proceed with meter replacement logic
  - **No**: Block submission, inform user to provide valid reading
- **Meter Replacement Logic**: 
  - Add remark about new meter installation with current date
  - Set previous reading to 0 in database
  - Allow submission to proceed
  - Flag reading as meter replacement for audit trail

### **Technical Implementation**
- **Frontend Validation**: JavaScript validation in `app.js` before form submission
- **SweetAlert Integration**: Context-appropriate dialog for meter replacement confirmation
- **Database Logic**: Create separate meter replacement stored procedure (based on `save-tenant-reading-procedure.sql`) to handle previous reading = 0
- **Remarks Integration**: Automatic remark addition for new meter scenarios
- **Audit Trail**: Flag meter replacement readings in `t_tenant_reading_ext` table

### **Implementation Files**
- `pages/qr-meter-reading/assets/js/app.js` - Add validation logic
- `database/save-tenant-reading-procedure.sql` - Reference for creating separate meter replacement procedure
- `pages/qr-meter-reading/api/save-reading.php` - Handle meter replacement flag

### **Success Criteria**
- [ ] Validation triggers when current reading < previous reading
- [ ] SweetAlert dialog appears with "Is this a new meter?" prompt
- [ ] "No" option blocks submission and shows error message
- [ ] "Yes" option proceeds with meter replacement logic
- [ ] Remarks automatically updated with new meter information and current date
- [ ] Previous reading set to 0 in database for new meters
- [ ] Meter replacement flag added to audit trail
- [ ] User experience is clear and intuitive
- [ ] No impact on normal meter reading workflow

---

## 6) Reporting Linkage
- Report endpoint queries `t_tenant_reading` (+ join `t_tenant_reading_ext`) by date range/property/technician
- Usage = `current_reading - prev_reading`
- Exports: PDF, Excel, CSV
