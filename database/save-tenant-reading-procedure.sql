USE [RMS];
GO

-- Drop procedure if it exists
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[sp_t_SaveTenantReading]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[sp_t_SaveTenantReading];
GO

CREATE PROCEDURE [dbo].[sp_t_SaveTenantReading]
    @propertyCode NVARCHAR(10),
    @unitNo NVARCHAR(10),
    @currentReading DECIMAL(18,2),
    @remarks NVARCHAR(500) = NULL,
    @readingBy NVARCHAR(32),
    @createdBy NVARCHAR(32),
    @ipAddress NVARCHAR(45) = NULL,
    @userAgent NVARCHAR(500) = NULL,
    @deviceInfo NVARCHAR(200) = 'QR System Mobile'
AS
/*
Author		:	Aldrich Delos Santos
Date		:	2025-08-29

Description	:	Stored procedure to save meter readings with business logic for date calculations
                and comprehensive audit trail. Handles tenant lookup, reading validation,
                and saves to both t_tenant_reading and t_tenant_reading_ext tables.
                
Parameters	:	@propertyCode		- Property code from QR scan
                @unitNo			- Unit number from QR scan
                @currentReading	- Current meter reading value
                @remarks		- Optional remarks/notes
                @readingBy		- Username/ID of technician taking reading
                @createdBy		- User ID creating the record
                @ipAddress		- IP address for audit trail
                @userAgent		- User agent for audit trail
                @deviceInfo		- Device information for audit trail

Returns		:	JSON response with success status and reading details
                or error message if operation fails

Tables Used	:	m_tenant, t_tenant_reading, t_tenant_reading_ext, vw_TenantReading
                s_tenant_reading_default (for default values)

Business Logic:
- Primary tenant lookup: Active tenant by property + unit
- Fallback: Last active tenant from vw_TenantReading
- Reading period: 1st to last day of reading month
- Billing period: 1st to last day of next month
- Move-in/out overrides for transition readings
- Comprehensive audit trail with extended properties
*/
BEGIN
    SET NOCOUNT ON;
    
    -- Declare variables
    DECLARE @tenantCode NVARCHAR(10);
    DECLARE @tenantName NVARCHAR(100);
    DECLARE @prevReading DECIMAL(18,2);
    DECLARE @readingDate DATETIME = GETDATE();
    DECLARE @dateFrom DATETIME;
    DECLARE @dateTo DATETIME;
    DECLARE @billingDateFrom DATETIME;
    DECLARE @billingDateTo DATETIME;
    DECLARE @readingId DECIMAL(18,0);
    DECLARE @errorMessage NVARCHAR(500);
    DECLARE @errorSeverity INT;
    DECLARE @errorState INT;
    
    BEGIN TRY
        -- Validate input parameters
        IF @propertyCode IS NULL OR @unitNo IS NULL OR @currentReading IS NULL
        BEGIN
            RAISERROR('Property code, unit number, and current reading are required', 16, 1);
            RETURN;
        END
        
        IF @currentReading <= 0
        BEGIN
            RAISERROR('Current reading must be greater than 0', 16, 1);
            RETURN;
        END
        
        -- =====================================================
        -- TENANT LOOKUP LOGIC
        -- =====================================================
        
        -- Primary: Look for active tenant
        SELECT TOP 1 
            @tenantCode = tenant_code,
            @tenantName = tenant_name
        FROM m_tenant 
        WHERE real_property_code = @propertyCode 
          AND unit_no = @unitNo 
          AND ISNULL(terminated,'N') = 'N'
        ORDER BY actual_move_in_date DESC;
        
        -- Fallback: If no active tenant found, get last active tenant from vw_TenantReading
        IF @tenantCode IS NULL
        BEGIN
            SELECT TOP 1 
                @tenantCode = tenant_code,
                @tenantName = tenant_name
            FROM vw_TenantReading 
            WHERE property_code = @propertyCode 
              AND unit_no = @unitNo
            ORDER BY ISNULL(reading_date, convert(date, reading_date_to)) DESC;
            
            IF @tenantCode IS NULL
            BEGIN
                RAISERROR('No tenant history found for property %s unit %s', 16, 1, @propertyCode, @unitNo);
                RETURN;
            END
        END
        
        -- =====================================================
        -- GET PREVIOUS READING
        -- =====================================================
        
        -- Get last reading for the unit (property + unit combination) using vw_TenantReading
        -- This handles first-time readings (new units) where @prevReading will be NULL
        -- Uses reading_date_to for proper chronological ordering (handles late encoding scenarios)
        SELECT TOP 1 
            @prevReading = current_reading
        FROM vw_TenantReading 
        WHERE property_code = @propertyCode 
          AND unit_no = @unitNo
        ORDER BY ISNULL(reading_date, convert(date, reading_date_to)) DESC;
        
        -- For first-time readings (new units), @prevReading will be NULL
        -- This is expected and valid - the system will save the reading with prev_reading = NULL
        
        -- =====================================================
        -- DATE CALCULATION LOGIC
        -- =====================================================
        
        -- Reading period: 1st to last day of reading month
        SET @dateFrom = DATEADD(DAY, 1-DAY(@readingDate), @readingDate);
        SET @dateTo = EOMONTH(@readingDate);
        
        -- Billing period: 1st to last day of next month
        SET @billingDateFrom = DATEADD(MONTH, 1, @dateFrom);
        SET @billingDateTo = EOMONTH(@billingDateFrom);
        
        -- =====================================================
        -- TENANT MOVE-IN/OUT OVERRIDES
        -- =====================================================
        
        -- If reading is taken between tenants (post move-out, pre move-in)
        -- Check if this is a transition reading
        IF @prevReading IS NOT NULL
        BEGIN
            DECLARE @lastReadingDateTo DATETIME;
            
            SELECT TOP 1 @lastReadingDateTo = CONVERT(DATETIME, reading_date_to)
            FROM vw_TenantReading 
            WHERE property_code = @propertyCode 
              AND unit_no = @unitNo
            ORDER BY ISNULL(reading_date, convert(date, reading_date_to)) DESC;
            
            IF @lastReadingDateTo IS NOT NULL
            BEGIN
                DECLARE @daysSinceLastReading INT = DATEDIFF(DAY, @lastReadingDateTo, @readingDate);
                
                -- If reading is within 7 days of last reading's end date, it might be a transition
                IF @daysSinceLastReading <= 7
                BEGIN
                    -- Check if there's a move-out scenario using date_terminated
                    DECLARE @moveOutDate DATETIME;
                    
                    SELECT TOP 1 @moveOutDate = date_terminated
                    FROM m_tenant 
                    WHERE real_property_code = @propertyCode 
                      AND unit_no = @unitNo 
                      AND date_terminated IS NOT NULL
                      AND date_terminated >= @lastReadingDateTo
                    ORDER BY date_terminated DESC;
                    
                    IF @moveOutDate IS NOT NULL
                    BEGIN
                        -- This is a move-out reading - adjust periods
                        SET @dateTo = @moveOutDate;
                        SET @billingDateTo = DATEADD(DAY, 1, @moveOutDate); -- +1 day to move-out date
                    END
                END
            END
        END
        
        -- =====================================================
        -- SAVE READING TO DATABASE
        -- =====================================================
        
        BEGIN TRANSACTION;
        
        -- Insert into t_tenant_reading
        INSERT INTO t_tenant_reading 
            (tenant_code, date_from, date_to, prev_reading, current_reading, 
             reading_date, reading_by, remarks, billing_date_from, billing_date_to,
             created_by, date_created)
        VALUES 
            (@tenantCode, @dateFrom, @dateTo, @prevReading, @currentReading, 
             @readingDate, @readingBy, ISNULL(@remarks, 'QR System Reading'), 
             @billingDateFrom, @billingDateTo, @createdBy, GETDATE());
        
        SET @readingId = SCOPE_IDENTITY();
        
        -- Insert into t_tenant_reading_ext for audit trail
        INSERT INTO t_tenant_reading_ext 
            (reading_id, ip_address, user_agent, device_info, created_date)
        VALUES 
            (@readingId, @ipAddress, @userAgent, @deviceInfo, GETDATE());
        
        COMMIT TRANSACTION;
        
        -- =====================================================
        -- CREATE CHARGE CODE ENTRIES (CUCF AND CUCNF)
        -- Note: Charge code creation is secondary - reading is already saved
        -- =====================================================
        
        -- Get company code for the tenant
        DECLARE @companyCode NVARCHAR(5);
        SELECT TOP 1 @companyCode = company_code 
        FROM m_tenant 
        WHERE tenant_code = @tenantCode;
        
        -- Create CUCF charge entry (LEAC) - with error handling
        BEGIN TRY
            DECLARE @cucfResult INT;
            EXEC @cucfResult = sp_t_TenantReading_Charges_Save 
                @strMode = 'SAVE',
                @reading_id = @readingId,
                @reading_charge_id = 0,
                @charge_code = 'CUCF',
                @uid = @createdBy,
                @company_code = @companyCode,
                @ip_addr = @ipAddress;
        END TRY
        BEGIN CATCH
            -- Log error but don't fail the entire operation
            DECLARE @cucfError NVARCHAR(500) = 'CUCF charge creation failed: ' + ERROR_MESSAGE();
            -- Log to event log for tracking
            EXEC sp_s_EventLog 'sp_t_SaveTenantReading', @createdBy, @ipAddress, @cucfError, 'WARNING', @companyCode;
        END CATCH
        
        -- Create CUCNF charge entry (Electric Rate) - with error handling
        BEGIN TRY
            DECLARE @cucnfResult INT;
            EXEC @cucnfResult = sp_t_TenantReading_Charges_Save 
                @strMode = 'SAVE',
                @reading_id = @readingId,
                @reading_charge_id = 0,
                @charge_code = 'CUCNF',
                @uid = @createdBy,
                @company_code = @companyCode,
                @ip_addr = @ipAddress;
        END TRY
        BEGIN CATCH
            -- Log error but don't fail the entire operation
            DECLARE @cucnfError NVARCHAR(500) = 'CUCNF charge creation failed: ' + ERROR_MESSAGE();
            -- Log to event log for tracking
            EXEC sp_s_EventLog 'sp_t_SaveTenantReading', @createdBy, @ipAddress, @cucnfError, 'WARNING', @companyCode;
        END CATCH
        
        -- Return success response
        SELECT 
            'success' as status,
            'Reading saved successfully' as message,
            @readingId as readingId,
            @tenantCode as tenantCode,
            @tenantName as tenantName,
            @prevReading as prevReading,
            @currentReading as currentReading,
            @readingDate as readingDate,
            @dateFrom as dateFrom,
            @dateTo as dateTo,
            @billingDateFrom as billingDateFrom,
            @billingDateTo as billingDateTo,
            CASE WHEN @prevReading IS NOT NULL THEN @currentReading - @prevReading ELSE NULL END as usage,
            ISNULL(@remarks, 'QR System Reading') as remarks;
        
    END TRY
    BEGIN CATCH
        -- Rollback transaction if active
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;
        
        -- Get error information
        SELECT 
            ERROR_MESSAGE() as errorMessage,
            ERROR_SEVERITY() as errorSeverity,
            ERROR_STATE() as errorState;
        
        -- Log error via central event log
        DECLARE @__module nvarchar(50) = 'sp_t_SaveTenantReading';
        DECLARE @__user nvarchar(50) = ISNULL(CAST(SUSER_SNAME() AS nvarchar(50)), 'system');
        DECLARE @__ip nvarchar(50) = ISNULL(@ipAddress, 'unknown');
        DECLARE @__data nvarchar(max) = CONCAT('Error: ', ERROR_MESSAGE(), '; Severity: ', ERROR_SEVERITY(), '; State: ', ERROR_STATE(), '; Params: ',
                                               ' propertyCode=', ISNULL(@propertyCode,''),
                                               ' unitNo=', ISNULL(@unitNo,''),
                                               ' currentReading=', CONVERT(nvarchar(50), ISNULL(@currentReading, 0)),
                                               ' readingBy=', ISNULL(@readingBy,''));
        DECLARE @__action nvarchar(50) = 'ERROR';
        DECLARE @__company nvarchar(5) = (SELECT TOP 1 company_code FROM m_tenant WHERE real_property_code=@propertyCode AND unit_no=@unitNo);
        EXEC dbo.sp_s_EventLog @module_name=@__module, @user_name=@__user, @ip_addr=@__ip, @data=@__data, @db_action=@__action, @company_code=@__company;
        
    END CATCH
END;
GO

-- -- Grant execute permission
-- GRANT EXECUTE ON [dbo].[sp_t_SaveTenantReading] TO [web_app];
-- GO

-- Test the procedure
PRINT 'Stored procedure sp_t_SaveTenantReading created successfully';
GO

-- Example usage:
/*
EXEC [dbo].[sp_t_SaveTenantReading]
    @propertyCode = 'GCA',
    @unitNo = '101',
    @currentReading = 10510.00,
    @remarks = 'Monthly reading',
    @readingBy = 'technician1',
    @createdBy = 'admin',
    @ipAddress = '192.168.1.100',
    @userAgent = 'Mozilla/5.0...',
    @deviceInfo = 'QR System Mobile';
*/
