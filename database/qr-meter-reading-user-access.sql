USE [RMS];
GO

-- =====================================================
-- QR Meter Reading System - User Access Configuration
-- Adds module, user group, and access permissions
-- 
-- UPDATED: Grants access to ALL existing user groups
-- This ensures all current RMS users can access QR Meter Reading
-- while still creating a dedicated Field Technician group
-- =====================================================

-- Declare variables for transaction
DECLARE @qr_module_id INT;
DECLARE @field_tech_group_code INT;
DECLARE @error_message NVARCHAR(4000);

-- Begin transaction for atomicity
BEGIN TRANSACTION;

BEGIN TRY
    -- 1. Add QR Meter Reading Module (s_modules does NOT have IDENTITY - must provide module_id)
    -- Find the next available module_id
    SELECT @qr_module_id = ISNULL(MAX(module_id), 0) + 1 FROM s_modules;
    
    INSERT INTO s_modules (module_id, module_name)
    VALUES (@qr_module_id, 'QR METER READING');
    
    -- 2. Add Field Technician User Group (let database assign group_code)
    INSERT INTO s_user_group (group_desc)
    VALUES ('FIELD TECHNICIAN');
    
    -- Get the assigned group_code using SCOPE_IDENTITY()
    SET @field_tech_group_code = SCOPE_IDENTITY();
    
    -- 3. Grant ALL user groups access to QR Meter Reading module
    -- (This includes the new Field Technician group and all existing groups)
    INSERT INTO s_user_group_modules (group_code, module_id)
    SELECT group_code, @qr_module_id
    FROM s_user_group;
    
    -- If we get here, everything succeeded - commit the transaction
    COMMIT TRANSACTION;
    
    PRINT 'QR Meter Reading access configuration completed successfully.';
    PRINT 'Module ID: ' + CAST(@qr_module_id AS NVARCHAR(10));
    PRINT 'Field Technician Group Code: ' + CAST(@field_tech_group_code AS NVARCHAR(10));
    
END TRY
BEGIN CATCH
    -- If any error occurred, rollback the entire transaction
    ROLLBACK TRANSACTION;
    
    -- Get error information
    SET @error_message = ERROR_MESSAGE();
    
    -- Log the error
    PRINT 'Error occurred during QR Meter Reading access configuration:';
    PRINT 'Error Number: ' + CAST(ERROR_NUMBER() AS NVARCHAR(10));
    PRINT 'Error Severity: ' + CAST(ERROR_SEVERITY() AS NVARCHAR(10));
    PRINT 'Error State: ' + CAST(ERROR_STATE() AS NVARCHAR(10));
    PRINT 'Error Message: ' + @error_message;
    PRINT 'Error Line: ' + CAST(ERROR_LINE() AS NVARCHAR(10));
    
    -- Re-raise the error
    THROW;
END CATCH;

-- =====================================================
-- VERIFICATION QUERIES (Only run if transaction succeeded)
-- =====================================================

-- Check if transaction was successful by verifying module exists
IF EXISTS (SELECT 1 FROM s_modules WHERE module_name = 'QR METER READING')
BEGIN
    -- Get the actual IDs for verification
    SELECT @qr_module_id = module_id FROM s_modules WHERE module_name = 'QR METER READING';
    SELECT @field_tech_group_code = group_code FROM s_user_group WHERE group_desc = 'FIELD TECHNICIAN';
    
    PRINT 'Verification: Transaction completed successfully.';
    
    -- Verify module was added
    SELECT 'Module Added:' as Status, module_id, module_name 
    FROM s_modules 
    WHERE module_id = @qr_module_id;

    -- Verify user group was added  
    SELECT 'User Group Added:' as Status, group_code, group_desc
    FROM s_user_group 
    WHERE group_code = @field_tech_group_code;

    -- Verify access was granted to ALL user groups
    SELECT 'Access Granted to All Groups:' as Status, 
           ug.group_code, 
           ug.group_desc, 
           m.module_id, 
           m.module_name
    FROM s_user_group_modules ugm
    INNER JOIN s_user_group ug ON ugm.group_code = ug.group_code
    INNER JOIN s_modules m ON ugm.module_id = m.module_id
    WHERE ugm.module_id = @qr_module_id
    ORDER BY ug.group_code;

    -- Count total user groups with QR Meter Reading access
    SELECT 'Total Groups with Access:' as Status, COUNT(*) as group_count
    FROM s_user_group_modules ugm
    WHERE ugm.module_id = @qr_module_id;
END
ELSE
BEGIN
    PRINT 'Verification: Transaction failed - no verification queries will run.';
    PRINT 'Check error messages above for details.';
END

-- =====================================================
-- USAGE INSTRUCTIONS
-- =====================================================

/*
QR METER READING ACCESS CONFIGURATION:

✅ ALL EXISTING USER GROUPS NOW HAVE ACCESS TO QR METER READING MODULE
✅ NEW FIELD TECHNICIAN GROUP CREATED FOR DEDICATED QR METER READING USERS

TO ASSIGN QR METER READING ACCESS TO A USER:

Option 1: Assign to any existing user group (all have access)
1. Find the user in s_users table:
   SELECT user_id, first_name, last_name FROM s_users WHERE user_id = 'USER_ID_HERE';

2. Assign the user to any existing group:
   INSERT INTO s_user_group_users (user_id, group_code) 
   VALUES ('USER_ID_HERE', [EXISTING_GROUP_CODE]);

Option 2: Assign to new Field Technician group (recommended for QR meter reading)
1. Find the user in s_users table:
   SELECT user_id, first_name, last_name FROM s_users WHERE user_id = 'USER_ID_HERE';

2. Find the Field Technician group code:
   SELECT group_code FROM s_user_group WHERE group_desc = 'FIELD TECHNICIAN';

3. Assign the user to Field Technician group (replace [FIELD_TECH_GROUP_CODE] with actual code):
   INSERT INTO s_user_group_users (user_id, group_code) 
   VALUES ('USER_ID_HERE', [FIELD_TECH_GROUP_CODE]);

4. Verify assignment:
   SELECT u.user_id, u.first_name, u.last_name, ug.group_desc
   FROM s_users u
   INNER JOIN s_user_group_users ugu ON u.user_id = ugu.user_id  
   INNER JOIN s_user_group ug ON ugu.group_code = ug.group_code
   WHERE ug.group_desc = 'FIELD TECHNICIAN';

QR METER READING MODULE INCLUDES ACCESS TO:
- QR Scanner/Reading Interface (pages/qr-meter-reading/)
- QR Generator (pages/qr-generator/)  
- Tenant Reading Management (future implementation)
- Meter Reading Reports (pages/qr-meter-reading/api/meter-reading-report.php)

CURRENT USER GROUPS WITH ACCESS:
- ALL EXISTING USER GROUPS (automatically granted access)
- FIELD TECHNICIAN (new group created) - NEW

To see all groups with QR Meter Reading access:
SELECT ug.group_code, ug.group_desc, m.module_name
FROM s_user_group_modules ugm
INNER JOIN s_user_group ug ON ugm.group_code = ug.group_code
INNER JOIN s_modules m ON ugm.module_id = m.module_id
WHERE m.module_name = 'QR METER READING'
ORDER BY ug.group_code;
*/
