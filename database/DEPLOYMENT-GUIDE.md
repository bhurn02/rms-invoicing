# QR Meter Reading System - Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying the QR Meter Reading System database components.

## Prerequisites
- SQL Server Management Studio (SSMS) or sqlcmd access
- Database user with CREATE PROCEDURE and ALTER TABLE permissions
- Backup of the RMS database (recommended)

## Deployment Steps

### 1. Database Schema Updates
Execute the schema update script to add required columns and tables:

```sql
-- Run this script first
database/schema-updates-qr-reading.sql
```

**What this script does:**
- Adds `reading_date` and `reading_by` columns to `t_tenant_reading`
- Creates `t_tenant_reading_ext` table for audit trail
- Creates performance indexes
- Verifies existing tables and views

### 2. Stored Procedure Deployment
Execute the stored procedure creation script:

```sql
-- Run this script second
database/save-tenant-reading-procedure.sql
```

**What this script does:**
- Creates `sp_t_SaveTenantReading` stored procedure
- Implements all business logic for meter reading
- Handles tenant lookup, date calculations, and audit trail
- Grants execute permission to web_app user

### 3. Verification Steps

#### Verify Schema Updates
```sql
-- Check if new columns exist
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 't_tenant_reading' 
  AND COLUMN_NAME IN ('reading_date', 'reading_by')
ORDER BY ORDINAL_POSITION;

-- Check if new table exists
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 't_tenant_reading_ext';
```

#### Verify Stored Procedure
```sql
-- Check if procedure exists
SELECT ROUTINE_NAME, ROUTINE_TYPE 
FROM INFORMATION_SCHEMA.ROUTINES 
WHERE ROUTINE_NAME = 'sp_t_SaveTenantReading';

-- Test procedure with sample data
EXEC [dbo].[sp_t_SaveTenantReading]
    @propertyCode = 'GCA',
    @unitNo = '101',
    @currentReading = 10510.00,
    @remarks = 'Test reading',
    @readingBy = 'test_user',
    @createdBy = 'test_admin',
    @ipAddress = '127.0.0.1',
    @userAgent = 'Test Browser',
    @deviceInfo = 'Test Device';
```

## Rollback Plan

### If Schema Updates Fail
```sql
-- Remove added columns (if needed)
ALTER TABLE t_tenant_reading DROP COLUMN reading_date;
ALTER TABLE t_tenant_reading DROP COLUMN reading_by;

-- Drop new table (if needed)
DROP TABLE t_tenant_reading_ext;
```

### If Stored Procedure Fails
```sql
-- Drop procedure
DROP PROCEDURE [dbo].[sp_t_SaveTenantReading];
```

## Testing

### 1. Test Database Connection
Run the test script to verify database connectivity:
```bash
cd pages/qr-meter-reading
php test-db-connection.php
```

### 2. Test QR Reading Flow
1. Open the QR Meter Reading page
2. Scan a QR code or manually enter property/unit
3. Enter a meter reading value
4. Submit the form
5. Verify data is saved to both tables

### 3. Verify Data Integrity
```sql
-- Check recent readings
SELECT TOP 10 * FROM t_tenant_reading ORDER BY date_created DESC;

-- Check audit trail
SELECT TOP 10 * FROM t_tenant_reading_ext ORDER BY created_date DESC;

-- Verify foreign key relationship
SELECT r.reading_id, ext.id, ext.ip_address
FROM t_tenant_reading r
INNER JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
ORDER BY r.date_created DESC;
```

## Troubleshooting

### Common Issues

#### 1. Permission Denied
```sql
-- Grant necessary permissions
GRANT EXECUTE ON [dbo].[sp_t_SaveTenantReading] TO [web_app];
GRANT INSERT, UPDATE ON t_tenant_reading TO [web_app];
GRANT INSERT ON t_tenant_reading_ext TO [web_app];
```

#### 2. Foreign Key Constraint Violation
- Verify `reading_id` data types match between tables
- Check if referenced `reading_id` exists in `t_tenant_reading`

#### 3. Stored Procedure Errors
- Check SQL Server error logs
- Verify all required tables exist
- Test procedure with minimal parameters

### Error Logs
The stored procedure returns error details in the result set:
```sql
-- Errors are returned directly from the stored procedure
-- Check the application logs for detailed error information
```

## Performance Considerations

### Indexes
The deployment script creates these performance indexes:
- `IX_reading_ext_reading_id` on `t_tenant_reading_ext(reading_id)`
- `IX_reading_ext_audit` on `t_tenant_reading_ext(ip_address, created_date)`

### Monitoring
Monitor these performance metrics:
- Stored procedure execution time
- Transaction completion rates
- Lock contention on reading tables

## Security Notes

### Input Validation
- All input parameters are validated in the stored procedure
- SQL injection protection via parameterized queries
- Audit trail captures IP addresses and user agents

### Access Control
- Only authenticated users can execute the procedure
- Web application user has minimal required permissions
- Consider implementing row-level security if needed

## Support

For deployment issues:
1. Check SQL Server error logs
2. Verify database permissions
3. Test with minimal data first
4. Review stored procedure execution plan

## Next Steps

After successful deployment:
1. Update application configuration if needed
2. Train users on new QR reading workflow
3. Monitor system performance
4. Plan for production deployment
