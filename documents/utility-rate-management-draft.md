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