USE [RMS];
GO

SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE OR ALTER PROCEDURE [dbo].[sp_rpt_Tenant_Reading]
	@condition nvarchar(1000)
	,@sortby nvarchar(500)=NULL
	,@groupby nvarchar(500)=NULL
AS
/*
Author		:	Resalie Usi
Date		:	xxxx-xx-xx

Description	:	Used for crystal report in extracting tenant reading via dynamic sql (report_tenant_reading_customized.rpt)

Usage		:	
    EXEC dbo.sp_rpt_Tenant_Reading
    @condition = N'date_from >= ''09/01/2025'' and dateadd(d,1,date_to) <= ''10/01/2025''',
    @sortby    = NULL,
    @groupby   = NULL;

Fixes:
    2025-09-29 Aldrich Delos Santos 
    - Replace wildcard select with explicit column list and target column list to ignore new columns and prevent insert column count errors.
*/

DECLARE @company_code char(5), @ssql nvarchar(4000);

SET @company_code = 'THC';

	IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[tmp_rg_tenant_reading]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
	DROP TABLE [dbo].[tmp_rg_tenant_reading];
	
	CREATE TABLE [dbo].[tmp_rg_tenant_reading] (
		[reading_id] [decimal](18, 0) NOT NULL ,
		[tenant_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_from] [datetime] NULL ,
		[date_to] [datetime] NULL ,
		[prev_reading] [decimal](18, 0) NULL ,
		[current_reading] [decimal](18, 0) NULL ,
		[remarks] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[billing_date_from] [datetime] NULL ,
		[billing_date_to] [datetime] NULL ,
		[created_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_created] [datetime] NULL ,
		[updated_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_updated] [datetime] NULL, 
		[invoice_no] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[invoice_detail_id] [decimal](18, 0) NULL ,
		[tenant_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[real_property_code] [char] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[charge_desc] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL 
	) ON [PRIMARY];

	SET @ssql = ' INSERT INTO tmp_rg_tenant_reading (
		reading_id,
		tenant_code,
		date_from,
		date_to,
		prev_reading,
		current_reading,
		remarks,
		billing_date_from,
		billing_date_to,
		created_by,
		date_created,
		updated_by,
		date_updated,
		invoice_no,
		invoice_detail_id,
		tenant_name,
		real_property_code,
		building_code,
		unit_no,
		real_property_name,
		charge_desc,
		or_company_name,
		or_company_address,
		or_company_contact_no1,
		or_company_contact_no2,
		group_by
	)
	SELECT 
		t_tenant_reading.reading_id,
		t_tenant_reading.tenant_code,
		t_tenant_reading.date_from,
		t_tenant_reading.date_to,
		t_tenant_reading.prev_reading,
		t_tenant_reading.current_reading,
		t_tenant_reading.remarks,
		t_tenant_reading.billing_date_from,
		t_tenant_reading.billing_date_to,
		t_tenant_reading.created_by,
		t_tenant_reading.date_created,
		t_tenant_reading.updated_by,
		t_tenant_reading.date_updated,
		t_tenant_reading_charges.trc_invoice_no AS invoice_no,
		t_tenant_reading_charges.trc_invoice_detail_id AS invoice_detail_id,
		m_tenant.tenant_name,
		m_tenant.real_property_code,
		m_tenant.building_code,
		m_tenant.unit_no,
		m_real_property.real_property_name,
		m_charges.charge_desc,
		s_company.or_company_name,
		s_company.or_company_address1 + '', '' + s_company.or_company_address2 AS or_company_address,
		s_company.or_company_contact_no1,
		s_company.or_company_contact_no2';

	IF LTRIM(RTRIM(ISNULL(@groupby,''))) <> ''
		SET @ssql = @ssql + ',UPPER(' + @groupby + ')'
	ELSE
		SET @ssql = @ssql + ','''' ' 
	
	SET @ssql = @ssql + ' FROM t_tenant_reading
	LEFT JOIN t_tenant_reading_charges ON t_tenant_reading.reading_id = trc_reading_id
	LEFT JOIN m_tenant ON t_tenant_reading.tenant_code = m_tenant.tenant_code
	LEFT JOIN m_real_property ON m_tenant.real_property_code = m_real_property.real_property_code
	LEFT JOIN m_charges ON t_tenant_reading_charges.trc_charge_code = m_charges.charge_code
	LEFT JOIN s_company ON m_real_property.company_code = s_company.company_code
	where  s_company.company_code =  ''' + @company_code + '''';

	IF LTRIM(RTRIM(ISNULL(@condition,''))) <> ''
		SET @ssql = @ssql + ' AND ' + @condition;

	IF LTRIM(RTRIM(ISNULL(@sortby,''))) <> ''
		SET @ssql = @ssql + ' ORDER BY ' + @sortby; -- optional default ordering commented in original

	PRINT @ssql;
	EXEC sp_executesql @ssql;

	SELECT * FROM tmp_rg_tenant_reading;
GO
