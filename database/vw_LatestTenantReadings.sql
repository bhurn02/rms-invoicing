USE [RMS]
GO

/****** Object:  View [dbo].[vw_LatestTenantReadings]    Script Date: 1/27/2025 10:51:14 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE OR ALTER VIEW [dbo].[vw_LatestTenantReadings]
AS
/*
Author		:	Aldrich Delos Santos
Date		:	2025-09-26

Description	:	Returns the latest/most recent meter reading for each property code + unit no combination
				Uses window functions for optimal performance to get the most recent tenant reading
				per property/unit with all relevant reading details and dates
*/
WITH LatestReadings AS (
    SELECT 
        property_code,
        unit_no,
        property_name,
        tenant_code,
        tenant_name,
        terminated,
        current_reading,
        prev_reading,
        usage,
        reading_date,
        billing_from,
        billing_to,
        reading_date_from,
        reading_date_to,
        remarks,
        unit_desc,
        date_created,
        -- Rank readings by date (most recent first)
        -- Uses reading_date, falls back to date_created, then billing_to
        ROW_NUMBER() OVER (
            PARTITION BY property_code, unit_no 
            ORDER BY 
                ISNULL(reading_date, ISNULL(date_created, billing_to)) DESC
        ) as rn
    FROM vw_TenantReading
    WHERE 
        -- Filter out completely null readings (no current or previous reading)
        (current_reading IS NOT NULL OR prev_reading IS NOT NULL)
)
SELECT 
    property_code,
    unit_no,
    property_name,
    tenant_code,
    tenant_name,
    terminated,
    current_reading,
    prev_reading,
    usage,
    reading_date,
    billing_from,
    billing_to,
    reading_date_from,
    reading_date_to,
    remarks,
    unit_desc,
    date_created
FROM LatestReadings
WHERE rn = 1;

GO
