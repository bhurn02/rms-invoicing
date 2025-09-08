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

-- 1. Add QR Meter Reading Module
INSERT INTO s_modules (module_id, module_name)
VALUES (25, 'QR METER READING');

-- 2. Add Field Technician User Group
INSERT INTO s_user_group (group_code, group_desc)
VALUES (12, 'FIELD TECHNICIAN');

-- 3. Grant Field Technician access to QR Meter Reading module
INSERT INTO s_user_group_modules (group_code, module_id)
VALUES (12, 25);

-- 4. Grant ALL existing user groups access to QR Meter Reading module
-- (This includes all groups that existed before adding Field Technician)
INSERT INTO s_user_group_modules (group_code, module_id)
SELECT group_code, 25
FROM s_user_group
WHERE group_code NOT IN (12); -- Exclude Field Technician (already added above)

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Verify module was added
SELECT 'Module Added:' as Status, module_id, module_name 
FROM s_modules 
WHERE module_id = 25;

-- Verify user group was added  
SELECT 'User Group Added:' as Status, group_code, group_desc
FROM s_user_group 
WHERE group_code = 12;

-- Verify access was granted to ALL user groups
SELECT 'Access Granted to All Groups:' as Status, 
       ug.group_code, 
       ug.group_desc, 
       m.module_id, 
       m.module_name
FROM s_user_group_modules ugm
INNER JOIN s_user_group ug ON ugm.group_code = ug.group_code
INNER JOIN s_modules m ON ugm.module_id = m.module_id
WHERE ugm.module_id = 25
ORDER BY ug.group_code;

-- Count total user groups with QR Meter Reading access
SELECT 'Total Groups with Access:' as Status, COUNT(*) as group_count
FROM s_user_group_modules ugm
WHERE ugm.module_id = 25;

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

2. Assign the user to Field Technician group:
   INSERT INTO s_user_group_users (user_id, group_code) 
   VALUES ('USER_ID_HERE', 12);

3. Verify assignment:
   SELECT u.user_id, u.first_name, u.last_name, ug.group_desc
   FROM s_users u
   INNER JOIN s_user_group_users ugu ON u.user_id = ugu.user_id  
   INNER JOIN s_user_group ug ON ugu.group_code = ug.group_code
   WHERE ugu.group_code = 12;

QR METER READING MODULE INCLUDES ACCESS TO:
- QR Scanner/Reading Interface (pages/qr-meter-reading/)
- QR Generator (pages/qr-generator/)  
- Tenant Reading Management (future implementation)
- Meter Reading Reports (pages/qr-meter-reading/api/meter-reading-report.php)

CURRENT USER GROUPS WITH ACCESS:
- ADMINISTRATOR (group_code: 1)
- ADMIN ASSISTANT (group_code: 7)
- ACCOUNTING (group_code: 9)
- SALES EXECUTIVE (group_code: 10)
- OFFICE ADMIN (group_code: 11)
- FIELD TECHNICIAN (group_code: 12) - NEW
*/
