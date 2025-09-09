You're a senior business analyst and also a senior full stack developer. I need your help with organizing my thoughts and to be able to document my current issues and a provide solution. 

I am planning to add some major enhancement to our current RMS system.

This is an old system built in PHP 5.3 but i was able to add feature using PHP 7.2 (pages/utilities folder).  Currently on a monthly basis before generating invoices for tenant, rms admin will have to input the new CNMI Electric and LEAC rate both for commercial and residential as this changes as well on a monthly basis. 

1. The issue is that they have to manually locate all active tenant and edit the rate changes which is very tedious specially when there are like 100+ tenants. 
    - m_units table should have new column, is_residential default to 1
    - There should only be a single point of entry (page) for Electric and LEAC rate and when submitted, it will automatically updates all active tenants charge code: CUCNF rate (Electric Rate) and CUCF (LEAC) rate and value depends if residential or not

2. Also for meter reading per tenant that don't have their own meter (tenant_reading.php). Staff will have to take rounds on the actual location of units, manually write down meter reading then rms admin will have to input the meter reading to this system. We want to reduce the process by using their mobile phone, scan a QR code on the meter for inputting the meter reading: 
    Web-Based Scanner Workflow (Idea)
    1. User opens browser > navigates to your RMS system Meter QR Scanning page
    2. If user is not yet logged in, User logs in > authenticated session
    3. User clicks "Scan Meter" > camera activates 
    4. User scans QR code 
    5. System parses data > extracts property ID, unit number, (optional) meter id
    6. Form auto-populates > user just enters meter reading
    7. User submits > data saved to database
    8. property ID, unit number, (optional) meter id and reading displayed on the UI table 
    9. Repeat > scan next meter

If you have a better solution for issue #2, feel free to suggest. 

Please understand clearly the existing code base so you can utilize whatever is already existing and have a better context on the system.    
You may also use sqlcmd to connect and read the actual content of database table for you to have a deep understanding of the current system.

References:
database/RMS database schema 2025-08-06 1531.sql
database/RMS database table views 2025-08-28 1600.sql
database/RMS database stored procedure 2025-08-28 1600.sql
database/vw_TenantReading.sql
pages/utilities/config.local.php (mssql login info)

table that might help:
m_real_property
m_units
m_tenant
t_tenant_reading
m_tenant_charges
m_unit_charges
m_space_type

Tech Specs:
Windows Server 2019
IIS
PHP 7.2
MSSQL 2019
use Bootstrap 5 with modern design

I will also need a qr code generator for all the active tenant. and improve qr code to include in the printed qr code the real property and unit code. add it in qr-generator.html there should be additional section to batch generate depending on the selected active tenants from a table.


so the idea is, since we are just capturing the real_property_code and unit_no when scanning QR code, during saving, we need to identify the active tenant that is using that real_property_code + unit_no then save it to t_tenant_reading using the tenant_code, 

first, we need to check s_tenant_reading_default if there is a set default values for trd_charge_code CUCF or CUCNF and use those values, if no records then we use the following formula: for columns date_from and date_to, reading period usually is between 25th-29th of the month and date_from and date_to value is the 1st and last day of the reading date, and for the billing_date_from and billing_date_to values are the 1st and last day of the next month of reading date: 
e.g.
Actual reading date: 08/29/2025 1428

current reading: 10510
last reading: 10374

data saved are:
date_from: 08/01/2025
date_to: 08/31/2025
billing_date_from: 09/01/2025
billing_date_to: 09/30/2025
curr_reading: 10510
prev_reading: 10374



we also need to add new columns to t_tenant_reading:
reading_date datetime
reading_by nvarchar(32)

and a new table t_tenant_reading_ext (ext neabs extebded properties)
id primary
reading_id int foreign key
ip_address
user_agent
other useful meta data for audit log


there should also be a meter reading report with filter so that we can review and validate and audit if readings are correct.


I would also like to add the scenario for tenant move in/out and how date period value for reading are made where default values are not used:

e.g.
real_property_code: GC A
unit_no: 101

tenant_code: 001
tenant_name: AARON
move out date: 08/15/2025

tenant_code: 002
tenant_name: ALDRICH
move out date: 08/16/2025

technician will have to make a reading 08/16/2025 after tenant 001 moved out and before tenant 002 move in:

reading date: 08/16/2025
curr_reading: 10510
prev_reading: 10374

t_tenant_reading values saved will be:
tenant_code: 001
date_from: 08/01/2025
date_to: 08/15/2025
billing_date_from: 08/01/2025
billing_date_to: 08/16/2025 (+1day to move out date)
reading_date: 08/16/2025
curr_reading: 10510
prev_reading: 10374

technician proceeds with scheduled monthly 25th-29th reading:
tenant_code: 002
date_from: 08/16/2025 (do we get last reading date_to then add 1 day?)
date_to: 08/31/2025 (still follows default from s_tenant_reading_default)
billing_date_from: 09/01/2025 (still follows default from s_tenant_reading_default)
billing_date_to: 09/30/2025 (still follows default from s_tenant_reading_default)
reading_date: 08/26/2025
curr_reading: 10585
prev_reading: 10510 (get last know reading via property_code + unit_no, refer to vw_TenantReading.sql)

vw_TenantReading.sql


also include in the implementation task that since t_tenant_reading has remarks column, we should also be able to input it during qr meter reading. Reading Date in meter reading form should be read only as we don't want technicians to tamper with the actual reading date. also upon successful qr scan, it should auto focus on current meter reading input field.


we also need to add entries for the following tables for the module (user page access rights), we need to add the QR Meter Reading, also a new user group  Field Technician
s_modules
s_user_group
s_user_group_modules


PLAN, can you update @utility-rate-management-implementation v1.0.md to utility-rate-management-implementation v1.1.md and @memory-bank documentation. As a veteran front-end developer, follow the best practice for modern UI and UX design principles, focusing on user-centric convenience. UI/UX to improve:

1. When to use notifications, dialog box and alerts. I've noticed you are using dialog box when logging out when the modern UX design practice does not have a dialog box for log out. Also for invalid user name and passwords, you are using dialog box sweet alerts, adding more clicks interactions for the user, making it inefficient. 

2. Upon successful submit reading, you are showing sweet alert with only continue button. The purpose of QR meter reading scanning multiple meter reading, you should make the UX seamless. Provide options for users to proceed with the next scan and minimize user interactions or additional click events.

3. Field Technicians who are doing the scans have limited mobile data or possibly does not have internet access, this project should work even when offline and has capability to sync the offline data processed. Similar to modern web apps, they can work offline and has sync option at top with progress, you can either manually sync or auto sync.

Target device that will use QR Meter Reading are:
Samsung A15
iPhone 14 pro max