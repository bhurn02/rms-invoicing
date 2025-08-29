USE [RMS]
GO

/****** Object:  View [dbo].[vw_TenantReading]    Script Date: 8/28/2025 4:02:48 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE OR ALTER VIEW [dbo].[vw_TenantReading]
AS
/*
Author		:	Aldrich Delos Santos
Date		:	2025-08-28

Description	:	Reflects the meter reading history of property code + unit no
*/
select 
    real_property_name as property_name
	,p.real_property_code as property_code
	,t.building_code
	,t.unit_no
    ,r.tenant_code    
	,upper(t.tenant_name) as tenant_name
	,terminated	
	,convert(varchar(10),r.billing_date_from,101) as billing_from
	,convert(varchar(10),r.billing_date_to,101) as billing_to	
	,convert(varchar(10),date_from,101) as reading_date_from
	,convert(varchar(10),date_to,101) as reading_date_to
	,isnull(current_reading,0) as current_reading
	,isnull(prev_reading,0) as prev_reading
	,isnull(current_reading,0) - isnull(prev_reading,0) as usage
	,ISNULL(r.reading_date, r.date_created) as reading_date
    ,'(' + convert(varchar(10),date_from,101) + '-' + convert(varchar(10),date_to,101) + ') '
		+ case 
            when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 
            then 'Current Reading: ' + convert(varchar(10),current_reading) 
                + '; Previous Reading: ' + convert(varchar(10),prev_reading) 
                + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';' 
		else '' 
		end as remarks    
    ,upper(ltrim(rtrim( t.real_property_code))) + '/' +upper(ltrim(rtrim( t.building_code))) + '/' + upper(ltrim(rtrim(t.unit_no))) as unit_desc
	,r.date_created
from t_tenant_reading r
left join m_tenant t on r.tenant_code = t.tenant_code
left join m_real_property p on p.real_property_code=t.real_property_code


GO


