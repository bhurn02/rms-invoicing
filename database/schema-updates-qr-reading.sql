-- =====================================================
-- QR Meter Reading System - Database Schema Updates
-- =====================================================
-- This script adds new columns and tables required for
-- the enhanced QR meter reading functionality
-- =====================================================

USE [RMS]; -- Replace with your actual database name
GO

-- =====================================================
-- 1. UPDATE t_tenant_reading TABLE
-- =====================================================
-- Add new tracking columns to existing table

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_NAME = 't_tenant_reading' 
               AND COLUMN_NAME = 'reading_date')
BEGIN
    ALTER TABLE t_tenant_reading 
    ADD reading_date datetime NULL;           -- Actual date/time reading taken
    
    PRINT 'Added reading_date column to t_tenant_reading table';
END
ELSE
BEGIN
    PRINT 'reading_date column already exists in t_tenant_reading table';
END

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_NAME = 't_tenant_reading' 
               AND COLUMN_NAME = 'reading_by')
BEGIN
    ALTER TABLE t_tenant_reading 
    ADD reading_by nvarchar(32) NULL;        -- Technician who took reading
    
    PRINT 'Added reading_by column to t_tenant_reading table';
END
ELSE
BEGIN
    PRINT 'reading_by column already exists in t_tenant_reading table';
END

-- =====================================================
-- 2. CREATE t_tenant_reading_ext TABLE
-- =====================================================
-- New table for audit trail and metadata

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES 
               WHERE TABLE_NAME = 't_tenant_reading_ext')
BEGIN
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
    
    PRINT 'Created t_tenant_reading_ext table';
    
    -- Performance indexes for audit queries
    CREATE INDEX IX_reading_ext_reading_id ON t_tenant_reading_ext(reading_id);
    CREATE INDEX IX_reading_ext_audit ON t_tenant_reading_ext(ip_address, created_date);
    
    PRINT 'Created performance indexes for t_tenant_reading_ext table';
END
ELSE
BEGIN
    PRINT 't_tenant_reading_ext table already exists';
END

-- =====================================================
-- 3. VERIFY s_tenant_reading_default TABLE EXISTS
-- =====================================================
-- This table should already exist for default values

IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES 
           WHERE TABLE_NAME = 's_tenant_reading_default')
BEGIN
    PRINT 's_tenant_reading_default table exists - ready for default values lookup';
    
    -- Show current structure
    SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 's_tenant_reading_default'
    ORDER BY ORDINAL_POSITION;
END
ELSE
BEGIN
    PRINT 'WARNING: s_tenant_reading_default table does not exist';
    PRINT 'This table is required for default charge code values';
END

-- =====================================================
-- 4. VERIFY vw_TenantReading VIEW EXISTS
-- =====================================================
-- This view should already exist for tenant lookup

IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.VIEWS 
           WHERE TABLE_NAME = 'vw_TenantReading')
BEGIN
    PRINT 'vw_TenantReading view exists - ready for tenant lookup';
END
ELSE
BEGIN
    PRINT 'WARNING: vw_TenantReading view does not exist';
    PRINT 'This view is required for tenant resolution and reading lookup';
END

-- =====================================================
-- 5. VERIFICATION QUERIES
-- =====================================================
-- Test the new schema

PRINT '=== SCHEMA VERIFICATION ===';

-- Check t_tenant_reading structure
SELECT 't_tenant_reading columns:' as info;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 't_tenant_reading'
ORDER BY ORDINAL_POSITION;

-- Check t_tenant_reading_ext structure
SELECT 't_tenant_reading_ext columns:' as info;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 't_tenant_reading_ext'
ORDER BY ORDINAL_POSITION;

-- Check foreign key constraint
SELECT 'Foreign key constraints:' as info;
SELECT 
    fk.name as constraint_name,
    OBJECT_NAME(fk.parent_object_id) as table_name,
    COL_NAME(fkc.parent_object_id, fkc.parent_column_id) as column_name,
    OBJECT_NAME(fk.referenced_object_id) as referenced_table_name,
    COL_NAME(fkc.referenced_object_id, fkc.referenced_column_id) as referenced_column_name
FROM sys.foreign_keys fk
INNER JOIN sys.foreign_key_columns fkc ON fk.object_id = fkc.constraint_object_id
WHERE OBJECT_NAME(fk.parent_object_id) = 't_tenant_reading_ext';

PRINT '=== SCHEMA UPDATE COMPLETE ===';
PRINT 'The QR reading system is now ready for enhanced functionality';
GO
