USE [RMS]
GO
/****** Object:  Table [dbo].[t_tenant_reading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_tenant_reading](
	[reading_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[tenant_code] [varchar](20) NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[prev_reading] [decimal](18, 0) NULL,
	[current_reading] [decimal](18, 0) NULL,
	[remarks] [varchar](100) NULL,
	[billing_date_from] [datetime] NULL,
	[billing_date_to] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[date_created] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
 CONSTRAINT [PK_t_tenant_reading] PRIMARY KEY CLUSTERED 
(
	[reading_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_invoice_detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_invoice_detail](
	[invoice_detail_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[invoice_no] [varchar](20) NOT NULL,
	[tenant_code] [char](10) NULL,
	[charge_code] [char](5) NULL,
	[charge_type] [char](1) NULL,
	[charge_amount] [decimal](18, 6) NULL,
	[total_charge_amount] [decimal](18, 2) NULL,
	[paid_amount] [decimal](18, 2) NULL,
	[balance_amount] [decimal](18, 2) NULL,
	[remarks] [varchar](500) NULL,
 CONSTRAINT [PK_t_invoice_detail_1] PRIMARY KEY CLUSTERED 
(
	[invoice_detail_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_invoice_header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_invoice_header](
	[invoice_no] [varchar](20) NOT NULL,
	[invoice_no_type] [char](1) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[real_property_code] [char](10) NULL,
	[billing_from] [datetime] NULL,
	[billing_to] [datetime] NULL,
	[document_no] [varchar](20) NULL,
	[sap_code] [varchar](20) NULL,
	[remarks] [varchar](100) NULL,
	[status] [char](1) NULL,
	[created_by] [varchar](50) NULL,
	[date_created] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
 CONSTRAINT [PK_t_invoice_header] PRIMARY KEY CLUSTERED 
(
	[invoice_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_charges](
	[charge_code] [char](5) NOT NULL,
	[charge_desc] [varchar](50) NULL,
	[gl_code] [varchar](50) NULL,
	[charge_type] [char](1) NULL,
	[exclude_prorate] [tinyint] NULL,
 CONSTRAINT [PK_charges] PRIMARY KEY CLUSTERED 
(
	[charge_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_invoice_detail_reading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_invoice_detail_reading](
	[invoice_detail_reading_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[invoice_no] [varchar](20) NULL,
	[invoice_detail_id] [decimal](18, 0) NULL,
	[reading_id] [decimal](18, 0) NULL,
 CONSTRAINT [PK_t_invoice_detail_sub] PRIMARY KEY CLUSTERED 
(
	[invoice_detail_reading_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_real_property]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_real_property](
	[real_property_code] [char](5) NOT NULL,
	[real_property_name] [varchar](100) NULL,
	[real_property_company_name] [varchar](100) NULL,
	[real_property_dba_name] [varchar](100) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[address3] [varchar](50) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[tot_no_of_units] [int] NULL,
	[lot_space] [varchar](20) NULL,
	[space_type] [char](1) NULL,
	[remarks] [varchar](100) NULL,
	[cost_center] [varchar](20) NULL,
	[company_code] [varchar](5) NULL,
 CONSTRAINT [PK_m_real_property] PRIMARY KEY CLUSTERED 
(
	[real_property_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_tenant](
	[tenant_code] [char](10) NOT NULL,
	[tenant_name] [varchar](100) NOT NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[bill_to] [char](10) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contract_eff_date] [datetime] NULL,
	[contract_expiry_date] [datetime] NULL,
	[sap_code] [varchar](20) NULL,
	[terminated] [char](1) NULL,
	[date_terminated] [datetime] NULL,
	[actual_move_in_date] [datetime] NULL,
	[email_add] [varchar](256) NULL,
	[is_affiliate_employee] [char](1) NULL,
	[employer] [varchar](100) NULL,
	[tenant_type] [char](2) NOT NULL,
	[is_sap_affiliate] [char](1) NULL,
	[new_code] [varchar](50) NULL,
	[business_area] [varchar](50) NULL,
	[company_code] [varchar](5) NULL,
	[last_meter_reading] [varchar](20) NULL,
	[security_deposit_amount] [decimal](18, 2) NULL,
	[tenant_remarks] [varchar](500) NULL,
	[is_employee_benefit] [char](1) NULL,
	[employee_benefit_cc] [varchar](7) NULL,
	[is_notifications] [char](1) NULL,
	[date_created] [datetime] NULL,
	[created_by] [char](10) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [char](10) NULL,
 CONSTRAINT [PK_m_tenant] PRIMARY KEY CLUSTERED 
(
	[tenant_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[vw_TenantReadingHistory]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE   VIEW [dbo].[vw_TenantReadingHistory]
AS
select 
    real_property_name as property_name
	,p.real_property_code as property_code
	,t.building_code
	,t.unit_no
	,upper(t.tenant_name) as tenant_name
	,terminated
	--,d.invoice_detail_id
    ,d.invoice_no
	,convert(varchar(10),h.invoice_date,101) as invoice_date
	,convert(varchar(10),h.billing_from,101) as billing_from
	,convert(varchar(10),h.billing_to,101) as billing_to
	,charge_desc
    ,d.charge_code
    ,charge_amount
    ,total_charge_amount
	,convert(varchar(10),date_from,101) as reading_date_from
	,convert(varchar(10),date_to,101) as reading_date_to
	,isnull(current_reading,0) as current_reading
	,isnull(prev_reading,0) as prev_reading
	,isnull(current_reading,0) - isnull(prev_reading,0) as usage
    ,'(' + convert(varchar(10),date_from,101) + '-' + convert(varchar(10),date_to,101) + ') '
		+ case 
            when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 
            then 'Current Reading: ' + convert(varchar(10),current_reading) 
                + '; Previous Reading: ' + convert(varchar(10),prev_reading) 
                + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';' 
		else '' 
		end as remarks    
    ,upper(ltrim(rtrim( t.real_property_code))) + '/' +upper(ltrim(rtrim( t.building_code))) + '/' + upper(ltrim(rtrim(t.unit_no))) as unit_desc
    ,d.tenant_code    
    ,gl_code
    --,upper(ltrim(rtrim(d.charge_type))) as charge_type
    ----case when upper(ltrim(rtrim(t_invoice_detail.charge_type))) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type_desc,
    --,upper(ltrim(rtrim(d.charge_type))) as charge_type_desc
    --,isnull(t_invoice_detail_reading.invoice_detail_reading_id,0) as invoice_detail_reading_id
    --,isnull(t_invoice_detail_reading.reading_id,0) as reading_id
from t_invoice_detail d
left join t_invoice_header h on h.invoice_no=d.invoice_no
left join m_real_property p on p.real_property_code=h.real_property_code
left join m_tenant t on d.tenant_code = t.tenant_code
left join t_invoice_detail_reading on d.invoice_no = t_invoice_detail_reading.invoice_no and d.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id
left join m_charges on d.charge_code = m_charges.charge_code
--order by 
--    property_name
--	,t.unit_no
--	,h.invoice_date
--	,t.tenant_name	
--    ,upper(ltrim(rtrim( t.real_property_code))) + '/' +upper(ltrim(rtrim( t.building_code))) + '/' + upper(ltrim(rtrim(t.unit_no))) 
--    ,charge_desc
GO
/****** Object:  Table [dbo].[m_tenant_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_tenant_charges](
	[tenant_code] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 6) NULL,
 CONSTRAINT [PK_m_tenant_charges] PRIMARY KEY CLUSTERED 
(
	[tenant_code] ASC,
	[charge_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_company]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_company](
	[company_code] [varchar](5) NOT NULL,
	[company_name] [varchar](100) NULL,
	[dba_name] [varchar](100) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contact_no1] [varchar](50) NULL,
	[contact_no2] [varchar](50) NULL,
	[fax_no] [varchar](50) NULL,
	[or_company_name] [varchar](100) NULL,
	[or_company_address1] [varchar](50) NULL,
	[or_company_address2] [varchar](50) NULL,
	[or_company_contact_no1] [varchar](50) NULL,
	[or_company_contact_no2] [varchar](50) NULL,
 CONSTRAINT [PK_m_company] PRIMARY KEY CLUSTERED 
(
	[company_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[vw_TenantChargesListing_Active]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[vw_TenantChargesListing_Active]
AS
SELECT        TOP (100) PERCENT dbo.m_tenant_charges.tenant_code, dbo.m_tenant_charges.charge_code, dbo.m_tenant_charges.charge_amount, dbo.m_tenant.tenant_name, 
                         dbo.m_tenant.sap_code, dbo.m_tenant.real_property_code, dbo.m_tenant.building_code, dbo.m_tenant.unit_no, dbo.m_real_property.real_property_name, 
                         dbo.m_charges.charge_desc, dbo.m_tenant.actual_move_in_date
FROM            dbo.m_tenant_charges LEFT OUTER JOIN
                         dbo.m_tenant ON dbo.m_tenant_charges.tenant_code = dbo.m_tenant.tenant_code LEFT OUTER JOIN
                         dbo.m_real_property ON dbo.m_tenant.real_property_code = dbo.m_real_property.real_property_code LEFT OUTER JOIN
                         dbo.m_charges ON dbo.m_tenant_charges.charge_code = dbo.m_charges.charge_code LEFT OUTER JOIN
                         dbo.s_company ON dbo.m_real_property.company_code = dbo.s_company.company_code
WHERE        (ISNULL(dbo.m_tenant.terminated, 'N') <> 'Y')
GO
/****** Object:  Table [dbo].[m_space_type]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_space_type](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[space_type] [char](1) NOT NULL,
	[space_type_code] [varchar](3) NULL,
	[description] [varchar](128) NULL,
	[IsActive] [tinyint] NULL,
	[Created] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_tenant_cost_center]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_tenant_cost_center](
	[tenant_code] [char](10) NOT NULL,
	[cost_center] [varchar](10) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_m_tenant_cost_center] PRIMARY KEY CLUSTERED 
(
	[tenant_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_unit_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_unit_charges](
	[real_property_code] [char](10) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 6) NOT NULL,
 CONSTRAINT [PK_unit_charges] PRIMARY KEY CLUSTERED 
(
	[real_property_code] ASC,
	[building_code] ASC,
	[unit_no] ASC,
	[charge_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_units]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_units](
	[real_property_code] [char](5) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[lot_area] [varchar](20) NULL,
	[no_of_bedrooms] [int] NULL,
	[unit_type] [varchar](20) NULL,
	[is_reserved] [char](1) NULL,
	[is_complimentary] [tinyint] NULL,
	[complimentary_date_from] [date] NULL,
	[remarks] [varchar](50) NULL,
	[meter_number] [varchar](20) NULL,
 CONSTRAINT [PK_units] PRIMARY KEY CLUSTERED 
(
	[real_property_code] ASC,
	[building_code] ASC,
	[unit_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[m_units_movement]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[m_units_movement](
	[event_log_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[event_date] [datetime] NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[event_action] [varchar](50) NULL,
	[event_remarks] [varchar](4000) NULL,
	[tenant_code] [char](10) NULL,
	[charge_amount] [decimal](18, 6) NULL,
	[executed_by] [varchar](50) NULL,
	[date_executed] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy](
	[rec_id] [numeric](18, 0) IDENTITY(1,1) NOT NULL,
	[tran_date] [datetime] NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[real_property_code] [varchar](50) NULL,
	[remarks] [nvarchar](4000) NULL,
	[generated_by] [varchar](50) NULL,
	[date_generated] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_01252014]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_01252014](
	[rec_id] [numeric](18, 0) NOT NULL,
	[tran_date] [datetime] NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[real_property_code] [varchar](50) NULL,
	[remarks] [nvarchar](4000) NULL,
	[generated_by] [varchar](50) NULL,
	[date_generated] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_07312013]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_07312013](
	[rec_id] [numeric](18, 0) NOT NULL,
	[tran_date] [datetime] NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[real_property_code] [varchar](50) NULL,
	[remarks] [nvarchar](4000) NULL,
	[generated_by] [varchar](50) NULL,
	[date_generated] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_asof]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_asof](
	[rec_id] [numeric](18, 0) IDENTITY(1,1) NOT NULL,
	[real_property_code] [nvarchar](50) NOT NULL,
	[date_asof] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_complimentary] [decimal](18, 0) NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [nvarchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL,
	[rg_report_title] [nvarchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_asof_log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_asof_log](
	[rec_id] [numeric](18, 0) IDENTITY(1,1) NOT NULL,
	[tran_date] [datetime] NULL,
	[date_asof] [datetime] NULL,
	[real_property_code] [varchar](50) NULL,
	[remarks] [nvarchar](4000) NULL,
	[generated_by] [varchar](50) NULL,
	[date_generated] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist](
	[rec_id] [numeric](18, 0) IDENTITY(1,1) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_complimentary] [decimal](18, 0) NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist_02032014]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist_02032014](
	[rec_id] [numeric](18, 0) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist_07072015]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist_07072015](
	[rec_id] [numeric](18, 0) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist_07312013]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist_07312013](
	[rec_id] [numeric](18, 0) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist_old]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist_old](
	[rec_id] [numeric](18, 0) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_occupancy_hist_old2]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_occupancy_hist_old2](
	[rec_id] [numeric](18, 0) NOT NULL,
	[real_property_code] [varchar](50) NOT NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NOT NULL,
	[total_units_for_rent] [decimal](18, 0) NOT NULL,
	[total_units_occupied] [decimal](18, 0) NOT NULL,
	[total_vacant_units] [decimal](18, 0) NOT NULL,
	[room_income] [decimal](18, 2) NOT NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NOT NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[rg_table_fields]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[rg_table_fields](
	[field_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[field_name] [varchar](500) NULL,
	[field_desc] [varchar](100) NULL,
	[module_id] [decimal](18, 0) NOT NULL,
	[xorder] [int] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_email_alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_email_alert](
	[sea_company_code] [varchar](5) NOT NULL,
	[sea_rms_email] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_email_alert_log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_email_alert_log](
	[eal_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[eal_date_time] [datetime] NULL,
	[eal_alert_type] [varchar](50) NULL,
	[eal_notice_no] [int] NULL,
	[eal_as_of] [datetime] NULL,
	[eal_sap_code] [varchar](50) NULL,
	[eal_detail_id] [decimal](18, 0) NULL,
	[eal_tenant_code] [varchar](10) NULL,
	[eal_tenant_name] [varchar](100) NULL,
	[eal_email_add] [varchar](100) NULL,
	[eal_attachment] [varchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_event_log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_event_log](
	[event_log_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[event_date] [datetime] NULL,
	[module_name] [nvarchar](50) NULL,
	[user_name] [nvarchar](50) NULL,
	[ip_addr] [nvarchar](50) NULL,
	[data] [nvarchar](max) NULL,
	[db_action] [nvarchar](50) NULL,
	[company_code] [nvarchar](50) NULL,
 CONSTRAINT [PK_s_event_log] PRIMARY KEY CLUSTERED 
(
	[event_log_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_module_functions]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_module_functions](
	[function_id] [decimal](18, 0) NOT NULL,
	[function_name] [varchar](100) NOT NULL,
	[module_id] [decimal](18, 0) NOT NULL,
 CONSTRAINT [PK_s_module_functions] PRIMARY KEY CLUSTERED 
(
	[function_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_module_functions_search_list]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_module_functions_search_list](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[function_id] [decimal](18, 0) NOT NULL,
	[column_code] [varchar](500) NOT NULL,
	[column_name] [varchar](100) NULL,
	[data_type] [char](1) NULL,
	[order_by] [varchar](500) NULL,
	[xorder] [int] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_modules]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_modules](
	[module_id] [decimal](18, 0) NOT NULL,
	[module_name] [varchar](100) NOT NULL,
 CONSTRAINT [PK_s_modules] PRIMARY KEY CLUSTERED 
(
	[module_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_payment_sap_fields]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_payment_sap_fields](
	[trans_id] [int] NULL,
	[trans_desc] [varchar](50) NULL,
	[company_code] [varchar](50) NULL,
	[currency_code] [varchar](10) NULL,
	[doc_type] [varchar](10) NULL,
	[posting_key_hdr] [varchar](10) NULL,
	[acct_code_non_aff] [varchar](50) NULL,
	[posting_key_dtl1] [varchar](10) NULL,
	[posting_key_dtl2] [varchar](10) NULL,
	[gl_code_dtl] [varchar](50) NULL,
	[posting_key_hdr_sub] [varchar](10) NULL,
	[posting_key_dtl_sub] [varchar](10) NULL,
	[posting_key_hdr_aff] [varchar](10) NULL,
	[acct_code_aff] [varchar](50) NULL,
	[tax_code] [varchar](10) NULL,
	[buss_area_non_aff] [varchar](10) NULL,
	[cost_center_hdr] [varchar](10) NULL,
	[cost_center_dtl] [varchar](10) NULL,
	[job_order] [varchar](50) NULL,
	[doc_date_len] [int] NULL,
	[post_date_len] [int] NULL,
	[ref_doc_no_len] [int] NULL,
	[company_code_len] [int] NULL,
	[currency_code_len] [int] NULL,
	[doc_type_len] [int] NULL,
	[posting_key_len] [int] NULL,
	[account_code_len] [int] NULL,
	[amount_len] [int] NULL,
	[tax_code_len] [int] NULL,
	[buss_area_len] [int] NULL,
	[cost_center_len] [int] NULL,
	[job_order_len] [int] NULL,
	[base_date_len] [int] NULL,
	[alloc_len] [int] NULL,
	[new_code_len] [int] NULL,
	[text_len] [int] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_sap_fields]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_sap_fields](
	[company_code] [varchar](50) NULL,
	[currency_code] [varchar](10) NULL,
	[doc_type] [varchar](10) NULL,
	[posting_key_hdr] [varchar](10) NULL,
	[posting_key_dtl1] [varchar](10) NULL,
	[posting_key_dtl2] [varchar](10) NULL,
	[posting_key_hdr_sub] [varchar](10) NULL,
	[posting_key_dtl_sub] [varchar](10) NULL,
	[posting_key_hdr_aff] [varchar](10) NULL,
	[acct_code_aff] [varchar](50) NULL,
	[tax_code] [varchar](10) NULL,
	[buss_area_non_aff] [varchar](10) NULL,
	[cost_center_hdr] [varchar](10) NULL,
	[cost_center_dtl] [varchar](10) NULL,
	[job_order] [varchar](50) NULL,
	[doc_date_len] [int] NULL,
	[post_date_len] [int] NULL,
	[ref_doc_no_len] [int] NULL,
	[company_code_len] [int] NULL,
	[currency_code_len] [int] NULL,
	[doc_type_len] [int] NULL,
	[posting_key_len] [int] NULL,
	[account_code_len] [int] NULL,
	[amount_len] [int] NULL,
	[tax_code_len] [int] NULL,
	[buss_area_len] [int] NULL,
	[cost_center_len] [int] NULL,
	[job_order_len] [int] NULL,
	[base_date_len] [int] NULL,
	[alloc_len] [int] NULL,
	[new_code_len] [int] NULL,
	[text_len] [int] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_settings]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_settings](
	[company_code] [varchar](5) NOT NULL,
	[prepared_by] [varchar](50) NULL,
	[approved_by] [varchar](50) NULL,
	[paycheck_payable_to] [varchar](50) NULL,
	[address] [char](10) NULL,
	[off_unit_type] [varchar](10) NULL,
	[apt_rental_charge] [varchar](5) NULL,
	[off_rental_charge] [varchar](5) NULL,
	[whs_rental_charge] [varchar](5) NULL,
	[water_rental_charge] [varchar](5) NULL,
 CONSTRAINT [PK_s_settings] PRIMARY KEY CLUSTERED 
(
	[company_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_tenant_reading_default]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_tenant_reading_default](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[trd_charge_code] [char](5) NULL,
	[trd_date_from] [datetime] NULL,
	[trd_date_to] [datetime] NULL,
	[trd_billing_date_from] [datetime] NULL,
	[trd_billing_date_to] [datetime] NULL,
	[trd_date_updated] [datetime] NULL,
	[trd_updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_s_tenant_reading_default] PRIMARY KEY CLUSTERED 
(
	[rec_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_user_group]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_user_group](
	[group_code] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[group_desc] [varchar](50) NULL,
 CONSTRAINT [PK_s_user_group] PRIMARY KEY CLUSTERED 
(
	[group_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_user_group_modules]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_user_group_modules](
	[recid] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[group_code] [decimal](18, 0) NOT NULL,
	[module_id] [decimal](18, 0) NOT NULL,
 CONSTRAINT [PK_s_user_group_modules] PRIMARY KEY CLUSTERED 
(
	[recid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[s_users]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[s_users](
	[user_id] [char](15) NOT NULL,
	[last_name] [varchar](50) NULL,
	[first_name] [varchar](50) NULL,
	[middle_initial] [varchar](5) NULL,
	[user_password] [varchar](15) NULL,
	[group_code] [decimal](18, 0) NULL,
	[is_active] [char](1) NULL,
	[date_inactive] [datetime] NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[company_code] [varchar](5) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_ar_detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_ar_detail](
	[ar_detail_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NULL,
	[charge_code] [char](5) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_t_ar_detail] PRIMARY KEY CLUSTERED 
(
	[ar_detail_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_ar_header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_ar_header](
	[or_no] [varchar](20) NOT NULL,
	[or_no_type] [char](1) NULL,
	[or_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[amount] [decimal](18, 2) NULL,
	[document_no] [varchar](20) NULL,
	[mode_of_payment] [char](1) NULL,
	[bank_name] [varchar](100) NULL,
	[remarks] [varchar](200) NULL,
	[status] [char](1) NULL,
	[trans_type] [char](1) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_t_ar_header] PRIMARY KEY CLUSTERED 
(
	[or_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_ar_header_payment_mode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_ar_header_payment_mode](
	[ar_mode_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NULL,
	[payment_mode_type] [char](1) NULL,
	[amount] [decimal](18, 2) NULL,
	[account_no] [varchar](20) NULL,
	[bank_name] [varchar](50) NULL,
 CONSTRAINT [PK_t_ar_header_payment_mode] PRIMARY KEY CLUSTERED 
(
	[ar_mode_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_payment_detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_payment_detail](
	[payment_detail_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NULL,
	[invoice_no] [varchar](20) NULL,
	[invoice_detail_id] [decimal](18, 0) NULL,
	[total_charge_amount] [decimal](18, 2) NULL,
	[or_amount] [decimal](18, 2) NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[is_selected] [int] NULL,
	[trans_type] [char](1) NULL,
 CONSTRAINT [PK_t_payments_detail] PRIMARY KEY CLUSTERED 
(
	[payment_detail_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_payment_header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_payment_header](
	[or_no] [varchar](20) NOT NULL,
	[or_no_type] [char](1) NULL,
	[or_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[amount] [decimal](18, 2) NULL,
	[document_no] [varchar](20) NULL,
	[mode_of_payment] [char](1) NULL,
	[bank_name] [varchar](100) NULL,
	[remarks] [varchar](200) NULL,
	[status] [char](1) NULL,
	[trans_type] [char](1) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_t_billing_header] PRIMARY KEY CLUSTERED 
(
	[or_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_payment_header_payment_mode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_payment_header_payment_mode](
	[payment_mode_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NULL,
	[payment_mode_type] [char](1) NULL,
	[amount] [decimal](18, 2) NULL,
	[account_no] [varchar](20) NULL,
	[bank_name] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_payment_sap_upload]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_payment_sap_upload](
	[recid] [decimal](10, 0) IDENTITY(1,1) NOT NULL,
	[doc_date] [varchar](50) NULL,
	[posting_date] [varchar](50) NULL,
	[refdocno] [varchar](50) NULL,
	[company_code] [varchar](50) NULL,
	[currency] [varchar](50) NULL,
	[doctype] [varchar](50) NULL,
	[postingkey] [varchar](50) NULL,
	[account_code] [varchar](50) NULL,
	[amount] [varchar](50) NULL,
	[tax_code] [varchar](50) NULL,
	[buss_area] [varchar](50) NULL,
	[cost_center] [varchar](50) NULL,
	[job_order] [varchar](50) NULL,
	[baselndate] [varchar](50) NULL,
	[new_code] [varchar](50) NULL,
	[alloc] [varchar](50) NULL,
	[stext] [varchar](50) NULL,
	[date_uploaded] [datetime] NULL,
	[uploaded_by] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_sap_upload]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_sap_upload](
	[recid] [decimal](10, 0) IDENTITY(1,1) NOT NULL,
	[doc_date] [varchar](50) NULL,
	[posting_date] [varchar](50) NULL,
	[refdocno] [varchar](50) NULL,
	[company_code] [varchar](50) NULL,
	[currency] [varchar](50) NULL,
	[doctype] [varchar](50) NULL,
	[postingkey] [varchar](50) NULL,
	[account_code] [varchar](50) NULL,
	[amount] [varchar](50) NULL,
	[tax_code] [varchar](50) NULL,
	[buss_area] [varchar](50) NULL,
	[cost_center] [varchar](50) NULL,
	[job_order] [varchar](50) NULL,
	[baselndate] [varchar](50) NULL,
	[new_code] [varchar](50) NULL,
	[alloc] [varchar](50) NULL,
	[stext] [varchar](50) NULL,
	[date_uploaded] [datetime] NULL,
	[uploaded_by] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_security_deposit]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_security_deposit](
	[or_no] [varchar](20) NOT NULL,
	[or_no_type] [char](1) NULL,
	[or_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[amount] [decimal](18, 2) NULL,
	[remarks] [varchar](200) NULL,
	[status] [char](1) NULL,
	[trans_type] [char](1) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_t_security_deposit] PRIMARY KEY CLUSTERED 
(
	[or_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_security_deposit_detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_security_deposit_detail](
	[detail_id] [decimal](38, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NOT NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[amount] [decimal](18, 2) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
 CONSTRAINT [PK_t_security_deposit_detail] PRIMARY KEY CLUSTERED 
(
	[detail_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_security_deposit_payment_mode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_security_deposit_payment_mode](
	[payment_mode_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[or_no] [varchar](20) NULL,
	[payment_mode_type] [char](1) NULL,
	[amount] [decimal](18, 2) NULL,
	[account_no] [varchar](20) NULL,
	[bank_name] [varchar](50) NULL,
 CONSTRAINT [PK_t_security_deposit_payment_mode] PRIMARY KEY CLUSTERED 
(
	[payment_mode_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[t_tenant_reading_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[t_tenant_reading_charges](
	[trc_reading_id] [decimal](18, 0) NOT NULL,
	[trc_charge_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[trc_charge_code] [char](10) NULL,
	[trc_invoice_no] [varchar](50) NULL,
	[trc_invoice_detail_id] [decimal](18, 0) NULL,
	[trc_invoice_detail_reading_id] [decimal](18, 0) NULL,
 CONSTRAINT [PK_t_tenant_reading_charges] PRIMARY KEY CLUSTERED 
(
	[trc_charge_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_invoice_listing]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_invoice_listing](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[invoice_no] [varchar](20) NOT NULL,
	[invoice_no_type] [char](1) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[inv_real_property_code] [char](10) NULL,
	[billing_from] [datetime] NULL,
	[billing_to] [datetime] NULL,
	[document_no] [varchar](20) NULL,
	[sap_code] [varchar](20) NULL,
	[remarks] [varchar](100) NULL,
	[status] [char](1) NULL,
	[created_by] [varchar](50) NULL,
	[date_created] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
	[client_name] [varchar](200) NULL,
	[tenant_name] [varchar](200) NULL,
	[unit_no] [varchar](100) NULL,
	[charge_code] [varchar](10) NULL,
	[charge_desc] [varchar](200) NULL,
	[total_charge_amount] [decimal](18, 2) NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL,
	[group_by] [varchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_tenant](
	[tenant_code] [char](10) NOT NULL,
	[tenant_name] [varchar](100) NOT NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[bill_to] [char](10) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contract_eff_date] [datetime] NULL,
	[contract_expiry_date] [datetime] NULL,
	[sap_code] [varchar](20) NULL,
	[terminated] [char](1) NULL,
	[date_terminated] [datetime] NULL,
	[actual_move_in_date] [datetime] NULL,
	[email_add] [varchar](500) NULL,
	[is_affiliate_employee] [char](1) NULL,
	[employer] [varchar](100) NULL,
	[tenant_type] [char](2) NOT NULL,
	[is_sap_affiliate] [char](1) NULL,
	[new_code] [varchar](50) NULL,
	[business_area] [varchar](50) NULL,
	[company_code] [varchar](5) NULL,
	[last_meter_reading] [varchar](20) NULL,
	[security_deposit_amount] [decimal](18, 2) NULL,
	[tenant_remarks] [varchar](500) NULL,
	[is_employee_benefit] [char](1) NULL,
	[employee_benefit_cc] [varchar](7) NULL,
	[is_notifications] [char](1) NULL,
	[date_created] [datetime] NULL,
	[created_by] [char](10) NULL,
	[date_updated] [datetime] NULL,
	[updated_by] [char](10) NULL,
	[real_property_name] [varchar](200) NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL,
	[group_by] [varchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_tenant_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_tenant_charges](
	[tenant_code] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 6) NULL,
	[tenant_name] [varchar](100) NOT NULL,
	[sap_code] [varchar](20) NOT NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[real_property_name] [varchar](200) NULL,
	[charge_desc] [varchar](200) NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL,
	[actual_move_in_date] [datetime] NULL,
	[group_by] [varchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_tenant_reading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_tenant_reading](
	[reading_id] [decimal](18, 0) NOT NULL,
	[tenant_code] [varchar](20) NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[prev_reading] [decimal](18, 0) NULL,
	[current_reading] [decimal](18, 0) NULL,
	[remarks] [varchar](100) NULL,
	[billing_date_from] [datetime] NULL,
	[billing_date_to] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[date_created] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
	[invoice_no] [varchar](50) NULL,
	[invoice_detail_id] [decimal](18, 0) NULL,
	[tenant_name] [varchar](100) NOT NULL,
	[real_property_code] [char](5) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[real_property_name] [varchar](200) NULL,
	[charge_desc] [varchar](200) NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL,
	[group_by] [varchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_unit_charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_unit_charges](
	[real_property_code] [char](10) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 6) NOT NULL,
	[real_property_name] [varchar](200) NOT NULL,
	[charge_desc] [varchar](200) NOT NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL,
	[group_by] [varchar](200) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tmp_rg_units]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp_rg_units](
	[real_property_code] [char](10) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[lot_area] [varchar](20) NULL,
	[no_of_bedrooms] [int] NULL,
	[unit_type] [varchar](20) NULL,
	[is_reserved] [char](1) NULL,
	[is_complimentary] [tinyint] NULL,
	[complimentary_date_from] [date] NULL,
	[remarks] [varchar](50) NULL,
	[meter_number] [varchar](20) NULL,
	[status] [char](15) NOT NULL,
	[real_property_name] [varchar](200) NOT NULL,
	[or_company_name] [varchar](100) NOT NULL,
	[or_company_address] [varchar](100) NOT NULL,
	[or_company_contact_no1] [varchar](50) NOT NULL,
	[or_company_contact_no2] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[u_invoice_alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[u_invoice_alert](
	[invoice_no] [varchar](20) NOT NULL,
	[tagged] [tinyint] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
	[email_sent] [tinyint] NULL,
	[email_addr] [varchar](200) NULL,
	[date_email_sent] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[u_unpost_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[u_unpost_invoice](
	[recid] [decimal](10, 0) IDENTITY(1,1) NOT NULL,
	[doc_date] [varchar](50) NULL,
	[posting_date] [varchar](50) NULL,
	[refdocno] [varchar](50) NULL,
	[company_code] [varchar](50) NULL,
	[currency] [varchar](50) NULL,
	[doctype] [varchar](50) NULL,
	[postingkey] [varchar](50) NULL,
	[account_code] [varchar](50) NULL,
	[amount] [varchar](50) NULL,
	[tax_code] [varchar](50) NULL,
	[buss_area] [varchar](50) NULL,
	[cost_center] [varchar](50) NULL,
	[job_order] [varchar](50) NULL,
	[baselndate] [varchar](50) NULL,
	[new_code] [varchar](50) NULL,
	[alloc] [varchar](50) NULL,
	[stext] [varchar](50) NULL,
	[date_uploaded] [datetime] NULL,
	[uploaded_by] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[u_unvoid_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[u_unvoid_invoice](
	[recid] [decimal](10, 0) IDENTITY(1,1) NOT NULL,
	[doc_date] [varchar](50) NULL,
	[posting_date] [varchar](50) NULL,
	[refdocno] [varchar](50) NULL,
	[company_code] [varchar](50) NULL,
	[currency] [varchar](50) NULL,
	[doctype] [varchar](50) NULL,
	[postingkey] [varchar](50) NULL,
	[account_code] [varchar](50) NULL,
	[amount] [varchar](50) NULL,
	[tax_code] [varchar](50) NULL,
	[buss_area] [varchar](50) NULL,
	[cost_center] [varchar](50) NULL,
	[job_order] [varchar](50) NULL,
	[baselndate] [varchar](50) NULL,
	[new_code] [varchar](50) NULL,
	[alloc] [varchar](50) NULL,
	[stext] [varchar](50) NULL,
	[date_uploaded] [datetime] NULL,
	[uploaded_by] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[w_tenant_aging_detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[w_tenant_aging_detail](
	[wta_rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[wta_hdr_id] [decimal](18, 0) NOT NULL,
	[wta_sap_code] [varchar](20) NULL,
	[wta_tenant_name] [varchar](200) NULL,
	[wta_real_property_name] [varchar](200) NULL,
	[wta_total_balance] [decimal](18, 2) NULL,
	[wta_curr_balance] [decimal](18, 2) NULL,
	[wta_total_overdue] [decimal](18, 2) NULL,
	[wta_aging_1_30] [decimal](18, 2) NULL,
	[wta_aging_31_60] [decimal](18, 2) NULL,
	[wta_aging_61_90] [decimal](18, 2) NULL,
	[wta_aging_91_120] [decimal](18, 2) NULL,
	[wta_aging_121_150] [decimal](18, 2) NULL,
	[wta_aging_over_151] [decimal](18, 2) NULL,
	[wta_write_off] [decimal](18, 2) NULL,
	[wta_remarks] [varchar](500) NULL,
	[wta_notice_no] [varchar](100) NULL,
	[wta_email_sent] [tinyint] NULL,
	[wta_date_email_sent] [datetime] NULL,
	[wta_email_add] [varchar](100) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[w_tenant_aging_header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[w_tenant_aging_header](
	[wth_hdr_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[wth_filename] [varchar](100) NULL,
	[wth_as_of] [datetime] NULL,
	[wth_email_sent] [tinyint] NULL,
	[wth_email_sent_final] [tinyint] NULL,
	[wth_summary_sent] [tinyint] NULL,
	[wth_date_uploaded] [datetime] NULL,
	[wth_uploaded_by] [char](10) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_charges$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_charges$](
	[charge_code] [char](5) NOT NULL,
	[charge_desc] [varchar](50) NULL,
	[gl_code] [varchar](50) NULL,
	[charge_type] [char](1) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_real_property$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_real_property$](
	[real_property_code] [char](10) NOT NULL,
	[real_property_name] [varchar](100) NULL,
	[real_property_company_name] [varchar](100) NULL,
	[real_property_dba_name] [varchar](100) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[tot_no_of_units] [int] NULL,
	[lot_space] [varchar](20) NULL,
	[space_type] [char](1) NULL,
	[remarks] [varchar](100) NULL,
	[cost_center] [varchar](20) NULL,
	[company_code] [varchar](5) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_tenant$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_tenant$](
	[tenant_code] [char](10) NULL,
	[tenant_name] [varchar](100) NULL,
	[real_property_code] [char](10) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL,
	[bill_to] [char](10) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contract_eff_date] [datetime] NULL,
	[contract_expiry_date] [datetime] NULL,
	[sap_code] [varchar](20) NULL,
	[terminated] [char](1) NULL,
	[date_terminated] [datetime] NULL,
	[actual_move_in_date] [datetime] NULL,
	[email_add] [varchar](50) NULL,
	[is_affiliate_employee] [char](1) NULL,
	[employer] [varchar](100) NULL,
	[tenant_type] [char](2) NULL,
	[is_sap_affiliate] [char](1) NULL,
	[new_code] [varchar](50) NULL,
	[business_area] [varchar](50) NULL,
	[company_code] [varchar](5) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_tenant_charges$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_tenant_charges$](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[tenant_code] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 6) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_unit_charges$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_unit_charges$](
	[real_property_code] [char](10) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[charge_code] [char](5) NOT NULL,
	[charge_amount] [decimal](18, 2) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_m_units$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_m_units$](
	[real_property_code] [char](10) NOT NULL,
	[building_code] [char](10) NOT NULL,
	[unit_no] [char](10) NOT NULL,
	[lot_area] [varchar](20) NULL,
	[no_of_bedrooms] [int] NULL,
	[unit_type] [varchar](20) NULL,
	[is_reserved] [char](1) NULL,
	[remarks] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_real_property$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_real_property$](
	[real_property_code] [char](10) NOT NULL,
	[real_property_name] [varchar](100) NULL,
	[real_property_company_name] [varchar](100) NULL,
	[real_property_dba_name] [varchar](100) NULL,
	[address1] [varchar](50) NULL,
	[address2] [varchar](50) NULL,
	[contact_no1] [varchar](20) NULL,
	[contact_no2] [varchar](20) NULL,
	[tot_no_of_units] [int] NULL,
	[lot_space] [varchar](20) NULL,
	[space_type] [char](1) NULL,
	[remarks] [varchar](100) NULL,
	[cost_center] [varchar](20) NULL,
	[company_code] [varchar](5) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_t_ar_033111$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_t_ar_033111$](
	[recid] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[INVOICE NO] [varchar](50) NULL,
	[INVOICE DATE] [datetime] NULL,
	[TENANT CODE (BILL TO)] [varchar](255) NULL,
	[TENANT NAME] [varchar](255) NULL,
	[REAL PROPERTY CODE] [varchar](255) NULL,
	[REAL PROPERTY NAME] [varchar](255) NULL,
	[BILLING FROM] [datetime] NULL,
	[BILLING TO] [datetime] NULL,
	[DOCUMENT NO] [varchar](53) NULL,
	[SAP CODE] [varchar](53) NULL,
	[REMARKS] [varchar](4000) NULL,
	[TENANT CODE (OCCUPANT)] [varchar](255) NULL,
	[TENANT NAME1] [varchar](255) NULL,
	[REAL PROPERTY CODE1] [varchar](255) NULL,
	[BUILDING CODE] [varchar](255) NULL,
	[UNIT NO#] [varchar](255) NULL,
	[CHARGE CODE] [varchar](255) NULL,
	[CHARGE DESC] [varchar](255) NULL,
	[CHARGE AMOUNT] [decimal](18, 2) NULL,
	[TOTAL CHARGE AMOUNT] [decimal](18, 0) NULL,
	[REMARKS1] [varchar](4000) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_invoice](
	[seq] [decimal](18, 0) NULL,
	[invoice_no] [varchar](30) NULL,
	[invoice_no_type] [char](1) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [char](10) NULL,
	[real_property_code] [char](10) NULL,
	[billing_from] [datetime] NULL,
	[billing_to] [datetime] NULL,
	[document_no] [varchar](20) NULL,
	[sap_code] [varchar](20) NULL,
	[remarks] [varchar](500) NULL,
	[status] [char](1) NULL,
	[created_by] [varchar](50) NULL,
	[date_created] [datetime] NULL,
	[updated_by] [varchar](50) NULL,
	[date_updated] [datetime] NULL,
	[invoice_detail_id] [decimal](18, 0) NULL,
	[charge_code] [varchar](10) NULL,
	[charge_amount] [decimal](18, 6) NULL,
	[total_charge_amount] [decimal](18, 2) NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[prev_reading] [decimal](18, 0) NULL,
	[current_reading] [decimal](18, 0) NULL,
	[detail_remarks] [varchar](500) NULL,
	[client_name] [varchar](200) NULL,
	[tenant_code] [varchar](20) NULL,
	[tenant_name] [varchar](200) NULL,
	[tenant_address1] [varchar](200) NULL,
	[tenant_address2] [varchar](200) NULL,
	[unit_area] [varchar](100) NULL,
	[charge_desc] [varchar](100) NULL,
	[real_property_name] [varchar](200) NULL,
	[real_property_company_name] [varchar](100) NULL,
	[dba_name] [varchar](100) NULL,
	[company_address] [varchar](100) NULL,
	[company_contact_no] [varchar](100) NULL,
	[prepared_by] [varchar](50) NULL,
	[approved_by] [varchar](50) NULL,
	[generated_by] [varchar](50) NULL,
	[date_generated] [varchar](50) NULL,
	[check_payable] [varchar](50) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_occupancy]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_occupancy](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[real_property_code] [varchar](50) NULL,
	[date_from] [datetime] NULL,
	[date_to] [datetime] NULL,
	[occupancy_date] [datetime] NULL,
	[total_units_for_rent] [decimal](18, 0) NULL,
	[total_complimentary] [decimal](18, 0) NULL,
	[total_units_occupied] [decimal](18, 0) NULL,
	[total_vacant_units] [decimal](18, 0) NULL,
	[room_income] [decimal](18, 2) NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
	[seq] [int] NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_occupancy_asof]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_occupancy_asof](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[real_property_code] [nvarchar](50) NULL,
	[date_asof] [datetime] NULL,
	[occupancy_date] [datetime] NULL,
	[total_units_for_rent] [decimal](18, 0) NULL,
	[total_complimentary] [decimal](18, 0) NULL,
	[total_units_occupied] [decimal](18, 0) NULL,
	[total_vacant_units] [decimal](18, 0) NULL,
	[room_income] [decimal](18, 2) NULL,
	[date_created] [datetime] NULL,
	[created_by] [nvarchar](50) NULL,
	[seq] [int] NULL,
	[remarks] [nvarchar](4000) NULL,
	[rg_tran_date] [datetime] NULL,
	[rg_report_title] [nvarchar](200) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_or_processing]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_or_processing](
	[recid] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[invoice_no] [varchar](20) NULL,
	[or_breakdown] [char](1) NULL,
	[date_created] [datetime] NULL,
	[created_by] [varchar](50) NULL,
 CONSTRAINT [PK_tmp_or_processing] PRIMARY KEY CLUSTERED 
(
	[recid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_sap_uploading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_sap_uploading](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[APT  ] [decimal](18, 2) NULL,
	[ADJ  ] [decimal](18, 2) NULL,
	[APTD ] [decimal](18, 2) NULL,
	[BOC  ] [decimal](18, 2) NULL,
	[BROK ] [decimal](18, 2) NULL,
	[REF  ] [decimal](18, 2) NULL,
	[CUCF ] [decimal](18, 2) NULL,
	[CUCNF] [decimal](18, 2) NULL,
	[ELEC ] [decimal](18, 2) NULL,
	[UTIL ] [decimal](18, 2) NULL,
	[ADJE ] [decimal](18, 2) NULL,
	[FAC  ] [decimal](18, 2) NULL,
	[INFR ] [decimal](18, 2) NULL,
	[WIFI ] [decimal](18, 2) NULL,
	[REIMB] [decimal](18, 2) NULL,
	[CABLE] [decimal](18, 2) NULL,
	[MCC  ] [decimal](18, 2) NULL,
	[OFF  ] [decimal](18, 2) NULL,
	[ADJO ] [decimal](18, 2) NULL,
	[OTH  ] [decimal](18, 2) NULL,
	[OTHE ] [decimal](18, 2) NULL,
	[RENOV] [decimal](18, 2) NULL,
	[DEPO ] [decimal](18, 2) NULL,
	[GENST] [decimal](18, 2) NULL,
	[TSLO ] [decimal](18, 2) NULL,
	[RSTAF] [decimal](18, 2) NULL,
	[WHS  ] [decimal](18, 2) NULL,
	[ADJWH] [decimal](18, 2) NULL,
	[ADJW ] [decimal](18, 2) NULL,
	[WATER] [decimal](18, 2) NULL,
	[WATBU] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_sap_uploading_view]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_sap_uploading_view](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[charge_code] [varchar](10) NULL,
	[total_amount] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_send_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_send_invoice](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[total_amount] [decimal](18, 6) NULL,
	[client_name] [varchar](200) NULL,
	[unit_no] [varchar](20) NULL,
	[tagged] [tinyint] NULL,
	[email_sent] [tinyint] NULL,
	[email_addr] [varchar](100) NULL,
	[date_email_sent] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_soa]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_soa](
	[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
	[company_name] [varchar](200) NULL,
	[company_address] [varchar](200) NULL,
	[company_contact1] [varchar](200) NULL,
	[company_contact2] [varchar](200) NULL,
	[client_code] [varchar](200) NULL,
	[client_name] [varchar](200) NULL,
	[sap_code] [varchar](200) NULL,
	[address1] [varchar](200) NULL,
	[address2] [varchar](200) NULL,
	[date_from] [char](10) NULL,
	[date_to] [char](10) NULL,
	[activity_code] [tinyint] NULL,
	[activity_desc] [varchar](50) NULL,
	[ref_no] [varchar](50) NULL,
	[ref_date] [datetime] NULL,
	[ref_detail_id] [decimal](18, 0) NULL,
	[ref_no2] [varchar](200) NULL,
	[tenant_code] [varchar](200) NULL,
	[tenant_name] [varchar](200) NULL,
	[charge_code] [varchar](200) NULL,
	[charge_desc] [varchar](200) NULL,
	[charge_amount] [decimal](18, 2) NULL,
	[aging_current] [decimal](18, 2) NULL,
	[aging_30] [decimal](18, 2) NULL,
	[aging_60] [decimal](18, 2) NULL,
	[aging_90] [decimal](18, 2) NULL,
	[aging_over] [decimal](18, 2) NULL,
	[date_generated] [varchar](50) NULL,
	[generated_by] [varchar](200) NULL,
 CONSTRAINT [PK_z_tmp_soa] PRIMARY KEY CLUSTERED 
(
	[rec_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_tenantforpaymentnotice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_tenantforpaymentnotice](
	[rec_id] [decimal](18, 0) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_tenantforpaymentnotice_tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_tenantforpaymentnotice_tenant](
	[rec_id] [decimal](18, 0) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_tenantforpaymentnoticefinal_tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_tenantforpaymentnoticefinal_tenant](
	[rec_id] [decimal](18, 0) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_tenantforrenewalnotice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_tenantforrenewalnotice](
	[tenant_code] [varchar](20) NULL,
	[tenant_name] [varchar](100) NULL,
	[real_property_code] [char](5) NULL,
	[real_property_name] [varchar](100) NULL,
	[building_code] [char](10) NULL,
	[unit_no] [char](10) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_tenantforrenewalnotice_tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_tenantforrenewalnotice_tenant](
	[tenant_code] [varchar](20) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_unpost_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_unpost_invoice](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[APT  ] [decimal](18, 2) NULL,
	[ADJ  ] [decimal](18, 2) NULL,
	[APTD ] [decimal](18, 2) NULL,
	[BOC  ] [decimal](18, 2) NULL,
	[BROK ] [decimal](18, 2) NULL,
	[REF  ] [decimal](18, 2) NULL,
	[CUCF ] [decimal](18, 2) NULL,
	[CUCNF] [decimal](18, 2) NULL,
	[ELEC ] [decimal](18, 2) NULL,
	[UTIL ] [decimal](18, 2) NULL,
	[ADJE ] [decimal](18, 2) NULL,
	[FAC  ] [decimal](18, 2) NULL,
	[INFR ] [decimal](18, 2) NULL,
	[WIFI ] [decimal](18, 2) NULL,
	[REIMB] [decimal](18, 2) NULL,
	[CABLE] [decimal](18, 2) NULL,
	[MCC  ] [decimal](18, 2) NULL,
	[OFF  ] [decimal](18, 2) NULL,
	[ADJO ] [decimal](18, 2) NULL,
	[OTH  ] [decimal](18, 2) NULL,
	[OTHE ] [decimal](18, 2) NULL,
	[RENOV] [decimal](18, 2) NULL,
	[DEPO ] [decimal](18, 2) NULL,
	[GENST] [decimal](18, 2) NULL,
	[TSLO ] [decimal](18, 2) NULL,
	[RSTAF] [decimal](18, 2) NULL,
	[WHS  ] [decimal](18, 2) NULL,
	[ADJWH] [decimal](18, 2) NULL,
	[ADJW ] [decimal](18, 2) NULL,
	[WATER] [decimal](18, 2) NULL,
	[WATBU] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_unpost_invoice_search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_unpost_invoice_search](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[charge_code] [varchar](10) NULL,
	[total_amount] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_unvoid_invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_unvoid_invoice](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[APT  ] [decimal](18, 2) NULL,
	[ADJ  ] [decimal](18, 2) NULL,
	[APTD ] [decimal](18, 2) NULL,
	[BOC  ] [decimal](18, 2) NULL,
	[BROK ] [decimal](18, 2) NULL,
	[REF  ] [decimal](18, 2) NULL,
	[CUCF ] [decimal](18, 2) NULL,
	[CUCNF] [decimal](18, 2) NULL,
	[ELEC ] [decimal](18, 2) NULL,
	[UTIL ] [decimal](18, 2) NULL,
	[ADJE ] [decimal](18, 2) NULL,
	[FAC  ] [decimal](18, 2) NULL,
	[INFR ] [decimal](18, 2) NULL,
	[WIFI ] [decimal](18, 2) NULL,
	[REIMB] [decimal](18, 2) NULL,
	[CABLE] [decimal](18, 2) NULL,
	[MCC  ] [decimal](18, 2) NULL,
	[OFF  ] [decimal](18, 2) NULL,
	[ADJO ] [decimal](18, 2) NULL,
	[OTH  ] [decimal](18, 2) NULL,
	[OTHE ] [decimal](18, 2) NULL,
	[RENOV] [decimal](18, 2) NULL,
	[DEPO ] [decimal](18, 2) NULL,
	[GENST] [decimal](18, 2) NULL,
	[TSLO ] [decimal](18, 2) NULL,
	[RSTAF] [decimal](18, 2) NULL,
	[WHS  ] [decimal](18, 2) NULL,
	[ADJWH] [decimal](18, 2) NULL,
	[ADJW ] [decimal](18, 2) NULL,
	[WATER] [decimal](18, 2) NULL,
	[WATBU] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[z_tmp_unvoid_invoice_search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[z_tmp_unvoid_invoice_search](
	[invoice_no] [varchar](20) NULL,
	[invoice_date] [datetime] NULL,
	[client_code] [varchar](20) NULL,
	[tenant_code] [varchar](20) NULL,
	[charge_code] [varchar](10) NULL,
	[total_amount] [decimal](18, 2) NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[zz_data_master_file$]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[zz_data_master_file$](
	[TENANT_CODE] [varchar](255) NULL,
	[TENANT_SAP_CODE] [varchar](53) NULL,
	[TENANT_NAME] [varchar](255) NULL,
	[TENANT_REAL_PROPERTY_CODE] [varchar](255) NULL,
	[TENANT_BLDG_CODE] [varchar](255) NULL,
	[TENANT_UNIT_NO] [varchar](53) NULL,
	[PAYEE_CODE] [varchar](255) NULL,
	[PAYEE_NAME] [varchar](255) NULL,
	[PAYEE_SAP_CODE] [varchar](53) NULL,
	[CONTACT_NO1] [varchar](255) NULL,
	[CONTACT_NO2] [varchar](255) NULL,
	[ADDRESS1] [varchar](255) NULL,
	[ADDRESS2] [varchar](53) NULL,
	[CONTRACT_EFF_DATE] [datetime] NULL,
	[CONTRACT_EXPIRY_DATE] [datetime] NULL,
	[IS_TERMINATED] [char](255) NULL,
	[DATE_TERMINATED] [datetime] NULL,
	[ACTUAL_MOVEIN_DATE] [datetime] NULL,
	[EMAIL_ADDRESS] [varchar](255) NULL,
	[IS_THC_EMPLOYEE] [varchar](255) NULL,
	[EMPLOYER] [varchar](255) NULL,
	[TENANT_TYPE] [varchar](255) NULL,
	[IS_SAP_AFFILIATE] [varchar](255) NULL,
	[SAP_COMPANY_CODE] [varchar](255) NULL,
	[SAP_BUSINESS_AREA] [varchar](255) NULL,
	[COMPANY_CODE] [varchar](255) NULL,
	[REAL_PROPERTY_CODE] [varchar](255) NULL,
	[REAL_PROPERTY_NAME] [varchar](255) NULL,
	[REAL_PROPERTY_COMPANY] [varchar](255) NULL,
	[REAL_PROPERTY_DBA_NAME] [varchar](255) NULL,
	[REAL_PROPERTY_ADDRESS_1] [varchar](255) NULL,
	[REAL_PROPERTY_ADDRESS_2] [varchar](255) NULL,
	[REAL_PROPERTY_CONTACT_NO1] [varchar](53) NULL,
	[REAL_PROPERTY_CONTACT_NO2] [varchar](53) NULL,
	[NO_OF_UNITS] [varchar](53) NULL,
	[LOT_SPACE] [varchar](255) NULL,
	[SPACE_TYPE] [varchar](255) NULL,
	[REMARKS] [varchar](255) NULL,
	[COST_CENTER] [varchar](53) NULL,
	[CHARGE_CODE] [varchar](255) NULL,
	[CHARGE_DESC] [varchar](255) NULL,
	[GL_CODE] [varchar](53) NULL,
	[CHARGE_TYPE] [varchar](255) NULL,
	[CHARGE_CODE2] [varchar](255) NULL,
	[CHARGE_AMOUNT] [decimal](18, 6) NULL
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[m_space_type] ADD  DEFAULT ((0)) FOR [IsActive]
GO
ALTER TABLE [dbo].[m_space_type] ADD  DEFAULT (getdate()) FOR [Created]
GO
ALTER TABLE [dbo].[m_tenant] ADD  CONSTRAINT [DF_m_tenant_date_created]  DEFAULT (getdate()) FOR [date_created]
GO
ALTER TABLE [dbo].[m_tenant] ADD  CONSTRAINT [DF_m_tenant_date_updated]  DEFAULT (getdate()) FOR [date_updated]
GO
ALTER TABLE [dbo].[m_units_movement] ADD  CONSTRAINT [DF_m_units_movement_date_executed]  DEFAULT (getdate()) FOR [date_executed]
GO
ALTER TABLE [dbo].[rg_occupancy] ADD  CONSTRAINT [DF_rg_occupancy_tran_date]  DEFAULT (getdate()) FOR [tran_date]
GO
ALTER TABLE [dbo].[rg_occupancy_asof_log] ADD  CONSTRAINT [DF_rg_occupancy_asof_log_date_generated]  DEFAULT (getdate()) FOR [date_generated]
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_m_Charges]
	@strMode varchar(10),
	@code varchar(5),
	@desc varchar(50),
	@glcode varchar(50),
	@charge_type char(1),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)

AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'CHARGES'

if @strMode = 'SAVE'
	begin
		if not exists (select * from m_charges where upper(ltrim(charge_code)) = upper(ltrim(@code)))
			begin
				insert into m_charges (charge_code,charge_desc,gl_code,charge_type)	
				select upper(@code),@desc,@glcode,@charge_type

				set @data = 'insert into m_charges ' +	
					' select ' + upper(@code)+ ',' + @desc+ ',' + @glcode+ ',' + @charge_type
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update m_charges set 
					charge_desc = @desc,
					gl_code = @glcode,
					charge_type = @charge_type
				where upper(ltrim(charge_code)) = upper(ltrim(@code))

				set @data = ' update m_charges set ' +
					' charge_desc = ' + @desc +','+
					' gl_code = ' + @glcode+','+
					' charge_type =' + @charge_type +
					' where upper(ltrim(charge_code)) = ' + upper(ltrim(@code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_charges where upper(ltrim(charge_code)) = upper(ltrim(@code)))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select charge_code,charge_desc,gl_code,
		case when ltrim(rtrim(charge_type)) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type
		from m_charges where charge_code like '%' + @desc + '%' or charge_desc like '%' + @desc + '%'
		order by charge_desc
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 * from m_charges where upper(ltrim(charge_code)) like upper(ltrim(@code)) + '%'
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from m_unit_charges where upper(ltrim(charge_code)) = upper(ltrim(@code))) and not exists (select * from m_tenant_charges where upper(ltrim(charge_code)) = upper(ltrim(@code)))
			begin
				delete from m_charges where upper(ltrim(charge_code)) = upper(ltrim(@code))
				set @data = 'delete from m_charges where upper(ltrim(charge_code)) = ' + upper(ltrim(@code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_RealProperty]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_m_RealProperty]
	@strMode varchar(10),
	@code varchar(10),
	@name varchar(100),
	@real_property_company_name varchar(100),
	@real_property_dba_name varchar(100),
	@address1 varchar(100),
	@address2 varchar(100),
	@contact_no varchar(20),
	@contact_no2 varchar(20),
	@tot_no_of_units int,
	@lot_space varchar(20),
	@space_type char(1),
	@remarks varchar(100),
	@cost_center varchar(20),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'REAL PROPERTY'

if @strMode = 'SAVE'
	begin
		if not exists (select * from m_real_property where upper(ltrim(real_property_code)) = upper(ltrim(@code)))
			begin
				insert into m_real_property (real_property_code,real_property_name,	real_property_company_name,real_property_dba_name,
					address1,address2,contact_no1,contact_no2,tot_no_of_units,lot_space,space_type,remarks,cost_center,company_code)
				select upper(@code),@name,@real_property_company_name,@real_property_dba_name,
					@address1,@address2,@contact_no,@contact_no2,@tot_no_of_units,@lot_space,@space_type,@remarks,@cost_center,@company_code
	
				set @data ='insert into m_real_property (real_property_code,real_property_name,real_property_company_name,real_property_dba_name,' + 
					'address1,address2,contact_no1,contact_no2,tot_no_of_units,lot_space,space_type,remarks,cost_center,company_code) ' + 
					'select ' +  upper(@code) + ',' + @name+ ',' + @real_property_company_name+ ',' + @real_property_dba_name+ ',' + 
					'' + @address1+ ',' + @address2+ ',' + @contact_no+ ',' + @contact_no2+ ',' + convert(varchar(20),@tot_no_of_units)+ ',' + @lot_space+ ',' + @space_type+ ',' + @remarks+ ',' +  
					@cost_center + ',' + @company_code
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update m_real_property set 
					real_property_name = @name,
					real_property_company_name = @real_property_company_name,
					real_property_dba_name = @real_property_dba_name,
					address1 = @address1,
					address2 = @address2,
					contact_no1 = @contact_no,
					contact_no2 = @contact_no2,
					tot_no_of_units = @tot_no_of_units,
					lot_space = @lot_space,
					space_type = @space_type,
					remarks = @remarks,
					cost_center = @cost_center,
					company_code = @company_code
				where upper(ltrim(real_property_code)) = upper(ltrim(@code))

				set @data = ' update m_real_property set ' +
					' real_property_name = ' + @name + ',' +
					' real_property_company_name = ' + @real_property_company_name+ ',' +
					' real_property_dba_name = ' +@real_property_dba_name+ ',' +
					' address1 = ' +@address1 + ',' +
					' address2 = ' +@address2+ ',' +
					' contact_no1 = ' +@contact_no+ ',' +
					' contact_no2 = ' +@contact_no2+ ',' +
					' tot_no_of_units = ' + convert(varchar(20),@tot_no_of_units)+ ',' +
					' ot_space = ' +@lot_space+ ',' +
					' space_type = ' +@space_type+ ',' +
					' remarks = ' +@remarks+ ',' +
					' cost_center = ' + @cost_center  + ',' +
					' company_code = ' +@company_code+
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_real_property where upper(ltrim(real_property_code)) = upper(ltrim(@code))) 
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select * from m_real_property where real_property_code like '%' + @name + '%' or real_property_name like '%' + @name + '%'
		order by real_property_name
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 * from m_real_property where upper(ltrim(real_property_code)) like upper(ltrim(@code)) + '%'
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from m_units where upper(ltrim(real_property_code)) = upper(ltrim(@code)))
			begin
				delete from m_real_property where upper(ltrim(real_property_code)) = upper(ltrim(@code))
				set @data = 'delete from m_real_property where upper(ltrim(real_property_code)) = ' + upper(ltrim(@code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_m_Tenant]
	@strMode varchar(50),
	@code varchar(10),
	@name varchar(100),
	@real_property_code varchar(5),
	@building_code varchar(10),
	@unit_no varchar(10),
	@bill_to varchar(10),
	@contact_no1 varchar(20),
	@contact_no2 varchar(20),
	@address1 varchar(50),
	@address2 varchar(50),
	@contract_eff_date datetime,
	@contract_expiry_date datetime,
	@sap_code varchar(20),
	@terminated char(1),
	@date_terminated datetime,
	@actual_move_in_date datetime,
	@email_add varchar(256),
	@is_affiliate_employee char(1),
	@employer varchar(100),
	@tenant_type char(2),
	@is_sap_affiliate char(1),
	@new_code varchar(50),
	@business_area varchar(50),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)
declare @next_code_settings int
declare @tmp_real_property_code varchar(5),@tmp_building_code varchar(10),@tmp_unit_no varchar(10),@tmp_tenant_type char(2),@event_remarks varchar(4000),@tmp_name varchar(100)

set @module_name = 'TENANT'
set @next_code_settings  = 9
set @tenant_type = upper(ltrim(rtrim(isnull(@tenant_type,'OC'))))	
set @name = upper(ltrim(rtrim(@name)))

	if exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))) 
		begin
			select top 1 @tmp_real_property_code = real_property_code,@tmp_building_code = building_code,@tmp_unit_no = unit_no,@tmp_tenant_type = tenant_type,
				@tmp_name = tenant_name
			from m_tenant
			where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
		end

if @strMode = 'SAVE'
	begin
		if  upper(ltrim(rtrim(isnull(@code,'')))) = '' or not exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))) 
			begin
				select @code = max(
								convert(decimal(18,0),right(ltrim(rtrim(tenant_code)),@next_code_settings))
							)
				from m_tenant 
		
				set @code = isnull(@code,0)
				
				set @code = @code + 1
				set @code = ltrim(rtrim(replace(space(@next_code_settings - len(@code)),' ','0') + convert(varchar(10),@code)))
				set @code = 'T'  + convert(varchar(9),@code)

				insert into m_tenant	
					(tenant_code,tenant_name,real_property_code,building_code,unit_no,bill_to,contact_no1,contact_no2,address1,address2,
					contract_eff_date,contract_expiry_date,sap_code,terminated,date_terminated,actual_move_in_date,email_add,is_affiliate_employee,
					employer,tenant_type,is_sap_affiliate,new_code,business_area,company_code)
				select upper(ltrim(rtrim(@code))),@name,@real_property_code,@building_code,@unit_no,
					case when @tenant_type = 'OC' or @tenant_type = 'C' then upper(@code) else @bill_to end,
					@contact_no1,@contact_no2,@address1,@address2,
					@contract_eff_date,@contract_expiry_date,@sap_code,@terminated,@date_terminated,@actual_move_in_date,@email_add,@is_affiliate_employee,
					@employer,@tenant_type,@is_sap_affiliate,@new_code,@business_area,@company_code

				insert into m_tenant_charges					
				select upper(ltrim(rtrim(@code))),charge_code,charge_amount from m_unit_charges
				where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 

				set @data = 'insert into m_tenant ' +	
					' (tenant_code,tenant_name,real_property_code,building_code,unit_no,bill_to,contact_no1,contact_no2,address1,address2,' +
					' contract_eff_date,contract_expiry_date,sap_code,terminated,date_terminated,actual_move_in_date,email_add,is_affiliate_employee,' +
					' employer,tenant_type,is_sap_affiliate,new_code,business_area,company_code)' +
					' select ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @real_property_code+ ',' + @building_code+ ',' + @unit_no+ ',' + 
					'  case when ' + @tenant_type + '= ''OC'' or ' + @tenant_type + '= ''C'' then ' + upper(@code) + ' else ' + @bill_to  + 'end,' +
					@contact_no1+ ',' + @contact_no2+ ',' + @address1+ ',' + @address2+ ',' + 
					convert(varchar(10),@contract_eff_date,101)+ ',' +convert(varchar(10),@contract_expiry_date,101)+ ',' +@sap_code+ ',' +@terminated+ ',' +convert(varchar(10),@date_terminated,101)+ ',' +convert(varchar(10),@actual_move_in_date,101)+ ',' +@email_add+ ',' +
					@is_affiliate_employee+ ',' +@employer+ ',' +@tenant_type+ ',' +@is_sap_affiliate+ ',' +@new_code+ ',' +@business_area+ ',' +@company_code

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				set @module_name = 'TENANT CHARGES'
				
				set @data = 'insert into m_tenant_charges	 ' +				
					' select ' + upper(ltrim(rtrim(@code)))+',charge_code,charge_amount from m_unit_charges ' +
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + ' and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
					' and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no)) 

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				if @tenant_type <> 'C'
					begin
						set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
						exec sp_m_Units_Movement @real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@uid	
					end
			end
		else
			begin
				update m_tenant set 
					tenant_name = @name,
					real_property_code = @real_property_code,
					building_code = @building_code,
					unit_no = @unit_no,
					bill_to = case when @tenant_type = 'OC' or @tenant_type = 'C' then upper(@code) else @bill_to end,
					contact_no1 = @contact_no1,
					contact_no2 = @contact_no2,
					address1 = @address1,
					address2 = @address2,
					contract_eff_date = @contract_eff_date,
					contract_expiry_date = @contract_expiry_date,
					sap_code = @sap_code,
					terminated = @terminated,
					date_terminated = @date_terminated,
					actual_move_in_date = @actual_move_in_date,
					email_add = @email_add,
					is_affiliate_employee = @is_affiliate_employee,
					employer = @employer,
					tenant_type = @tenant_type,
					is_sap_affiliate = @is_sap_affiliate,
					new_code = @new_code,
					business_area = @business_area,
					company_code = @company_code
				where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
	
				if not exists(select * from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
					begin
						insert into m_tenant_charges
						select upper(ltrim(rtrim(@code))),charge_code,charge_amount from m_unit_charges
						where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
						and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 
					end

				set @data = 'update m_tenant set ' +
					' tenant_name = ' + @name + ',' +
					' real_property_code =' + @real_property_code+ ',' +
					' building_code =' + @building_code+ ',' +
					' unit_no =' + @unit_no+ ',' +
					' bill_to = case when ' + @tenant_type + '=''OC'' or ' + @tenant_type +'= ''C'' then ' + upper(@code) +' else '+ @bill_to + ' end,' +
					' contact_no1 =' + @contact_no1+ ',' +
					' contact_no2 =' + @contact_no2+ ',' +
					' address1 =' + @address1+ ',' +
					' address2 =' + @address2+ ',' +
					' contract_eff_date =' + convert(varchar(10),@contract_eff_date,101)+ ',' +
					' contract_expiry_date =' + convert(varchar(10),@contract_expiry_date,101) + ',' +
					' sap_code =' + @sap_code+ ',' +
					' terminated =' + @terminated+ ',' +
					' date_terminated =' + convert(varchar(10),@date_terminated,101) + ',' +
					' actual_move_in_date =' + convert(varchar(10),@actual_move_in_date,101) + ',' +
					' email_add =' + @email_add+ ',' +
					' is_affiliate_employee =' + @is_affiliate_employee+ ',' +
					' employer =' + @employer+ ',' +
					' tenant_type =' + @tenant_type+ ',' +
					' is_sap_affiliate =' + @is_sap_affiliate+ ',' +
					' new_code =' + @new_code+ ',' +
					' business_area = ' + @business_area+ ',' +
					' company_code =' + @company_code +
					' where upper(ltrim(rtrim(tenant_code))) =' + upper(ltrim(rtrim(@code)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

				set @module_name = 'TENANT CHARGES'

				set @data = 'insert into m_tenant_charges	 ' +				
					' select ' + upper(ltrim(rtrim(@code)))+',charge_code,charge_amount from m_unit_charges ' +
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + ' and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
					' and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no)) 

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				if @tmp_real_property_code <> @real_property_code and @tmp_building_code <> @building_code and @tmp_unit_no <> @unit_no
					begin
						if @tmp_tenant_type <> @tenant_type and @tenant_type <>'C'
							begin
								set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
								exec sp_m_Units_Movement @real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@uid
							end
						else if @tmp_tenant_type <> @tenant_type and @tenant_type = 'C'
							begin
								set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type + '. TENANT BECAME A CLIENT.'
								exec sp_m_Units_Movement @tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@uid
							end
						else if @tmp_tenant_type = @tenant_type and @tenant_type <>'C'
							begin
								set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type
								exec sp_m_Units_Movement @tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@uid
				
								set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
								exec sp_m_Units_Movement @real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@uid							
							end
					end
				else if @tmp_tenant_type <> @tenant_type and @tenant_type = 'C'
					begin
						set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type + '. TENANT BECAME A CLIENT.'
						exec sp_m_Units_Movement @tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@uid
					end

			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code))))
			select 1 as x,'Tenant Code already exists.' as msg		
		/*else if upper(ltrim(rtrim(@real_property_code))) <> '' and upper(ltrim(rtrim(@building_code))) <> '' and upper(ltrim(rtrim(@unit_no))) <> '' 
			begin
				if exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) <> upper(ltrim(rtrim(@code))) and upper(ltrim(rtrim(real_property_code)))  = upper(ltrim(rtrim(@real_property_code))) and upper(ltrim(rtrim(building_code))) = upper(ltrim(rtrim(@building_code))) and upper(ltrim(rtrim(unit_no))) = upper(ltrim(rtrim(@unit_no))))
					select 1 as x,'Unit is occupied already.' as msg		
			end*/
		else
			--if upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
			select 0 as x,'' as msg
	end

if @strMode = 'CHECK_UNIT_AVAILABLE'
	begin
		if upper(ltrim(rtrim(@real_property_code))) <> '' and upper(ltrim(rtrim(@building_code))) <> '' and upper(ltrim(rtrim(@unit_no))) <> '' 
			begin
				if exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) <> upper(ltrim(rtrim(@code))) and upper(ltrim(rtrim(real_property_code)))  = upper(ltrim(rtrim(@real_property_code))) and upper(ltrim(rtrim(building_code))) = upper(ltrim(rtrim(@building_code))) and upper(ltrim(rtrim(unit_no))) = upper(ltrim(rtrim(@unit_no))))
					select 1 as x,'Unit is occupied already.' as msg		
			end
		else
			--if upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
			select 0 as x,'' as msg
	end

if @strMode = 'CHECK_UNIT'
	begin
		if upper(ltrim(rtrim(@real_property_code))) <> '' and upper(ltrim(rtrim(@building_code))) <> '' and upper(ltrim(rtrim(@unit_no))) <> '' 
			begin
				if not exists (select * from m_units where upper(ltrim(rtrim(real_property_code))) = upper(ltrim(rtrim(@real_property_code))) and upper(ltrim(rtrim(building_code))) = upper(ltrim(rtrim(@building_code))) and upper(ltrim(rtrim(unit_no))) = upper(ltrim(rtrim(@unit_no)))) 			
					select 1 as x,'Invalid Unit' as msg
			end
		else
			--if upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
			select 0 as x,'' as msg
	end

if @strMode = 'VIEW'
	begin
		select m_tenant.*,m_real_property.real_property_name,
		case 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'OC' then 'OCCUPANT & CLIENT' 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'O' then 'OCCUPANT' 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'C' then 'CLIENT' 
			else ''
		end as tenant_type_desc,
		case when ltrim(rtrim(m_tenant.tenant_type)) = 'O' then bill_to.tenant_name else '' end as bill_to_name
		from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where m_tenant.tenant_code like '%' + @name + '%' or m_tenant.tenant_name like '%' + @name + '%'
		or bill_to.tenant_name like '%' + @name + '%' or m_tenant.tenant_type like '%' + @name + '%'
		or m_real_property.real_property_name like '%' + @name + '%' or m_tenant.building_code like '%' + @name + '%'
		or m_tenant.unit_no like '%' + @name + '%'
		order by m_tenant.tenant_name
	end

if @strMode = 'VIEW_STAT'
	begin
		select m_tenant.*,m_real_property.real_property_name,
		case 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'OC' then 'OCCUPANT & CLIENT' 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'O' then 'OCCUPANT' 
			when ltrim(rtrim(m_tenant.tenant_type)) = 'C' then 'CLIENT' 
			else ''
		end as tenant_type_desc,
		case when ltrim(rtrim(m_tenant.tenant_type)) = 'O' then bill_to.tenant_name else '' end as bill_to_name
		from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where (m_tenant.tenant_code like '%' + @name + '%' or m_tenant.tenant_name like '%' + @name + '%'
		or bill_to.tenant_name like '%' + @name + '%' or m_tenant.tenant_type like '%' + @name + '%'
		or m_real_property.real_property_name like '%' + @name + '%' or m_tenant.building_code like '%' + @name + '%'
		or m_tenant.unit_no like '%' + @name + '%')
		and (m_tenant.tenant_code not in (select tenant_code from m_tenant_charges)
		and upper(ltrim(rtrim(isnull(m_tenant.terminated,'')))) <> 'Y'
		and upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'OC')))) <> 'C' 
		)
		order by m_tenant.tenant_name
	end

if @strMode = 'VACANT_UNITS'
	begin
		select m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @name + '%' or building_code like '%' + @name + '%' or unit_no like '%' + @name + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') <> 'Y'
			)
		order by m_real_property.real_property_name,building_code,unit_no 
	end

if @strMode = 'OCCUPIED_UNITS'
	begin
		select m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @name + '%' or building_code like '%' + @name + '%' or unit_no like '%' + @name + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') <> 'Y'
			)
		order by m_real_property.real_property_name,building_code,unit_no
	end

if @strMode = 'ALL_UNITS'
	begin
		select m_units.*,m_real_property.real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (m_real_property.real_property_name like '%' + @name + '%' or building_code like '%' + @name + '%' or unit_no like '%' + @name + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 		
		order by m_real_property.real_property_name,building_code,unit_no
	end

if @strMode = 'CLIENT_SEARCH'
	begin
		select *,m_real_property.real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		where (tenant_name like @name + '%')
		and upper(ltrim(rtrim(isnull(terminated,'N')))) <> 'Y'
		and (upper(ltrim(rtrim(isnull(tenant_type,'')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'')))) = 'C')
		order by tenant_name,m_real_property.real_property_name,building_code,unit_no
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 m_tenant.*,isnull(bill_to.tenant_name,'') as bill_to_name,m_real_property.real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where upper(ltrim(m_tenant.tenant_code)) like '%' + upper(ltrim(rtrim(@code))) + '%' or upper(ltrim(m_tenant.tenant_name)) like '%' + upper(ltrim(rtrim(@code))) + '%'
	end

if @strMode = 'RETRIEVE_FIND'
	begin
		select top 1 m_tenant.*,isnull(bill_to.tenant_name,'') as bill_to_name,m_real_property.real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where upper(ltrim(m_tenant.tenant_code)) like '%' + upper(ltrim(rtrim(@code))) + '%' --or upper(ltrim(m_tenant.tenant_code)) like '%' + upper(ltrim(rtrim(@code))) + '%'
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_invoice_header where upper(ltrim(client_code)) = upper(ltrim(@code))) and not exists (select * from t_invoice_detail where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
			and not exists (select * from t_tenant_reading where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
			begin
				
				if exists (select * from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
					begin
						delete from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@code))
						set @module_name = 'TENANT CHARGES'
						set @data = 'delete from m_tenant_charges where upper(ltrim(tenant_code)) = ' + upper(ltrim(@code))
						exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
						set @module_name = 'TENANT'
					end			
				
				if @tmp_tenant_type <> 'C'
					begin
						set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @tmp_name+ ',' + @tmp_tenant_type
						exec sp_m_Units_Movement @tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@uid						
					end

				delete from m_tenant where upper(ltrim(tenant_code)) = upper(ltrim(@code))
				
				set @data = 'delete from m_tenant where upper(ltrim(tenant_code)) =' + upper(ltrim(@code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Tenant_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_m_Tenant_List]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100)

AS

--FUNCTION ID = 1
--exec sp_m_Tenant_List '','m_tenant.tenant_remarks',''
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = 1 and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	set @ssql = ' select m_tenant.*,m_real_property.real_property_name, ' +
		' case ' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''OC'' then ''OCCUPANT & CLIENT''' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''O'' then ''OCCUPANT''' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''C'' then ''CLIENT''' +
			' else '''' ' +
		' end as tenant_type_desc,' +
		
		' case when ltrim(rtrim(m_tenant.tenant_type)) = ''O'' then bill_to.tenant_name else '''' end  
		
		+ case when isnull(m_tenant.tenant_remarks,'''') <> ''''
			then '' (REMARKS: '' + isnull(m_tenant.tenant_remarks,'''') + '')''
			end 
		
		as bill_to_name,

		' +
		
		' case when isnull(m_tenant.terminated,''N'') = ''N'' then ''NO'' else ''YES'' end as is_terminated ' +
		' from m_tenant ' +
		 'left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code' +
		' left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code ' 

		if ltrim(rtrim(@column_code)) <> '' 
			begin
				set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
			end

		if @strMode = 'VIEW_STAT'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (m_tenant.tenant_code not in (select tenant_code from m_tenant_charges) ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.terminated,'''')))) <> ''Y'' ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.tenant_type,''OC'')))) <> ''C'' ' +
					' ) '
			end

		if @strMode = 'TENANT_READING_LOOKUP'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (m_tenant.tenant_code  in (select tenant_code from m_tenant_charges left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code where isnull(charge_type,'''') = ''U'') ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.tenant_type,''OC'')))) <> ''C'' ' +
					' ) '
			end

		if @strMode = 'INVOICE_CLIENT_LOOKUP'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'''')))) = ''C'' or upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'''')))) = ''OC'')
							and m_tenant.tenant_code in (select bill_to from m_tenant where tenant_code in (select tenant_code from m_tenant_charges
							))'
			end

		if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by m_tenant.tenant_code'
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Tenant_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_m_Tenant_Retrieve]
	@strMode varchar(50),
	@code varchar(10),
	@name varchar(100)
	
AS

if @strMode = 'RETRIEVE'
	begin
		select top 1 m_tenant.*,isnull(bill_to.tenant_name,'') as bill_to_name,m_real_property.real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where  upper(ltrim(m_tenant.tenant_name)) like '%' + upper(ltrim(rtrim(@name))) + '%'
	end

if @strMode = 'RETRIEVE_FIND'
	begin
		select top 1 m_tenant.*,isnull(bill_to.tenant_name,'') as bill_to_name,m_real_property.real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where upper(ltrim(m_tenant.tenant_code)) =  upper(ltrim(rtrim(@code))) 
	end

GO
/****** Object:  StoredProcedure [dbo].[sp_m_Tenant_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_m_Tenant_Save]
	@strMode varchar(50),
	@code varchar(10),
	@name varchar(100),
	@real_property_code varchar(5),
	@building_code varchar(10),
	@unit_no varchar(10),
	@bill_to varchar(10),
	@contact_no1 varchar(20),
	@contact_no2 varchar(20),
	@address1 varchar(50),
	@address2 varchar(50),
	@contract_eff_date datetime,
	@contract_expiry_date datetime,
	@sap_code varchar(20),
	@terminated char(1),
	@date_terminated datetime,
	@actual_move_in_date datetime,
	@email_add varchar(256),
	@is_affiliate_employee char(1),
	@is_notifications char(1),
	@employer varchar(100),
	@tenant_type char(2),
	@is_sap_affiliate char(1),
	@new_code varchar(50),
	@business_area varchar(50),
	@last_meter_reading varchar(20),
	@security_deposit_amount decimal(9,2),
	@remarks varchar(500),
	@is_sap_employee_benefit char(1),
	@employee_benefit_cc varchar(50),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)
declare @next_code_settings int
declare @tmp_real_property_code varchar(5),@tmp_building_code varchar(10),@tmp_unit_no varchar(10),@tmp_tenant_type char(2),@event_remarks varchar(4000),@tmp_name varchar(100),
@tmp_terminated char(1),@tmp_actual_move_in_date datetime

set @module_name = 'TENANT'
set @next_code_settings  = 9
set @tenant_type = upper(ltrim(rtrim(isnull(@tenant_type,'OC'))))	
set @name = upper(ltrim(rtrim(@name)))

	if exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))) 
		begin
			select top 1 @tmp_real_property_code = real_property_code,@tmp_building_code = building_code,@tmp_unit_no = unit_no,@tmp_tenant_type = tenant_type,
				@tmp_name = tenant_name,@tmp_terminated = ltrim(rtrim(isnull(terminated,''))),@tmp_actual_move_in_date = isnull(actual_move_in_date,'1/1/1900')
			from m_tenant
			where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
		end

if @strMode = 'SAVE'
	begin
		if  upper(ltrim(rtrim(isnull(@code,'')))) = '' or not exists (select * from m_tenant where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))) 
			begin
				select @code = max(
								convert(decimal(18,0),right(ltrim(rtrim(tenant_code)),@next_code_settings))
							)
				from m_tenant 
		
				set @code = isnull(@code,0)
				
				set @code = @code + 1
				set @code = ltrim(rtrim(replace(space(@next_code_settings - len(@code)),' ','0') + convert(varchar(10),@code)))
				set @code = 'T'  + convert(varchar(9),@code)

				insert into m_tenant	
					(tenant_code,tenant_name,real_property_code,building_code,unit_no,bill_to,contact_no1,contact_no2,address1,address2,
					contract_eff_date,contract_expiry_date,sap_code,terminated,date_terminated,actual_move_in_date,email_add,is_affiliate_employee,is_notifications,
					employer,tenant_type,is_sap_affiliate,new_code,business_area,company_code,last_meter_reading,security_deposit_amount,tenant_remarks,is_employee_benefit,employee_benefit_cc,created_by)
				select upper(ltrim(rtrim(@code))),@name,@real_property_code,@building_code,@unit_no,
					case when @tenant_type = 'OC' or @tenant_type = 'C' then upper(@code) else @bill_to end,
					@contact_no1,@contact_no2,@address1,@address2,
					@contract_eff_date,@contract_expiry_date,@sap_code,@terminated,@date_terminated,@actual_move_in_date,@email_add,@is_affiliate_employee,@is_notifications,
					@employer,@tenant_type,@is_sap_affiliate,@new_code,@business_area,@company_code,@last_meter_reading,@security_deposit_amount,@remarks,@is_sap_employee_benefit,@employee_benefit_cc,@uid

				insert into m_tenant_charges					
				select upper(ltrim(rtrim(@code))),charge_code,charge_amount from m_unit_charges
				where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 

				set @data = 'insert into m_tenant ' +	
					' (tenant_code,tenant_name,real_property_code,building_code,unit_no,bill_to,contact_no1,contact_no2,address1,address2,' +
					' contract_eff_date,contract_expiry_date,sap_code,terminated,date_terminated,actual_move_in_date,email_add,is_affiliate_employee,is_notifications,' +
					' employer,tenant_type,is_sap_affiliate,new_code,business_area,company_code,last_meter_reading,security_deposit_amount,remarks,is_employee_benefit,employee_benefit_cc)' +
					' select ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @real_property_code+ ',' + @building_code+ ',' + @unit_no+ ',' + 
					'  case when ' + @tenant_type + '= ''OC'' or ' + @tenant_type + '= ''C'' then ' + upper(@code) + ' else ' + @bill_to  + 'end,' +
					@contact_no1+ ',' + @contact_no2+ ',' + @address1+ ',' + @address2+ ',' + 
					convert(varchar(10),@contract_eff_date,101)+ ',' +convert(varchar(10),@contract_expiry_date,101)+ ',' +@sap_code+ ',' +@terminated+ ',' +convert(varchar(10),@date_terminated,101)+ ',' +convert(varchar(10),@actual_move_in_date,101)+ ',' +@email_add+ ',' +
					@is_affiliate_employee+ ','+@is_notifications+',' +@employer+ ',' +@tenant_type+ ',' +@is_sap_affiliate+ ',' +@new_code+ ',' +@business_area+ ',' +@company_code+ ','+	
					@last_meter_reading+ ',' +convert(varchar(10),@security_deposit_amount)+ ',' +@remarks + ',' + @is_sap_employee_benefit + ',' + @employee_benefit_cc

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				set @module_name = 'TENANT CHARGES'
				
				set @data = 'insert into m_tenant_charges	 ' +				
					' select ' + upper(ltrim(rtrim(@code)))+',charge_code,charge_amount from m_unit_charges ' +
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + ' and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
					' and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no)) 

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				if @tenant_type <> 'C'
					begin
						if ltrim(rtrim(isnull(@terminated,''))) = 'Y'
							begin
								set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type + ', TERMINATED'
								exec sp_m_Units_Movement @date_terminated,
								@real_property_code,@building_code,@unit_no,'VACATED',@event_remarks,@code,0,@uid	
							end
						else
							begin
								if isnull(@actual_move_in_date,'1/1/1900') <> '1/1/1900'
									begin
										set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
										exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@code,0,@uid	
									end						
	end
					end
			end
		else
			begin
				update m_tenant set 
					tenant_name = @name,
					real_property_code = @real_property_code,
					building_code = @building_code,
					unit_no = @unit_no,
					bill_to = case when @tenant_type = 'OC' or @tenant_type = 'C' then upper(@code) else @bill_to end,
					contact_no1 = @contact_no1,
					contact_no2 = @contact_no2,
					address1 = @address1,
					address2 = @address2,
					contract_eff_date = @contract_eff_date,
					contract_expiry_date = @contract_expiry_date,
					sap_code = @sap_code,
					terminated = @terminated,
					date_terminated = @date_terminated,
					actual_move_in_date = @actual_move_in_date,
					email_add = @email_add,
					is_affiliate_employee = @is_affiliate_employee,
					is_notifications = @is_notifications,
					employer = @employer,
					tenant_type = @tenant_type,
					is_sap_affiliate = @is_sap_affiliate,
					new_code = @new_code,
					business_area = @business_area,
					company_code = @company_code,
					last_meter_reading = @last_meter_reading,
					security_deposit_amount = @security_deposit_amount,
					tenant_remarks = @remarks,
					is_employee_benefit = @is_sap_employee_benefit,
					employee_benefit_cc = @employee_benefit_cc,
					updated_by = @uid,
					date_updated = getdate()
				where upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@code)))
	
				if not exists(select * from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
					begin
						insert into m_tenant_charges
						select upper(ltrim(rtrim(@code))),charge_code,charge_amount from m_unit_charges
						where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
						and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 
					end

				set @data = 'update m_tenant set ' +
					' tenant_name = ' + @name + ',' +
					' real_property_code =' + @real_property_code+ ',' +
					' building_code =' + @building_code+ ',' +
					' unit_no =' + @unit_no+ ',' +
					' bill_to = case when ' + @tenant_type + '=''OC'' or ' + @tenant_type +'= ''C'' then ' + upper(@code) +' else '+ @bill_to + ' end,' +
					' contact_no1 =' + @contact_no1+ ',' +
					' contact_no2 =' + @contact_no2+ ',' +
					' address1 =' + @address1+ ',' +
					' address2 =' + @address2+ ',' +
					' contract_eff_date =' + convert(varchar(10),@contract_eff_date,101)+ ',' +
					' contract_expiry_date =' + convert(varchar(10),@contract_expiry_date,101) + ',' +
					' sap_code =' + @sap_code+ ',' +
					' terminated =' + @terminated+ ',' +
					' date_terminated =' + convert(varchar(10),@date_terminated,101) + ',' +
					' actual_move_in_date =' + convert(varchar(10),@actual_move_in_date,101) + ',' +
					' email_add =' + @email_add+ ',' +
					' is_affiliate_employee =' + @is_affiliate_employee+ ',' +
					' is_notifications =' + @is_notifications + ',' +
					' employer =' + @employer+ ',' +
					' tenant_type =' + @tenant_type+ ',' +
					' is_sap_affiliate =' + @is_sap_affiliate+ ',' +
					' new_code =' + @new_code+ ',' +
					' business_area = ' + @business_area+ ',' +
					' company_code =' + @company_code + ',' +
					' last_meter_reading = ' + @last_meter_reading+ ',' +
					' security_deposit_amount = ' +  convert(varchar(10),@security_deposit_amount)+ ',' +
					' remarks = ' + @remarks + 
					' is_employee_benefit = ' + @is_sap_employee_benefit +
					' employee_benefit_cc = ' + @employee_benefit_cc +
					' where upper(ltrim(rtrim(tenant_code))) =' + upper(ltrim(rtrim(@code)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

				set @module_name = 'TENANT CHARGES'

				set @data = 'insert into m_tenant_charges	 ' +				
					' select ' + upper(ltrim(rtrim(@code)))+',charge_code,charge_amount from m_unit_charges ' +
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + ' and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
					' and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no)) 

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				if ltrim(rtrim(isnull(@terminated,''))) <> 'Y'
					begin
						if upper(ltrim(rtrim(@tmp_real_property_code))) <> upper(ltrim(rtrim(@real_property_code))) or 
							upper(ltrim(rtrim(@tmp_building_code))) <> upper(ltrim(rtrim(@building_code))) or 
							upper(ltrim(rtrim(@tmp_unit_no))) <> upper(ltrim(rtrim(@unit_no)))
							begin
								if @tmp_tenant_type <> @tenant_type and @tenant_type <>'C'
									begin
										set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
										exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@code,0,@uid
									end
								else if @tmp_tenant_type <> @tenant_type and @tenant_type = 'C'
									begin
										set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type + '. TENANT BECAME A CLIENT.'
										exec sp_m_Units_Movement null,@tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@code,0,@uid
									end
								else if @tmp_tenant_type = @tenant_type and @tenant_type <>'C'
									begin
										set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type
										exec sp_m_Units_Movement null,@tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@code,0,@uid
						
										set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
										exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@code,0,@uid
									end
							end
						else if @tmp_tenant_type <> @tenant_type and @tenant_type = 'C'
							begin
								set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tmp_tenant_type + '. TENANT BECAME A CLIENT.'
								exec sp_m_Units_Movement null,@tmp_real_property_code,@tmp_building_code,@tmp_unit_no,'VACATED',@event_remarks,@code,0,@uid
							end
						else
							begin
								set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
								exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'OCCUPIED',@event_remarks,@code,0,@uid
							end
					end
				else if  ltrim(rtrim(isnull(@terminated,''))) = 'Y'
					begin
						set @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type + ', TERMINATED'
						exec sp_m_Units_Movement @date_terminated,
						@real_property_code,@building_code,@unit_no,'VACATED',@event_remarks,@code,0,@uid	
					end

				if isnull(@tmp_actual_move_in_date,'1/1/1900') <> isnull(@actual_move_in_date,'1/1/1900')
					begin
						set @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(@code))) + ',' + @name+ ',' + @tenant_type
						exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'MOVE IN DATE',@event_remarks,@code,0,@uid
					end
			end
	end
	select @code as tenant_code
GO
/****** Object:  StoredProcedure [dbo].[sp_m_TenantCharges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_m_TenantCharges]
	@strMode varchar(50),
	@tenant_code varchar(10),
	@charge_code varchar(5),
	@charge_amount decimal(18,6),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_amount decimal(18,6),@tmp_tenant_code char(10)
declare @data nvarchar(4000),@module_name varchar(50),@event_remarks varchar(4000)
declare @is_rental_hist char(1), @is_rental char(1)

set @module_name = 'TENANT CHARGES'

declare @apt_rental char(10),@off_rental char(10),@whs_rental char(10)
				
select top 1 @apt_rental = apt_rental_charge,@off_rental=off_rental_charge,@whs_rental=whs_rental_charge from s_settings

set @is_rental_hist = ''
set @is_rental = ''

if @strMode = 'SAVE'
	begin
		if exists (select * from m_tenant_charges where tenant_code = @tenant_code 
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			set @is_rental_hist = 'Y'	

		if not exists (select * from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code)))
			begin
				insert into m_tenant_charges	
				select upper(@tenant_code),upper(@charge_code),@charge_amount

				set @data = 'insert into m_tenant_charges	 ' +
					'select ' + upper(@tenant_code) + ',' + upper(@charge_code)+ ',' + convert(varchar(20),@charge_amount)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
	
			end
		else
			begin
				update m_tenant_charges set 
					charge_amount = @charge_amount				
				where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code))

				set @data = 'update m_tenant_charges set ' +
					' charge_amount = ' + convert(varchar(20),@charge_amount) +				
					' where upper(ltrim(tenant_code)) = ' + upper(ltrim(@tenant_code)) + ' and upper(ltrim(charge_code)) = ' + upper(ltrim(@charge_code))
--print @data
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

			end

		if exists (select * from m_tenant_charges where tenant_code = @tenant_code 
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			set @is_rental = 'Y'	

		if @is_rental_hist = '' and @is_rental = 'Y'
			begin
				select top 1 @event_remarks = 'OCCUPIED BY ' + upper(ltrim(rtrim(tenant_code))) + ',' + tenant_name+ ',' + tenant_type from m_tenant where tenant_code = @tenant_code
				exec sp_m_Units_Movement null,'','','','OCCUPIED',@event_remarks,@tenant_code,0,@uid	
			end
		else if @is_rental_hist = 'Y' and @is_rental = ''
			begin
				select top 1 @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(tenant_code))) + ',' + tenant_name+ ',' + tenant_type from m_tenant where tenant_code = @tenant_code
				exec sp_m_Units_Movement null,'','','','VACATED',@event_remarks,@tenant_code,@charge_amount,@uid	
			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_tenant_charges where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code)))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select * from m_tenant_charges 
		left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
		order by charge_desc
	end


if @strMode = 'VIEW_TENANT'
	begin
		select m_tenant.*,m_real_property.real_property_name,
		case 
			when upper(ltrim(rtrim(m_tenant.tenant_type))) = 'OC' then 'OCCUPANT & CLIENT' 
			when upper(ltrim(rtrim(m_tenant.tenant_type))) = 'O' then 'OCCUPANT' 
			when upper(ltrim(rtrim(m_tenant.tenant_type))) = 'C' then 'CLIENT' 
			else ''
		end as tenant_type_desc,
		case when ltrim(rtrim(m_tenant.tenant_type)) = 'O' then bill_to.tenant_name else '' end as bill_to
		from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
		left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
		where (m_tenant.tenant_code like '%' + @uid + '%' or m_tenant.tenant_name like '%' + @uid + '%')
		and (upper(ltrim(rtrim(m_tenant.tenant_type))) = 'OC' or upper(ltrim(rtrim(m_tenant.tenant_type))) = 'O')
		order by m_tenant.tenant_name
	end

if @strMode = 'RETRIEVE_TENANT'
	begin
		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))) from m_tenant 
		where upper(ltrim(tenant_code)) like '%' +  upper(ltrim(@tenant_code)) + '%' or  upper(ltrim(tenant_name)) like '%' + upper(ltrim(@tenant_code)) + '%' 

		select @total_amount = sum(isnull(charge_amount,0)) from m_tenant_charges where upper(ltrim(rtrim(tenant_code))) = @tmp_tenant_code

		select top 1*,isnull(@total_amount,0) as total_amount from m_tenant 
		where upper(ltrim(rtrim(tenant_code))) = @tmp_tenant_code
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))) from m_tenant 
		where upper(ltrim(tenant_code)) like '%' +  upper(ltrim(@tenant_code)) + '%' 

		select m_tenant_charges.*,charge_desc,gl_code,tenant_name from m_tenant_charges 
		left join m_tenant on m_tenant_charges.tenant_code = m_tenant.tenant_code
		left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
		where upper(ltrim(rtrim(m_tenant_charges.tenant_code))) = @tmp_tenant_code
		order by tenant_name,charge_desc
	end

if @strMode = 'DELETE'		
	begin
		if exists (select * from m_tenant_charges where tenant_code = @tenant_code 
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			begin
				set @is_rental_hist = 'Y'					
			end
		set @charge_amount = 0
		if @is_rental_hist = 'Y'		
			begin
				if @charge_code = @apt_rental or @charge_code = @off_rental or @charge_code = @whs_rental
					select top 1 @charge_amount = isnull(charge_amount,0) from m_tenant_charges
					where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code))
			end

		delete from m_tenant_charges 
		where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code))
		set @data = 'delete from m_tenant_charges ' +
			' where upper(ltrim(tenant_code)) = ' + upper(ltrim(@tenant_code)) + 'and upper(ltrim(charge_code)) = ' +upper(ltrim(@charge_code))
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

		if exists (select * from m_tenant_charges where tenant_code = @tenant_code 
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			set @is_rental = 'Y'	

		if @is_rental_hist = 'Y' and @is_rental = ''
			begin
				select top 1 @event_remarks = 'VACATED BY ' + upper(ltrim(rtrim(tenant_code))) + ',' + tenant_name+ ',' + tenant_type from m_tenant where tenant_code = @tenant_code
				exec sp_m_Units_Movement null,'','','','VACATED',@event_remarks,@tenant_code,@charge_amount,@uid	
			end

		select 0 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_UnitCharges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_m_UnitCharges]
	@strMode varchar(50),
	@real_property_code varchar(10),
	@building_code varchar(10),
	@unit_no varchar(10),
	@charge_code varchar(5),
	@charge_amount decimal(18,6),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)
declare @is_rental_hist char(1), @is_rental char(1),@event_remarks varchar(4000)

set @module_name = 'UNIT CHARGES'

declare @apt_rental char(10),@off_rental char(10),@whs_rental char(10)
				
select top 1 @apt_rental = apt_rental_charge,@off_rental=off_rental_charge,@whs_rental=whs_rental_charge from s_settings

set @is_rental_hist = ''
set @is_rental = ''

if exists (select * from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
	and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))
	and charge_code in (@apt_rental,@off_rental,@whs_rental))	
		set @is_rental_hist = 'Y'	

if @strMode = 'SAVE'
	begin
		if not exists (select * from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code)))
			begin
				insert into m_unit_charges	
				select upper(@real_property_code),upper(@building_code),upper(@unit_no), upper(@charge_code),@charge_amount

				set @data = 'insert into m_unit_charges ' +	
					' select ' + upper(@real_property_code) + ',' + upper(@building_code)+ ',' +upper(@unit_no)+ ',' + upper(@charge_code)+ ',' +convert(varchar(20),@charge_amount)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update m_unit_charges set 
					charge_amount = @charge_amount				
				where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code))
	
				set @data = 'update m_unit_charges set ' +
					' charge_amount = ' +convert(varchar(20),@charge_amount) +
					' where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + ' and upper(ltrim(building_code)) =' + upper(ltrim(@building_code)) +
					' and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no)) + ' and upper(ltrim(charge_code)) = ' + upper(ltrim(@charge_code))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end

		if exists (select * from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
		and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			set @is_rental = 'Y'	

		if @is_rental_hist = '' and @is_rental = 'Y'
			begin
				exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'CREATED','','',0,@uid	
			end
		else if @is_rental_hist = 'Y' and @is_rental = ''
			begin
				exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'DELETED','','',0,@uid	
			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code)))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select * from m_unit_charges 
		left join m_charges on m_unit_charges.charge_code = m_charges.charge_code
		order by charge_desc
	end

if @strMode = 'RETRIEVE_REAL_PROPERTY'
	begin
		select top 1 m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (upper(ltrim(m_units.real_property_code)) like upper(ltrim(@real_property_code)) + '%' 
			or upper(ltrim(m_real_property.real_property_name)) like upper(ltrim(@real_property_code)) + '%')
			and upper(ltrim(building_code)) like upper(ltrim(@building_code)) + '%' 
			and upper(ltrim(unit_no)) like upper(ltrim(@unit_no)) + '%' 
	end

if @strMode = 'FIND_REAL_PROPERTY'
	begin
		select top 1 m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where upper(ltrim(m_units.real_property_code)) = upper(ltrim(@real_property_code))
			and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 
	end

if @strMode = 'RETRIEVE'
	begin
		select m_unit_charges.*,charge_desc,gl_code from m_unit_charges 
		left join m_charges on m_unit_charges.charge_code = m_charges.charge_code
		where upper(ltrim(m_unit_charges.real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 
		order by charge_desc
	end

if @strMode = 'RETRIEVE_FIND'
	begin
		select m_unit_charges.*,charge_desc,gl_code from m_unit_charges 
		left join m_charges on m_unit_charges.charge_code = m_charges.charge_code
		where upper(ltrim(m_unit_charges.real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) 
		order by charge_desc
	end

if @strMode = 'DELETE'		
	begin
		delete from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
		and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code))

		set @data = 'delete from m_unit_charges where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + 'and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
			' and upper(ltrim(unit_no)) =  ' + upper(ltrim(@unit_no)) + ' and upper(ltrim(charge_code)) = ' + upper(ltrim(@charge_code))

		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

		if exists (select * from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
		and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))
		and charge_code in (@apt_rental,@off_rental,@whs_rental))	
			set @is_rental = 'Y'	

		if @is_rental_hist = 'Y' and @is_rental = ''
			begin
				exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'DELETED','','',0,@uid	
			end
		
		select 0 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Units]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_m_Units]
	@strMode varchar(20),
	@real_property_code varchar(10),
	@building_code varchar(10),
	@unit_no varchar(10),
	@lot_area varchar(20),
	@no_of_bedrooms int,
	@unit_type varchar(20),
	@remarks varchar(50),
	@is_reserved char(1),
	@uid varchar(100),
	@company_code char(5),
	@ip_addr varchar(20),
	@is_complimentary tinyint=0
AS

/*
FIXES:	
		2024-10-18	Aldrich
		- Added new parameter @is_complimentary
*/

declare @data nvarchar(4000),@module_name varchar(50)
declare @tmp_is_reserved char(1),@event_action varchar(50),@event_remarks varchar(4000)

set @module_name = 'UNITS'
set @event_remarks = ''

if @strMode = 'SAVE'
	begin
		if not exists (select * from m_units where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)))
			begin
				insert into m_units (real_property_code,building_code,unit_no,lot_area,no_of_bedrooms,unit_type,is_reserved,remarks, is_complimentary)
				select upper(@real_property_code),upper(@building_code),upper(@unit_no),@lot_area,@no_of_bedrooms,@unit_type,@is_reserved,@remarks, @is_complimentary
	
				set @data = 'insert into m_units (real_property_code,building_code,unit_no,lot_area,no_of_bedrooms,unit_type,is_reserved,remarks) ' +
				'select ' + upper(@real_property_code) + ',' + upper(@building_code)+ ',' +upper(@unit_no)+ ',' +@lot_area+ ',' +convert(varchar(20),@no_of_bedrooms)+ ',' +
				@unit_type+ ',' +@is_reserved+ ',' +@remarks
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				if isnull(@is_reserved,'') = 'Y'
					begin
						exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'CREATED',@event_remarks,'',0,@uid
						exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'RESERVED_Y',@event_remarks,'',0,@uid
					end
			end
		else
			begin
				select top 1 @tmp_is_reserved = isnull(is_reserved,'N') from m_units
				where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code))  
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))

				if not exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
					and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and event_action = 'CREATED') 
					exec sp_m_Units_Movement @real_property_code,@building_code,@unit_no,'','','',@uid

				update m_units set 
					lot_area = @lot_area,
					no_of_bedrooms = @no_of_bedrooms,
					unit_type = @unit_type,
					is_reserved = @is_reserved,
					is_complimentary = @is_complimentary,
					remarks = @remarks				
				where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code))  
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))

				set @data = 'update m_units set ' +
					' lot_area = ' + @lot_area+ ',' +
					' no_of_bedrooms = ' +convert(varchar(20),@no_of_bedrooms)+ ',' +
					' unit_type = ' +@unit_type+ ',' +
					' is_reserved = ' +@is_reserved+ ',' +
					' is_complimentary = ' +@is_complimentary+ ',' +
					' remarks = ' +@remarks	+			
					'where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + 'and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code))  +
					'and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

				if isnull(@is_reserved,'N') <> isnull(@tmp_is_reserved,'N')
				begin	
					set @event_action = 'RESERVED_' + isnull(@is_reserved,'N')
					exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,@event_action,@event_remarks,'',0,@uid
				end
			end
	end

if @strMode = 'FIND'
	begin
		if exists (select * from m_units where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select * from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where real_property_name like '%' + @remarks + '%' or building_code like '%' + @remarks + '%' or unit_no like '%' + @remarks + '%'
		order by real_property_name,building_code,unit_no
	end

if @strMode = 'VIEW_STAT'
	begin
		select * from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @remarks + '%' or building_code like '%' + @remarks + '%' or unit_no like '%' + @remarks + '%')
		and (upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
		not in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))  from m_unit_charges)
		and upper(ltrim(rtrim(isnull(is_reserved,'')))) <> 'Y')
		order by real_property_name,building_code,unit_no
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where upper(ltrim(m_units.real_property_code)) like upper(ltrim(@real_property_code)) + '%' and upper(ltrim(building_code)) like upper(ltrim(@building_code)) + '%' 
			and upper(ltrim(unit_no)) like upper(ltrim(@unit_no)) + '%' 
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(tenant_type))) <> 'C')
			begin
				delete from m_unit_charges where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))

				delete from m_units where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))
				
				set @data = 'delete from m_units where upper(ltrim(real_property_code)) = ' + upper(ltrim(@real_property_code)) + 'and upper(ltrim(building_code)) = ' + upper(ltrim(@building_code)) +
				'and upper(ltrim(unit_no)) = ' + upper(ltrim(@unit_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				exec sp_m_Units_Movement null,@real_property_code,@building_code,@unit_no,'DELETED',@event_remarks,'',0,@uid

				select 0 as x
			end
		else
			begin
				select 1 as x
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_m_Units_Movement]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_m_Units_Movement]
	@event_date datetime,
	@real_property_code char(5),
	@building_code char(10),
	@unit_no char(10),
	@event_action varchar(100),
	@event_remarks varchar(4000),
	@tenant_code char(10),
	@charge_amount decimal(18,6),
	@uid varchar(50)
AS
/*
RESERVED_Y = TAGGED AS RESERVED
RESERVED_N = TAGGED AS NOT RESERVED
OCCUPIED 
VACATED
DELETED
CREATED
*/

if isnull(@event_date,'1/1/1900') = '1/1/1900'
	set @event_date = getdate()

declare @apt_rental char(10),@off_rental char(10),@whs_rental char(10)
				
select top 1 @apt_rental = apt_rental_charge,@off_rental=off_rental_charge,@whs_rental=whs_rental_charge from s_settings

	if ltrim(rtrim(@tenant_code + '')) <> '' 
		begin
			if ltrim(rtrim(@real_property_code)) = ''
				select top 1 @real_property_code = real_property_code ,@building_code = building_code,@unit_no = unit_no
				from m_tenant where tenant_code = @tenant_code
	
			if ltrim(rtrim(@event_action)) = 'OCCUPIED'
				begin
					if not exists (select charge_code from m_tenant_charges where tenant_code = @tenant_code 
						and charge_code in (@apt_rental,@off_rental,@whs_rental)) or
					exists (select * from m_tenant  where tenant_code = @tenant_code and isnull(actual_move_in_date,'1/1/1900') ='1/1/1900'  )	
						set @event_action = ''		
					else
						begin
							select @charge_amount =sum(isnull(charge_amount,0) ) from m_tenant_charges 
							where tenant_code = @tenant_code 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
						end
				end

			if  ltrim(rtrim(@event_action)) = 'MOVE IN DATE'
				begin
					update m_units_movement set event_date = (select top 1 actual_move_in_date from m_tenant where tenant_code = @tenant_code),date_executed = getdate()
					where event_log_id in (select top 1 event_log_id from m_units_movement where event_action like '%OCCUPIED%' and tenant_code = @tenant_code)

					set @event_action = ''	
				end
			
			if ltrim(rtrim(@event_action)) = 'OCCUPIED'
				begin
					if exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
					and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'OCCUPIED' and tenant_code = @tenant_code and event_date = (select top 1 actual_move_in_date from m_tenant where tenant_code = @tenant_code))
						set @event_action = ''	

					else if exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
					and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'OCCUPIED' and tenant_code = @tenant_code and event_date <> (select top 1 actual_move_in_date from m_tenant where tenant_code = @tenant_code))
						begin
							update m_units_movement 
							set event_date = (select top 1 actual_move_in_date from m_tenant where tenant_code = @tenant_code),date_executed = getdate()
							where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
							and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'OCCUPIED' and tenant_code = @tenant_code 

							set @event_action = ''
						end

					if exists (select * from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
						and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' and tenant_code = @tenant_code and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900')
						begin
							if exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
							and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'VACATED' and tenant_code = @tenant_code and right(event_remarks,10) = 'TERMINATED')
								begin
									delete from m_units_movement 
									where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
									and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'VACATED' and tenant_code = @tenant_code and right(event_remarks,10) = 'TERMINATED'
								end
						end
				end
			

			if ltrim(rtrim(@event_action)) = 'VACATED' and isnull(@charge_amount,0) = 0
				begin
					if exists (select charge_code from m_tenant_charges where tenant_code = @tenant_code 
						and charge_code in (@apt_rental,@off_rental,@whs_rental))							
						begin
							select @charge_amount =sum(isnull(charge_amount,0) ) from m_tenant_charges 
							where tenant_code = @tenant_code 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
						end
				end
		end

	if not exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))) 
			and ltrim(rtrim(@event_action)) <> 'CREATED'
			and exists ( 
				select * from m_unit_charges  where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) 
				and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no))
				and charge_code in (@apt_rental,@off_rental,@whs_rental)
			)
		begin
			insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by)
			select @event_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),'CREATED','',@uid
	
			if exists (select * from m_units where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and isnull(is_reserved,'N') = 'Y') and  ltrim(rtrim(@event_action)) <> 'RESERVED_Y'
				insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by)
				select @event_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),'RESERVED_Y','',@uid
		end	

	if ltrim(rtrim(@event_action)) = 'VACATED'
		begin
			/*if exists (select * from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' )*/
			if (select count(*) from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900' ) > 1
				set @event_action = 'VACATED BY ONE OF THE TENANTS'

			if not exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'OCCUPIED')

				insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by,tenant_code,charge_amount)
				select top 1 actual_move_in_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),'OCCUPIED','OCCUPIED BY: ' + right(@event_remarks,(len(@event_remarks)-11)),@uid,@tenant_code,@charge_amount
				from m_tenant where tenant_code = @tenant_code
				--select getdate(),upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),'OCCUPIED','OCCUPIED BY: ' +  right(@event_remarks,7),@uid
		end

	if ltrim(rtrim(@event_action)) = 'VACATED'
		begin
			if exists (select * from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) = 'Y' and tenant_code = @tenant_code and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900')
				begin
					if exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
					and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'VACATED' and tenant_code = @tenant_code and right(event_remarks,10) = 'TERMINATED')
						begin
							update m_units_movement 
							set event_date = @event_date,date_executed = getdate()
							where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
							and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'VACATED' and tenant_code = @tenant_code and right(event_remarks,10) = 'TERMINATED'

							set @event_action = ''
						end
				end
				
		end

	/*if ltrim(rtrim(@event_action)) = 'VACATED'
		begin
			if exists (select * from m_units_movement where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
			and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and ltrim(rtrim(event_action)) = 'VACATED' and tenant_code = @tenant_code)
				set @event_action = ''
		end*/

	if ltrim(rtrim(@event_action)) = 'OCCUPIED'
		begin
			if (select count(*) from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
				and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900' ) > 1
				set @event_action = 'OCCUPIED BY ANOTHER TENANT'
		end

	if ltrim(rtrim(@event_action)) = 'OCCUPIED'
		begin
			insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by,tenant_code,charge_amount)
			select top 1 actual_move_in_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),@event_action,@event_remarks,@uid,@tenant_code,@charge_amount
			from m_tenant where tenant_code = @tenant_code and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900'

			set @event_action = ''
		end
	
	if ltrim(rtrim(@event_action)) <> ''
		begin
			if @event_action = 'OCCUPIED BY ANOTHER TENANT'
				begin
					if not exists (select tenant_code from m_units_movement where event_action = 'OCCUPIED' 
						and upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
						and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and tenant_code <> @tenant_code
						and tenant_code in (
						select tenant_code from m_tenant where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
						and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900' ) 
						and tenant_code <> @tenant_code
						)
						begin
							insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by,tenant_code,charge_amount)
							select top 1 actual_move_in_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),'OCCUPIED','OCCUPIED BY ' + upper(ltrim(rtrim(tenant_code))) + ',' + tenant_name+ ',' + tenant_type,
							@uid,tenant_code,@charge_amount
							from m_tenant
							where upper(ltrim(real_property_code)) = upper(ltrim(@real_property_code)) and upper(ltrim(building_code)) = upper(ltrim(@building_code)) 
							and upper(ltrim(unit_no)) = upper(ltrim(@unit_no)) and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y' and isnull(actual_move_in_date,'1/1/1900') <>'1/1/1900'
							and tenant_code <> @tenant_code 
							order by actual_move_in_date
						end
				end

			insert into m_units_movement (event_date,real_property_code,building_code,unit_no,event_action,event_remarks,executed_by,tenant_code,charge_amount)
			select @event_date,upper(ltrim(@real_property_code)),upper(ltrim(@building_code)) , upper(ltrim(@unit_no)),@event_action,@event_remarks,@uid,@tenant_code,@charge_amount
		end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Charges]
AS

declare @company_code char(5)

	set @company_code = 'THC'

	select m_charges.*,
	case when upper(ltrim(rtrim(charge_type))) = 'U' then 'BASED ON USAGE'
	else 'FIXED RATE'
	end as charge_type_desc,
	s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2
	from m_charges
	left join s_company on s_company.company_code = @company_code
	order by charge_desc
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_Invoice]
	@invoice_no varchar(20),
	@invoice_date_from varchar(10),
	@invoice_date_to varchar(10),
	@uid varchar(20),
	@date_generated varchar(50)
AS

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @invoice_date_from = upper(ltrim(rtrim(isnull(@invoice_date_from,''))))
set @invoice_date_to = upper(ltrim(rtrim(isnull(@invoice_date_to,''))))

if @invoice_date_from = '' and @invoice_date_to = ''
	begin
		select * from z_tmp_invoice
		where generated_by = @uid and date_generated = @date_generated
		order by invoice_no,tenant_name,upper(charge_desc)
	end

else if @invoice_date_from <> '' and @invoice_date_to <> ''
	begin
		select * from z_tmp_invoice
		where generated_by = @uid and date_generated = @date_generated
		and invoice_date >=convert(datetime,@invoice_date_from) and invoice_date <=convert(datetime,@invoice_date_to)
		order by invoice_no,tenant_name,upper(charge_desc)
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Invoice_Alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_rpt_Invoice_Alert]
	@invoice_no varchar(20),
	@invoice_date_from varchar(10),
	@invoice_date_to varchar(10)
AS

/*
FIXES:

	2024-06-06	Aldrich
	- Replaced Prepared By from ARRIANE ANTONIO to AARON VELASCO
*/

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @invoice_date_from = upper(ltrim(rtrim(isnull(@invoice_date_from,''))))
set @invoice_date_to = upper(ltrim(rtrim(isnull(@invoice_date_to,''))))
--print  @invoice_date_from
if @invoice_date_from = '' and @invoice_date_to = ''
	begin
		select -- t_invoice_header.*, 
		invoice_no = case when isnull(status,'') = 'V' then t_invoice_header.invoice_no + ' (VOID)' else  t_invoice_header.invoice_no end,
		t_invoice_header.invoice_no_type,
		t_invoice_header.invoice_date,
		t_invoice_header.client_code,
		t_invoice_header.real_property_code,
		t_invoice_header.billing_from,
		t_invoice_header.billing_to,
		t_invoice_header.document_no,
		t_invoice_header.sap_code,
		t_invoice_header.remarks,
		t_invoice_header.status,
		t_invoice_header.created_by,
		t_invoice_header.date_created,
		t_invoice_header.updated_by,
		t_invoice_header.date_updated,
		t_invoice_detail.invoice_detail_id,t_invoice_detail.charge_code, 
		t_invoice_detail.charge_amount,t_invoice_detail.total_charge_amount, 
		t_tenant_reading.date_from,t_tenant_reading.date_to,prev_reading,current_reading, 
		isnull(case when ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_from,101))) = '' and ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_to,101))) = '' then '' else
			'(' + convert(varchar(10),t_tenant_reading.date_from,101) + '-' + convert(varchar(10),t_tenant_reading.date_to,101) + ') '
		end
		+ 
		case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then 
			'Previous Reading: ' + convert(varchar(10),prev_reading) + '; Current Reading: ' + convert(varchar(10),current_reading) + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';'
		else ''
			end,'') as detail_remarks,
		upper(client.tenant_name) as client_name,
		m_tenant.tenant_code,	
		upper(m_tenant.tenant_name + ' (' +  ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)) + ')' 
			)   as tenant_name,
		upper(client.address1) as tenant_address1,upper(client.address2) as tenant_address2,
		case when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  '' then '' 
		when ltrim(rtrim(isnull(m_units.unit_no,''))) <> '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  ''  then upper(ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))))
		when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) <>  ''  then upper(ltrim(rtrim(isnull(m_units.lot_area,''))))
		else  upper( ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))) + ' / ' + ltrim(rtrim(isnull(m_units.lot_area,'')))) end as unit_area,
		upper(m_charges.charge_desc) as charge_desc,
		m_real_property.real_property_name,upper(m_real_property.real_property_company_name) as real_property_company_name,
		'dba ' + m_real_property.real_property_dba_name as dba_name,
		s_company.address1 + ' ' + s_company.address2 as company_address,
		isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,'') +
		isnull(' FAX: ' + s_company.fax_no,'') as company_contact_no,
		--upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,
		--case when t_invoice_header.real_property_code <> 'WT' then 'NORMA BATU' else 'NIMFA TAN' end AS prepared_by,
		--'NORMA BATU' AS prepared_by,
		--'RUBY PANOY' AS prepared_by,
		'AARON VELASCO' AS prepared_by,
		--'ARRIANE ANTONIO' AS prepared_by,
		upper(s_settings.approved_by) as approved_by,
		case when t_invoice_header.real_property_code <> 'WT' then 'L&T GROUP OF COMPANIES, LTD.' else 'W&T INTERNATIONAL CORPORATION' end AS check_payable	
		from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no 
		left join
		(
		select t_invoice_detail_reading.invoice_no,t_invoice_detail_reading.invoice_detail_id,tenant_code,t_tenant_reading_charges.trc_charge_code as charge_code,
		t_tenant_reading.reading_id,t_tenant_reading.date_from,t_tenant_reading.date_to,
		t_tenant_reading.prev_reading,t_tenant_reading.current_reading
		from t_invoice_detail_reading 
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id 
		left join t_tenant_reading_charges on t_tenant_reading.reading_id = t_tenant_reading_charges.trc_reading_id 
		)t_tenant_reading on t_invoice_detail.invoice_no = t_tenant_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_tenant_reading.invoice_detail_id
			and t_invoice_detail.tenant_code = t_tenant_reading.tenant_code
			and t_invoice_detail.charge_code = t_tenant_reading.charge_code
		left join m_tenant client on t_invoice_header.client_code = client.tenant_code 
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code 
		left join m_units on client.real_property_code = m_units.real_property_code and client.building_code = m_units.building_code and client.unit_no = m_units.unit_no 
		left join m_units tenant_unit on m_tenant.real_property_code = tenant_unit.real_property_code 
			and m_tenant.building_code = tenant_unit.building_code and m_tenant.unit_no = tenant_unit.unit_no 
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code 
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code 
		left join s_company on m_real_property.company_code = s_company.company_code 
		left join s_settings on s_company.company_code = s_settings.company_code 
		left join s_users on t_invoice_header.updated_by = s_users.[user_id]
		where t_invoice_header.invoice_no like '%' + @invoice_no + '%' and isnull(t_invoice_detail.total_charge_amount,0) <> 0
		order by t_invoice_header.invoice_no,m_tenant.tenant_name,upper(m_charges.charge_desc)
	end

else if @invoice_date_from <> '' and @invoice_date_to <> ''
	begin
		select -- t_invoice_header.*, 
		invoice_no = case when isnull(status,'') = 'V' then t_invoice_header.invoice_no + ' (VOID)' else  t_invoice_header.invoice_no end,
		t_invoice_header.invoice_no_type,
		t_invoice_header.invoice_date,
		t_invoice_header.client_code,
		t_invoice_header.real_property_code,
		t_invoice_header.billing_from,
		t_invoice_header.billing_to,
		t_invoice_header.document_no,
		t_invoice_header.sap_code,
		t_invoice_header.remarks,
		t_invoice_header.status,
		t_invoice_header.created_by,
		t_invoice_header.date_created,
		t_invoice_header.updated_by,
		t_invoice_header.date_updated,
		t_invoice_detail.invoice_detail_id,t_invoice_detail.charge_code, 
		t_invoice_detail.charge_amount,t_invoice_detail.total_charge_amount, 
		t_tenant_reading.date_from,t_tenant_reading.date_to,prev_reading,current_reading, 
		isnull(case when ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_from,101))) = '' and ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_to,101))) = '' then '' else
			'(' + convert(varchar(10),t_tenant_reading.date_from,101) + '-' + convert(varchar(10),t_tenant_reading.date_to,101) + ') '
		end
		+ 
		case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then 
			'Previous Reading: ' + convert(varchar(10),prev_reading) + '; Current Reading: ' + convert(varchar(10),current_reading) + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';'
		else ''
			end,'') as detail_remarks,
		upper(client.tenant_name) as client_name,
		m_tenant.tenant_code,	
		upper(m_tenant.tenant_name + ' (' +  ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no))  + ')'
			)   as tenant_name,
		upper(client.address1) as tenant_address1,upper(client.address2) as tenant_address2,
		case when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  '' then '' 
		when ltrim(rtrim(isnull(m_units.unit_no,''))) <> '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  ''  then upper(ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))))
		when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) <>  ''  then upper(ltrim(rtrim(isnull(m_units.lot_area,''))))
		else  upper( ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))) + ' / ' + ltrim(rtrim(isnull(m_units.lot_area,'')))) end as unit_area,
		upper(m_charges.charge_desc) as charge_desc,
		m_real_property.real_property_name,upper(m_real_property.real_property_company_name) as real_property_company_name,
		'dba ' + m_real_property.real_property_dba_name as dba_name,
		s_company.address1 + ' ' + s_company.address2 as company_address,
		isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,'') +
		isnull(' FAX: ' + s_company.fax_no,'') as company_contact_no,
		--upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,
		--case when t_invoice_header.real_property_code <> 'WT' then 'NORMA BATU' else 'NIMFA TAN' end AS prepared_by,
		--'NORMA BATU' AS prepared_by,
		--'RUBY PANOY' AS prepared_by,
		--'AARON VELASCO' AS prepared_by,
		'ARRIANE ANTONIO' AS prepared_by,
		upper(s_settings.approved_by) as approved_by,
		case when t_invoice_header.real_property_code <> 'WT' then 'L&T GROUP OF COMPANIES, LTD.' else 'W&T INTERNATIONAL CORPORATION' end AS check_payable	
		from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no 
		left join
		(
		select t_invoice_detail_reading.invoice_no,t_invoice_detail_reading.invoice_detail_id,tenant_code,t_tenant_reading_charges.trc_charge_code as charge_code,
		t_tenant_reading.reading_id,t_tenant_reading.date_from,t_tenant_reading.date_to,
		t_tenant_reading.prev_reading,t_tenant_reading.current_reading
		from t_invoice_detail_reading 
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id 
		left join t_tenant_reading_charges on t_tenant_reading.reading_id = t_tenant_reading_charges.trc_reading_id 
		)t_tenant_reading on t_invoice_detail.invoice_no = t_tenant_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_tenant_reading.invoice_detail_id
			and t_invoice_detail.tenant_code = t_tenant_reading.tenant_code
			and t_invoice_detail.charge_code = t_tenant_reading.charge_code
		left join m_tenant client on t_invoice_header.client_code = client.tenant_code 
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code 
		left join m_units on client.real_property_code = m_units.real_property_code and client.building_code = m_units.building_code and client.unit_no = m_units.unit_no 
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code 
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code 
		left join s_company on m_real_property.company_code = s_company.company_code 
		left join s_settings on s_company.company_code = s_settings.company_code 
		left join s_users on t_invoice_header.updated_by = s_users.[user_id]
		where t_invoice_header.invoice_no like '%' + @invoice_no + '%'
		and t_invoice_header.invoice_date >=convert(datetime,@invoice_date_from) and t_invoice_header.invoice_date <=convert(datetime,@invoice_date_to)
		and isnull(t_invoice_detail.total_charge_amount,0) <> 0
		order by t_invoice_header.invoice_no,m_tenant.tenant_name,upper(m_charges.charge_desc)
	end

/*
	set @tmp_table = 'tmp_table' + replace(convert(varchar(30),getdate()),' ','')	

	if exists (select * from dbo.sysobjects where id = object_id(N'[@tmp_table]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [@tmp_table]
	
	create table [@tmp_table] (invoice_no varchar(20),
				invoice_no_type char(1),
				invoice_date datetime,
				client_code char(10),
				real_property_code char(5),
				billing_from datetime,
				billing_to datetime,
				document_no varchar(20),
				sap_code varchar(20),
				remarks varchar(100),
				status char(1),
				created_by varchar(50),
				date_created datetime,
				updated_by varchar(50),
				date_updated datetime,
				invoice_detail_id decimal(9,0),
				charge_code char(5),
				charge_amount decimal(9,2),
				total_charge_amount decimal(9,2),
				date_from datetime,
				date_to datetime,
				prev_reading decimal(9,0),
				current_reading decimal(9,0),
				detail_remarks varchar(4000),
				tenant_name varchar(100),
				tenant_address1 varchar(100),
				tenant_address2 varchar(100),
				unit_area varchar(1000),
				charge_desc varchar(100),
				real_property_name varchar(100),
				real_property_company_name varchar(100),
				dba_name varchar(100),
				company_address varchar(200),
				company_contact_no varchar(200),
				prepared_by varchar(100),
				approved_by varchar(100)
				)

	set @invoice_no_from = upper(ltrim(rtrim(isnull(@invoice_no_from,''))))
	set @invoice_no_to = upper(ltrim(rtrim(isnull(@invoice_no_to,''))))
	set @invoice_date_from = upper(ltrim(rtrim(isnull(@invoice_date_from,''))))
	set @invoice_date_to = upper(ltrim(rtrim(isnull(@invoice_date_to,''))))

	set @company_code = 'THC'

	set @ssql = 'insert into ' + @tmp_table + ' select t_invoice_header.*, ' +
		't_invoice_detail.invoice_detail_id,t_invoice_detail.charge_code, ' +
		't_invoice_detail.charge_amount,t_invoice_detail.total_charge_amount, ' +
		't_tenant_reading.date_from,t_tenant_reading.date_to,prev_reading,current_reading, ' +
		'''('' + convert(varchar(10),t_tenant_reading.date_from,101) + ''-'' + convert(varchar(10),t_tenant_reading.date_to,101) + '') '' ' +
		'+ ' +
		'case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then ' +
		'	''Previous Reading: '' + convert(varchar(10),prev_reading) + ''; Current Reading: '' + convert(varchar(10),current_reading) + ''; Usage: '' + convert(varchar(10),current_reading - prev_reading) + '';'' ' +
		'else ''''' +
		'	end as detail_remarks, ' +
		'upper(m_tenant.tenant_name) as tenant_name,m_tenant.address1 as tenant_address1,m_tenant.address2 as tenant_address2, ' +
		'ltrim(rtrim(m_units.unit_no)) + '' / '' + m_units.lot_area as unit_area, ' +
		'm_charges.charge_desc, ' +
		'm_real_property.real_property_name,m_real_property.real_property_company_name,''dba '' + m_real_property.real_property_dba_name as dba_name, ' +
		's_company.address1 + '' '' + s_company.address2 as company_address, ' +
		'isnull(''TEL. : '' +s_company.contact_no1,'''') +  isnull('' '' +s_company.contact_no2,'''') + ' +
		'isnull('' FAX: '' + s_company.fax_no,'''') as company_contact_no, ' +
		's_settings.prepared_by,s_settings.approved_by ' +
		'from t_invoice_header ' +
		'left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no ' +
		'left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no ' +
		'	and t_invoice_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id ' +
		'left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id ' +
		'left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code ' +
		'left join m_units on m_tenant.real_property_code = m_units.real_property_code and m_tenant.building_code = m_units.building_code and m_tenant.unit_no = m_units.unit_no ' +
		'left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code ' +
		'left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code ' +
		'left join s_company on m_real_property.company_code = s_company.company_code ' +
		'left join s_settings on s_company.company_code = s_settings.company_code ' +
		'where s_settings.company_code = ''' + @company_code + ''' '

		if @invoice_no_from <> '' and @invoice_no_to <> ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,'+ convert(varchar(5),len(@invoice_no_from)) +') >= ''' + @invoice_no_from + ''' ' + 
					'and left(t_invoice_header.invoice_no,' + convert(varchar(5),len(@invoice_no_to)) + ') <= ''' + @invoice_no_to + ''' '
			end
		else if @invoice_no_from <> '' and @invoice_no_to = ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,'+ convert(varchar(5),len(@invoice_no_from)) +') >= ''' + @invoice_no_from + ''' ' 
			end

		else if @invoice_no_from = '' and @invoice_no_to <> ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,' + convert(varchar(5),len(@invoice_no_to)) + ') <= ''' + @invoice_no_to + ''' '
			end

		if @invoice_date_from <> '' and @invoice_date_to <> ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_from = ''' + @invoice_date_from + ''' and t_invoice_header.billing_to = ''' + @invoice_date_to + ''' '
			end
		else if @invoice_date_from <> '' and @invoice_date_to = ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_from = ''' + @invoice_date_from + ''' '
			end
		else if @invoice_date_from = '' and @invoice_date_to <> ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_to = ''' + @invoice_date_to + ''' '
			end
		--print @ssql
		exec sp_executesql @ssql

		set @ssql = 'select * from ' + @tmp_table

		exec sp_executesql @ssql

*/
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Invoice_Listing]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Invoice_Listing]
	@condition nvarchar(1000),
	@sortby nvarchar(500),
	@groupby nvarchar(500)
AS

declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_invoice_listing]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_invoice_listing]
	
	CREATE TABLE [dbo].[tmp_rg_invoice_listing] (
		[rec_id] [decimal](18, 0) IDENTITY(1,1) NOT NULL,
		[invoice_no] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[invoice_no_type] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[invoice_date] [datetime] NULL ,
		[client_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[inv_real_property_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[billing_from] [datetime] NULL ,
		[billing_to] [datetime] NULL ,
		[document_no] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[sap_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[remarks] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[status] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[created_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_created] [datetime] NULL ,
		[updated_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_updated] [datetime] NULL ,
		[client_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[tenant_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,		
		[unit_no] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[charge_code] [varchar] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[charge_desc] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[total_charge_amount] [decimal](18, 2) NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL 
	) ON [PRIMARY]

	set @ssql = ' insert into tmp_rg_invoice_listing (
				invoice_no,
				invoice_no_type,
				invoice_date,
				client_code,
				inv_real_property_code,
				billing_from,
				billing_to,
				document_no,
				sap_code,
				remarks,
				status,
				created_by,
				date_created,
				updated_by,
				date_updated,
				client_name,
				tenant_name,		
				unit_no,
				charge_code,
				charge_desc,
				total_charge_amount,
				or_company_name,
				or_company_address,
				or_company_contact_no1,
				or_company_contact_no2,
				group_by
		)
		select t_invoice_header.*,
		bill_to.tenant_name,m_tenant.tenant_name,
		ltrim(rtrim(m_tenant.real_property_code)) + ''/'' + ltrim(rtrim(m_tenant.building_code)) + ''/'' + ltrim(rtrim(m_tenant.unit_no)),
		t_invoice_detail.charge_code,
		m_charges.charge_desc,
		isnull(t_invoice_detail.total_charge_amount,0),
		s_company.or_company_name,
		or_company_address1 + '', '' + or_company_address2 ,
		or_company_contact_no1, or_company_contact_no2  '
			
	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 
	
	set @ssql = @ssql + ' from t_invoice_header
			left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
			left join m_tenant bill_to on t_invoice_header.client_code = bill_to.tenant_code
			left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
			left join  m_charges on  t_invoice_detail.charge_code = m_charges.charge_code
			left join s_company on m_tenant.company_code = s_company.company_code
		where  s_company.company_code =  ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby


	print @ssql
	exec sp_executesql @ssql

	--select * from tmp_rg_invoice_listing
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Invoice_Listing_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Invoice_Listing_View]
	
AS
	select * from tmp_rg_invoice_listing order by rec_id
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Invoice_Proc]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_Invoice_Proc]
	@invoice_no varchar(20),
	@invoice_date_from varchar(10),
	@invoice_date_to varchar(10),
	@status tinyint,
	@uid varchar(20),
	@date_generated varchar(50)
AS

/*
FIXES:
	2025-05-20	Aldrich
	- Replaced Prepared By from AARON VELASCO to ROSALIE MANGULABNAN

	2024-06-06	Aldrich
	- Replaced Prepared By from ARRIANE ANTONIO to AARON VELASCO
*/

declare @ssql nvarchar(4000)
declare @status1 tinyint,@status2 tinyint

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @invoice_date_from = upper(ltrim(rtrim(isnull(@invoice_date_from,''))))
set @invoice_date_to = upper(ltrim(rtrim(isnull(@invoice_date_to,''))))
set @status = isnull(convert(tinyint,@status),0)

if @status = 0
	begin
		set @status1 = 0
		set @status2 = 1
	end
else if @status = 1
	begin
		set @status1 = 1
		set @status2 = 1
	end
else if @status = 2
	begin
		set @status1 = 0
		set @status2 = 0
	end

	if not exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_invoice]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
		begin	
			CREATE TABLE [dbo].[z_tmp_invoice] (
				[seq] [decimal](18, 0) NULL ,
				[invoice_no] [varchar] (30) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[invoice_no_type] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[invoice_date] [datetime] NULL ,
				[client_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[real_property_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[billing_from] [datetime] NULL ,
				[billing_to] [datetime] NULL ,
				[document_no] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[sap_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[remarks] [varchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[status] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[created_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[date_created] [datetime] NULL ,
				[updated_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[date_updated] [datetime] NULL ,
				[invoice_detail_id] [decimal](18, 0) NULL ,
				[charge_code] [varchar] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[charge_amount] [decimal](18, 6) NULL ,
				[total_charge_amount] [decimal](18, 2) NULL ,
				[date_from] [datetime] NULL ,
				[date_to] [datetime] NULL ,
				[prev_reading] [decimal](18, 0) NULL ,
				[current_reading] [decimal](18, 0) NULL ,
				[detail_remarks] [varchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[client_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[tenant_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[tenant_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[tenant_address1] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[tenant_address2] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[unit_area] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[charge_desc] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[real_property_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[dba_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[company_contact_no] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[prepared_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[approved_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[generated_by] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
				[date_generated] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
				[check_payable] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
			) ON [PRIMARY]
		end

	delete from z_tmp_invoice where generated_by = @uid

	insert into z_tmp_invoice (invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,
		status,created_by,date_created,updated_by,date_updated,invoice_detail_id,charge_code,charge_amount,total_charge_amount,date_from,date_to,
		prev_reading,current_reading,detail_remarks,client_name,tenant_code,tenant_name,tenant_address1,tenant_address2,unit_area,charge_desc,real_property_name,
		real_property_company_name,dba_name,company_address,company_contact_no,prepared_by,approved_by,generated_by,date_generated,check_payable)
	select -- t_invoice_header.*, 
		invoice_no = case when isnull(status,'') = 'V' then t_invoice_header.invoice_no + ' (VOID)' else  t_invoice_header.invoice_no end,
		t_invoice_header.invoice_no_type,
		t_invoice_header.invoice_date,
		t_invoice_header.client_code,
		t_invoice_header.real_property_code,
		t_invoice_header.billing_from,
		t_invoice_header.billing_to,
		t_invoice_header.document_no,
		t_invoice_header.sap_code,
		t_invoice_header.remarks,
		t_invoice_header.status,
		t_invoice_header.created_by,
		t_invoice_header.date_created,
		t_invoice_header.updated_by,
		t_invoice_header.date_updated,
		t_invoice_detail.invoice_detail_id,t_invoice_detail.charge_code, 
		t_invoice_detail.charge_amount,t_invoice_detail.total_charge_amount, 
		t_tenant_reading.date_from,t_tenant_reading.date_to,prev_reading,current_reading, 
		isnull(case when ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_from,101))) = '' and ltrim(rtrim(convert(varchar(10),t_tenant_reading.date_to,101))) = '' then '' else
			'(' + convert(varchar(10),t_tenant_reading.date_from,101) + '-' + convert(varchar(10),t_tenant_reading.date_to,101) + ') '
		end
		+ 
		case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then 
			'Previous Reading: ' + convert(varchar(10),prev_reading) + '; Current Reading: ' + convert(varchar(10),current_reading) + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';'
		else ''
			end,'') as detail_remarks,
		upper(client.tenant_name) as client_name,
		m_tenant.tenant_code,	
		upper(m_tenant.tenant_name + ' (' +  ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)) + ')' 
			)   as tenant_name,
		upper(client.address1) as tenant_address1,upper(client.address2) as tenant_address2,
		case when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  '' then '' 
		when ltrim(rtrim(isnull(m_units.unit_no,''))) <> '' and ltrim(rtrim(isnull(m_units.lot_area,''))) =  ''  then upper(ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))))
		when ltrim(rtrim(isnull(m_units.unit_no,''))) = '' and ltrim(rtrim(isnull(m_units.lot_area,''))) <>  ''  then upper(ltrim(rtrim(isnull(m_units.lot_area,''))))
		else  upper( ltrim(rtrim(isnull(m_units.building_code,''))) + ' / ' + ltrim(rtrim(isnull(m_units.unit_no,''))) + ' / ' + ltrim(rtrim(isnull(m_units.lot_area,'')))) end as unit_area,
		upper(m_charges.charge_desc) as charge_desc,
		m_real_property.real_property_name,upper(m_real_property.real_property_company_name) as real_property_company_name,
		'dba ' + m_real_property.real_property_dba_name as dba_name,
		s_company.address1 + ' ' + s_company.address2 as company_address,
		isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,'') +
		isnull(' FAX: ' + s_company.fax_no,'') as company_contact_no,
		--upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,
		--case when t_invoice_header.real_property_code <> 'WT' then 'NORMA BATU' else 'NIMFA TAN' end AS prepared_by,
		--'NORMA BATU' AS prepared_by,
		--'RUBY PANOY' AS prepared_by,
		'ROSALIE MANGULABNAN' AS prepared_by,
		--'AARON VELASCO' AS prepared_by,
		--'ARRIANE ANTONIO' AS prepared_by,
		upper(s_settings.approved_by) as approved_by,@uid,@date_generated,
		case when t_invoice_header.real_property_code <> 'WT' then 'L&T GROUP OF COMPANIES, LTD.' else 'W&T INTERNATIONAL CORPORATION' end AS check_payable
		from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no 
		left join
		(
		select t_invoice_detail_reading.invoice_no,t_invoice_detail_reading.invoice_detail_id,tenant_code,t_tenant_reading_charges.trc_charge_code as charge_code,
		t_tenant_reading.reading_id,t_tenant_reading.date_from,t_tenant_reading.date_to,
		t_tenant_reading.prev_reading,t_tenant_reading.current_reading
		from t_invoice_detail_reading 
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id 
		left join t_tenant_reading_charges on t_tenant_reading.reading_id = t_tenant_reading_charges.trc_reading_id 
		)t_tenant_reading on t_invoice_detail.invoice_no = t_tenant_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_tenant_reading.invoice_detail_id
			and t_invoice_detail.tenant_code = t_tenant_reading.tenant_code
			and t_invoice_detail.charge_code = t_tenant_reading.charge_code
		left join m_tenant client on t_invoice_header.client_code = client.tenant_code 
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code 
		left join m_units on client.real_property_code = m_units.real_property_code and client.building_code = m_units.building_code and client.unit_no = m_units.unit_no 
		left join m_units tenant_unit on m_tenant.real_property_code = tenant_unit.real_property_code 
			and m_tenant.building_code = tenant_unit.building_code and m_tenant.unit_no = tenant_unit.unit_no 
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code 
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code 
		left join s_company on m_real_property.company_code = s_company.company_code 
		left join s_settings on s_company.company_code = s_settings.company_code 
		left join s_users on t_invoice_header.updated_by = s_users.[user_id]
		left join u_invoice_alert on t_invoice_header.invoice_no = u_invoice_alert.invoice_no
		where t_invoice_header.invoice_no like '%' + @invoice_no + '%' and isnull(t_invoice_detail.total_charge_amount,0) <> 0
			and isnull(u_invoice_alert.email_sent,0) in (@status1,@status2)
		order by t_invoice_header.invoice_no,m_tenant.tenant_name,upper(m_charges.charge_desc)
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Occupancy]

	@date_from datetime,
	@date_to datetime,
	@date_created datetime,
	@uid varchar(50)

AS

--ALTER PROCEDURE [dbo].[sp_rpt_Occupancy]
/*declare
	@date_from datetime = '07/24/2021',
	@date_to datetime = '08/14/2021',
	@date_created datetime = '8/19/2021',
	@uid varchar(50) = 'maximoj'
*/
--AS
--exec sp_rpt_Occupancy '11/14/2020','12/05/2020','12/09/2020','maximoj'
--exec sp_rpt_Occupancy_View '11/14/2020','12/05/2020','12/09/2020','maximoj'
--truncate table z_tmp_occupancy
--select * from rg_occupancy_hist where date_from>='7/24/2021' order by date_from
--delete from rg_occupancy_hist where date_from>='7/24/2021'
--select * from z_tmp_occupancy order by real_property_code,occupancy_date
--select * from rg_occupancy_hist order by date_from
--select * from rg_occupancy_hist where date_created < '8/19/2021' order by date_from
--select datepart(dw,'7/25/2021')
--

--cut off every saturday of the week


	declare @real_property_code char(5),@tmp_date_from datetime,@tmp_date_to datetime,@rental_charge_code char(5),@day_diff int,@seq int
	declare @rg_date_from datetime,@rg_date_to datetime

	set @rg_date_from = @date_from
	set @rg_date_to = @date_to
	set @tmp_date_from = @date_from
	set @tmp_date_to = @date_to
	select top 1 @rental_charge_code = apt_rental_charge from s_settings

	declare @apt_rental char(10),@off_rental char(10),@whs_rental char(10)
				
	select top 1 @apt_rental = apt_rental_charge,@off_rental=off_rental_charge,@whs_rental=whs_rental_charge from s_settings
	
	delete from z_tmp_occupancy where created_by = @uid
	delete from rg_occupancy_hist where date_to = @date_to
	delete from rg_occupancy_hist where (month(date_to) = month(@date_to) and  year(date_to) = year(@date_to))
		or occupancy_date >= dateadd (day, -28, @date_to)
		 
	
	--//check rg_occupancy_hist
	declare @dt_max datetime

	select  top 1 @dt_max =  occupancy_date from rg_occupancy_hist order by occupancy_date desc

	if @dt_max < @date_to
		begin
			set @date_from = dateadd(day,1,@dt_max)
			set @tmp_date_from = @date_from
		end
	else if @dt_max = @date_to and datediff(day,@dt_max,getdate()) < 7
		begin
			delete from rg_occupancy_hist where occupancy_date >= @dt_max
		end
	--//
	--print 'top'
	--print @date_from
	declare xxx cursor scroll for 
	select real_property_code from m_real_property where upper(ltrim(rtrim(isnull(space_type,'A')))) = 'A'  and real_property_code <> 'LNHSE'
	order by real_property_name
	set @seq = 1
	open xxx
	fetch next from xxx into @real_property_code
	while @@fetch_status = 0
		begin
			if (datepart(dw,@date_from) <> 7 and @date_from <> @date_to)
				and @date_from <> dateadd(day,-1,(convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from))))))
			begin
				set @day_diff = 7 - (datepart(dw,@date_from))
				set @date_from = dateadd(day,@day_diff,@date_from)
				if @date_from > @date_to
					set @date_from = @date_to				
			end

			print 'c'
			print @date_from
			print @date_to
			print 'd'

			while @date_from <= @date_to
				begin
					print 'a'
					print @date_from
					print @date_to
					print 'b'
					declare @total_created_units decimal(9,0),@total_deleted_units decimal(9,0),@total_reserved_units decimal(9,0),@total_not_reserved_units decimal(9,0)
					declare @total_units_for_rent decimal(9,0),@occupied decimal(9,0),@vacant decimal(9,0)
					declare @total_units_occupied decimal(9,0),@total_units_vacant decimal(9,0),@room_income decimal(18,6),
					@total_occupied_amount decimal(18,6),@total_vacated_amount decimal(18,6),
					@total_occupied_byanother_amount decimal(18,6),@total_vacated_byoneof_amount decimal(18,6),
					@total_occupied_amount1 decimal(18,6),@total_occupied_amount2 decimal(18,6)

					declare @total_compli_units decimal(9,0) = 0

					set @occupied = 0
					set @vacant = 0
					set @room_income = 0
					set @total_occupied_amount = 0
					set @total_vacated_amount = 0
					set @total_occupied_byanother_amount = 0
					set @total_vacated_byoneof_amount = 0
					set @total_reserved_units = 0
					set @total_not_reserved_units = 0

					select @total_compli_units = count(unit_no) from m_units where real_property_code = @real_property_code
						and isnull(is_complimentary,0) = 1 and complimentary_date_from <= @date_from

					set @total_compli_units = isnull(@total_compli_units,0)
				
					if @real_property_code not in ('FT A','FT B')
						begin

							select @total_reserved_units = count(*)
							from m_units
							where  real_property_code = @real_property_code
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
							select @total_not_reserved_units = 0

							select @total_created_units = count(*)
							from m_units
							where  real_property_code = @real_property_code
							and upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) in
							(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
							where  real_property_code = @real_property_code
							and charge_code in (@apt_rental,@off_rental,@whs_rental))
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
		
							set @total_deleted_units = 0

							select @occupied = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code = @real_property_code
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							
							and 
								(
									upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
									from m_tenant 
									inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
									--and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
									and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
									and actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code = @real_property_code
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
									
									or
									
									upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
									from m_tenant 
									left join m_tenant client on m_tenant.bill_to = client.tenant_code
									inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
									--and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
									and (m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
									and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code = @real_property_code
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
								)
								
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code = @real_property_code
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
							select @vacant = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code = @real_property_code
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(
								select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
								from m_tenant 
								where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
								and date_terminated < dateadd(day,1,@date_from)
								and m_tenant.real_property_code = @real_property_code
								)
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code = @real_property_code
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
							and isnull(m_units.is_complimentary,0) <> 1
												
							select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where 
							(upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
							and 
							(date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code = @real_property_code
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code = @real_property_code
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' )
								
							select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
							left join m_tenant on m_tenant.bill_to = client.tenant_code
							where 
							(upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							and 
							(m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code = @real_property_code
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code = @real_property_code
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' )
		
							set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
							set @total_occupied_byanother_amount = 0
							set @total_vacated_amount = 0
							set @total_vacated_byoneof_amount = 0
		
							set @room_income = isnull(@total_occupied_amount,0) 

							set @total_units_for_rent = isnull(@total_created_units,0) 
							set @total_units_occupied = isnull(@occupied,0) 
							set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
						end

					else if @real_property_code ='FT A'
						begin
							
							select @total_reserved_units = count(*)
							from m_units
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
							in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
							from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
							set @total_not_reserved_units = 0

							select @total_created_units = count(*)
							from m_units
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and isnull(no_of_bedrooms,0) = 1
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in 
							(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
							set @total_deleted_units = 0
	
							select @occupied = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
							and isnull(no_of_bedrooms,0) = 1
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							and 
							(
								upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
									from m_tenant 
									inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
									and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
									and actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code in ('FT A' , 'FT B') 
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
									
								or
								
								upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
									from m_tenant 
									left join m_tenant client on m_tenant.bill_to = client.tenant_code
									inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
									and (m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
									and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code in ('FT A' , 'FT B') 
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
							)		
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
							select @vacant = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
							and isnull(no_of_bedrooms,0) = 1
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(
								select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
								from m_tenant 
								where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
								and date_terminated < dateadd(day,1,@date_from)
								and m_tenant.real_property_code in ('FT A' , 'FT B') 
								)
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
							and isnull(m_units.is_complimentary,0) <> 1
												
							select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
							and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
								from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
								and isnull(no_of_bedrooms,0) = 1)
								
							select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
							left join m_tenant on m_tenant.bill_to = client.tenant_code
							where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							and (m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
								from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
								and isnull(no_of_bedrooms,0) = 1)
		
							set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
							set @total_occupied_byanother_amount = 0
							set @total_vacated_amount = 0
							set @total_vacated_byoneof_amount = 0
	
							set @room_income = isnull(@total_occupied_amount,0) 

							set @total_units_for_rent = isnull(@total_created_units,0) 
							set @total_units_occupied = isnull(@occupied,0)
							--set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
							set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
							--print isnull(@vacant,0)
						end

					else if @real_property_code ='FT B'
						begin

							select @total_reserved_units = count(*)
							from m_units
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
							in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
							from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
							set @total_not_reserved_units = 0

							select @total_created_units = count(*)
							from m_units
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and isnull(no_of_bedrooms,0) = 2
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in 
							(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
							set @total_deleted_units = 0
	
							select @occupied = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
							and isnull(no_of_bedrooms,0) = 2
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							and 
							(
								upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
									from m_tenant 
									inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
									and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
									and actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code in ('FT A' , 'FT B') 
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
									
								or
								
								upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
									(
									select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
									from m_tenant 
									left join m_tenant client on m_tenant.bill_to = client.tenant_code
									inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
									where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
									and (m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
									and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
									and m_tenant.real_property_code in ('FT A' , 'FT B') 
									and charge_code in (@apt_rental,@off_rental,@whs_rental)
									)
							)		
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
							select @vacant = count(*)
							from m_units
							left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
							where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
							and isnull(no_of_bedrooms,0) = 2
							and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(
								select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
								from m_tenant 
								where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
								and date_terminated < dateadd(day,1,@date_from)
								and m_tenant.real_property_code in ('FT A' , 'FT B') 
								)
							and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
								where real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental))
							and isnull(m_units.is_complimentary,0) <> 1				
							
							select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
							and (date_terminated >= dateadd(day,1,@date_from) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
								from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
								and isnull(no_of_bedrooms,0) = 2)
								
							select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
							from m_tenant_charges
							inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
							left join m_tenant on m_tenant.bill_to = client.tenant_code
							where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							and (m_tenant.date_terminated >= dateadd(day,1,@date_from) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@date_from)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
								(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
								from m_unit_charges
								left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
									m_unit_charges.building_code = m_units.building_code and 
									m_unit_charges.unit_no = m_units.unit_no  
								where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
								and charge_code in (@apt_rental,@off_rental,@whs_rental)
								and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
								and isnull(no_of_bedrooms,0) = 2)
		
							set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
							set @total_occupied_byanother_amount = 0
							set @total_vacated_amount = 0
							set @total_vacated_byoneof_amount = 0
	
							set @room_income = isnull(@total_occupied_amount,0) 

							set @total_units_for_rent = isnull(@total_created_units,0) 
							set @total_units_occupied = isnull(@occupied,0)
							--set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
							set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
						end
			
					set @seq = @seq + 1

					--print ' @date_from'
					--print @date_from 
					--print ' @date_to'
					--print @date_to
					--HERE
					/*insert into z_tmp_occupancy (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,total_units_occupied,total_vacant_units,room_income,
						date_created,created_by,seq,remarks)
					select @real_property_code,@tmp_date_from,@date_to,@date_from,@total_units_for_rent,@total_units_occupied,@total_units_vacant,isnull(@room_income,0),@date_created,@uid,2,
						isnull((select top 1 remarks from rg_occupancy where date_from = @rg_date_from and date_to = @rg_date_to and real_property_code = @real_property_code 
						and generated_by = @uid and date_generated = @date_created order by tran_date desc),'')
					*/--HERE
					/*if exists (select occupancy_date from rg_occupancy_hist where occupancy_date = @date_from and real_property_code = @real_property_code)
						delete from rg_occupancy_hist where occupancy_date = @date_from and real_property_code = @real_property_code*/
					-------------HERE***
					if not exists (select occupancy_date from rg_occupancy_hist where occupancy_date = @date_from and real_property_code = @real_property_code)
						begin
						print ' @date_from'
						print @date_from 
						print ' @date_to'
						print @date_to

							insert into rg_occupancy_hist (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,
								total_complimentary,
								total_units_occupied,total_vacant_units,room_income,
								date_created,created_by,seq,remarks)		
							select @real_property_code,
								dateadd(day,-6,@date_from),
								--case when (month(@date_from) in (4,6,9,11) and day(@date_from) = 30)
								--	or (month(@date_from) in (1,3,5,7,8,10) and day(@date_from) = 31)
								--	or (month(@date_from) = 2 and day(@date_from) in (28,29))
								--	then @date_from
								--else dateadd(day,-6,@date_from)
								--end,
								@date_from
								
								,@date_from
								
								,@total_units_for_rent,
								isnull(@total_compli_units,0),
								@total_units_occupied,@total_units_vacant,isnull(@room_income,0),
								@date_created,@uid,2,isnull((select top 1 remarks from rg_occupancy where date_from = @rg_date_from and date_to = @rg_date_to and real_property_code = @real_property_code 
								and generated_by = @uid and date_generated = @date_created order by tran_date desc),'')
						end											
					
					set @day_diff = 0
					if @date_from = @date_to						
						set @day_diff = 1

					print 'loop'
					print @date_from
					print @date_to
					print day(dateadd(day,7,@date_from)) 
					print dateadd(day,-1,(convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from))))))
					print 'loop'

					--e.g. day(09/26/2020) > day(10/03/2020)
					if day(@date_from) > day(dateadd(day,7,@date_from)) 
						and @date_from <> dateadd(day,-1,(convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from))))))
						begin
							print '1'
							set @date_from = convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from))))
							set @date_from = dateadd(day,-1,@date_from)
						end
					else if @date_from = dateadd(day,-1,(convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from))))))
						and @date_from <> @date_to
						and datediff(day,@date_from,@date_to) <> 7
						and datepart(dw,(dateadd(day,-1,(convert(datetime,convert(varchar(2),month(dateadd(day,7,@date_from))) + '/01/' + convert(varchar(4),year(dateadd(day,7,@date_from)))))))) <> 7
						begin
							print '2'
							--e.g. if @date_from = 09/30/2020
							DECLARE @TodayNumber int = DATEPART(dw, @date_from) -- Get the day number
							set @date_from = DATEADD(DAY, (7-@TodayNumber)%7, @date_from) 							
						end
					else
						begin
							print '3'
							set @date_from = dateadd(day,7,@date_from)	
						end
					
					if @date_from > @date_to and @day_diff = 0
						set @date_from = @date_to	

				end

			set @date_from = @tmp_date_from
			set @date_to = @tmp_date_to

			print 'end'
			print @date_from
			print @date_to
			print 'end'

			fetch next from xxx into @real_property_code
		end

	close xxx
	deallocate xxx

	/*insert into z_tmp_occupancy (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,total_units_occupied,total_vacant_units,room_income,date_created,created_by,seq)
	select 'OVERALL',date_from,date_to,occupancy_date,sum(total_units_for_rent),sum(total_units_occupied),sum(total_vacant_units),sum(room_income),
	date_created,created_by,1
	from z_tmp_occupancy where date_created = @date_created and created_by = @uid 
	group by date_from,date_to,occupancy_date,date_created,created_by
	*/
	------------HERE
	if exists (select distinct occupancy_date from rg_occupancy_hist where occupancy_date not in 
			(select occupancy_date from rg_occupancy_hist where real_property_code = 'OVERALL'))
		begin
			insert into rg_occupancy_hist (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,
				total_complimentary,
				total_units_occupied,total_vacant_units,room_income,
				date_created,created_by,seq,remarks)		
			select 'OVERALL',date_from,date_to,occupancy_date,sum(total_units_for_rent),
			sum(isnull(total_complimentary,0)),
			sum(total_units_occupied),sum(total_vacant_units),sum(room_income),
			@date_created,@uid,1,''
			from rg_occupancy_hist where date_created = @date_created and created_by = @uid 
			and occupancy_date not in (select occupancy_date from rg_occupancy_hist where real_property_code = 'OVERALL')
			--and real_property_code not in ('OVERALL','WT')
			and real_property_code not in ('OVERALL')
			group by date_from,date_to, occupancy_date
		end
	
	delete from z_tmp_occupancy where date_created = @date_created and created_by = @uid 
	
	insert into z_tmp_occupancy (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,
		total_complimentary,
		total_units_occupied,total_vacant_units,room_income,
		date_created,created_by,seq,remarks)
	select distinct real_property_code,@rg_date_from,@rg_date_to,occupancy_date,total_units_for_rent,
		isnull(total_complimentary,0),
		total_units_occupied,total_vacant_units,isnull(room_income,0),@date_created,@uid,seq,
		isnull((select top 1 remarks from rg_occupancy where date_from = @rg_date_from and date_to = @rg_date_to and real_property_code = rg_occupancy_hist.real_property_code 
		and generated_by = @uid and date_generated = @date_created order by tran_date desc),'')
	from rg_occupancy_hist
	where occupancy_date between @rg_date_from and @rg_date_to 
	--and datepart(dw,occupancy_date) = 7
	
	
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_AsOf]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_AsOf]
	@asof date,
	@date_created datetime,
	@uid nvarchar(50)
AS
/*
PURPOSE: Monthly Occupancy Report
CREATED BY: Resalie Usi
DATE CREATED: 05.03.2019
*/

--exec sp_rpt_Occupancy_AsOf '4/30/2019','5/3/2019','res'
--exec sp_rpt_Occupancy_AsOf_View '4/30/2019','5/3/2019','res'
--select convert(varchar, getdate(), 107)

	declare @real_property_code char(5),@rental_charge_code char(5),@day_diff int,@seq int
	declare @rg_date_asof datetime
	declare @counter int = 1, @maxctr int = 0,@tmpasof date, @report_title nvarchar(200) = 'OCCUPANCY REPORT AS OF ' + upper(convert(varchar, @asof, 101))

	set @rg_date_asof = @asof	
	set @tmpasof = dateadd(day,1,@asof)
	
	select top 1 @rental_charge_code = apt_rental_charge from s_settings

	declare @apt_rental nvarchar(10),@off_rental nvarchar(10),@whs_rental nvarchar(10)
				
	select top 1 @apt_rental = apt_rental_charge,@off_rental=off_rental_charge,@whs_rental=whs_rental_charge from s_settings
	
	delete from z_tmp_occupancy_asof where created_by = @uid
	delete from rg_occupancy_asof where date_asof = @asof or month(date_asof) = month(@asof)
	--delete from rg_occupancy_asof where month(date_asof) = month(@asof)
	
	--//check rg_occupancy_asof
	declare @dt_max datetime

	select top 1 @dt_max = occupancy_date from rg_occupancy_asof order by occupancy_date desc

	if @dt_max = @asof and datediff(day,@dt_max,getdate()) < 7
		begin
			delete from rg_occupancy_asof where occupancy_date >= @dt_max
		end
	--//

	--print @asof select * from m_real_property 
	--//TSL AND WAREHOUSE ONLY
	IF OBJECT_ID('tempdb.dbo.#temp') IS NOT NULL
		/*Then it exists*/
		DROP TABLE #temp

	--// SORT LEAVE DATES BY LEAVE TYPE
	select rowctr=ROW_NUMBER() OVER(ORDER BY real_property_name),real_property_code into #temp
	from m_real_property where upper(ltrim(rtrim(isnull(space_type,'A')))) = 'W'  
	or real_property_code in ('TSL')
	order by real_property_name

	select @maxctr = count(rowctr) from #temp

	while @maxctr > 0 and @maxctr >= @counter
		begin
			
			select top 1 @real_property_code = real_property_code from #temp where rowctr = @counter
								
			declare @total_created_units decimal(9,0),@total_deleted_units decimal(9,0),@total_reserved_units decimal(9,0),@total_not_reserved_units decimal(9,0)
			declare @total_units_for_rent decimal(9,0),@occupied decimal(9,0),@vacant decimal(9,0)
			declare @total_units_occupied decimal(9,0),@total_units_vacant decimal(9,0),@room_income decimal(18,6),
			@total_occupied_amount decimal(18,6),@total_vacated_amount decimal(18,6),
			@total_occupied_byanother_amount decimal(18,6),@total_vacated_byoneof_amount decimal(18,6),
			@total_occupied_amount1 decimal(18,6),@total_occupied_amount2 decimal(18,6)

			declare @total_compli_units decimal(9,0) = 0

			set @occupied = 0
			set @vacant = 0
			set @room_income = 0
			set @total_occupied_amount = 0
			set @total_vacated_amount = 0
			set @total_occupied_byanother_amount = 0
			set @total_vacated_byoneof_amount = 0
			set @total_reserved_units = 0
			set @total_not_reserved_units = 0

			select @total_compli_units = count(unit_no) from m_units where real_property_code = @real_property_code
						and isnull(is_complimentary,0) = 1 and complimentary_date_from <= @asof
				
			if @real_property_code not in ('FT A','FT B')
				begin

					select @total_reserved_units = count(*)
					from m_units
					where  real_property_code = @real_property_code
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
					select @total_not_reserved_units = 0

					select @total_created_units = count(*)
					from m_units
					where  real_property_code = @real_property_code
					and upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) in
					(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
					where  real_property_code = @real_property_code
					and charge_code in (@apt_rental,@off_rental,@whs_rental))
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
		
					set @total_deleted_units = 0

					select @occupied = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code = @real_property_code
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
							
					and 
						(
							upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
							from m_tenant 
							inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 							
							and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code = @real_property_code
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
									
							or
									
							upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
							from m_tenant 
							left join m_tenant client on m_tenant.bill_to = client.tenant_code
							inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							--and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and (m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code = @real_property_code
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
						)
								
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code = @real_property_code
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
					select @vacant = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code = @real_property_code
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(
						select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
						from m_tenant 
						where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
						and date_terminated < dateadd(day,1,@asof)
						and m_tenant.real_property_code = @real_property_code
						)
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code = @real_property_code
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
												
					select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
					where 
					(upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
					and 
					(date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
					and actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code = @real_property_code
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code = @real_property_code
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' )
								
					select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
					left join m_tenant on m_tenant.bill_to = client.tenant_code
					where 
					(upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
					and 
					(m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
					and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code = @real_property_code
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code = @real_property_code
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' )
		
					set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
					set @total_occupied_byanother_amount = 0
					set @total_vacated_amount = 0
					set @total_vacated_byoneof_amount = 0
		
					set @room_income = isnull(@total_occupied_amount,0) 

					set @total_units_for_rent = isnull(@total_created_units,0) 
					set @total_units_occupied = isnull(@occupied,0) 
					--set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
					set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
				end

			else if @real_property_code ='FT A'
				begin
							
					select @total_reserved_units = count(*)
					from m_units
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
					in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
					from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and isnull(no_of_bedrooms,0) = 1)
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
					set @total_not_reserved_units = 0

					select @total_created_units = count(*)
					from m_units
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and isnull(no_of_bedrooms,0) = 1)
					and isnull(no_of_bedrooms,0) = 1
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in 
					(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
					set @total_deleted_units = 0
	
					select @occupied = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
					and isnull(no_of_bedrooms,0) = 1
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
					and 
					(
						upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
							from m_tenant 
							inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
							and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
									
						or
								
						upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
							from m_tenant 
							left join m_tenant client on m_tenant.bill_to = client.tenant_code
							inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							and (m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
					)		
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
					select @vacant = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
					and isnull(no_of_bedrooms,0) = 1
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(
						select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
						from m_tenant 
						where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
						and date_terminated < dateadd(day,1,@asof)
						and m_tenant.real_property_code in ('FT A' , 'FT B') 
						)
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
												
					select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
					where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
					and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
					and actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code in ('FT A' , 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
						from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
						and isnull(no_of_bedrooms,0) = 1)
								
					select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
					left join m_tenant on m_tenant.bill_to = client.tenant_code
					where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
					and (m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
					and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code in ('FT A' , 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
						from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
						and isnull(no_of_bedrooms,0) = 1)
		
					set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
					set @total_occupied_byanother_amount = 0
					set @total_vacated_amount = 0
					set @total_vacated_byoneof_amount = 0
	
					set @room_income = isnull(@total_occupied_amount,0) 

					set @total_units_for_rent = isnull(@total_created_units,0) 
					set @total_units_occupied = isnull(@occupied,0)
					--set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
					set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
					--print isnull(@vacant,0)
				end

			else if @real_property_code ='FT B'
				begin

					select @total_reserved_units = count(*)
					from m_units
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
					in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
					from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and isnull(no_of_bedrooms,0) = 2)
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) = 'Y'
		
					set @total_not_reserved_units = 0

					select @total_created_units = count(*)
					from m_units
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and isnull(no_of_bedrooms,0) = 2)
					and isnull(no_of_bedrooms,0) = 2
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y'
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in 
					(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
					where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
					set @total_deleted_units = 0
	
					select @occupied = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
					and isnull(no_of_bedrooms,0) = 2
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
					and 
					(
						upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
							from m_tenant 
							inner join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
							and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
							and actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
									
						or
								
						upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
							(
							select upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) 
							from m_tenant 
							left join m_tenant client on m_tenant.bill_to = client.tenant_code
							inner join m_tenant_charges on client.tenant_code = m_tenant_charges.tenant_code
							where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
							and (m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
							and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
							and m_tenant.real_property_code in ('FT A' , 'FT B') 
							and charge_code in (@apt_rental,@off_rental,@whs_rental)
							)
					)		
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
		
					select @vacant = count(*)
					from m_units
					left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
					where m_units.real_property_code in ( 'FT A' ,  'FT B') 							
					and isnull(no_of_bedrooms,0) = 2
					and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(
						select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
						from m_tenant 
						where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') = 'Y'
						and date_terminated < dateadd(day,1,@asof)
						and m_tenant.real_property_code in ('FT A' , 'FT B') 
						)
					and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) from m_unit_charges
						where real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental))
												
							
					select @total_occupied_amount1 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					left join m_tenant  on m_tenant.tenant_code = m_tenant_charges.tenant_code
					where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') 
					and (date_terminated >= dateadd(day,1,@asof) or isnull(terminated,'N') <> 'Y' or isnull(date_terminated,'1/1/1900') = '1/1/1900')
					and actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code in ('FT A' , 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
						from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
						and isnull(no_of_bedrooms,0) = 2)
								
					select @total_occupied_amount2 = sum(isnull(charge_amount,0)) 
					from m_tenant_charges
					inner join m_tenant client on m_tenant_charges.tenant_code = client.tenant_code
					left join m_tenant on m_tenant.bill_to = client.tenant_code
					where (upper(ltrim(rtrim(isnull(client.tenant_type,'OC')))) = 'C') 
					and (m_tenant.date_terminated >= dateadd(day,1,@asof) or isnull(m_tenant.terminated,'N') <> 'Y' or isnull(m_tenant.date_terminated,'1/1/1900') = '1/1/1900')
					and m_tenant.actual_move_in_date < dateadd(day,1,@asof)
					and m_tenant.real_property_code in ('FT A' , 'FT B') 
					and charge_code in (@apt_rental,@off_rental,@whs_rental)
					and upper(ltrim(rtrim(isnull(m_tenant.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_tenant.unit_no,'')))) in
						(select upper(ltrim(rtrim(isnull(m_unit_charges.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_unit_charges.unit_no,'')))) 
						from m_unit_charges
						left join m_units on m_unit_charges.real_property_code = m_units.real_property_code and 
							m_unit_charges.building_code = m_units.building_code and 
							m_unit_charges.unit_no = m_units.unit_no  
						where m_unit_charges.real_property_code in ('FT A' , 'FT B') 
						and charge_code in (@apt_rental,@off_rental,@whs_rental)
						and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
						and isnull(no_of_bedrooms,0) = 2)
		
					set @total_occupied_amount = isnull(@total_occupied_amount1,0) + isnull(@total_occupied_amount2,0)
					set @total_occupied_byanother_amount = 0
					set @total_vacated_amount = 0
					set @total_vacated_byoneof_amount = 0
	
					set @room_income = isnull(@total_occupied_amount,0) 

					set @total_units_for_rent = isnull(@total_created_units,0) 
					set @total_units_occupied = isnull(@occupied,0)
					--set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
					set @total_units_vacant = isnull(@total_units_for_rent,0) - (isnull(@total_units_occupied,0) + isnull(@total_compli_units,0))
				end
											
			if not exists (select occupancy_date from rg_occupancy_asof where occupancy_date = @asof and real_property_code = @real_property_code)
				begin
					insert into rg_occupancy_asof (real_property_code,date_asof,occupancy_date,total_units_for_rent,total_units_occupied,total_vacant_units,room_income,
						date_created,created_by,seq,remarks)		
					select @real_property_code,@asof,@asof,@total_units_for_rent,@total_units_occupied,@total_units_vacant,isnull(@room_income,0),
						@date_created,@uid,2,isnull((select top 1 remarks from rg_occupancy_asof_log where date_asof = @rg_date_asof and real_property_code = @real_property_code 
						and generated_by = @uid and date_generated = @date_created order by tran_date desc),'')
				end					
					
			set @counter+=1

		end

	if exists (select distinct occupancy_date from rg_occupancy_asof where occupancy_date not in 
			(select occupancy_date from rg_occupancy_asof where real_property_code = 'OVERALL'))
		begin
			insert into rg_occupancy_asof (real_property_code,date_asof,occupancy_date,total_units_for_rent,
				total_complimentary,
				total_units_occupied,total_vacant_units,room_income,
				date_created,created_by,seq,remarks,rg_report_title)		
			select 'OVERALL',date_asof,occupancy_date,sum(total_units_for_rent),
				sum(total_complimentary),
				sum(total_units_occupied),sum(total_vacant_units),sum(room_income),
			@date_created,@uid,1,'',@report_title
			from rg_occupancy_asof where date_created = @date_created and created_by = @uid 
			and occupancy_date not in (select occupancy_date from rg_occupancy_asof where real_property_code = 'OVERALL')
			--and real_property_code not in ('OVERALL','WT')
			and real_property_code not in ('OVERALL')
			group by date_asof, occupancy_date
		end
	
	delete from z_tmp_occupancy_asof where date_created = @date_created and created_by = @uid 
	
	insert into z_tmp_occupancy_asof (real_property_code,date_asof,occupancy_date,total_units_for_rent,total_complimentary,total_units_occupied,total_vacant_units,room_income,
		date_created,created_by,seq,remarks,rg_report_title)
	select real_property_code,@rg_date_asof,occupancy_date,total_units_for_rent,
		total_complimentary,
		total_units_occupied,total_vacant_units,isnull(room_income,0),@date_created,@uid,seq,
		isnull((select top 1 remarks from rg_occupancy_asof_log where date_asof = @rg_date_asof and real_property_code = rg_occupancy_asof.real_property_code 
		and generated_by = @uid and date_generated = @date_created order by tran_date desc),''),@report_title
	from rg_occupancy_asof
	where occupancy_date = @rg_date_asof
	
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_AsOf_Log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_AsOf_Log]
	@date_asof datetime,
	@real_property_code varchar(10),
	@remarks nvarchar(4000),
	@date_generated datetime,
	@user_id varchar(50)
AS

	delete from rg_occupancy_asof_log where date_asof = @date_asof
	and real_property_code = @real_property_code and date_generated = @date_generated and generated_by = @user_id
	
	insert into rg_occupancy_asof_log(date_asof,real_property_code,remarks,generated_by,date_generated)
	select @date_asof,@real_property_code,@remarks,@user_id,@date_generated

GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_AsOf_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_AsOf_View]
	@date_asof varchar(10),
	@date_created varchar(20),
	@uid varchar(50)
AS
declare @company char(5)
declare @tmp_date_asof datetime,@tmp_date_to datetime,@tmp_date_created datetime

set @tmp_date_asof = convert(datetime,@date_asof)
set @tmp_date_created = convert(datetime,@date_created)

set @company = 'THC'

	select z_tmp_occupancy_asof.real_property_code,date_asof,
	occupancy_date,
	total_units_for_rent,
	total_complimentary,
	total_units_occupied = case when total_units_occupied > total_units_for_rent then total_units_for_rent else total_units_occupied end,
	total_vacant_units = case when total_vacant_units < 0 then 0 else total_vacant_units end,
	case when total_units_occupied=0 then 0 else room_income end as room_income,
	date_created,created_by,

	(total_units_occupied/(case when (total_units_for_rent-total_complimentary)=0 then 1 
		else (total_units_for_rent-total_complimentary) end)) * 100.0 as occupancy_rate,

	case when total_units_occupied=0 then 0 else (room_income/(case when total_units_occupied=0 then 1 else total_units_occupied end)) * 1.0 end as averate_rate,
	case when isnull(m_real_property.real_property_name,'') = '' then 'OVERALL' 
	when isnull(m_real_property.real_property_code,'') = 'FT A' then 'FINASISU TERRACES A & B 1BR' 
	when isnull(m_real_property.real_property_code,'') = 'FT B' then 'FINASISU TERRACES A & B 2BR' 
	else upper(m_real_property.real_property_name) end as real_property_name,
	upper(s_company.or_company_name) as or_company_name,
	upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
	s_company.or_company_contact_no1,s_company.or_company_contact_no2,'dba ' + s_company.dba_name as dba_name,
	upper(s_settings.prepared_by) as prepared_by,
	rpt_remarks = case when ltrim(rtrim(isnull(z_tmp_occupancy_asof.remarks,''))) = '' then '' else 'NOTE: ' + char(13) + z_tmp_occupancy_asof.remarks end,
	rg_report_title
	from z_tmp_occupancy_asof 
	left join m_real_property on z_tmp_occupancy_asof.real_property_code = m_real_property.real_property_code
	left join s_company on s_company.company_code = @company
	left join s_settings on s_company.company_code = s_company.company_code
	where 
	z_tmp_occupancy_asof.date_created =  @tmp_date_created
	and  z_tmp_occupancy_asof.created_by = @uid and isnull(z_tmp_occupancy_asof.total_units_for_rent,0) > 0
	order by seq,z_tmp_occupancy_asof.real_property_code,occupancy_date

GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_Log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_Log]
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@remarks nvarchar(4000),
	@date_generated datetime,
	@user_id varchar(50)
AS

	delete from rg_occupancy 
		where convert(datetime,convert(varchar(12),date_from)) = convert(datetime,convert(varchar(12),@date_from))
		and convert(datetime,convert(varchar(12),date_to)) = convert(datetime,convert(varchar(12),@date_to)) 
		and real_property_code = @real_property_code 
		and convert(datetime,convert(varchar(12),date_generated)) = convert(datetime,convert(varchar(12),@date_generated)) 
		and generated_by = @user_id
	
	insert into rg_occupancy(date_from,date_to,real_property_code,remarks,generated_by,date_generated)
	select @date_from,@date_to,@real_property_code,@remarks,@user_id,@date_generated
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_Old]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_Old]
	@date_from datetime,
	@date_to datetime,
	@date_created datetime,
	@uid varchar(50)
AS
--cut off every saturday of the week

	declare @real_property_code char(5),@tmp_date_from datetime,@tmp_date_to datetime,@rental_charge_code char(5),@day_diff int,@seq int
	declare @rg_date_from datetime,@rg_date_to datetime

	set @rg_date_from = @date_from
	set @rg_date_to = @date_to
	set @tmp_date_from = @date_from
	set @tmp_date_to = @date_to
	select top 1 @rental_charge_code = apt_rental_charge from s_settings

	delete from z_tmp_occupancy where created_by = @uid

	declare xxx cursor scroll for 
	select real_property_code from m_real_property where upper(ltrim(rtrim(isnull(space_type,'A')))) = 'A'  and real_property_code <> 'LNHSE'
	order by real_property_name
	set @seq = 1
	open xxx
	fetch next from xxx into @real_property_code
	while @@fetch_status = 0
		begin
			if datepart(dw,@date_from) <> 7 and @date_from <> @date_to
			begin
				set @day_diff = 7 - (datepart(dw,@date_from))
				set @date_from = dateadd(day,@day_diff,@date_from)
				if @date_from > @date_to
					set @date_from = @date_to				
			end
			while @date_from <= @date_to
				begin
--print @date_from
					declare @total_created_units decimal(9,0),@total_deleted_units decimal(9,0),@total_reserved_units decimal(9,0),@total_not_reserved_units decimal(9,0)
					declare @total_units_for_rent decimal(9,0),@occupied decimal(9,0),@vacant decimal(9,0)
					declare @total_units_occupied decimal(9,0),@total_units_vacant decimal(9,0),@room_income decimal(18,6),
					@total_occupied_amount decimal(18,6),@total_vacated_amount decimal(18,6),
					@total_occupied_byanother_amount decimal(18,6),@total_vacated_byoneof_amount decimal(18,6)

					set @occupied = 0
					set @vacant = 0
					set @room_income = 0
					set @total_occupied_amount = 0
					set @total_vacated_amount = 0
					set @total_occupied_byanother_amount = 0
					set @total_vacated_byoneof_amount = 0
					set @total_reserved_units = 0
					set @total_not_reserved_units = 0

				
					if @real_property_code not in ('FT A','FT B')
						begin
							select @total_reserved_units = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_Y'
		
							select @total_not_reserved_units = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_N'

							select @total_created_units = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'CREATED'
		
							select @total_deleted_units = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'DELETED'

							select @occupied = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @vacant = count(*)
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
												
							select @total_occupied_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @total_occupied_byanother_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED BY ANOTHER TENANT'
		
							select @total_vacated_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
		
							select @total_vacated_byoneof_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where  real_property_code = @real_property_code
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED BY ONE OF THE TENANTS'
		
							set @room_income = (isnull(@total_occupied_amount,0) + isnull(@total_occupied_byanother_amount,0)) - 
									(isnull(@total_vacated_amount,0) + isnull(@total_vacated_byoneof_amount,0))
		--PRINT @occupied
							--set @total_reserved_units = 0
							--set @total_not_reserved_units = 0
							set @total_units_for_rent = isnull(@total_created_units,0) - ((isnull(@total_deleted_units,0)+isnull(@total_reserved_units,0) )-isnull(@total_not_reserved_units,0))
							set @total_units_occupied = isnull(@occupied,0) - isnull(@vacant,0)
							set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
						end

					else if @real_property_code ='FT A'
						begin
							select @total_reserved_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_Y'
		
							select @total_not_reserved_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_N'

							select @total_created_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'CREATED'
		
							select @total_deleted_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'DELETED'

							select @occupied = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @vacant = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
												
							select @total_occupied_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @total_occupied_byanother_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED BY ANOTHER TENANT'
		
							select @total_vacated_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
		
							select @total_vacated_byoneof_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 1)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED BY ONE OF THE TENANTS'
		
							set @room_income = (isnull(@total_occupied_amount,0) + isnull(@total_occupied_byanother_amount,0)) - 
									(isnull(@total_vacated_amount,0) + isnull(@total_vacated_byoneof_amount,0))
		
							--set @total_reserved_units = 0
							--set @total_not_reserved_units = 0
							set @total_units_for_rent = isnull(@total_created_units,0) - ((isnull(@total_deleted_units,0)+isnull(@total_reserved_units,0) )-isnull(@total_not_reserved_units,0))
							set @total_units_occupied = isnull(@occupied,0) - isnull(@vacant,0)
							set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
						end

					else if @real_property_code ='FT B'
						begin
							select @total_reserved_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_Y'
		
							select @total_not_reserved_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'RESERVED_N'

							select @total_created_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'CREATED'
		
							select @total_deleted_units = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'DELETED'

							select @occupied = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @vacant = count(*)
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
												
							select @total_occupied_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED'
		
							select @total_occupied_byanother_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'OCCUPIED BY ANOTHER TENANT'
		
							select @total_vacated_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED'
		
							select @total_vacated_byoneof_amount = sum(isnull(charge_amount,0))  
							from m_units_movement
							where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and unit_no in (select unit_no from m_units where ( real_property_code = 'FT A' or real_property_code = 'FT B') 
							and isnull(no_of_bedrooms,0) = 2)
							and event_date < dateadd(day,1,@date_from) and upper(ltrim(rtrim(event_action))) = 'VACATED BY ONE OF THE TENANTS'
		
							set @room_income = (isnull(@total_occupied_amount,0) + isnull(@total_occupied_byanother_amount,0)) - 
									(isnull(@total_vacated_amount,0) + isnull(@total_vacated_byoneof_amount,0))
		
							--set @total_reserved_units = 0
							--set @total_not_reserved_units = 0
							set @total_units_for_rent = isnull(@total_created_units,0) - ((isnull(@total_deleted_units,0)+isnull(@total_reserved_units,0) )-isnull(@total_not_reserved_units,0))
							set @total_units_occupied = isnull(@occupied,0) - isnull(@vacant,0)
							set @total_units_vacant = isnull(@total_units_for_rent,0) - isnull(@total_units_occupied,0)
						end
			
					set @seq = @seq + 1
	
					insert into z_tmp_occupancy (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,total_units_occupied,total_vacant_units,room_income,
						date_created,created_by,seq,remarks)
					select @real_property_code,@tmp_date_from,@date_to,@date_from,@total_units_for_rent,@total_units_occupied,@total_units_vacant,isnull(@room_income,0),@date_created,@uid,@seq,
						isnull((select top 1 remarks from rg_occupancy where date_from = @rg_date_from and date_to = @rg_date_to and real_property_code = @real_property_code 
						and generated_by = @uid and date_generated = @date_created order by tran_date desc),'')
					
					/*select top 1 remarks from rg_occupancy where date_from = @rg_date_from and date_to = @rg_date_to and real_property_code = @real_property_code 
						and generated_by = @uid and date_generated = @date_created order by tran_date desc*/
					set @day_diff = 0
					if @date_from = @date_to						
						set @day_diff = 1

					set @date_from = dateadd(day,7,@date_from)	
					
					if @date_from > @date_to and @day_diff = 0
						set @date_from = @date_to	

				end

			set @date_from = @tmp_date_from
			set @date_to = @tmp_date_to
			fetch next from xxx into @real_property_code
		end

	close xxx
	deallocate xxx

	insert into z_tmp_occupancy (real_property_code,date_from,date_to,occupancy_date,total_units_for_rent,total_units_occupied,total_vacant_units,room_income,date_created,created_by,seq)
	select 'OVERALL',date_from,date_to,occupancy_date,sum(total_units_for_rent),sum(total_units_occupied),sum(total_vacant_units),sum(room_income),
	date_created,created_by,1
	from z_tmp_occupancy where date_created = @date_created and created_by = @uid 
	group by date_from,date_to,occupancy_date,date_created,created_by
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Occupancy_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO



CREATE PROCEDURE [dbo].[sp_rpt_Occupancy_View]
	@date_from varchar(10),
	@date_to varchar(10),
	@date_created varchar(20),
	@uid varchar(50)
AS
declare @company char(5)
declare @tmp_date_from datetime,@tmp_date_to datetime,@tmp_date_created datetime

set @tmp_date_from = convert(datetime,@date_from)
set @tmp_date_to = convert(datetime,@date_to)
set @tmp_date_created = convert(datetime,@date_created)

set @company = 'THC'

	select z_tmp_occupancy.real_property_code,date_from ,date_to ,
	occupancy_date,
	total_units_for_rent,
	total_complimentary,
	total_units_occupied = case when total_units_occupied > total_units_for_rent then total_units_for_rent else total_units_occupied end,
	total_vacant_units = case when total_vacant_units < 0 then 0 else total_vacant_units end,
	case when total_units_occupied=0 then 0 else room_income end as room_income,
	date_created,created_by,
	
	(total_units_occupied/(case when (total_units_for_rent-total_complimentary)<=0 then 1 else (total_units_for_rent-total_complimentary) end)) 
		* 100.0 as occupancy_rate,
	
	case when total_units_occupied=0 then 0 
		else (room_income/(case when total_units_occupied=0 then 1 else total_units_occupied end)) * 1.0 
	end as averate_rate,
	
	case when isnull(m_real_property.real_property_name,'') = '' then 'OVERALL' 
	when isnull(m_real_property.real_property_code,'') = 'FT A' then 'FINASISU TERRACES A & B 1BR' 
	when isnull(m_real_property.real_property_code,'') = 'FT B' then 'FINASISU TERRACES A & B 2BR' 
	else upper(m_real_property.real_property_name) end as real_property_name,
	upper(s_company.or_company_name) as or_company_name,
	upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
	s_company.or_company_contact_no1,s_company.or_company_contact_no2,'dba ' + s_company.dba_name as dba_name,
	upper(s_settings.prepared_by) as prepared_by,
	rpt_remarks = case when ltrim(rtrim(isnull(z_tmp_occupancy.remarks,''))) = '' then '' else 'NOTE: ' + char(13) + z_tmp_occupancy.remarks end
	from z_tmp_occupancy 
	left join m_real_property on z_tmp_occupancy.real_property_code = m_real_property.real_property_code
	left join s_company on s_company.company_code = @company
	left join s_settings on s_company.company_code = s_company.company_code
	where 
	z_tmp_occupancy.date_created =  @tmp_date_created
	and  z_tmp_occupancy.created_by = @uid and isnull(z_tmp_occupancy.total_units_for_rent,0) > 0
	order by seq,z_tmp_occupancy.real_property_code,occupancy_date
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_Advance_Payment_PaymentMode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_Advance_Payment_PaymentMode]
--	@or_no varchar(20)
AS
declare @company_code char(5)

set @company_code = 'THC'

	select t_ar_header_payment_mode.*,
	case when payment_mode_type = '1' then 'CASH'  when payment_mode_type = '2' then 'CHARGE' else 'CHECK' end as payment_mode_type_desc,
	upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by
	from t_ar_header_payment_mode
	left join t_ar_header on t_ar_header_payment_mode.or_no = t_ar_header.or_no
	left join s_users on t_ar_header.updated_by = s_users.[user_id]
	order by t_ar_header_payment_mode.or_no,payment_mode_type
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_AdvancePayment]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_AdvancePayment]
	@or_no varchar(20),
	@or_date_from varchar(10),
	@or_date_to varchar(10)
AS
declare @company char(5),@cnt int

set @company = 'THC'

set @or_no = upper(ltrim(rtrim(isnull(@or_no,''))))
set @or_date_from = upper(ltrim(rtrim(isnull(@or_date_from,''))))
set @or_date_to = upper(ltrim(rtrim(isnull(@or_date_to,''))))

select @cnt = isnull(count(*),0) from t_ar_detail where upper(ltrim(rtrim(or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'

if @or_date_from = '' and @or_date_to = ''
	begin
		select t_ar_header.*,t_ar_header.amount as or_amount,
			upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_ar_detail.ar_detail_id,			
			upper(charge_desc) as charge_desc,						
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_settings.prepared_by) as prepared_by,@cnt as detail_cnt
		from t_ar_header
		left join m_tenant client on t_ar_header.client_code = client.tenant_code
		left join t_ar_detail on t_ar_header.or_no = t_ar_detail.or_no
		left join m_charges on t_ar_detail.charge_code = m_charges.charge_code
		left join s_company on s_company.company_code = @company
		left join s_settings on s_company.company_code = s_company.company_code
		where upper(ltrim(rtrim(t_ar_header.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'
		--order by t_payment_detail.invoice_no,tenant_name,charge_desc
	end

else if @or_date_from <> '' and @or_date_to <> ''
	begin
		select t_ar_header.*,t_ar_header.amount as or_amount,
			upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_ar_detail.ar_detail_id,			
			upper(charge_desc) as charge_desc,						
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_settings.prepared_by) as prepared_by,@cnt as detail_cnt
		from t_ar_header
		left join m_tenant client on t_ar_header.client_code = client.tenant_code
		left join t_ar_detail on t_ar_header.or_no = t_ar_detail.or_no
		left join m_charges on t_ar_detail.charge_code = m_charges.charge_code
		left join s_company on s_company.company_code = @company
		left join s_settings on s_company.company_code = s_company.company_code
		where upper(ltrim(rtrim(t_ar_header.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'
		and t_ar_header.or_date >= convert(datetime,@or_date_from) and t_ar_header.or_date <= convert(datetime,@or_date_to) 
		--order by t_payment_detail.invoice_no,tenant_name,charge_desc
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_Listing]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_Listing]
	@or_date_from varchar(10),
	@or_date_to varchar(10)
AS
declare @company char(5)

set @company = 'THC'

set @or_date_from = upper(ltrim(rtrim(isnull(@or_date_from,''))))
set @or_date_to = upper(ltrim(rtrim(isnull(@or_date_to,''))))

if @or_date_from = '' and @or_date_to = ''
	begin
		select * from
		(
		select t_payment_header.or_no,or_date, t_payment_header.client_code,tenant_name,
		t_payment_detail.invoice_no,isnull(t_payment_detail.or_amount,0)as or_amount,t_payment_header.remarks,t_payment_header.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		1 as trans_type
		from t_payment_header 
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_payment_detail.or_amount,0) > 0

		union 	

		select t_ar_header.or_no,or_date, t_ar_header.client_code,tenant_name,
		'' as invoice_no,isnull(t_ar_header.amount,0) as or_amount,t_ar_header.remarks,t_ar_header.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		2 as  trans_type
		from t_ar_header 
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_ar_header.amount,0) > 0

		union

		select t_security_deposit.or_no,or_date, t_security_deposit.client_code,tenant_name,
		'' as invoice_no,isnull(t_security_deposit_detail.amount,0)as or_amount,t_security_deposit.remarks,t_security_deposit.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		1 as trans_type
		from t_security_deposit 
		left join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_security_deposit_detail.amount,0) > 0

		) a

		order by or_no,invoice_no	
	end

else if @or_date_from <> '' and @or_date_to <> ''
	begin
		select * from
		(
		select t_payment_header.or_no,or_date, t_payment_header.client_code,tenant_name,
		t_payment_detail.invoice_no,isnull(t_payment_detail.or_amount,0)as or_amount,t_payment_header.remarks,t_payment_header.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		1 as trans_type
		from t_payment_header 
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_payment_detail.or_amount,0) > 0

		union 	

		select t_ar_header.or_no,or_date, t_ar_header.client_code,tenant_name,
		'' as invoice_no,isnull(t_ar_header.amount,0) as or_amount,t_ar_header.remarks,t_ar_header.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		2 as  trans_type
		from t_ar_header 
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_ar_header.amount,0) > 0

		union

		select t_security_deposit.or_no,or_date, t_security_deposit.client_code,tenant_name,
		'' as invoice_no,isnull(t_security_deposit_detail.amount,0)as or_amount,t_security_deposit.remarks,t_security_deposit.updated_by,
		upper(s_company.or_company_name) as or_company_name,
		upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
		s_company.or_company_contact_no1,s_company.or_company_contact_no2,
		1 as trans_type
		from t_security_deposit 
		left join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code
		left join s_company on s_company.company_code = @company
		where upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and isnull(t_security_deposit_detail.amount,0) > 0

		) a

		where or_date >= convert(datetime,@or_date_from) and or_date <= convert(datetime,@or_date_to) 
		order by or_no,invoice_no	
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_SecurityDeposit]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_SecurityDeposit]
	@or_no varchar(20),
	@or_date_from varchar(10),
	@or_date_to varchar(10)
AS
declare @company char(5)

set @company = 'THC'


set @or_no = upper(ltrim(rtrim(isnull(@or_no,''))))
set @or_date_from = upper(ltrim(rtrim(isnull(@or_date_from,''))))
set @or_date_to = upper(ltrim(rtrim(isnull(@or_date_to,''))))

if @or_date_from = '' and @or_date_to = ''
	begin
		select t_security_deposit.*,upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_security_deposit_detail.detail_id,t_security_deposit_detail.real_property_code,t_security_deposit_detail.building_code,t_security_deposit_detail.unit_no,
			t_security_deposit_detail.amount as unit_amount,
			upper(client.tenant_name) as client_name,upper(real_property_name) as real_property_name,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,'PAYMENT FOR SECURITY DEPOSIT. ' + t_security_deposit.remarks as final_remarks
		from t_security_deposit
		left join m_tenant client on t_security_deposit.client_code = client.tenant_code
		inner join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no
		left join m_real_property on t_security_deposit_detail.real_property_code = m_real_property.real_property_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_security_deposit.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_security_deposit.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'		
	end

else if @or_date_from <> '' and @or_date_to <> ''
	begin
		select t_security_deposit.*,upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_security_deposit_detail.detail_id,t_security_deposit_detail.real_property_code,t_security_deposit_detail.building_code,t_security_deposit_detail.unit_no,
			t_security_deposit_detail.amount as unit_amount,
			upper(client.tenant_name) as client_name,upper(real_property_name) as real_property_name,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,'PAYMENT FOR SECURITY DEPOSIT. ' + t_security_deposit.remarks as final_remarks
		from t_security_deposit
		left join m_tenant client on t_security_deposit.client_code = client.tenant_code
		inner join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no
		left join m_real_property on t_security_deposit_detail.real_property_code = m_real_property.real_property_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_security_deposit.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_security_deposit.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'
		and t_security_deposit.or_date >= convert(datetime,@or_date_from) and t_security_deposit.or_date <= convert(datetime,@or_date_to) 
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_SecurityDeposit_PaymentMode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_SecurityDeposit_PaymentMode]
--	@or_no varchar(20)
AS
declare @company_code char(5),@prepared_by varchar(100)

set @company_code = 'THC'

	select t_security_deposit_payment_mode.*,
	case when payment_mode_type = '1' then 'CASH'  when payment_mode_type = '2' then 'CHARGE' else 'CHECK' end as payment_mode_type_desc,
	upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by
	from t_security_deposit_payment_mode
	left join t_security_deposit on t_security_deposit_payment_mode.or_no = t_security_deposit.or_no
	left join s_users on t_security_deposit.updated_by = s_users.[user_id]
	order by t_security_deposit_payment_mode.or_no,payment_mode_type
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_UnpaidBills]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_UnpaidBills]
	@or_no varchar(20),
	@or_date_from varchar(10),
	@or_date_to varchar(10)
AS
declare @company char(5)

set @company = 'THC'


set @or_no = upper(ltrim(rtrim(isnull(@or_no,''))))
set @or_date_from = upper(ltrim(rtrim(isnull(@or_date_from,''))))
set @or_date_to = upper(ltrim(rtrim(isnull(@or_date_to,''))))

if @or_date_from = '' and @or_date_to = ''
	begin
		select 'U' as trans_type,t_payment_header.or_no,or_date,t_payment_header.remarks,
			upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_payment_detail.payment_detail_id,
			t_payment_detail.invoice_no,t_payment_detail.invoice_detail_id,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,isnull(t_payment_detail.or_amount,0) as or_amount,
			case when t_invoice_detail.tenant_code = t_payment_header.client_code then '' 
			else upper(m_tenant.tenant_name + ' ('+ ltrim(rtrim(m_tenant.real_property_code)) + '/' + ltrim(rtrim(m_tenant.building_code)) + '/' + ltrim(rtrim(m_tenant.unit_no)) + ')'
			) end as tenant_name,
			upper(charge_desc) as charge_desc,
			isnull(t_invoice_detail.paid_amount,0) - isnull(t_payment_detail.or_amount,0) as paid_amount,
			t_invoice_header.invoice_date,
			(isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) + isnull(t_payment_detail.or_amount,0) as balance_amount,
			case when t_payment_header.mode_of_payment = '1' then 'CASH'  when t_payment_header.mode_of_payment = '2' then 'CHARGE' else 'CHECK' end as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,
			1 as ctr
		from t_payment_header
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join t_invoice_detail on t_payment_detail.invoice_no = t_payment_detail.invoice_no
			and t_payment_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id 
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_invoice_header on t_invoice_detail.invoice_no = t_invoice_header.invoice_no
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_payment_detail.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'
		--order by t_payment_detail.invoice_no,tenant_name,charge_desc

		union

		select top 1 'A' as trans_type,t_payment_header.or_no,t_payment_header.or_date,t_payment_header.remarks,
		upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			0 as payment_detail_id,
			'' as invoice_no,0 as invoice_detail_id,
			0 as total_charge_amount,isnull(t_ar_header.amount,0) as or_amount,
			'' as tenant_name,
			'ADVANCE PAYMENT' as charge_desc,
			0 as paid_amount,
			null as invoice_date,
			0 as balance_amount,
			'' as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,2 as ctr
		from t_ar_header 
		inner join t_payment_header on t_ar_header.or_no = t_payment_header.or_no
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_ar_header.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%' and isnull(t_ar_header.trans_type,'') = 'U'

		union

		select top 1 'S' as trans_type,t_payment_header.or_no,t_payment_header.or_date,t_payment_header.remarks,
		upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			0 as payment_detail_id,
			'' as invoice_no,0 as invoice_detail_id,
			0 as total_charge_amount,isnull(t_security_deposit_detail.amount,0) as or_amount,
			'UNIT ' + ltrim(rtrim(t_security_deposit_detail.real_property_code)) + '/' + ltrim(rtrim(t_security_deposit_detail.building_code)) + '/' + ltrim(rtrim(t_security_deposit_detail.unit_no)) as tenant_name,
			'SECURITY DEPOSIT' as charge_desc,
			0 as paid_amount,
			null as invoice_date,
			0 as balance_amount,
			'' as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,3 as ctr
		from t_security_deposit
		left join t_security_deposit_detail on  t_security_deposit_detail.or_no = t_security_deposit.or_no
		left join t_payment_header on t_security_deposit_detail.or_no = t_payment_header.or_no
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_security_deposit.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%' and isnull(t_security_deposit.trans_type,'') = 'U'

		order by ctr
	end

else if @or_date_from <> '' and @or_date_to <> ''
	begin
		select 'U' as trans_type,t_payment_header.or_no,t_payment_header.or_date,t_payment_header.remarks,
			upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			t_payment_detail.payment_detail_id,
			t_payment_detail.invoice_no,t_payment_detail.invoice_detail_id,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,isnull(t_payment_detail.or_amount,0) as or_amount,
			upper(m_tenant.tenant_name) as tenant_name,upper(charge_desc) as charge_desc,
			isnull(t_invoice_detail.paid_amount,0) - isnull(t_payment_detail.or_amount,0) as paid_amount,
			t_invoice_header.invoice_date,
			(isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) + isnull(t_payment_detail.or_amount,0) as balance_amount,
			case when t_payment_header.mode_of_payment = '1' then 'CASH'  when t_payment_header.mode_of_payment = '2' then 'CHARGE' else 'CHECK' end as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,1 as ctr
		from t_payment_header
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join t_invoice_detail on t_payment_detail.invoice_no = t_payment_detail.invoice_no
			and t_payment_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id 
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_invoice_header on t_invoice_detail.invoice_no = t_invoice_header.invoice_no
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_payment_detail.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%'
		and t_payment_header.or_date >= convert(datetime,@or_date_from) and t_payment_header.or_date <= convert(datetime,@or_date_to) 
		
		union

		select top 1 'A' as trans_type,t_payment_header.or_no,t_payment_header.or_date,t_payment_header.remarks,
		upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			0 as payment_detail_id,
			'' as invoice_no,0 as invoice_detail_id,
			0 as total_charge_amount,isnull(t_ar_header.amount,0) as or_amount,
			'' as tenant_name,
			'ADVANCE PAYMENT' as charge_desc,
			0 as paid_amount,
			null as invoice_date,
			0 as balance_amount,
			'' as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,2 as ctr
		from t_ar_header 
		inner join t_payment_header on t_ar_header.or_no = t_payment_header.or_no
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_ar_header.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%' and isnull(t_ar_header.trans_type,'') = 'U'
		and t_payment_header.or_date >= convert(datetime,@or_date_from) and t_payment_header.or_date <= convert(datetime,@or_date_to) 

		union

		select top 1 'S' as trans_type,t_payment_header.or_no,t_payment_header.or_date,t_payment_header.remarks,
		upper(client.tenant_name) as client_name,upper(client.sap_code) as client_customer_no,
			0 as payment_detail_id,
			'' as invoice_no,0 as invoice_detail_id,
			0 as total_charge_amount,isnull(t_security_deposit_detail.amount,0) as or_amount,
			'UNIT ' + ltrim(rtrim(t_security_deposit_detail.real_property_code)) + '/' + ltrim(rtrim(t_security_deposit_detail.building_code)) + '/' + ltrim(rtrim(t_security_deposit_detail.unit_no)) as tenant_name,
			'SECURITY DEPOSIT' as charge_desc,
			0 as paid_amount,
			null as invoice_date,
			0 as balance_amount,
			'' as mode_of_payment_desc,
			upper(s_company.or_company_name) as or_company_name,
			upper(case when isnull(s_company.or_company_address1,'') = '' then '' else s_company.or_company_address1 + ', ' end + s_company.or_company_address2) as or_company_address,
			s_company.or_company_contact_no1,s_company.or_company_contact_no2,
			upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by,3 as ctr
		from t_security_deposit
		left join t_security_deposit_detail on  t_security_deposit_detail.or_no = t_security_deposit.or_no
		left join t_payment_header on t_security_deposit_detail.or_no = t_payment_header.or_no
		left join m_tenant client on t_payment_header.client_code = client.tenant_code
		left join s_company on s_company.company_code = @company
		left join s_users on t_payment_header.updated_by = s_users.[user_id]
		where upper(ltrim(rtrim(t_security_deposit.or_no))) like '%' + upper(ltrim(rtrim(@or_no))) + '%' and isnull(t_security_deposit.trans_type,'') = 'U'
		and t_payment_header.or_date >= convert(datetime,@or_date_from) and t_payment_header.or_date <= convert(datetime,@or_date_to) 

		order by ctr
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_OR_UnpaidBills_PaymentMode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_OR_UnpaidBills_PaymentMode]
--	@or_no varchar(20)
AS
declare @company_code char(5),@prepared_by varchar(100)

set @company_code = 'THC'

	select t_payment_header_payment_mode.*,
	case when payment_mode_type = '1' then 'CASH'  when payment_mode_type = '2' then 'CHARGE' else 'CHECK' end as payment_mode_type_desc,
	upper(s_users.first_name + ' ' + s_users.last_name) as prepared_by
	from t_payment_header_payment_mode
	left join t_payment_header on t_payment_header_payment_mode.or_no = t_payment_header.or_no
	left join s_users on t_payment_header.updated_by = s_users.[user_id]
	order by t_payment_header_payment_mode.or_no,payment_mode_type
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_RealProperty]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_RealProperty]
AS

declare @company_code char(5)

	set @company_code = 'THC'

	select m_real_property.*,
	case when upper(ltrim(rtrim(space_type))) = 'A' then 'APARTMENT'
	when upper(ltrim(rtrim(space_type))) = 'O' then 'OFFICE'
	when upper(ltrim(rtrim(space_type))) = 'W' then 'WAREHOUSE'
	end as space_type_desc,
	s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2
	from m_real_property
	left join s_company on m_real_property.company_code = s_company.company_code
	where s_company.company_code = @company_code
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_SOA_NewBalance]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_SOA_NewBalance]
	@client_code char(10),
	@date_from varchar(10),
	@date_to varchar(10),
	@uid varchar(50),
	@date_generated varchar(50)
AS

set @client_code = upper(ltrim(rtrim(isnull(@client_code,''))))
set @date_from = upper(ltrim(rtrim(isnull(@date_from,''))))
set @date_to = upper(ltrim(rtrim(isnull(@date_to,''))))

	
	insert into z_tmp_soa (company_name,company_address,company_contact1,company_contact2,
			client_code,client_name,sap_code,address1,address2,date_from,date_to,activity_code,activity_desc,
			ref_no,ref_date,ref_detail_id,
			tenant_code,tenant_name,charge_code,charge_desc,charge_amount,
			date_generated,generated_by)	
	select s_company.or_company_name,s_company.address1 + ' ' + s_company.address2 as company_address,
			isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,''),
			isnull(' FAX: ' + s_company.fax_no,''),
			t_invoice_header.client_code, client.tenant_name as client_name,t_invoice_header.sap_code,client.address1,client.address2,@date_from,@date_to,1,'NEW BALANCE DETAILS',
			t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_detail.invoice_detail_id,
			t_invoice_detail.tenant_code,m_tenant.tenant_name,t_invoice_detail.charge_code,m_charges.charge_desc,isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,
			@date_generated,@uid
	from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no 		
		left join m_tenant client on t_invoice_header.client_code = client.tenant_code 
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code 
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code 
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code 
		left join s_company on m_real_property.company_code = s_company.company_code 
		left join s_settings on s_company.company_code = s_settings.company_code 
	where datediff(month,billing_from,convert(datetime,@date_to)) = 0 and billing_from <= convert(datetime,@date_to) and billing_from >= convert(datetime,@date_from)
	and t_invoice_header.client_code like upper(ltrim(rtrim(@client_code))) + '%'
	and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
	and isnull(t_invoice_detail.total_charge_amount,0) > 0
	order by t_invoice_header.invoice_date desc,t_invoice_header.invoice_no desc
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_SOA_Payments]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_SOA_Payments]
	@client_code char(10),
	@date_from varchar(10),
	@date_to varchar(10),
	@uid varchar(50),
	@date_generated varchar(50)
AS
declare @prev_date datetime,@date_to_tmp datetime,@company char(5)

set @company = 'THC'
set @client_code = upper(ltrim(rtrim(isnull(@client_code,''))))
set @date_from = upper(ltrim(rtrim(isnull(@date_from,''))))
set @date_to = upper(ltrim(rtrim(isnull(@date_to,''))))
set @date_to_tmp = convert(datetime,@date_to)
set @prev_date = convert(datetime,convert(char(2),datepart(month,@date_to_tmp)) + '/01/' + convert(varchar(4),datepart(year,@date_to_tmp)))

	insert into z_tmp_soa (company_name,company_address,company_contact1,company_contact2,
			client_code,client_name,sap_code,address1,address2,date_from,date_to,activity_code,activity_desc,
			ref_no,ref_date,ref_detail_id,ref_no2,
			tenant_code,tenant_name,charge_code,charge_desc,charge_amount,
			date_generated,generated_by)	
	select  upper(s_company.or_company_name),s_company.address1 + ' ' + s_company.address2 as company_address,
			isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,''),
			isnull(' FAX: ' + s_company.fax_no,''),
		t_payment_header.client_code,upper(client.tenant_name),upper(client.sap_code),client.address1,client.address2,@date_from,@date_to,3,'PAYMENT DETAILS',
		t_payment_header.or_no + ' (INV#: '+t_payment_detail.invoice_no+')',t_payment_header.or_date,t_payment_detail.payment_detail_id,t_payment_detail.invoice_no,
		t_invoice_detail.tenant_code,m_tenant.tenant_name,t_invoice_detail.charge_code,m_charges.charge_desc,(t_payment_detail.or_amount * -1.0) as or_amount,
		@date_generated,@uid
	from t_payment_header
	left join m_tenant client on t_payment_header.client_code = client.tenant_code
	left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
	left join t_invoice_detail on t_payment_detail.invoice_no = t_payment_detail.invoice_no
		and t_payment_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id 
	left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no
		and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
	left join t_invoice_header on t_invoice_detail.invoice_no = t_invoice_header.invoice_no
	left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
	left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
	left join s_company on s_company.company_code = @company
	--where t_payment_header.or_date < @prev_date and t_payment_header.or_date >= convert(datetime,@date_from)
	where t_payment_header.or_date <= convert(datetime,@date_to)
	and t_payment_header.client_code like upper(ltrim(rtrim(@client_code))) + '%'
	and upper(ltrim(rtrim(isnull(t_payment_header.status,'')))) <> 'V'
	and isnull(t_payment_detail.or_amount,0) > 0 and isnull(t_invoice_detail.paid_amount,0) < isnull(t_invoice_detail.total_charge_amount,0)
	order by t_payment_header.or_date desc,t_payment_header.or_no desc
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_SOA_PrevBalance]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_SOA_PrevBalance]
	@client_code char(10),
	@date_from varchar(10),
	@date_to varchar(10),
	@uid varchar(50),
	@date_generated varchar(50)
AS
declare @prev_date datetime,@date_to_tmp datetime

set @client_code = upper(ltrim(rtrim(isnull(@client_code,''))))
set @date_from = upper(ltrim(rtrim(isnull(@date_from,''))))
set @date_to = upper(ltrim(rtrim(isnull(@date_to,''))))
set @date_to_tmp = convert(datetime,@date_to)
set @prev_date = convert(datetime,convert(char(2),datepart(month,@date_to_tmp)) + '/01/' + convert(varchar(4),datepart(year,@date_to_tmp)))

	
	insert into z_tmp_soa (company_name,company_address,company_contact1,company_contact2,
			client_code,client_name,sap_code,address1,address2,date_from,date_to,activity_code,activity_desc,
			ref_no,ref_date,ref_detail_id,
			tenant_code,tenant_name,charge_code,charge_desc,charge_amount,
			date_generated,generated_by)	
	select upper(s_company.or_company_name),s_company.address1 + ' ' + s_company.address2 as company_address,
			isnull('TEL. : ' +s_company.contact_no1,'') +  isnull(' ' +s_company.contact_no2,''),
			isnull(' FAX: ' + s_company.fax_no,''),
			t_invoice_header.client_code, client.tenant_name as client_name,t_invoice_header.sap_code,client.address1,client.address2,@date_from,@date_to,2,'BALANCE DETAILS',
			t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_detail.invoice_detail_id,
			t_invoice_detail.tenant_code,m_tenant.tenant_name,t_invoice_detail.charge_code,m_charges.charge_desc,isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,
			@date_generated,@uid				
	from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no 		
		left join m_tenant client on t_invoice_header.client_code = client.tenant_code 
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code 
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code 
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code 
		left join s_company on m_real_property.company_code = s_company.company_code 
		left join s_settings on s_company.company_code = s_settings.company_code 
	--where billing_from < @prev_date and billing_from >= convert(datetime,@date_from)
	where t_invoice_header.invoice_date <= convert(datetime,@date_to)
	and t_invoice_header.client_code like upper(ltrim(rtrim(@client_code))) + '%'
	and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
	and isnull(t_invoice_detail.total_charge_amount,0) > 0 and isnull(t_invoice_detail.paid_amount,0) < isnull( t_invoice_detail.total_charge_amount,0)
	order by t_invoice_header.invoice_date desc,t_invoice_header.invoice_no desc
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_StatementOfAccount]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_StatementOfAccount]
	@client_code char(10),
	@date_from varchar(10),
	@date_to varchar(10),
	@uid varchar(50),
	@date_generated varchar(50)
AS

set @client_code = upper(ltrim(rtrim(isnull(@client_code,''))))
set @date_from = upper(ltrim(rtrim(isnull(@date_from,''))))
set @date_to = upper(ltrim(rtrim(isnull(@date_to,''))))

declare @company_code char(5)

set @company_code = 'THC'
		
	select z_tmp_soa.rec_id,
	z_tmp_soa.company_name,
	z_tmp_soa.company_address,
	z_tmp_soa.company_contact1,
	z_tmp_soa.company_contact2,
	z_tmp_soa.client_code,
	z_tmp_soa.client_name,
	z_tmp_soa.sap_code,
	z_tmp_soa.address1,
	z_tmp_soa.address2,
	z_tmp_soa.date_from,
	z_tmp_soa.date_to,
	z_tmp_soa.activity_code,
	z_tmp_soa.activity_desc,
	z_tmp_soa.ref_no,
	z_tmp_soa.ref_date,
	z_tmp_soa.ref_detail_id,
	z_tmp_soa.ref_no2,
	z_tmp_soa.tenant_code,
	case when z_tmp_soa.client_code = z_tmp_soa.tenant_code then '' else z_tmp_soa.tenant_name end as tenant_name,
	z_tmp_soa.charge_code,
	z_tmp_soa.charge_desc,
	z_tmp_soa.charge_amount,
	isnull(aging_current,0) as aging_current,
	isnull(aging_30,0) as aging_30,
	isnull(aging_60,0) as aging_60,
	isnull(aging_90,0) as aging_90,
	isnull(aging_over,0) as aging_over,
	z_tmp_soa.date_generated,
	z_tmp_soa.generated_by,
	isnull(new_balance.new_balance_total,0) as new_balance_total,
	isnull(prev_balance.prev_balance_total,0) as prev_balance_total,
	isnull(prev_payments.prev_payments_total,0) as prev_payments_total,
	isnull(advance_payment.sum_advance_payment_amount,0) as advance_payment_total_balance
	from z_tmp_soa
	left join m_tenant on z_tmp_soa.client_code = m_tenant.tenant_code
	left join 
		(select client_code,sum(charge_amount) as new_balance_total from z_tmp_soa where activity_code = 1
		and generated_by = @uid and date_generated = @date_generated
		group by client_code
		) new_balance on z_tmp_soa.client_code = new_balance.client_code
	left join 
		(select client_code,sum(charge_amount) as prev_balance_total from z_tmp_soa where activity_code = 2
		and generated_by = @uid and date_generated = @date_generated
		group by client_code
		) prev_balance on z_tmp_soa.client_code = prev_balance.client_code
	left join 
		(select client_code,sum(charge_amount) as prev_payments_total from z_tmp_soa where activity_code = 3
		and generated_by = @uid and date_generated = @date_generated
		group by client_code
		) prev_payments on z_tmp_soa.client_code = prev_payments.client_code
	left join 
		(select t_ar_header.client_code,sum(isnull(t_ar_header.amount,0)) - sum(isnull(t_payment_detail.sum_deducted_amount,0)) as sum_advance_payment_amount from t_ar_header
			left join 
			(
			select or_no,sum(or_amount) as sum_deducted_amount from t_payment_detail 		
			group by or_no
			) t_payment_detail on t_ar_header.or_no = t_payment_detail.or_no
		group by client_code
		) advance_payment on z_tmp_soa.client_code = advance_payment.client_code
	where 
	(upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'')))) = 'C' or upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'')))) = 'OC')
	and z_tmp_soa.client_code like upper(ltrim(rtrim(@client_code))) + '%'
	and generated_by = @uid and date_generated = @date_generated
	order by z_tmp_soa.client_name,ref_date desc,ref_no desc
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_StatementOfAccountProc]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_StatementOfAccountProc]
	@client_code char(10),
	@date_from varchar(10),
	@date_to varchar(10),
	@uid varchar(50),
	@date_generated varchar(50)
AS

set @client_code = upper(ltrim(rtrim(isnull(@client_code,''))))
set @date_from = upper(ltrim(rtrim(isnull(@date_from,''))))
set @date_to = upper(ltrim(rtrim(isnull(@date_to,''))))

declare @company_code char(5)

set @company_code = 'THC'

	delete from z_tmp_soa where datediff(minute,date_generated,getdate()) > 15 or date_generated = @date_generated

	--exec sp_rpt_SOA_NewBalance @client_code,@date_from,@date_to,@uid,@date_generated
	exec sp_rpt_SOA_PrevBalance @client_code,@date_from,@date_to,@uid,@date_generated
	exec sp_rpt_SOA_Payments @client_code,@date_from,@date_to,@uid,@date_generated

	declare xxx cursor scroll for
	select distinct client_code from z_tmp_soa
	where generated_by = @uid and date_generated = @date_generated		

	declare @tmp_client_code varchar(20)

	open xxx
	fetch next from xxx into @tmp_client_code
	while @@fetch_status = 0
		begin
			declare @aging_current decimal(18,2),@aging_30 decimal(18,2), @aging_60 decimal(18,2), @aging_90 decimal(18,2), @aging_over decimal(18,2)

			set @aging_current = 0
			set @aging_30 = 0
			set @aging_60 = 0
			set @aging_90 = 0
			set @aging_over = 0

			select @aging_current = sum(isnull(charge_amount,0))  from z_tmp_soa
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) <= 30

			select @aging_30 = sum(isnull(charge_amount,0))  from z_tmp_soa
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) >= 31 
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) <=60

			select @aging_60 = sum(isnull(charge_amount,0))  from z_tmp_soa
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) >= 61 
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) <=90

			select @aging_90 = sum(isnull(charge_amount,0))  from z_tmp_soa
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) >= 91 
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) <=120

			select @aging_over = sum(isnull(charge_amount,0))  from z_tmp_soa
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code
			and datediff(day,convert(datetime,ref_date,101),convert(datetime,@date_to,101)) >= 121 

			set @aging_current = isnull(@aging_current,0)
			set @aging_30 = isnull(@aging_30,0)
			set @aging_60 = isnull(@aging_60,0)
			set @aging_90 = isnull(@aging_90,0)
			set @aging_over = isnull(@aging_over,0)

			update z_tmp_soa
			set aging_current = @aging_current,
			aging_30 = @aging_30, aging_60 = @aging_60, aging_90 = @aging_90, aging_over = @aging_over
			where generated_by = @uid and date_generated = @date_generated		
			and client_code = @tmp_client_code			

			fetch next from xxx into @tmp_client_code
		end
	close xxx
	deallocate xxx
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Tenant]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Tenant]
	@condition nvarchar(1000),
	@sortby nvarchar(500),
	@groupby nvarchar(100)
AS
--exec sp_rpt_Tenant 'actual_move_in_date >= ''06/01/2016''', 'ltrim(rtrim(isnull(m_tenant.real_property_code,''''))) , m_tenant.building_code , m_tenant.unit_no',''
declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_tenant]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_tenant]
	
	CREATE TABLE [dbo].[tmp_rg_tenant] (
		[tenant_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[tenant_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[real_property_code] [char] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[bill_to] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[contact_no1] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[contact_no2] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[address1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[address2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[contract_eff_date] [datetime] NULL ,
		[contract_expiry_date] [datetime] NULL ,
		[sap_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[terminated] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_terminated] [datetime] NULL ,
		[actual_move_in_date] [datetime] NULL ,
		[email_add] [varchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_affiliate_employee] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[employer] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[tenant_type] [char] (2) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[is_sap_affiliate] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[new_code] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[business_area] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[company_code] [varchar] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[last_meter_reading] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[security_deposit_amount] [decimal](18, 2) NULL ,
		[tenant_remarks] [varchar] (500) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_employee_benefit] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[employee_benefit_cc] [varchar] (7) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_notifications] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_created] [datetime] NULL ,
		[created_by] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[date_updated] [datetime] NULL ,
		[updated_by] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL 
	) ON [PRIMARY]

	set @ssql = ' insert into tmp_rg_tenant select tenant_code,tenant_name,
		m_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no,bill_to,m_tenant.contact_no1,m_tenant.contact_no2,m_tenant.address1,m_tenant.address2,contract_eff_date,
		contract_expiry_date,sap_code,	terminated,date_terminated,actual_move_in_date,email_add,is_affiliate_employee,employer,tenant_type,is_sap_affiliate,new_code,
		business_area,m_tenant.company_code,last_meter_reading,security_deposit_amount,tenant_remarks,is_employee_benefit,employee_benefit_cc,
		is_notifications,date_created,created_by,date_updated,updated_by,m_real_property.real_property_name, s_company.or_company_name, or_company_address1 + '', '' + or_company_address2 , or_company_contact_no1, or_company_contact_no2  '

	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 

	set @ssql = @ssql + ' from m_tenant
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join s_company on   m_tenant.company_code = s_company.company_code 
	where  s_company.company_code =  ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby 


	print @ssql
	exec sp_executesql @ssql

	select * from tmp_rg_tenant
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Tenant_Charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Tenant_Charges]
	@condition nvarchar(1000),
	@sortby nvarchar(500),
	@groupby nvarchar(100)
AS

declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_tenant_charges]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_tenant_charges]
	
	CREATE TABLE [dbo].[tmp_rg_tenant_charges] (
		[tenant_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[charge_code] [char] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[charge_amount] [decimal](18, 6) NULL ,
		[tenant_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[sap_code] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[real_property_code] [char] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[charge_desc] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[actual_move_in_date] [datetime] ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL 
	) ON [PRIMARY]


	set @ssql = 'insert into tmp_rg_tenant_charges select m_tenant_charges.*,m_tenant.tenant_name,m_tenant.sap_code,m_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no,
	m_real_property.real_property_name,m_charges.charge_desc,
	s_company.or_company_name,or_company_address1 + '', '' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2,m_tenant.actual_move_in_date '

	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 
	
	set @ssql = @ssql + ' from m_tenant_charges
	left join m_tenant on m_tenant_charges.tenant_code = m_tenant.tenant_code
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
	left join s_company on m_real_property.company_code = s_company.company_code
	where  s_company.company_code =  ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby 


	--print @ssql
	exec sp_executesql @ssql

	select * from tmp_rg_tenant_charges
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Tenant_Lease_Agreement]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Tenant_Lease_Agreement]
	@tenant_code varchar(20)	
AS

declare @company_code char(5), @apt_rent decimal(9,2),@water_charge decimal(9,2)

	set @company_code = 'THC'

	select @apt_rent = sum(isnull(charge_amount,0)) from m_tenant_charges where tenant_code = @tenant_code and charge_code in
	(select top 1 apt_rental_charge from s_settings)

	select @water_charge = sum(isnull(charge_amount,0)) from m_tenant_charges where tenant_code = @tenant_code and charge_code in
	(select top 1 water_rental_charge from s_settings)

	select top 1  m_tenant.*,
	upper(m_real_property.real_property_dba_name)  as real_property_dba_name,upper(m_real_property.address3) as real_property_address3,
	case when (datediff(month,m_tenant.contract_eff_date,m_tenant.contract_expiry_date) + 1) % 12 = 0 then 
	(datediff(year,m_tenant.contract_eff_date,m_tenant.contract_expiry_date)+1) else (datediff(month,m_tenant.contract_eff_date,m_tenant.contract_expiry_date)+1) end as lease_term,
	case when (datediff(month,m_tenant.contract_eff_date,m_tenant.contract_expiry_date)+1) % 12 = 0 then 
	'YR/S' else 'MO/S' end as lease_term_unit,isnull(@apt_rent,0) as apt_rent_amount,isnull(@water_charge,0) as water_charge,
	m_units.meter_number,
	s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2,
	bill_to.tenant_name as bill_to_name
	from m_tenant
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join m_units on m_tenant.real_property_code = m_units.real_property_code
		and m_tenant.building_code = m_units.building_code
		and m_tenant.unit_no = m_units.unit_no
	left join s_company on m_real_property.company_code = s_company.company_code
	left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code
	where s_company.company_code = @company_code
	and m_tenant.tenant_code = @tenant_code
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Tenant_Reading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_rpt_Tenant_Reading]
	@condition nvarchar(1000)
	,@sortby nvarchar(500)=NULL
	,@groupby nvarchar(500)=NULL
AS

declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_tenant_reading]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_tenant_reading]
	
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
	) ON [PRIMARY]

	set @ssql = ' insert into tmp_rg_tenant_reading select t_tenant_reading.*,t_tenant_reading_charges.trc_invoice_no as invoice_no,
	t_tenant_reading_charges.trc_invoice_detail_id as invoice_detail_id,
	m_tenant.tenant_name,m_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no,
	m_real_property.real_property_name,m_charges.charge_desc,
	s_company.or_company_name,or_company_address1 + '', '' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2 '

	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 
	
	set @ssql = @ssql + ' from t_tenant_reading
	left join t_tenant_reading_charges on t_tenant_reading.reading_id = trc_reading_id
	left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join m_charges on t_tenant_reading_charges.trc_charge_code = m_charges.charge_code
	left join s_company on m_real_property.company_code = s_company.company_code
	where  s_company.company_code =  ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby --+ ',date_from,date_to '
	/*else
		set @ssql = @ssql +  ' order by date_from,date_to '*/


	print @ssql
	exec sp_executesql @ssql

	select * from tmp_rg_tenant_reading
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymenNotice_AlertListSent]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymenNotice_AlertListSent]

AS
	declare @wth_hdr_id decimal(18,0)

	select top 1 @wth_hdr_id = wth_hdr_id from w_tenant_aging_header where (isnull(wth_email_sent,0) = 1 or isnull(wth_email_sent_final,0) = 1)
	and wth_hdr_id in (select top 1 wta_hdr_id from w_tenant_aging_detail order by wta_date_email_sent desc)
	and isnull(wth_summary_sent,0) = 0
	order by wth_as_of desc

	update w_tenant_aging_header set wth_summary_sent = 1 where wth_hdr_id = @wth_hdr_id

	select * from w_tenant_aging_header
	left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
	where wth_hdr_id = @wth_hdr_id
	--and wta_email_sent = 1
	AND (ISNULL(w_tenant_aging_detail.wta_notice_no, '') <> '')
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice]
	@dtAsOf varchar(10),
	@dtNotice varchar(10),
	@dtPaymentDue varchar(10)
AS

declare @company_code char(5),@months varchar(100),@dtMonth1 datetime,@dtMonth2 datetime
declare @strMonth1 varchar(20),@strMonth2 varchar(20),@dtAsOfTemp datetime,@dtPaymentDueTemp datetime
declare @dtNoticeTemp datetime

set @company_code = 'THC'

set @dtAsOfTemp = convert(datetime,@dtAsOf) 
set @dtMonth2 = @dtAsOfTemp
set @dtMonth1 = dateadd(month,-1,@dtMonth2)
set @strMonth1 = dbo.fn_GetMonthName(datepart(month,@dtMonth1))
set @strMonth2 = dbo.fn_GetMonthName(datepart(month,@dtMonth2))
set @dtNoticeTemp = convert(datetime,@dtNotice) 

set @dtPaymentDueTemp = convert(datetime,@dtPaymentDue) 

if datepart(year,@dtMonth1) = datepart(year,@dtMonth2)
	set @months = @strMonth1 + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))
else
	set @months = @strMonth1 + ' ' + convert(varchar(4), datepart(year,@dtMonth1)) + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))

	select dtNotice = upper(dbo.fn_GetMonthName(datepart(month,@dtNoticeTemp)) + ' ' + convert(varchar(2),datepart(day,@dtNoticeTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtNoticeTemp))), dtPaymentDue = @dtPaymentDue ,
	dtPaymentDueWords = dbo.fn_GetMonthName(datepart(month,@dtPaymentDueTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPaymentDueTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPaymentDueTemp)),
	dtAsOfWords = dbo.fn_GetMonthName(datepart(month,wth_as_of)) + ' ' + convert(varchar(2),datepart(day,wth_as_of)) + ', ' +
	convert(varchar(4), datepart(year,wth_as_of)),
	months = @months,
	w_tenant_aging_header.*,
	w_tenant_aging_detail.*,
	/*wta_rec_id,wta_hdr_id,wta_sap_code,wta_tenant_name,wta_real_property_name,
	wta_total_balance,
	wta_curr_balance,
	wta_total_overdue = convert(decimal(18,2),wta_total_overdue),
	wta_aging_1_30 = convert(decimal(18,2),wta_aging_1_30),
	wta_aging_31_60 = convert(decimal(18,2),wta_aging_31_60),
	wta_aging_61_90 = convert(decimal(18,2),wta_aging_61_90),
	wta_aging_91_120 = convert(decimal(18,2),wta_aging_91_120),
	wta_aging_121_150 = convert(decimal(18,2),wta_aging_121_150),
	wta_aging_over_151 = convert(decimal(18,2),wta_aging_over_151),
	wta_write_off,wta_remarks,*/
	--bal_amount = convert(decimal(18,2),wta_curr_balance) + convert(decimal(18,2),wta_aging_1_30),
	--bal_amount = convert(decimal(18,2),wta_aging_1_30) + convert(decimal(18,2),wta_aging_31_60),
	--bal_amount = isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0),
	bal_amount = isnull(wta_curr_balance,0) + isnull(wta_aging_1_30,0) +isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)+ isnull(wta_aging_91_120,0)+ isnull(wta_aging_121_150,0)+ 
		isnull(wta_aging_over_151,0),
	s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	or_company_contact_no1, or_company_contact_no2 
	from w_tenant_aging_header
	left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
	left join s_company on  s_company.company_code = @company_code
	where wta_rec_id in (select rec_id from z_tmp_tenantforpaymentnotice_tenant)
	and s_company.company_code =  @company_code
	and wth_as_of = convert(datetime,@dtAsOf) 
	/*and (isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)) > 0
	and isnull(wta_aging_91_120,0) <= 0
	and isnull(wta_aging_121_150,0) <= 0
	and isnull(wta_aging_over_151,0) <= 0*/
	and wta_rec_id in (select wta_rec_id from w_tenant_aging_detail where ltrim(rtrim(isnull(wta_notice_no,''))) = '1')
	order by wta_real_property_name,wta_tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice_Alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice_Alert]
	@rec_id varchar(10),
	--@wta_rec_id decimal(18,0),
	@dtNotice varchar(10),
	@dtPaymentDue varchar(10)
AS

declare @company_code char(5),@months varchar(100),@dtMonth1 datetime,@dtMonth2 datetime
declare @strMonth1 varchar(20),@strMonth2 varchar(20),@dtAsOfTemp datetime,@dtPaymentDueTemp datetime
declare @dtNoticeTemp datetime
declare @dtAsOf varchar(10),@wta_rec_id decimal(18,0)

set @company_code = 'THC'

set @wta_rec_id = convert(decimal(18,0),@rec_id)
select  top 1 @dtAsOf =  convert(varchar(2),datepart(month,wth_as_of)) + '/' + convert(varchar(2),datepart(day,wth_as_of)) + '/' +
	convert(varchar(4), datepart(year,wth_as_of)) from w_tenant_aging_header
	where wth_hdr_id in (select wta_hdr_id from w_tenant_aging_detail where wta_rec_id = @wta_rec_id)
	order by wth_as_of desc


set @dtAsOfTemp = convert(datetime,@dtAsOf) 
set @dtMonth2 = @dtAsOfTemp
set @dtMonth1 = dateadd(month,-1,@dtMonth2)
set @strMonth1 = dbo.fn_GetMonthName(datepart(month,@dtMonth1))
set @strMonth2 = dbo.fn_GetMonthName(datepart(month,@dtMonth2))
set @dtNoticeTemp = convert(datetime,@dtNotice) 

--set @dtPaymentDueTemp = convert(datetime,@dtPaymentDue) 
set @dtPaymentDueTemp = dateadd(day,7,convert(datetime,@dtNoticeTemp))

if datepart(year,@dtMonth1) = datepart(year,@dtMonth2)
	set @months = @strMonth1 + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))
else
	set @months = @strMonth1 + ' ' + convert(varchar(4), datepart(year,@dtMonth1)) + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))

	select top 1 dtNotice = upper(dbo.fn_GetMonthName(datepart(month,@dtNoticeTemp)) + ' ' + convert(varchar(2),datepart(day,@dtNoticeTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtNoticeTemp))), dtPaymentDue = @dtPaymentDue ,
	dtPaymentDueWords = dbo.fn_GetMonthName(datepart(month,@dtPaymentDueTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPaymentDueTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPaymentDueTemp)),
	dtAsOfWords = dbo.fn_GetMonthName(datepart(month,wth_as_of)) + ' ' + convert(varchar(2),datepart(day,wth_as_of)) + ', ' +
	convert(varchar(4), datepart(year,wth_as_of)),
	months = @months,
	w_tenant_aging_header.*,
	--w_tenant_aging_detail.*,
	wta_rec_id,wta_hdr_id,wta_sap_code,wta_tenant_name,-- = tenant_name,
	wta_real_property_name,
	wta_total_balance,wta_curr_balance,wta_total_overdue,wta_aging_1_30,wta_aging_31_60,
	wta_aging_61_90,wta_aging_91_120,wta_aging_121_150,
	wta_aging_over_151,wta_write_off,wta_remarks,
	--bal_amount = isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0),
	bal_amount = isnull(wta_curr_balance,0) + isnull(wta_aging_1_30,0) +isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)+ isnull(wta_aging_91_120,0)+ isnull(wta_aging_121_150,0)+ 
		isnull(wta_aging_over_151,0),
	s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	or_company_contact_no1, or_company_contact_no2 
	from w_tenant_aging_header
	left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
	--left join m_tenant on wta_sap_code = sap_code and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
	left join (select distinct sap_code,tenant_type,email_add from m_tenant where ltrim(rtrim(isnull(terminated,'N'))) <> 'Y')m_tenant on wta_sap_code = sap_code
		and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
	left join s_company on  s_company.company_code = @company_code
	where wta_rec_id = @wta_rec_id
	and s_company.company_code =  @company_code
	and wth_as_of = convert(datetime,@dtAsOf) 
	/*and (isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)) > 0
	and isnull(wta_aging_91_120,0) <= 0
	and isnull(wta_aging_121_150,0) <= 0
	and isnull(wta_aging_over_151,0) <= 0*/
	and wta_rec_id in (select wta_rec_id from w_tenant_aging_detail where ltrim(rtrim(isnull(wta_notice_no,''))) = '1')
	order by wta_real_property_name,wta_tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice_AlertSave]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice_AlertSave]
	
AS
declare @wth_as_of datetime,@wth_hdr_id decimal(18,0)

	delete from z_tmp_tenantforpaymentnotice_tenant

	if exists(select top 1 * from w_tenant_aging_header where isnull(wth_email_sent,0) = 0 order by wth_as_of desc)
		begin	
			select top 1 @wth_hdr_id = wth_hdr_id,@wth_as_of = isnull(wth_as_of,'1/1/1900') from w_tenant_aging_header where isnull(wth_email_sent,0) = 0 order by wth_as_of desc

			insert into z_tmp_tenantforpaymentnotice_tenant
			select wta_rec_id from w_tenant_aging_header
			left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
			where wth_as_of = @wth_as_of and wth_hdr_id = @wth_hdr_id
			/*and (isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)) > 0
			and isnull(wta_aging_91_120,0) <= 0
			and isnull(wta_aging_121_150,0) <= 0
			and isnull(wta_aging_over_151,0) <= 0*/
			and wta_rec_id in (select wta_rec_id from w_tenant_aging_detail where ltrim(rtrim(isnull(wta_notice_no,''))) = '1')

			update w_tenant_aging_header set wth_email_sent = 1 where wth_hdr_id = @wth_hdr_id
		end

		SELECT  distinct wta_rec_id,tenant_name = wta_tenant_name,
			email_add,
			--email_add =(select top 1 sea_rms_email from s_email_alert),
			--email_add = 'resalie_garcia@tanholdings.com',
			--email_add = 'res_garcia@yahoo.com',
			tenant_code='',wta_real_property_name,
			--email_add_bcc = 'resalie_garcia@tanholdings.com'
			--email_add_bcc = ''
			email_add_bcc =(select top 1 sea_rms_email from s_email_alert)
		FROM        z_tmp_tenantforpaymentnotice_tenant c
		left join  w_tenant_aging_detail a on wta_rec_id = c.rec_id
		--left join m_tenant on wta_sap_code = sap_code
		left join (select distinct sap_code,tenant_type,email_add from m_tenant where ltrim(rtrim(isnull(terminated,'N'))) <> 'Y')
			m_tenant on wta_sap_code = sap_code
		and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
		where isnull(email_add,'') <> ''
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '1'
		order by wta_real_property_name,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice_AlertUpdate]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice_AlertUpdate]
	@wta_rec_id decimal(18,0),
	@email_add varchar(100)
AS

	update w_tenant_aging_detail set wta_email_sent = 1, wta_date_email_sent = getdate() ,wta_email_add = @email_add
	where wta_rec_id = @wta_rec_id
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO




CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice_Save]
	@strMode varchar(20),
	@tenant_code varchar(10)

AS

if @strMode = 'DELETE'
	begin
		delete from z_tmp_tenantforpaymentnotice_tenant
	end

if @strMode = 'SAVE'
	begin
		insert into z_tmp_tenantforpaymentnotice_tenant
		select @tenant_code
	end


GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNotice_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNotice_Search]
	@date_as_of datetime,
	@sort_by varchar(30),
	@search_value varchar(100)
AS

if @sort_by = 'TENANT'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_tenant_name like '%' + @search_value + '%'
		/*and (
		(isnull(wta_aging_31_60,0) + isnull(wta_aging_61_90,0)) > 0
		and isnull(wta_aging_91_120,0) <= 0
		and isnull(wta_aging_121_150,0) <= 0
		and isnull(wta_aging_over_151,0) <= 0
		)*/ -- and wta_rec_id=103
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '1'
		order by wta_tenant_name
	end

else if @sort_by = 'REAL PROPERTY'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_real_property_name like '%' + @search_value + '%'
		/*and (
		(isnull(wta_aging_31_60,0) + isnull(wta_aging_61_90,0)) > 0
		and isnull(wta_aging_91_120,0) <= 0
		and isnull(wta_aging_121_150,0) <= 0
		and isnull(wta_aging_over_151,0) <= 0
		)*/
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '1'
		order by wta_real_property_name
	end

else if @sort_by = 'SAP ACCOUNT CODE'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_sap_code like '%' + @search_value + '%'
		/*and (
		(isnull(wta_aging_31_60,0) + isnull(wta_aging_61_90,0)) > 0
		and isnull(wta_aging_91_120,0) <= 0
		and isnull(wta_aging_121_150,0) <= 0
		and isnull(wta_aging_over_151,0) <= 0
		)*/
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '1'
		order by wta_sap_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNoticeFinal]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO




CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNoticeFinal]
	@dtAsOf varchar(10),
	@dtNotice varchar(10),
	@dtPastDueAccount varchar(10),	
	@dtPaymentDue varchar(10)
AS

declare @company_code char(5),@months varchar(100),@dtMonth1 datetime,@dtMonth2 datetime
declare @strMonth1 varchar(20),@strMonth2 varchar(20),@dtAsOfTemp datetime,@dtPaymentDueTemp datetime
declare @dtNoticeTemp datetime,@dtPastDueAccountTemp datetime

set @company_code = 'THC'

set @dtAsOfTemp = convert(datetime,@dtAsOf) 
set @dtMonth2 = @dtAsOfTemp
set @dtMonth1 = dateadd(month,-3,@dtMonth2)
set @strMonth1 = dbo.fn_GetMonthName(datepart(month,@dtMonth1))
set @strMonth2 = dbo.fn_GetMonthName(datepart(month,@dtMonth2))
set @dtNoticeTemp = convert(datetime,@dtNotice) 

set @dtPastDueAccountTemp = convert(datetime,@dtPastDueAccount) 
set @dtPaymentDueTemp = convert(datetime,@dtPaymentDue) 

if datepart(year,@dtMonth1) = datepart(year,@dtMonth2)
	set @months = @strMonth1 + ' to ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))
else
	set @months = @strMonth1 + ' ' + convert(varchar(4), datepart(year,@dtMonth1)) + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))

	select dtNotice = upper(dbo.fn_GetMonthName(datepart(month,@dtNoticeTemp)) + ' ' + convert(varchar(2),datepart(day,@dtNoticeTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtNoticeTemp))), dtPaymentDue = @dtPaymentDue ,
	dtPastDueAccountWords = dbo.fn_GetMonthName(datepart(month,@dtPastDueAccountTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPastDueAccountTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPastDueAccountTemp)),
	dtPaymentDueWords = dbo.fn_GetMonthName(datepart(month,@dtPaymentDueTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPaymentDueTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPaymentDueTemp)),
	dtAsOfWords = dbo.fn_GetMonthName(datepart(month,wth_as_of)) + ' ' + convert(varchar(2),datepart(day,wth_as_of)) + ', ' +
	convert(varchar(4), datepart(year,wth_as_of)),
	months = @months,
	w_tenant_aging_header.*,w_tenant_aging_detail.*,
	--bal_amount = (isnull(wta_aging_121_150,0)+ isnull(wta_aging_over_151,0)),
	bal_amount = isnull(wta_curr_balance,0) + isnull(wta_aging_1_30,0) +isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)+ isnull(wta_aging_91_120,0)+ isnull(wta_aging_121_150,0)+ 
		isnull(wta_aging_over_151,0),
	s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	or_company_contact_no1, or_company_contact_no2 
	from w_tenant_aging_header
	left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
	left join s_company on  s_company.company_code = @company_code
	where wta_rec_id in (select rec_id from z_tmp_tenantforpaymentnoticefinal_tenant)
	and s_company.company_code =  @company_code
	and wth_as_of = convert(datetime,@dtAsOf)
	--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
	and wta_rec_id in (select wta_rec_id from w_tenant_aging_detail where ltrim(rtrim(isnull(wta_notice_no,''))) = '2')
	order by wta_real_property_name,wta_tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Alert]
	@rec_id varchar(10),
	@dtNotice varchar(10),
	@dtPaymentDue varchar(10)
AS

declare @company_code char(5),@months varchar(100),@dtMonth1 datetime,@dtMonth2 datetime
declare @strMonth1 varchar(20),@strMonth2 varchar(20),@dtAsOfTemp datetime,@dtPaymentDueTemp datetime
declare @dtNoticeTemp datetime,@dtPastDueAccountTemp datetime
declare @wta_rec_id decimal(18,0)
declare	@dtAsOf varchar(10),@dtPastDueAccount varchar(10)

set @company_code = 'THC'
set @wta_rec_id = convert(decimal(18,0),@rec_id)

select  top 1 @dtAsOf =  convert(varchar(2),datepart(month,wth_as_of)) + '/' + convert(varchar(2),datepart(day,wth_as_of)) + '/' +
	convert(varchar(4), datepart(year,wth_as_of)) from w_tenant_aging_header 
	where wth_hdr_id in (select wta_hdr_id from w_tenant_aging_detail where wta_rec_id = @wta_rec_id)
	order by wth_as_of desc

set @dtAsOfTemp = convert(datetime,@dtAsOf) 
set @dtMonth2 = @dtAsOfTemp
set @dtMonth1 = dateadd(month,-3,@dtMonth2)
set @strMonth1 = dbo.fn_GetMonthName(datepart(month,@dtMonth1))
set @strMonth2 = dbo.fn_GetMonthName(datepart(month,@dtMonth2))
set @dtNoticeTemp = convert(datetime,@dtNotice) 

--set @dtPastDueAccountTemp = convert(datetime,@dtPastDueAccount) 
select top 1 @dtPastDueAccountTemp = eal_date_time from  s_email_alert_log 
where eal_alert_type = 'FINAL PAYMENT NOTICE' and eal_detail_id = @wta_rec_id
and eal_date_time < convert(datetime,convert(varchar(12),getdate()))
order by eal_date_time desc

--set @dtPastDueAccountTemp = isnull(@dtPastDueAccountTemp,'1/1/1900')
set @dtPastDueAccountTemp = @dtAsOfTemp

--set @dtPaymentDueTemp = convert(datetime,@dtPaymentDue) 
set @dtPaymentDueTemp = dateadd(day,-1,convert(datetime,convert(varchar(2),month(DATEADD(month,1,@dtAsOfTemp))) + '/01/' + convert(varchar(4),year(@dtAsOfTemp))))

if datepart(year,@dtMonth1) = datepart(year,@dtMonth2)
	set @months = @strMonth1 + ' to ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))
else
	set @months = @strMonth1 + ' ' + convert(varchar(4), datepart(year,@dtMonth1)) + ' and ' + @strMonth2 + ' ' + convert(varchar(4), datepart(year,@dtMonth2))

	select dtNotice = upper(dbo.fn_GetMonthName(datepart(month,@dtNoticeTemp)) + ' ' + convert(varchar(2),datepart(day,@dtNoticeTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtNoticeTemp))), dtPaymentDue = @dtPaymentDue ,
	dtPastDueAccountWords = case when @dtPastDueAccountTemp = '1/1/1900' then '{N/A}' else dbo.fn_GetMonthName(datepart(month,@dtPastDueAccountTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPastDueAccountTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPastDueAccountTemp)) end,
	dtPaymentDueWords = dbo.fn_GetMonthName(datepart(month,@dtPaymentDueTemp)) + ' ' + convert(varchar(2),datepart(day,@dtPaymentDueTemp)) + ', ' +
	convert(varchar(4), datepart(year,@dtPaymentDueTemp)),
	dtAsOfWords = dbo.fn_GetMonthName(datepart(month,wth_as_of)) + ' ' + convert(varchar(2),datepart(day,wth_as_of)) + ', ' +
	convert(varchar(4), datepart(year,wth_as_of)),
	months = @months,
	w_tenant_aging_header.*,
	wta_rec_id,wta_hdr_id,wta_sap_code,wta_tenant_name,-- = tenant_name,
	wta_real_property_name,
	wta_total_balance,wta_curr_balance,wta_total_overdue,wta_aging_1_30,wta_aging_31_60,
	wta_aging_61_90,wta_aging_91_120,wta_aging_121_150,
	wta_aging_over_151,wta_write_off,wta_remarks,
	--bal_amount = (isnull(wta_aging_121_150,0)+ isnull(wta_aging_over_151,0)),
	bal_amount = isnull(wta_curr_balance,0) + isnull(wta_aging_1_30,0) +isnull(wta_aging_31_60,0)+ isnull(wta_aging_61_90,0)+ isnull(wta_aging_91_120,0)+ isnull(wta_aging_121_150,0)+ 
		isnull(wta_aging_over_151,0),
	s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	or_company_contact_no1, or_company_contact_no2 
	from w_tenant_aging_header
	left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
	--left join m_tenant on wta_sap_code = sap_code and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
	left join (select distinct sap_code,tenant_type,email_add from m_tenant)m_tenant on wta_sap_code = sap_code
		and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
	left join s_company on  s_company.company_code = @company_code
	where wta_rec_id = @wta_rec_id
	and s_company.company_code =  @company_code
	and wth_as_of = convert(datetime,@dtAsOf) 
	--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
	and wta_rec_id in (select wta_rec_id from w_tenant_aging_detail where ltrim(rtrim(isnull(wta_notice_no,''))) = '2')
	order by wta_real_property_name,wta_tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNoticeFinal_AlertSave]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO



CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNoticeFinal_AlertSave]
	
AS
declare @wth_as_of datetime,@wth_hdr_id decimal(18,0)

	delete from z_tmp_tenantforpaymentnoticefinal_tenant

	if exists(select top 1 * from w_tenant_aging_header where isnull(wth_email_sent_final,0) = 0 order by wth_as_of desc)
		begin	
			select top 1 @wth_hdr_id = wth_hdr_id,@wth_as_of = isnull(wth_as_of,'1/1/1900') from w_tenant_aging_header where isnull(wth_email_sent_final,0) = 0 order by wth_as_of desc

			insert into z_tmp_tenantforpaymentnoticefinal_tenant
			select wta_rec_id from w_tenant_aging_header
			left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
			where wth_as_of = @wth_as_of and wth_hdr_id = @wth_hdr_id
			--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
			and ltrim(rtrim(isnull(wta_notice_no,''))) = '2'

			update w_tenant_aging_header set wth_email_sent_final = 1 where wth_hdr_id = @wth_hdr_id
		end		

		SELECT  distinct wta_rec_id,tenant_name = wta_tenant_name,
			email_add,
			--email_add =(select top 1 sea_rms_email from s_email_alert),
			--email_add = 'res_garcia@yahoo.com',
			tenant_code='',wta_real_property_name,
			--email_add_bcc = ''
			--email_add_bcc = 'resalie_garcia@tanholdings.com'
			email_add_bcc =(select top 1 sea_rms_email from s_email_alert)
		FROM z_tmp_tenantforpaymentnoticefinal_tenant c
		left join  w_tenant_aging_detail a on wta_rec_id = c.rec_id
		left join (select distinct sap_code,tenant_type,email_add from m_tenant where ltrim(rtrim(isnull(terminated,'N'))) <> 'Y')m_tenant on wta_sap_code = sap_code
		and (LTRIM(RTRIM(isnull(tenant_type,'OC'))) in ('OC', 'C'))
		where isnull(email_add,'') <> ''
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '2'
		order by wta_real_property_name,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Save]
	@strMode varchar(20),
	@tenant_code varchar(10)

AS

if @strMode = 'DELETE'
	begin
		delete from z_tmp_tenantforpaymentnoticefinal_tenant
	end

if @strMode = 'SAVE'
	begin
		insert into z_tmp_tenantforpaymentnoticefinal_tenant
		select @tenant_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForPaymentNoticeFinal_Search]
	@date_as_of datetime,
	@sort_by varchar(30),
	@search_value varchar(100)
AS

if @sort_by = 'TENANT'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_tenant_name like '%' + @search_value + '%'
		--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '2'
		order by wta_tenant_name
	end

else if @sort_by = 'REAL PROPERTY'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_real_property_name like '%' + @search_value + '%'
		--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '2'
		order by wta_real_property_name
	end

else if @sort_by = 'SAP ACCOUNT CODE'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_sap_code like '%' + @search_value + '%'
		--and (isnull(wta_aging_121_150,0) + isnull(wta_aging_over_151,0)) > 0
		and ltrim(rtrim(isnull(wta_notice_no,''))) = '2'
		order by wta_sap_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewal]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewal]
	@date_from varchar(10),
	@date_to varchar(10)
AS

declare @company_code char(5)

set @company_code = 'THC'

	select m_tenant.*,real_property_name,
	 s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	 or_company_contact_no1, or_company_contact_no2 
	from m_tenant 
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join s_company on   m_tenant.company_code = s_company.company_code 
	where  s_company.company_code =  @company_code
	and isnull(terminated,'N') = 'N'
	and contract_expiry_date <= convert(datetime,@date_from) --and contract_expiry_date <= convert(datetime,@date_to)
	order by m_tenant.real_property_code,building_code,unit_no,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice]
	@dtNotice varchar(10),
	@date_from varchar(10),
	@date_to varchar(10)
AS

declare @company_code char(5)

set @company_code = 'THC'

	select dtNotice = @dtNotice,m_tenant.*,real_property_name,
	 s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	 or_company_contact_no1, or_company_contact_no2 
	from m_tenant 
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join s_company on   m_tenant.company_code = s_company.company_code 
	where tenant_code in (select tenant_code from z_tmp_tenantforrenewalnotice_tenant)
	and s_company.company_code =  @company_code
	and isnull(terminated,'N') = 'N'
	and contract_expiry_date >= convert(datetime,@date_from) and contract_expiry_date <= convert(datetime,@date_to)
	order by m_tenant.real_property_code,building_code,unit_no,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice_Alert]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice_Alert]
	@tenant_code char(10),
	@dtNotice varchar(10),
	@date_from varchar(10),
	@date_to varchar(10)
AS

declare @company_code char(5)

set @company_code = 'THC'

	select dtNotice = @dtNotice,m_tenant.*,real_property_name,
	 s_company.or_company_name,
	or_company_address =  or_company_address1 + ', ' + or_company_address2 ,
	 or_company_contact_no1, or_company_contact_no2 
	from m_tenant 
	left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
	left join s_company on   m_tenant.company_code = s_company.company_code 
	where tenant_code = @tenant_code
	and s_company.company_code =  @company_code
	and isnull(terminated,'N') = 'N'
	and contract_expiry_date >= convert(datetime,@date_from) and contract_expiry_date <= convert(datetime,@date_to)
	order by m_tenant.real_property_code,building_code,unit_no,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice_AlertList]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice_AlertList]
	@date_from varchar(10),
	@date_to varchar(10)
AS

	select a.tenant_code,tenant_name from z_tmp_tenantforrenewalnotice_tenant a
	left join m_tenant on a.tenant_code = m_tenant.tenant_code
	order by m_tenant.real_property_code,building_code,unit_no,tenant_name
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice_AlertSave]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice_AlertSave]
	@date_from varchar(10),
	@date_to varchar(10)
AS
declare @dt1 datetime,@dt2 datetime
set @dt1 = dateadd(month,1,dateadd(day,-5,convert(datetime,@date_from))) --sunday
set @dt2 = dateadd(month,1,dateadd(day,1,convert(datetime,@date_to))) -- saturday

	delete from z_tmp_tenantforrenewalnotice_tenant

	insert into z_tmp_tenantforrenewalnotice_tenant
	select tenant_code
	from m_tenant 
	where isnull(terminated,'N') = 'N'
	and isnull(is_notifications,'N') = 'Y'
	and contract_expiry_date >= @dt1 and contract_expiry_date <= convert(datetime,@dt2)

	select a. tenant_code,tenant_name,email_add from z_tmp_tenantforrenewalnotice_tenant a
	left join m_tenant on a.tenant_code = m_tenant.tenant_code
	order by m_tenant.real_property_code,building_code,unit_no,tenant_name

	--select @dt1,@dt2
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice_Save]
	@strMode varchar(20),
	@tenant_code varchar(10)

AS

if @strMode = 'DELETE'
	begin
		delete from z_tmp_tenantforrenewalnotice_tenant
	end

if @strMode = 'SAVE'
	begin
		insert into z_tmp_tenantforrenewalnotice_tenant
		select @tenant_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_TenantForRenewalNotice_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_TenantForRenewalNotice_Search]
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@keyword varchar(200)

AS
	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_tenantforrenewalnotice]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
		drop table [dbo].[z_tmp_tenantforrenewalnotice]

	create table z_tmp_tenantforrenewalnotice (tenant_code varchar(20),tenant_name varchar(100),real_property_code char(5),real_property_name varchar(100),building_code char(10),unit_no char(10))

	declare @xxx table (tenant_code varchar(20),tenant_name varchar(100),real_property_code char(5),real_property_name varchar(100),building_code char(10),unit_no char(10))

	if ltrim(rtrim(isnull(@real_property_code,''))) = ''
		begin
			insert into @xxx
			select tenant_code,tenant_name,m_tenant.real_property_code,real_property_name,m_tenant.building_code,m_tenant.unit_no
			from m_tenant 
			left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
			where isnull(terminated,'N') = 'N'
			and contract_expiry_date >= convert(datetime,@date_from) and contract_expiry_date <= convert(datetime,@date_to)
		end
	else
		begin
			insert into @xxx
			select tenant_code,tenant_name,m_tenant.real_property_code,real_property_name,m_tenant.building_code,m_tenant.unit_no
			from m_tenant 
			left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
			where isnull(terminated,'N') = 'N'
			and contract_expiry_date >= convert(datetime,@date_from) and contract_expiry_date <= convert(datetime,@date_to)
			and upper(ltrim(rtrim(m_tenant.real_property_code))) = @real_property_code 
		end

	if @sort_by = 'TENANT'
		insert into z_tmp_tenantforrenewalnotice
		select * from @xxx where tenant_name like '%' + @keyword + '%'
		order by tenant_name
	
	else if @sort_by = 'BUILDING CODE'
		insert into z_tmp_tenantforrenewalnotice
		select * from @xxx where building_code like '%' + @keyword + '%'
		order by building_code

	else if @sort_by = 'UNIT NO.'
		insert into z_tmp_tenantforrenewalnotice
		select * from @xxx where unit_no like '%' + @keyword + '%'
		order by unit_no

	if @sort_by = 'TENANT'
		select * from z_tmp_tenantforrenewalnotice
		order by tenant_name
	
	else if @sort_by = 'BUILDING CODE'
		select * from z_tmp_tenantforrenewalnotice
		order by building_code

	else if @sort_by = 'UNIT NO.'
		select * from z_tmp_tenantforrenewalnotice
		order by unit_no
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Unit_Charges]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Unit_Charges]
	@condition nvarchar(1000),
	@sortby nvarchar(500),
	@groupby nvarchar(100)
AS

declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_unit_charges]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_unit_charges]

	CREATE TABLE [dbo].[tmp_rg_unit_charges] (
		[real_property_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[charge_code] [char] (5) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[charge_amount] [decimal](18, 6) NOT NULL,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[charge_desc] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL 
	) ON [PRIMARY]

	set @ssql = 'insert into tmp_rg_unit_charges select m_unit_charges.*,
	m_real_property.real_property_name,
	m_charges.charge_desc,
	s_company.or_company_name,or_company_address1 + '', '' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2' 

	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 

	set @ssql = @ssql + ' from m_unit_charges
	left join m_real_property on m_unit_charges.real_property_code = m_real_property.real_property_code
	left join m_charges on m_unit_charges.charge_code = m_charges.charge_code
	left join s_company on m_real_property.company_code = s_company.company_code
	where s_company.company_code = ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	--if ltrim(rtrim(isnull(@groupby,''))) <> ''
		--set @ssql = @ssql +  ' group by ' +  @groupby

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby 


	print @ssql
	exec sp_executesql @ssql

	select * from tmp_rg_unit_charges
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Units]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_rpt_Units]
	@mode varchar(5)
AS
--exec sp_rpt_Units 'V'

declare @company_code char(5)

	set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_units]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_units]

	CREATE TABLE [dbo].[tmp_rg_units] (
		[real_property_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[lot_area] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[no_of_bedrooms] [int] NULL ,
		[unit_type] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_reserved] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_complimentary] [tinyint],
		[complimentary_date_from] [date],
		[remarks] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[meter_number] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL, 
		[status] [char] (15) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL 
	) ON [PRIMARY]

if @mode = 'R'
	begin
		insert into tmp_rg_units
		select m_units.*,status = 'RESERVED',
		upper(m_real_property.real_property_name),
		s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
		or_company_contact_no1,or_company_contact_no2
		from m_units
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		left join s_company on m_real_property.company_code = s_company.company_code
		where s_company.company_code = @company_code and isnull(is_reserved,'N') = 'Y'
		order by real_property_name,building_code,unit_no
	end

else if @mode = 'O'
	begin
		insert into tmp_rg_units
		select m_units.*,status = 'OCCUPIED',
		upper(m_real_property.real_property_name),
		s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
		or_company_contact_no1,or_company_contact_no2
		from m_units
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		left join s_company on m_real_property.company_code = s_company.company_code
		where s_company.company_code = @company_code 
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') <> 'Y'
			)
		order by real_property_name,building_code,unit_no
	end

else if @mode = 'V'
	begin
		insert into tmp_rg_units
		select m_units.*,status = 'VACANT',
		upper(m_real_property.real_property_name),
		s_company.or_company_name,or_company_address1 + ', ' + or_company_address2 as or_company_address,
		or_company_contact_no1,or_company_contact_no2
		from m_units
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		left join s_company on m_real_property.company_code = s_company.company_code
		where s_company.company_code = @company_code 
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where (upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O') and isnull(terminated,'N') <> 'Y'
			)
		order by real_property_name,building_code,unit_no
	end
	
	select * from tmp_rg_units
GO
/****** Object:  StoredProcedure [dbo].[sp_rpt_Units_Customized]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_rpt_Units_Customized]
	@condition nvarchar(1000),
	@sortby nvarchar(500),
	@groupby nvarchar(100)
AS

declare @company_code char(5),@ssql nvarchar(4000)

set @company_code = 'THC'

	if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tmp_rg_units]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
	drop table [dbo].[tmp_rg_units]

	CREATE TABLE [dbo].[tmp_rg_units] (
		[real_property_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[building_code] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[unit_no] [char] (10) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[lot_area] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[no_of_bedrooms] [int] NULL ,
		[unit_type] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[is_reserved] [char] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[remarks] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
		[meter_number] [varchar] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NULL, 
		[status] [char] (15) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[real_property_name] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_name] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_address] [varchar] (100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no1] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[or_company_contact_no2] [varchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
		[group_by] [varchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL 
	) ON [PRIMARY]

	set @ssql = 'insert into tmp_rg_units select m_units.*,'''',
	m_real_property.real_property_name,
	s_company.or_company_name,or_company_address1 + '', '' + or_company_address2 as or_company_address,
	or_company_contact_no1,or_company_contact_no2' 

	if ltrim(rtrim(isnull(@groupby,''))) <> ''
		set @ssql = @ssql + ',upper(' + @groupby + ')'
	else
		set @ssql = @ssql + ','''' ' 

	set @ssql = @ssql + ' from m_units
	left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
	left join s_company on m_real_property.company_code = s_company.company_code
	where s_company.company_code = ''' + @company_code + ''''

	if ltrim(rtrim(isnull(@condition,''))) <> ''
		set @ssql = @ssql + ' and ' + @condition 

	--if ltrim(rtrim(isnull(@groupby,''))) <> ''
		--set @ssql = @ssql +  ' group by ' +  @groupby

	if ltrim(rtrim(isnull(@sortby,''))) <> ''
		set @ssql = @ssql +  ' order by ' +  @sortby 


	print @ssql
	exec sp_executesql @ssql

	select * from tmp_rg_units
GO
/****** Object:  StoredProcedure [dbo].[sp_s_Configuration]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_Configuration]
	@strMode varchar(50),
	@company_code varchar(5),
	@company_name varchar(100),
	@dba_name varchar(100),
	@address1 varchar(50),
	@address2 varchar(50),
	@contact_no1 varchar(50),
	@contact_no2 varchar(50),
	@fax_no varchar(50),
	@or_company_name varchar(100),
	@or_company_address1 varchar(50),
	@or_company_address2 varchar(50),
	@or_company_contact_no1 varchar(50),
	@or_company_contact_no2 varchar(50),
	@prepared_by varchar(50),
	@approved_by varchar(50),
	@uid varchar(100),
	@ip_addr varchar(100)

AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'COMPANY'

if @strMode = 'SAVE'
	begin
		if not exists (select * from s_company where upper(ltrim(rtrim(company_code))) = upper(ltrim(rtrim(@company_code)))) 
			begin
				insert into s_company (company_code,company_name,dba_name,address1,address2,contact_no1,contact_no2,fax_no,or_company_name,or_company_address1,
					or_company_address2,or_company_contact_no1,or_company_contact_no2)
				select upper(ltrim(rtrim(@company_code))),upper(ltrim(rtrim(@company_name))),upper(ltrim(rtrim(@dba_name))),upper(ltrim(rtrim(@address1))),upper(ltrim(rtrim(@address2))),
					upper(ltrim(rtrim(@contact_no1))),upper(ltrim(rtrim(@contact_no2))),upper(ltrim(rtrim(@fax_no))),upper(ltrim(rtrim(@or_company_name))),upper(ltrim(rtrim(@or_company_address1))),
					upper(ltrim(rtrim(@or_company_address2))),upper(ltrim(rtrim(@or_company_contact_no1))),upper(ltrim(rtrim(@or_company_contact_no2)))

				set @data = 'insert into s_company (company_code,company_name,dba_name,address1,address2,contact_no1,contact_no2,fax_no,or_company_name,or_company_address1,
					or_company_address2,or_company_contact_no1,or_company_contact_no2) ' +
					'select ' + upper(ltrim(rtrim(@company_code)))+','+upper(ltrim(rtrim(@company_name)))+','+upper(ltrim(rtrim(@dba_name)))+','+upper(ltrim(rtrim(@address1)))+','+
					upper(ltrim(rtrim(@address2)))+','+upper(ltrim(rtrim(@contact_no1)))+','+upper(ltrim(rtrim(@contact_no2)))+','+upper(ltrim(rtrim(@fax_no)))+','+
					upper(ltrim(rtrim(@or_company_name)))+','+upper(ltrim(rtrim(@or_company_address1)))+','+upper(ltrim(rtrim(@or_company_address2)))+','+
					upper(ltrim(rtrim(@or_company_contact_no1)))+','+upper(ltrim(rtrim(@or_company_contact_no2)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update s_company set 
					company_code = upper(ltrim(rtrim(@company_code))),
					company_name = upper(ltrim(rtrim(@company_name))),
					dba_name = upper(ltrim(rtrim(@dba_name))),
					address1 = upper(ltrim(rtrim(@address1))),
					address2 = upper(ltrim(rtrim(@address2))),
					contact_no1 = upper(ltrim(rtrim(@contact_no1))),
					contact_no2 = upper(ltrim(rtrim(@contact_no2))),
					fax_no = upper(ltrim(rtrim(@fax_no))),
					or_company_name = upper(ltrim(rtrim(@or_company_name))),
					or_company_address1 = upper(ltrim(rtrim(@or_company_address1))),
					or_company_address2 = upper(ltrim(rtrim(@or_company_address2))),
					or_company_contact_no1 = upper(ltrim(rtrim(@or_company_contact_no1))),
					or_company_contact_no2 = upper(ltrim(rtrim(@or_company_contact_no2)))
				where upper(ltrim(rtrim(company_code))) = upper(ltrim(rtrim(@company_code)))

				set @data = 'update s_company set ' +
						'company_code = ' +upper(ltrim(rtrim(@company_code))) +','+
						'company_name =' + upper(ltrim(rtrim(@company_name)))+','+
						'dba_name = ' +upper(ltrim(rtrim(@dba_name)))+','+
						'address1 = ' +upper(ltrim(rtrim(@address1)))+','+
						'address2 = ' +upper(ltrim(rtrim(@address2)))+','+
						'contact_no1 =' + upper(ltrim(rtrim(@contact_no1)))+','+
						'contact_no2 =' + upper(ltrim(rtrim(@contact_no2)))+','+
						'fax_no =' + upper(ltrim(rtrim(@fax_no)))+','+
						'or_company_name =' + upper(ltrim(rtrim(@or_company_name)))+','+
						'or_company_address1 =' + upper(ltrim(rtrim(@or_company_address1)))+','+
						'or_company_address2 =' + upper(ltrim(rtrim(@or_company_address2)))+','+
						'or_company_contact_no1 =' + upper(ltrim(rtrim(@or_company_contact_no1)))+','+
						'or_company_contact_no2 =' + upper(ltrim(rtrim(@or_company_contact_no2)))+
						'where upper(ltrim(rtrim(company_code))) =' + upper(ltrim(rtrim(@company_code)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end

		set @module_name = 'SETTINGS'

		if not exists (select * from s_settings where upper(ltrim(rtrim(company_code))) = upper(ltrim(rtrim(@company_code)))) 
			begin
				insert into s_settings (company_code,prepared_by,approved_by)
				select upper(ltrim(rtrim(@company_code))),upper(ltrim(rtrim(@prepared_by))),upper(ltrim(rtrim(@approved_by)))

				set @data = 'insert into s_settings (company_code,prepared_by,approved_by) ' +
					'select ' + upper(ltrim(rtrim(@company_code)))+','+upper(ltrim(rtrim(@prepared_by)))+','+upper(ltrim(rtrim(@approved_by)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update s_settings set 
					prepared_by = upper(ltrim(rtrim(@prepared_by))),
					approved_by = upper(ltrim(rtrim(@approved_by)))
				where upper(ltrim(rtrim(company_code))) = upper(ltrim(rtrim(@company_code)))

				set @data = 'update s_settings set ' +
					'prepared_by ='+ upper(ltrim(rtrim(@prepared_by)))+','+
					'approved_by ='+ upper(ltrim(rtrim(@approved_by))) +
					'where upper(ltrim(rtrim(company_code))) ='+ upper(ltrim(rtrim(@company_code)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 s_company.*,s_settings.prepared_by,s_settings.approved_by
		from s_company 
		left join s_settings on s_company.company_code = s_settings.company_code
		where upper(ltrim(rtrim(s_company.company_code))) = upper(ltrim(rtrim(@company_code)))
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_s_EmailAlert_Log]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_s_EmailAlert_Log]
	@eal_date_time datetime,
	@eal_alert_type varchar(50),
	@eal_notice_no int,
	@eal_as_of datetime,
	@eal_sap_code varchar(50),
	@eal_detail_id decimal(18,0),
	@eal_tenant_code varchar(10),
	@eal_tenant_name varchar(100),
	@eal_email_add varchar(100),
	@eal_attachment varchar(200)
AS

	select top 1 @eal_as_of = wth_as_of from w_tenant_aging_header order by wth_as_of desc

	insert into s_email_alert_log (eal_date_time,eal_alert_type,eal_notice_no,eal_as_of,eal_sap_code,
		eal_detail_id,eal_tenant_code,eal_tenant_name,eal_email_add,eal_attachment)
	select @eal_date_time,@eal_alert_type,@eal_notice_no,@eal_as_of,@eal_sap_code,
		@eal_detail_id,tenant_code,@eal_tenant_name,@eal_email_add,@eal_attachment
	from m_tenant where sap_code = @eal_sap_code
GO
/****** Object:  StoredProcedure [dbo].[sp_s_EventLog]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_EventLog]
	@module_name nvarchar(50),
	@user_name nvarchar(50),
	@ip_addr nvarchar(50),
	@data nvarchar(max),
	@db_action nvarchar(50),
	@company_code nvarchar(5)
AS

	insert into s_event_log (event_date,module_name,[user_name],ip_addr,data,db_action,company_code)
		select getdate(),@module_name,@user_name,@ip_addr,@data,@db_action,@company_code
GO
/****** Object:  StoredProcedure [dbo].[sp_s_Login]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_s_Login]
	@user_id varchar(50),
	@password varchar(50),
	@company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(3000)

if exists (select top 1 * from s_users where [user_id] = @user_id and user_password = @password)
	begin
		select top 1 '1' as x, first_name as username from s_users where [user_id] = @user_id and user_password = @password

		set @data = 'USER ID: ' + @user_id + ';'
		exec sp_s_EventLog 'LOGIN',@user_id,@ip_addr,@data,'CHECK',@company_code
	end
else
	begin
		select 'Invalid User Name and/or Password.' as x,'' as username

		set @data = 'USER ID:' + @user_id +  '; Invalid User Name and/or Password.'
		exec sp_s_EventLog 'LOGIN',@user_id,@ip_addr,@data,'CHECK',@company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_s_Menu_Access]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_Menu_Access]
	@module_id decimal(9,0),
	@user_id varchar(50)
AS

	if not exists (select top 1 * from s_user_group_modules where module_id = @module_id and group_code in 
		(select group_code from s_users where [user_id] = @user_id)
	)
		select 0 as x
	else
		select 1 as x
GO
/****** Object:  StoredProcedure [dbo].[sp_s_Statistics]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_Statistics]
AS

declare @reading_wout_invoice decimal(9,0),@unposted_inv decimal(9,0),@unposted_or_unpaid decimal(9,0),@unposted_or_ar decimal(9,0)
declare @tenants_wout_charges decimal(9,0),@units_wout_charges decimal(9,0)

set @reading_wout_invoice = 0
set @unposted_inv = 0
set @unposted_or_unpaid = 0
set @unposted_or_ar = 0
set @tenants_wout_charges = 0
set @units_wout_charges = 0

	select @reading_wout_invoice = count(*) from t_tenant_reading where reading_id not in
	(
	select reading_id from t_invoice_detail_reading where invoice_no not in (select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'V')
	)

	select @unposted_inv = count(*) from t_invoice_header where ltrim(rtrim(isnull(status,''))) = ''

	select @unposted_or_unpaid = count(*) from t_payment_header where ltrim(rtrim(isnull(status,''))) = ''

	select @unposted_or_ar = count(*) from t_ar_header where ltrim(rtrim(isnull(status,''))) = ''

	select @tenants_wout_charges = count(*) from m_tenant 
	where tenant_code not in (select tenant_code from m_tenant_charges)
	and upper(ltrim(rtrim(isnull(terminated,'')))) <> 'Y'
	and upper(ltrim(rtrim(isnull(tenant_type,'OC')))) <> 'C' 

	select @units_wout_charges = count(*) from m_units
	where upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))
	not in (select upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,''))))  from m_unit_charges)
	and upper(ltrim(rtrim(isnull(is_reserved,'')))) <> 'Y'

	select isnull(@reading_wout_invoice,0) as reading_wout_invoice,isnull(@unposted_inv,0) as unposted_inv,isnull(@unposted_or_unpaid,0) as unposted_or_unpaid,
	isnull(@unposted_or_ar,0) as unposted_or_ar, isnull(@tenants_wout_charges,0) as tenants_wout_charges,isnull(@units_wout_charges,0) as units_wout_charges
GO
/****** Object:  StoredProcedure [dbo].[sp_s_TenantReading_AddCharges_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_TenantReading_AddCharges_List]
	@reading_id decimal(18,0)
AS

	select * from m_charges where charge_code not in (select trc_charge_code from t_tenant_reading_charges where trc_reading_id= @reading_id) 
	and isnull(charge_type,'') = 'U'
	and charge_code in (select charge_code from m_tenant_charges where  tenant_code in (select top 1 tenant_code from t_tenant_reading 
	where reading_id = @reading_id))
	order by charge_desc
GO
/****** Object:  StoredProcedure [dbo].[sp_s_TenantReading_Default_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_s_TenantReading_Default_Delete]
	@charge_code varchar(50),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING - DEFAULT'
	
	if (select count(*) from s_tenant_reading_default) > 1
		begin
			delete from s_tenant_reading_default where  trd_charge_code = @charge_code
		
			set @data = 'delete from s_tenant_reading_default where upper(ltrim(rtrim(trd_charge_code))) =' + upper(ltrim(rtrim(@charge_code)))
		end
	else
		update s_tenant_reading_default set trd_charge_code = ''
	
	exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

	--select 0 as x
GO
/****** Object:  StoredProcedure [dbo].[sp_s_TenantReading_Default_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_s_TenantReading_Default_Retrieve]
	@strMode varchar(50)
AS

if @strMode = 'RETRIEVE'
	select top 1 *
	from s_tenant_reading_default 

if @strMode = 'CHARGES'
	select charge_code,charge_desc
	from s_tenant_reading_default 
	left join m_charges on trd_charge_code = m_charges.charge_code
	and isnull(trd_charge_code,'') <> ''
	order by m_charges.charge_desc

if @strMode = 'ADD_CHARGES_LIST'
	select * from m_charges where charge_code not in (select trd_charge_code from s_tenant_reading_default) 
	and isnull(charge_type,'') = 'U'
	order by charge_desc
GO
/****** Object:  StoredProcedure [dbo].[sp_s_TenantReading_Default_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_s_TenantReading_Default_Save]
	@strMode varchar(50),
	@charge_code char(5),
	@reading_date_from datetime,
	@reading_date_to datetime,
	@billing_date_from datetime,
	@billing_date_to datetime,
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING - DEFAULT' 

if @strMode = 'SAVE'
	begin
		if not exists (select * from s_tenant_reading_default where trd_charge_code = @charge_code)
			begin
				insert into s_tenant_reading_default (trd_charge_code,trd_date_from,trd_date_to,trd_billing_date_from,trd_billing_date_to,trd_date_updated,trd_updated_by)	
				select @charge_code,@reading_date_from,@reading_date_to,@billing_date_from,@billing_date_to,getdate(),@uid

				set @data = 'insert into s_tenant_reading_default (trd_charge_code,trd_date_from,trd_date_to,trd_billing_date_from,trd_billing_date_to,trd_date_updated,trd_updated_by)	) ' +	
					'select ' + @charge_code+','+ convert(varchar(10),@reading_date_from,101)+','+convert(varchar(10),@reading_date_to,101)+',' +
					convert(varchar(10),@billing_date_from,101)+','+convert(varchar(10),@billing_date_to,101)

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update s_tenant_reading_default set 
					trd_date_from = @reading_date_from,
					trd_date_to = @reading_date_to,
					trd_billing_date_from = @billing_date_from,
					trd_billing_date_to = @billing_date_to,
					trd_date_updated = getdate(),
					trd_updated_by = @uid
				 where trd_charge_code = @charge_code

				set @data = 'update s_tenant_reading_default set ' + 
					'trd_date_from = ' + convert(varchar(10),@reading_date_from,101) +','+
					'trd_date_to = ' + convert(varchar(10),@reading_date_to,101) +','+
					'trd_billing_date_from =' + convert(varchar(10),@billing_date_from,101) +','+
					'trd_billing_date_to =' + convert(varchar(10),@billing_date_to,101) +
					'where trd_charge_code =' + @charge_code

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end

		if exists (select * from s_tenant_reading_default where isnull(trd_charge_code,'') <> '')
			delete  from s_tenant_reading_default where isnull(trd_charge_code,'') = ''
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_s_User]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_s_User]
	@strMode varchar(50),
	@user_id char(15),
	@last_name varchar(50),
	@first_name varchar(50),
	@middle_initial varchar(5),
	@user_password varchar(15),
	@group_code decimal(9,0),
	@is_active char(1),
	@date_inactive datetime,
	@company_code varchar(5),
	@uid varchar(100),
	@ip_addr varchar(20)

AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'USER MAINTENANCE'

if @strMode = 'SAVE'
	begin
		if not exists (select * from s_users where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))) 
			begin
				insert into s_users ([user_id],last_name,first_name,middle_initial,user_password,group_code,is_active,date_inactive,date_created,created_by,company_code)
				select ltrim(rtrim(@user_id)),@last_name,@first_name,@middle_initial,@user_password,@group_code,@is_active,@date_inactive,getdate(),@uid,@company_code

				set @data = 'insert into s_users ([user_id],last_name,first_name,middle_initial,user_password,group_code,is_active,date_inactive,date_created,created_by,company_code) ' +
					'select ' + ltrim(rtrim(@user_id))+','+@last_name+','+@first_name+','+@middle_initial+','+@user_password+','+@group_code+','+@is_active+','+convert(varchar(10),@date_inactive,101)+','+convert(varchar(20),getdate())+','+@uid+','+@company_code
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update s_users set 
					last_name = @last_name,
					first_name = @first_name,
					middle_initial = @middle_initial,
					group_code = @group_code,
					is_active = @is_active,
					date_inactive = @date_inactive,
					user_password = @user_password,
					date_created = getdate(),
					created_by = @uid		
				where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))

				set @data = 'update s_users set ' +
					'last_name ='+ @last_name+','+
					'first_name ='+ @first_name+','+
					'middle_initial ='+ @middle_initial+','+
					'group_code ='+ @group_code+','+
					'is_active ='+ @is_active+','+
					'date_inactive ='+ convert(varchar(10),@date_inactive,101)+','+
					'user_password ='+ @user_password+','+
					'date_created ='+  convert(varchar(20),getdate())+','+
					'created_by ='+ @uid +		
					'where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))'
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end



if @strMode = 'FIND'
	begin
		if exists (select * from s_users where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))) 
			select 1 as x
		else
			select 0 as x
	end


if @strMode = 'VIEW'
	begin
		select [user_id],upper(ltrim(rtrim(last_name))) + ' ' + upper(ltrim(rtrim(first_name))) + ' ' + upper(ltrim(rtrim(middle_initial))) as fullname,s_user_group.group_desc from s_users 
		left join s_user_group on s_users.group_code = s_user_group.group_code
		where s_users.[user_id] like '%' + @last_name + '%' or last_name like '%' + @last_name + '%' or first_name like '%' + @last_name + '%'
		order by s_users.[user_id]
	end


if @strMode = 'RETRIEVE'
	begin
		select top 1 s_users.*,s_user_group.group_desc from s_users 
		left join s_user_group on s_users.group_code = s_user_group.group_code
		where s_users.[user_id] like  '%' + @last_name + '%'
	end

if @strMode = 'RETRIEVE_USER_PWD'
	begin
		select top 1 s_users.*,s_user_group.group_desc from s_users 
		left join s_user_group on s_users.group_code = s_user_group.group_code
		where upper(ltrim(rtrim(s_users.[user_id]))) = upper(ltrim(rtrim(@user_id)))
	end

if @strMode = 'CHANGE_PASSWORD'
	begin
		update s_users set 
			user_password = @user_password,
			date_created = getdate(),
			created_by = @uid		
		where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))
	end


if @strMode = 'DELETE'
	begin
		if not exists (select * from s_event_log where upper(ltrim(rtrim([user_name]))) = upper(ltrim(rtrim(@user_id))))
			begin
				delete from s_users where upper(ltrim(rtrim([user_id]))) = upper(ltrim(rtrim(@user_id)))
				set @data = 'delete from s_users where upper(ltrim(rtrim([user_id]))) ='+ upper(ltrim(rtrim(@user_id)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				select 0 as x
			end
		else
			begin
				select 1 as x
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_s_User_Group]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_User_Group]
	@strMode varchar(20),
	@group_code decimal(9,0),
	@group_desc varchar(50),
	@company_code varchar(5),
	@uid varchar(100),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'USER GROUP'

if @strMode = 'SAVE'
	begin
		if not exists (select * from s_user_group where group_code = @group_code) 
			begin
				insert into s_user_group (group_desc)
				select upper(ltrim(rtrim(@group_desc)))

				set @data = 'insert into s_user_group (group_desc) ' +
					'select ' + upper(ltrim(rtrim(@group_desc)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update s_user_group set 
					group_desc = upper(ltrim(rtrim(@group_desc)))				
				 where group_code = @group_code

				set @data = 'update s_user_group set ' +
					'group_desc =' + upper(ltrim(rtrim(@group_desc))) +				
					'where group_code =' + convert(varchar(10),@group_code)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end



if @strMode = 'FIND'
	begin
		if exists (select * from s_user_group where upper(ltrim(rtrim(group_code))) = upper(ltrim(rtrim(@group_code)))) 
			select 1 as x
		else
			select 0 as x
	end


if @strMode = 'VIEW'
	begin
		select * from s_user_group 
		where group_desc like '%' + @group_desc + '%'
		order by group_desc
	end


if @strMode = 'RETRIEVE'
	begin
		select top 1 * from s_user_group 
		where group_desc like '%' + @group_desc + '%'
	end


if @strMode = 'DELETE'
	begin
		if not exists (select * from s_users where group_code = @group_code)
			begin
				delete from s_user_group where group_code = @group_code
				set @data = 'delete from s_user_group where group_code ='+ convert(varchar(10),@group_code)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
				select 0 as x
			end
		else
			begin
				select 1 as x
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_s_User_Group_Module]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_s_User_Group_Module]
	@strMode varchar(20),
	@group_code decimal(9,0),
	@module_id decimal(9,0),
	@remarks varchar(100),
	@uid varchar(100),
	@company_code char(5),
	@ip_addr varchar(50)

AS

declare @data nvarchar(4000),@module_name varchar(50)
declare @tmp_group_code decimal(9,0)
declare @x table (module_id decimal(9,0),module_name varchar(50),is_inc int)	

set @module_name = 'USER GROUP MODULE'

if @strMode = 'SAVE'
	begin
		if not exists (select * from s_user_group_modules where group_code = @group_code and module_id=@module_id ) 
			begin
				insert into s_user_group_modules (group_code,module_id)
				select @group_code,@module_id
	
				set @data = 'insert into s_user_group_modules (group_code,module_id) ' +
					'select ' + convert(varchar(10),@group_code)+','+convert(varchar(10),@module_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
	end

if @strMode = 'RETRIEVE_GROUP'
	begin
		select top 1 *  from s_user_group
		where group_desc like '%' + @remarks + '%' 
	end

if @strMode = 'FIND_GROUP'
	begin
		select top 1 *  from s_user_group
		where group_code = @group_code
	end

if @strMode = 'FIND'
	begin
		insert into @x	
		select s_user_group_modules.module_id,s_modules.module_name,1
		from s_user_group_modules
		left join s_modules on s_modules.module_id = s_user_group_modules.module_id
		where group_code = @group_code

		insert into @x	
		select module_id,module_name,0
		from s_modules where module_id not in
		(
			select module_id
			from s_user_group_modules
			where group_code = @group_code
		)

		select distinct * from @x	order by module_name
		
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 @tmp_group_code = group_code from s_user_group
		where group_desc like '%' + @remarks + '%' 

		insert into @x	
		select s_user_group_modules.module_id,s_modules.module_name,1
		from s_user_group_modules
		left join s_modules on s_modules.module_id = s_user_group_modules.module_id
		where group_code = @tmp_group_code

		insert into @x	
		select module_id,module_name,0
		from s_modules where module_id not in
		(
			select module_id
			from s_user_group_modules
			where group_code = @tmp_group_code
		)

		select distinct * from @x	order by module_name
		
	end


if @strMode = 'DELETE'
	begin
		delete from s_user_group_modules where group_code = @group_code
		set @data = 'delete from s_user_group_modules where group_code = ' +convert(varchar(10),@group_code)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Detail]
	@strMode varchar(50),
	@ar_detail_id decimal(9,0),
	@or_no varchar(20),
	@tenant_code char(10),
	@charge_code char(5),
	@or_amount decimal(9,2),
	@based_on_invoice char(1),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @deducted_amount decimal(9,2),@tmp_or_no varchar(20),@detail_cnt decimal(9,0),@total_detail_amount decimal(9,2)
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'ADVANCE PAYMENT DETAIL'

if @strMode = 'SAVE'
	begin
		if not exists (select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and charge_code = upper(ltrim(rtrim(@charge_code))))
			begin
				insert into t_ar_detail (or_no,charge_code,date_updated,updated_by)
				select upper(ltrim(rtrim(@or_no))) ,upper(ltrim(rtrim(@charge_code))),getdate(),@uid

				set @data = 'insert into t_ar_detail (or_no,charge_code,date_updated,updated_by) ' +
					'select ' + upper(ltrim(rtrim(@or_no))) +',' + upper(ltrim(rtrim(@charge_code)))+',' +convert(varchar(20),getdate())+',' +@uid
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
	end


if @strMode = 'FIND'
	begin
		if exists (select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and charge_code = upper(ltrim(rtrim(@charge_code))))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'RETRIEVE_HDR'
	begin
		select top 1 @tmp_or_no = or_no
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'

		select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
	
		set @detail_cnt = 0

		select @detail_cnt = count(*) from t_ar_detail
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name  as client_name,
			isnull(amount,0) as amount,isnull(@deducted_amount,0) as deducted_amount,isnull(amount,0) - isnull(@deducted_amount,0) as balance_amount,
			isnull(@detail_cnt,0) as detail_cnt,0 as total_detail_amount 	
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
	end

if @strMode = 'VIEW_ENTRY'
	begin
		select m_tenant_charges.*,tenant_name,charge_desc	
		from m_tenant_charges
		left join m_tenant on m_tenant_charges.tenant_code = m_tenant.tenant_code
		left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
		where  '*' + upper(ltrim(rtrim(@tenant_code))) + '*' +  upper(ltrim(rtrim(@charge_code)))  + '*'  not in
			(select '*' + upper(ltrim(rtrim(@tenant_code))) + '*' +  upper(ltrim(rtrim(@charge_code))) +  '*' from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			and upper(ltrim(rtrim(m_tenant.bill_to))) = isnull((select top 1 upper(ltrim(rtrim(client_code))) from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) ),'')
			and (upper(ltrim(rtrim(m_tenant.tenant_type))) = 'OC' or upper(ltrim(rtrim(m_tenant.tenant_type))) = 'O')
		order by tenant_name,charge_desc	
	end

if @strMode = 'ADD_ENTRY'
	begin
		if not exists (select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and charge_code = upper(ltrim(rtrim(@charge_code))))
			begin
				insert into t_ar_detail (or_no,charge_code,date_updated,updated_by)
				select top 1 upper(ltrim(rtrim(@or_no))) ,upper(ltrim(rtrim(@charge_code))),getdate(),@uid
				
				set @data = 'insert into t_ar_detail (or_no,charge_code,date_updated,updated_by) ' +
					'select ' + upper(ltrim(rtrim(@or_no))) +',' + upper(ltrim(rtrim(@charge_code)))+',' +convert(varchar(20),getdate())+',' +@uid
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end


if @strMode = 'RETRIEVE_DTL'
	begin
		select t_ar_detail.*,upper(ltrim(rtrim(charge_desc))) as charge_desc
		from t_ar_detail
		left join m_charges on t_ar_detail.charge_code = m_charges.charge_code
		where upper(ltrim(rtrim(t_ar_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
		order by charge_desc
	end

if @strMode = 'DELETE'		
	begin
		if not exists (select * from t_payment_header left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
			where upper(ltrim(rtrim(t_payment_header.or_no))) = upper(ltrim(rtrim(@or_no))) 
			and isnull(t_payment_header.status,'') <> 'V'
			and t_payment_detail.invoice_no in 
				(select invoice_no from t_invoice_detail where charge_code = upper(ltrim(rtrim(@charge_code)))))
			begin
				delete from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and ar_detail_id = @ar_detail_id
				set @data = 'delete from t_ar_detail where upper(ltrim(rtrim(or_no))) = ' +upper(ltrim(rtrim(@or_no))) + ' and ar_detail_id =' + convert(varchar(10),@ar_detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header]
	@strMode varchar(50),
	@or_no varchar(20),
	@or_date datetime,
	@client_code char(10),
	@amount decimal(9,2),
	@document_no varchar(20),
	@mode_of_payment char(1),
	@bank_name varchar(100),
	@remarks varchar(100),
	@status char(1),
	@payment_account_no varchar(20),
	@ar_mode_id decimal(9,2),
	@payment_mode_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_or_no varchar(20), @next_doc_no varchar(50),@next_doc_no_settings int,@or_len_settings int,@or_no_type char(1)
declare @or_no1 decimal(18,0),@or_no2 decimal(18,0),@deducted_amount decimal(18,2)
declare @doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)

set @module_name = 'ADVANCE PAYMENT HEADER'

set @or_len_settings = 4
set @or_no_type = ''
set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))
set @next_doc_no_settings = 10
set @document_no = ''

if @strMode = 'SAVE'
	begin
		if @or_no = '' 
			begin
				select @or_no = dbo.fn_GetNextORNo (@or_date)
				set @or_no_type = 'N'
			end

		/*if upper(ltrim(rtrim(isnull(@document_no,'')))) = ''
			begin	
				select @doc_no1 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_ar_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no2 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_payment_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no3 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_invoice_header where isnumeric(right(document_no,@next_doc_no_settings))=1
		
				if isnull(@doc_no1,0) >= isnull(@doc_no2,0) and isnull(@doc_no1,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no1		
				else if isnull(@doc_no2,0) >= isnull(@doc_no1,0) and isnull(@doc_no2,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no2	 
				else
					set @next_doc_no = @doc_no3

				if isnull(@next_doc_no,0) = 0
					set @next_doc_no = '1'
				else
					set @next_doc_no = @next_doc_no + 1

				set @document_no = ltrim(rtrim(replace(space(@next_doc_no_settings - len(@next_doc_no)),' ','0') + convert(varchar(10),@next_doc_no)))

			end
		*/
		
		if not exists(select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by)
				select @or_no,@or_no_type,@or_date,@client_code,@amount,@document_no,@mode_of_payment,@bank_name,@remarks,@status,getdate(),@uid

				set @data = 'insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by) ' +
					'select ' + @or_no+',' +@or_no_type+',' + convert(varchar(10),@or_date,101)+',' +@client_code+',' +convert(varchar(10),@amount)+',' +@document_no+',' +
					@mode_of_payment+',' +@bank_name+',' +@remarks+',' +@status+',' +convert(varchar(20),getdate())+',' +@uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				select @or_no as or_no
			end
		else
			begin
				update t_ar_header set
					or_date = @or_date,
					client_code = @client_code,
					amount = isnull(@amount,0),
					document_no = @document_no,
					mode_of_payment = @mode_of_payment,
					bank_name = @bank_name,
					remarks = @remarks,
					status = @status,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(isnull(status,'')))) <> 'P'

				set @data = 'update t_ar_header set ' +
					'or_date = ' + convert(varchar(10),@or_date,101)+',' +
					'client_code = ' + @client_code+',' +
					'amount = ' + convert(varchar(10),isnull(@amount,0))+',' +
					'document_no = ' + @document_no+',' +
					'mode_of_payment = ' + @mode_of_payment+',' +
					'bank_name = ' + @bank_name+',' +
					'remarks = ' + @remarks+',' +
					'status = ' + @status+',' +
					'date_updated = ' + convert(varchar(20),getdate())+',' +
					'updated_by = ' + @uid +
					'where upper(ltrim(rtrim(or_no))) =  ' + upper(ltrim(rtrim(@or_no))) + 'and upper(ltrim(rtrim(isnull(status,'''')))) <> ''P'''
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		
	end

if @strMode = 'FIND'
	begin
		if exists(select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))) or exists(select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'RETRIEVE'
	begin
		declare @payment_detail_cnt int,@tmp_or_no varchar(20),@total_payment_detail_amount decimal(9,2)

		select top 1 @tmp_or_no = or_no,@total_payment_detail_amount = isnull(amount,0)
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'

		select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,t_ar_header.remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name  as client_name,@payment_detail_cnt as payment_detail_cnt,isnull(@total_payment_detail_amount,0) as total_payment_detail_amount,
			isnull(amount,0) as amount,isnull(@deducted_amount,0) as deducted_amount,isnull(amount,0) - isnull(@deducted_amount,0) as balance_amount 	
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		if isnull(@total_payment_detail_amount,0) = 0
			delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
	end


if @strMode = 'VIEW'
	begin
		select distinct t_ar_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_ar_header 
		inner join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%'
		order by or_date desc, t_ar_header.or_no desc
	end


if @strMode = 'VIEW_STAT'
	begin
		select distinct t_ar_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_ar_header 
		inner join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where (or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%')
		and ltrim(rtrim(isnull(status,''))) = ''
		order by or_date desc, t_ar_header.or_no desc
	end

if @strMode = 'CLIENT_SEARCH'
	begin
		select * from m_tenant 
		where (tenant_name like @remarks + '%' or tenant_code like @remarks + '%'
		or building_code like @remarks + '%' or unit_no like @remarks + '%')
		and isnull(terminated,'N') <> 'Y'
		and (upper(ltrim(rtrim(tenant_type))) = 'OC' or upper(ltrim(rtrim(tenant_type))) = 'C')
		order by tenant_name
	end

if @strMode = 'POST'
	begin
		update t_ar_header set status = 'P' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  
		select 0 as x			
	end

if @strMode = 'VOID'
	begin
		if not exists (select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and upper(ltrim(rtrim(status))) = 'P')
			begin
				update t_ar_header set status = 'V' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				select 0 as x
			end
		else
			select 1 as x
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and upper(ltrim(rtrim(status))) = 'P' or or_no_type = 'N')
			begin
				delete from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				set @data = 'delete from t_ar_header where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end

if @strMode = 'SAVE_PAYMENT_MODE'
	begin
		if @ar_mode_id = 0
			begin
				if @mode_of_payment = 1
					begin
						if not exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and @mode_of_payment = 1)
							begin
								insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
								select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
							end
						else
							begin
								update t_ar_header_payment_mode set
									amount = isnull(amount,0) + @payment_mode_amount
								where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = @mode_of_payment
							end
					end
				else
					begin
						insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
					end
			end
		else
			begin
				update t_ar_header_payment_mode set
					payment_mode_type = @mode_of_payment,
					amount = @payment_mode_amount,
					account_no = @payment_account_no,	
					bank_name = @bank_name
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and ar_mode_id = @ar_mode_id
			end
		
	end

if @strMode = 'RETRIEVE_PAYMENT_MODE'
	begin
		delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) = 0
		
		if not exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
				select top 1 or_no,'1',amount,'','' from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		select ar_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_ar_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) > 0 order by payment_mode_type
	end

if @strMode = 'RETRIEVE_CASH_PAYMENT'
	begin
		select ar_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_ar_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '1'
	end

if @strMode = 'RETRIEVE_CHARGE_PAYMENT'
	begin
		select ar_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_ar_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '2'
	end

if @strMode = 'RETRIEVE_CHECK_PAYMENT'
	begin
		select ar_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_ar_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '3'
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_Delete]
	@strMode varchar(50),
	@or_no varchar(20),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'ADVANCE PAYMENT HEADER'

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and (upper(ltrim(rtrim(status))) = 'P' or or_no_type = 'N'))
			begin
				delete from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				set @data = 'delete from t_ar_header where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x,'' as or_no
			end
		else
			select 1 as x,@or_no as or_no
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_List]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000),@data_type char(1)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @ssql = 'select distinct t_ar_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'''')))) as status,
		case when upper(ltrim(rtrim(status))) = ''P'' then ''POSTED''
		when upper(ltrim(rtrim(status))) = ''V'' then ''VOID''
		else '''' end as status_desc
		from t_ar_header 
		inner join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where isnull(t_ar_header.trans_type,'''') <> ''U'' '

	if ltrim(rtrim(@column_code)) <> '' 
		begin
			if @data_type = 'S'
				set @ssql = @ssql + ' and ' + @column_code + ' like ''%' + @keyword + '%'''				
			else if @data_type = 'D' and @keyword <> ''
				set @ssql = @ssql + ' and convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
		end

	if @strMode = 'VIEW_STAT'
		begin
			if ltrim(rtrim(@column_code)) <> '' 
				set @ssql = @ssql + ' and '
			else
				set @ssql = @ssql + ' where '

			set @ssql = @ssql +' upper(ltrim(rtrim(isnull(t_ar_header.status,'''')))) = '''' '
		end

	if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by or_date desc,t_ar_header.or_no desc '
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_Retrieve]
	@strMode varchar(50),
	@or_no varchar(20)
AS

declare @deducted_amount decimal(18,2)

set @or_no = upper(ltrim(rtrim(@or_no)))

if @strMode = 'RETRIEVE'
	begin
		declare @payment_detail_cnt int,@tmp_or_no varchar(20),@total_payment_detail_amount decimal(9,2),@status char(1)

		select top 1 @tmp_or_no = or_no,@total_payment_detail_amount = isnull(amount,0),@status = isnull(t_ar_header.status,'')
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'
		and isnull(trans_type,'') <> 'U'

		select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
		and or_no in (select or_no from t_payment_header where isnull(trans_type,'') = 'A')

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,t_ar_header.remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			t_ar_header.date_updated,t_ar_header.updated_by,tenant_name  as client_name,@payment_detail_cnt as payment_detail_cnt,isnull(@total_payment_detail_amount,0) as total_payment_detail_amount,
			isnull(amount,0) as amount,isnull(@deducted_amount,0) as deducted_amount,isnull(amount,0) - isnull(@deducted_amount,0) as balance_amount 	
		from t_ar_header
		left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		if isnull(@total_payment_detail_amount,0) = 0 and @status <> 'P'
			delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_Retrieve_Payment_Mode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_Retrieve_Payment_Mode]
	@strMode varchar(50),
	@or_no varchar(20)

AS

if @strMode = 'RETRIEVE_PAYMENT_MODE'
	begin
		delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) = 0
		
		if not exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
				select top 1 or_no,'1',amount,'','' from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		select ar_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_ar_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) > 0 order by payment_mode_type
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_Save]
	@strMode varchar(50),
	@or_no varchar(20),
	@or_date datetime,
	@client_code char(10),
	@amount decimal(9,2),
	@document_no varchar(20),
	@mode_of_payment char(1),
	@bank_name varchar(100),
	@remarks varchar(100),
	@status char(1),
	@payment_account_no varchar(20),
	@ar_mode_id decimal(9,2),
	@payment_mode_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_or_no varchar(20), @next_doc_no varchar(50),@next_doc_no_settings int,@or_len_settings int,@or_no_type char(1)
declare @or_no1 decimal(18,0),@or_no2 decimal(18,0),@deducted_amount decimal(18,2)
declare @doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)

set @module_name = 'ADVANCE PAYMENT HEADER'

set @or_len_settings = 4
set @or_no_type = ''
set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))
set @next_doc_no_settings = 10
set @document_no = ''

if @strMode = 'SAVE'
	begin
		if @or_no = '' 
			begin
				select @or_no = dbo.fn_GetNextORNo (@or_date)
				set @or_no_type = 'N'
			end
		
		if not exists(select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by)
				select @or_no,@or_no_type,@or_date,@client_code,@amount,@document_no,@mode_of_payment,@bank_name,@remarks,@status,getdate(),@uid

				set @data = 'insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by) ' +
					'select ' + @or_no+',' +@or_no_type+',' + convert(varchar(10),@or_date,101)+',' +@client_code+',' +convert(varchar(10),@amount)+',' +@document_no+',' +
					@mode_of_payment+',' +@bank_name+',' +@remarks+',' +@status+',' +convert(varchar(20),getdate())+',' +@uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				select @or_no as or_no
			end
		else
			begin
				update t_ar_header set
					or_date = @or_date,
					client_code = @client_code,
					amount = isnull(@amount,0),
					document_no = @document_no,
					mode_of_payment = @mode_of_payment,
					bank_name = @bank_name,
					remarks = @remarks,
					status = @status,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(isnull(status,'')))) <> 'P'

				set @data = 'update t_ar_header set ' +
					'or_date = ' + convert(varchar(10),@or_date,101)+',' +
					'client_code = ' + @client_code+',' +
					'amount = ' + convert(varchar(10),isnull(@amount,0))+',' +
					'document_no = ' + @document_no+',' +
					'mode_of_payment = ' + @mode_of_payment+',' +
					'bank_name = ' + @bank_name+',' +
					'remarks = ' + @remarks+',' +
					'status = ' + @status+',' +
					'date_updated = ' + convert(varchar(20),getdate())+',' +
					'updated_by = ' + @uid +
					'where upper(ltrim(rtrim(or_no))) =  ' + upper(ltrim(rtrim(@or_no))) + 'and upper(ltrim(rtrim(isnull(status,'''')))) <> ''P'''
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_Header_Save_Payment_Mode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_Header_Save_Payment_Mode]
	@strMode varchar(50),
	@or_no varchar(20),
	@or_date datetime,
	@client_code char(10),
	@amount decimal(9,2),
	@document_no varchar(20),
	@mode_of_payment char(1),
	@bank_name varchar(100),
	@status char(1),
	@payment_account_no varchar(20),
	@ar_mode_id decimal(18,0),
	@payment_mode_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_or_no varchar(20), @next_doc_no varchar(50),@next_doc_no_settings int,@or_len_settings int,@or_no_type char(1)
declare @or_no1 decimal(18,0),@or_no2 decimal(18,0),@deducted_amount decimal(18,2)
declare @doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)

set @module_name = 'ADVANCE PAYMENT HEADER'

set @or_len_settings = 4
set @or_no_type = ''
set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))
set @next_doc_no_settings = 10
set @document_no = ''

if @strMode = 'SAVE_PAYMENT_MODE'
	begin
		if @ar_mode_id = 0
			begin
				if @mode_of_payment = 1
					begin
						if not exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and @mode_of_payment = 1)
							begin
								insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
								select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
							end
						else
							begin
								update t_ar_header_payment_mode set
									amount =  @payment_mode_amount
								where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = @mode_of_payment
							end
					end
				else
					begin
						insert into t_ar_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
					end
			end
		else
			begin
				update t_ar_header_payment_mode set
					payment_mode_type = @mode_of_payment,
					amount = @payment_mode_amount,
					account_no = @payment_account_no,	
					bank_name = @bank_name
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and ar_mode_id = @ar_mode_id
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_OR_Breakdown]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_OR_Breakdown] 
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@or_amount decimal(9,2),
	@or_no varchar(20),	
	@payment_detail_id decimal(9,2),
	@uid varchar(50),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'ADVANCE PAYMENT - OR BREAKDOWN'

if @strMode = 'OR'
	begin
		delete from t_payment_detail where isnull(is_selected,0) = 1 and isnull(or_amount,0) = 0 and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
		update t_payment_detail set is_selected = 0 where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))  

		declare @deducted_amount decimal(9,2),@ar_balance_amount decimal(9,2),@tmp_invoice_charge_amount decimal(9,2)
		declare @tmp_or_no varchar(20),@tmp_ar_amount decimal(9,2)
		declare @tmp_or_table table (or_no varchar(20),or_balance_amount decimal(9,2))
		
		
		if @or_no = '' 
			begin
				declare yyy cursor scroll for
				select or_no, amount
				from t_ar_header
				where client_code in (select top 1 client_code from t_invoice_header where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
				order by or_date
				
				open yyy
				fetch next from yyy into @tmp_or_no,@tmp_ar_amount
				while @@fetch_status = 0
					begin
						set @deducted_amount = 0
						set @ar_balance_amount = 0
				
						select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
			
						if isnull(@deducted_amount,0) < isnull(@tmp_ar_amount,0)
							begin
								set @ar_balance_amount =  isnull(@tmp_ar_amount,0) - isnull(@deducted_amount,0)
								insert into @tmp_or_table
								select @tmp_or_no,@ar_balance_amount
							end
						
						fetch next from yyy into @tmp_or_no,@tmp_ar_amount
					end
				close yyy
				deallocate yyy
			end
		else
			begin
				declare yyy cursor scroll for
				select or_no, amount
				from t_ar_header
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				order by or_date
		
				open yyy
				fetch next from yyy into @tmp_or_no,@tmp_ar_amount
				while @@fetch_status = 0
					begin
						set @deducted_amount = 0
						set @ar_balance_amount = 0
				
						select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
			
						if isnull(@deducted_amount,0) < isnull(@tmp_ar_amount,0)
							begin
								set @ar_balance_amount =  isnull(@tmp_ar_amount,0) - isnull(@deducted_amount,0)
								insert into @tmp_or_table
								select @tmp_or_no,@ar_balance_amount
							end
						
						fetch next from yyy into @tmp_or_no,@tmp_ar_amount
					end
				close yyy
				deallocate yyy	
			end

		declare zzz cursor scroll for
		select or_no from @tmp_or_table
		
		open zzz
		fetch next from zzz into @tmp_or_no
		while @@fetch_status = 0
			begin
				if exists (select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					begin
						insert into  t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
						select @tmp_or_no,invoice_no,invoice_detail_id,total_charge_amount,0,getdate(),@uid,1
						from t_invoice_detail						
						where upper(ltrim(t_invoice_detail.invoice_no)) = upper(ltrim(@invoice_no)) 
						and charge_code in (select charge_code from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
						and isnull(total_charge_amount,0) - isnull(paid_amount,0) > 0

						set @data = 'insert into  t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected) ' +
							'select ' + @tmp_or_no+',+invoice_no,invoice_detail_id,total_charge_amount,0,' + convert(varchar(20),getdate()) + ',' + @uid+',1' +
							'from t_invoice_detail ' +						
							'where upper(ltrim(t_invoice_detail.invoice_no)) =' + upper(ltrim(@invoice_no)) +
							'and charge_code in (select charge_code from t_ar_detail where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no))) + ')' +
							'and isnull(total_charge_amount,0) - isnull(paid_amount,0) > 0'

						exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
					end
				else
					begin
						insert into  t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
						select @tmp_or_no,invoice_no,invoice_detail_id,total_charge_amount,0,getdate(),@uid,1
						from t_invoice_detail						
						where upper(ltrim(t_invoice_detail.invoice_no)) = upper(ltrim(@invoice_no)) 
						and isnull(total_charge_amount,0) - isnull(paid_amount,0) > 0

						set @data = 'insert into  t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected) ' +
							'select ' + @tmp_or_no+',+invoice_no,invoice_detail_id,total_charge_amount,0,' + convert(varchar(20),getdate()) + ',' + @uid+',1' +
							'from t_invoice_detail ' +						
							'where upper(ltrim(t_invoice_detail.invoice_no)) =' + upper(ltrim(@invoice_no)) +						
							'and isnull(total_charge_amount,0) - isnull(paid_amount,0) > 0'

						exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
					end
				fetch next from zzz into @tmp_or_no
			end
		close zzz
		deallocate zzz	

		select * from @tmp_or_table
	end

if @strMode = 'RETRIEVE'
	begin
		select t_invoice_detail.invoice_detail_id,t_invoice_detail.invoice_no,t_invoice_detail.charge_code,
		m_tenant.tenant_name,t_invoice_detail.tenant_code,
		charge_desc,gl_code,upper(ltrim(rtrim(t_invoice_detail.charge_type))) as charge_type,
		case when upper(ltrim(rtrim(t_invoice_detail.charge_type))) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type_desc,
		t_invoice_detail.total_charge_amount,t_invoice_detail.paid_amount,t_invoice_detail.total_charge_amount - isnull(t_invoice_detail.paid_amount,0) as balance_amount,
		0 as payment_detail_id 
		from t_invoice_detail		
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code		
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code		
		where upper(ltrim(t_invoice_detail.invoice_no)) = upper(ltrim(@invoice_no)) 
		order by m_tenant.tenant_name,charge_desc
	end

if @strMode = 'RETRIEVE_WITH_OR'
	begin
		select t_invoice_detail.invoice_detail_id,t_invoice_detail.invoice_no,t_invoice_detail.charge_code,
		m_tenant.tenant_name,t_invoice_detail.tenant_code,
		charge_desc,gl_code,upper(ltrim(rtrim(t_invoice_detail.charge_type))) as charge_type,
		case when upper(ltrim(rtrim(t_invoice_detail.charge_type))) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type_desc,
		t_invoice_detail.total_charge_amount,t_invoice_detail.paid_amount,t_invoice_detail.total_charge_amount - isnull(t_invoice_detail.paid_amount,0) as balance_amount,
		t_payment_detail.payment_detail_id,t_payment_detail.or_amount
		from t_invoice_detail		
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code		
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		left join t_payment_detail on t_invoice_detail.invoice_no = t_payment_detail.invoice_no and t_invoice_detail.invoice_detail_id = t_payment_detail.invoice_detail_id
		where upper(ltrim(t_invoice_detail.invoice_no)) = upper(ltrim(@invoice_no)) 
		and upper(ltrim(rtrim(t_payment_detail.or_no))) = upper(ltrim(rtrim(@or_no))) 
		and isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) > 0 and isnull(t_payment_detail.is_selected,0) = 1
		order by m_tenant.tenant_name,charge_desc
	end

if @strMode = 'SAVE'
	begin		
		update t_payment_detail set 
			or_amount = @or_amount,
			date_created = getdate(),
			created_by = @uid
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_detail_id = @payment_detail_id
				
		--\\ update paid amount in invoice table
		exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
		--end update paid amount in invoice table

		exec sp_t_Payment_UpdatePaymentMode @or_no

		set @data = 'update t_payment_detail set ' +
			'or_amount ='+ convert(varchar(20),@or_amount) + ',' +
			'date_created ='+ convert(varchar(20),getdate()) + ',' +
			'created_by ='+ @uid +
			'where upper(ltrim(rtrim(or_no))) ='+ upper(ltrim(rtrim(@or_no))) +'and payment_detail_id =' + convert(varchar(20),@payment_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Advance_Payment_OR_Processing]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Advance_Payment_OR_Processing]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_date_from datetime,
	@invoice_date_to datetime,
	@real_property_code char(10),
	@date_updated datetime,
	@uid varchar(50),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @tmp_invoice_no varchar(20),@tmp_client_code char(10)
declare @tmp_invoice_detail_id decimal(9,0),@tmp_tenant_code char(10),@tmp_charge_code char(5)
declare @tmp_total_charge_amount decimal(9,2),@tmp_paid_amount decimal(9,2),@tmp_balance_amount decimal(9,2)
declare @deducted_amount decimal(9,2),@ar_balance_amount decimal(9,2),@tmp_invoice_charge_amount decimal(9,2)
declare @tmp_or_no varchar(20),@tmp_ar_amount decimal(9,2),@tmp_new_paid_amount decimal(9,2)
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'ADVANCE PAYMENT - OR PROCESSING'

if @strMode = 'BUILD'
	begin
		declare zzz cursor scroll for
		select distinct t_invoice_header.invoice_no		
		from t_invoice_header
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
		where invoice_date >= @invoice_date_from and invoice_date <=@invoice_date_to
		and upper(ltrim(rtrim(t_invoice_header.real_property_code))) like upper(ltrim(rtrim(isnull(@real_property_code,'')))) + '%'
		and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'	and total_charge_amount-isnull(paid_amount,0) > 0	
		and t_invoice_header.client_code in (
			select distinct client_code from t_ar_header
			left join (select or_no,sum(or_amount) as or_amount from t_payment_detail group by or_no)t_payment_detail 
				on t_ar_header.or_no = t_payment_detail.or_no
			where t_ar_header.amount - isnull(t_payment_detail.or_amount,0) > 0
		)
		order by t_invoice_header.invoice_no

		open zzz
		fetch next from zzz into @invoice_no
		while @@fetch_status = 0
			begin
				set @tmp_new_paid_amount = 0
				set @tmp_paid_amount = 0
				select @tmp_paid_amount = sum(isnull(paid_amount,0))
				from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
				
				declare yyy cursor scroll for
				select or_no, amount
				from t_ar_header
				where client_code in (select top 1 client_code from t_invoice_header where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
				order by or_date
			
				open yyy
				fetch next from yyy into @tmp_or_no,@tmp_ar_amount
				while @@fetch_status = 0
					begin
						set @deducted_amount = 0
						set @ar_balance_amount = 0
				
						select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
			
						if isnull(@deducted_amount,0) < isnull(@tmp_ar_amount,0)
							begin
								set @ar_balance_amount = @tmp_ar_amount - isnull(@deducted_amount,0)
		
								if isnull(@ar_balance_amount,0) > 0
									begin
										if exists(select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
											and charge_code in (select charge_code from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
											and total_charge_amount - isnull(paid_amount,0) > 0))
											begin
					
												declare xxx cursor scroll for
												select charge_code from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
												and charge_code in (select charge_code from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
												and total_charge_amount - isnull(paid_amount,0) > 0)
										
												open xxx
												fetch next from xxx into @tmp_charge_code
												while @@fetch_status = 0
													begin	
														set @tmp_invoice_charge_amount = 0
													
														select @tmp_invoice_charge_amount = sum(total_charge_amount - isnull(paid_amount,0))
														from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
														and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
				
														if @ar_balance_amount >= isnull(@tmp_invoice_charge_amount,0)
															begin
																set @tmp_new_paid_amount = @tmp_new_paid_amount + @tmp_invoice_charge_amount
																
																if not exists (select invoice_no from z_tmp_or_processing where invoice_no = @invoice_no and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
																begin
																	insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
																	select @invoice_no,'N',@date_updated,@uid
																end	
															end
														else
															begin
																set @tmp_new_paid_amount = @tmp_new_paid_amount + @ar_balance_amount
	
																if not exists (select invoice_no from z_tmp_or_processing where invoice_no = @invoice_no and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
																begin
																	insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
																	select @invoice_no,'Y',@date_updated,@uid
																end	
															end
														
														fetch next from xxx into @tmp_charge_code
													end	
												close xxx
												deallocate xxx		
											end		
										else --no ar detail 
											begin	
												set @tmp_invoice_charge_amount = 0
											
												select @tmp_invoice_charge_amount = sum(total_charge_amount - isnull(paid_amount,0))
												from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
												and total_charge_amount - isnull(paid_amount,0) > 0 
				
												if @ar_balance_amount >= isnull(@tmp_invoice_charge_amount,0)
													begin
													
														set @tmp_new_paid_amount = @tmp_new_paid_amount + @tmp_invoice_charge_amount
													
														if not exists (select invoice_no from z_tmp_or_processing where invoice_no = @invoice_no and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
														begin
															insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
															select @invoice_no,'N',@date_updated,@uid
														end
													end
												else
													begin
														set @tmp_new_paid_amount = @tmp_new_paid_amount + @ar_balance_amount

														if not exists (select invoice_no from z_tmp_or_processing where invoice_no = @invoice_no and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
														begin
															insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
															select @invoice_no,'Y',@date_updated,@uid
														end
													end
											end	
									end										
							end
		
						fetch next from yyy into @tmp_or_no,@tmp_ar_amount
					end
				close yyy
				deallocate yyy
				
				if isnull(@tmp_new_paid_amount,0) + isnull(@tmp_paid_amount,0)  = isnull(@tmp_paid_amount,0) 
					begin
						if not exists (select invoice_no from z_tmp_or_processing where invoice_no = @invoice_no and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
						begin
							insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
							select @invoice_no,'Y',@date_updated,@uid
						end
					end								

				fetch next from zzz into @invoice_no
			end
		close zzz
		deallocate zzz
		
		select t_invoice_header.invoice_no,convert(varchar(10),invoice_date,101) as invoice_date,upper(ltrim(rtrim(isnull(status,'')))) as status,
		sum(total_charge_amount) as total_charge_amount,sum(isnull(paid_amount,0)) as paid_amount,sum(total_charge_amount-isnull(paid_amount,0)) as balance_amount,
		m_tenant.tenant_name as client_name,upper(ltrim(rtrim(isnull(or_breakdown,'N')))) as or_breakdown
		from (select distinct invoice_no,or_breakdown from z_tmp_or_processing where convert(varchar(20),date_created) = convert(varchar(20),@date_updated)
		and created_by = @uid) z_tmp_or_processing
		left join t_invoice_header on z_tmp_or_processing.invoice_no = t_invoice_header.invoice_no
		left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		where invoice_date >= @invoice_date_from and invoice_date <=@invoice_date_to
		and upper(ltrim(rtrim(t_invoice_header.real_property_code))) like upper(ltrim(rtrim(isnull(@real_property_code,'')))) + '%'
		and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'	and total_charge_amount-isnull(paid_amount,0) > 0	
		and t_invoice_header.client_code in (
			select distinct client_code from t_ar_header
			left join (select or_no,sum(or_amount) as or_amount from t_payment_detail group by or_no)t_payment_detail 
				on t_ar_header.or_no = t_payment_detail.or_no
			where t_ar_header.amount - isnull(t_payment_detail.or_amount,0) > 0
		)
		group by t_invoice_header.invoice_no,invoice_date,status,m_tenant.tenant_name,or_breakdown
		order by t_invoice_header.invoice_no

	end

if @strMode = 'DELETE'
	begin
		delete from z_tmp_or_processing where created_by = @uid and convert(varchar(20),date_created) = convert(varchar(20),@date_updated)
	end

if @strMode = 'DELETE_ALL'
	begin
		delete from z_tmp_or_processing where created_by = @uid
	end

if @strMode = 'CHECK_TMP'
	begin
		if exists (select * from z_tmp_or_processing where isnull(or_breakdown,'N') = 'Y' and created_by = @uid and convert(varchar(20),date_created) = convert(varchar(20),@date_updated))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'PROCESS'
	begin
		/*set @tmp_paid_amount = 0
		select @tmp_paid_amount = sum(isnull(paid_amount,0))
		from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
		*/
		
		declare yyy cursor scroll for
		select or_no, amount
		from t_ar_header
		where client_code in (select top 1 client_code from t_invoice_header where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
		order by or_date
	
		open yyy
		fetch next from yyy into @tmp_or_no,@tmp_ar_amount
		while @@fetch_status = 0
			begin
				set @deducted_amount = 0
				set @ar_balance_amount = 0
		
				select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
	
				if isnull(@deducted_amount,0) < isnull(@tmp_ar_amount,0)
					begin
						set @ar_balance_amount = @tmp_ar_amount - isnull(@deducted_amount,0)

						if isnull(@ar_balance_amount,0) > 0
							begin
								if exists(select * from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
									and charge_code in (select charge_code from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
									and total_charge_amount - isnull(paid_amount,0) > 0))
									begin
			
										declare xxx cursor scroll for
										select charge_code from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
										and charge_code in (select charge_code from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
										and total_charge_amount - isnull(paid_amount,0) > 0)
								
										open xxx
										fetch next from xxx into @tmp_charge_code
										while @@fetch_status = 0
											begin	
												set @tmp_invoice_charge_amount = 0
											
												select @tmp_invoice_charge_amount = sum(total_charge_amount - isnull(paid_amount,0))
												from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
												and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
		
												if @ar_balance_amount >= isnull(@tmp_invoice_charge_amount,0)
													begin
														if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))))
															begin
																insert into t_payment_header (or_no,or_no_type,or_date,client_code,document_no,remarks,status,date_updated,updated_by)
																select top 1 or_no,or_no_type,or_date,client_code,document_no,remarks,'P',getdate(),@uid 
																from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
																
															end
		
														insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
														select upper(ltrim(rtrim(@tmp_or_no))),invoice_no,invoice_detail_id,total_charge_amount,total_charge_amount,getdate(),@uid,0
														from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
														and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
		
														update t_invoice_detail set
														paid_amount = total_charge_amount,
														balance_amount = 0
														where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
														and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
													end
												else
													begin
														if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))))
															begin
																insert into t_payment_header (or_no,or_no_type,or_date,client_code,document_no,remarks,status,date_updated,updated_by)
																select top 1 or_no,or_no_type,or_date,client_code,document_no,remarks,'P',getdate(),@uid 
																from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
																
															end
		
														insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
														select upper(ltrim(rtrim(@tmp_or_no))),invoice_no,invoice_detail_id,total_charge_amount,@ar_balance_amount,getdate(),@uid,0
														from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
														and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
		
														update t_invoice_detail set
														paid_amount = isnull(paid_amount,0) + @ar_balance_amount,
														balance_amount = isnull(total_charge_amount,0) - (isnull(paid_amount,0) + @ar_balance_amount)
														where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
														and total_charge_amount - isnull(paid_amount,0) > 0 and charge_code = @tmp_charge_code
													end
												
												fetch next from xxx into @tmp_charge_code
											end	
										close xxx
										deallocate xxx		
									end		
								else --no ar detail 
									begin	
										set @tmp_invoice_charge_amount = 0
									
										select @tmp_invoice_charge_amount = sum(total_charge_amount - isnull(paid_amount,0))
										from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
										and total_charge_amount - isnull(paid_amount,0) > 0 
		
										if @ar_balance_amount >= isnull(@tmp_invoice_charge_amount,0)
											begin
												if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))))
													begin
														insert into t_payment_header (or_no,or_no_type,or_date,client_code,document_no,remarks,status,date_updated,updated_by)
														select top 1 or_no,or_no_type,or_date,client_code,document_no,remarks,'P',getdate(),@uid 
														from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
														
													end
		
												insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
												select upper(ltrim(rtrim(@tmp_or_no))),invoice_no,invoice_detail_id,total_charge_amount,total_charge_amount - isnull(paid_amount,0),getdate(),@uid,0
												from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
												and total_charge_amount - isnull(paid_amount,0) > 0 
		
												update t_invoice_detail set
												paid_amount = total_charge_amount,
												balance_amount = 0
												where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
												and total_charge_amount - isnull(paid_amount,0) > 0 
											end
										else
											if isnull((select count(*) from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and total_charge_amount - isnull(paid_amount,0) > 0 ),0) = 1
												begin
													if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))))
														begin
															insert into t_payment_header (or_no,or_no_type,or_date,client_code,document_no,remarks,status,date_updated,updated_by)
															select top 1 or_no,or_no_type,or_date,client_code,document_no,remarks,'P',getdate(),@uid 
															from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
															
														end
			
													insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,total_charge_amount,or_amount,date_created,created_by,is_selected)
													select top 1 upper(ltrim(rtrim(@tmp_or_no))),invoice_no,invoice_detail_id,total_charge_amount,@ar_balance_amount,getdate(),@uid,0
													from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
													and total_charge_amount - isnull(paid_amount,0) > 0 
			
													update t_invoice_detail set
													paid_amount = isnull(paid_amount,0) + @ar_balance_amount,
													balance_amount = total_charge_amount - (isnull(paid_amount,0) + @ar_balance_amount)
													where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
													and total_charge_amount - isnull(paid_amount,0) > 0 
												end
											/*else
												begin
													insert into z_tmp_or_processing(invoice_no,or_breakdown,date_created,created_by)
													select @invoice_no,'N',@date_updated,@uid
												end*/
									end	
							end										
					end
				
				select @deducted_amount = sum(isnull(or_amount,0)) from t_payment_detail
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
				
				update t_payment_header set amount = @deducted_amount
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

				fetch next from yyy into @tmp_or_no,@tmp_ar_amount
			end
		close yyy
		deallocate yyy

		set @data = 'PROCESS: ' + @invoice_no + ', ' + convert(varchar(20),@invoice_date_from) + ', ' +  convert(varchar(20),@invoice_date_to) + ', ' + @real_property_code
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'PROCESS',@company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_GenerateInvoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO




CREATE PROCEDURE [dbo].[sp_t_GenerateInvoice]
	@strMode varchar(20),
	@billing_from datetime,
	@billing_to datetime,
	@invoice_date datetime,
	@real_property_code varchar(10),
	@tenant_code varchar(10),
	@tenant_name varchar(100),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

/*
	FIXES:

		2022-10-10
		- Added new line of codes to temporary create invoice # sequence if the invoice number already exist on wrong invoice date
*/
declare @next_invoice_no varchar(20),@real_property_code_next char(10),@next_doc_no char(10)
declare @invoice_len_settings int,@client_code char(10),@invoice_no_type char(1)
declare @real_property_code_len_settings int
declare @next_doc_no_settings int,@doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0)
declare @year varchar(4),@month varchar(2)

set @invoice_len_settings = 4
set @real_property_code_len_settings = 5
set @next_doc_no_settings = 10
set @tenant_code = upper(ltrim(rtrim(@tenant_code)))
set @invoice_no_type = 'N'
set @next_doc_no = ''
set @year = cast(datepart(year,@invoice_date) as varchar(4)) 
set @month =  right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) 

if @strMode = 'SAVE'
	begin
		select top 1 @client_code = upper(ltrim(rtrim(bill_to))),@real_property_code_next =  real_property_code from m_tenant where upper(ltrim(rtrim(tenant_code))) = @tenant_code
		set @real_property_code_next = upper(ltrim(@real_property_code_next))

		if not exists (select * from t_invoice_header where client_code = @client_code and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next
			and convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
			and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101)))

			begin
				select @next_invoice_no = max(
								convert(decimal(18,0),right(substring(invoice_no,@real_property_code_len_settings + 1,len(invoice_no)),@invoice_len_settings))
							)
				from t_invoice_header 
				where real_property_code = @real_property_code_next and isnull(invoice_no_type,'') = 'N'
				and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
				and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month
				group by real_property_code
	
				--// USE BELOW CODE IF INVOICE ALREADY EXIST OR INVOICE NOT SYNC with MONTH
				--select 
				--	@next_invoice_no = convert(decimal(18,0),right(substring(max(invoice_no),5 + 1,len(max(invoice_no))),4)) -- TEMPORARY
				--from t_invoice_header 
				--where real_property_code = @real_property_code_next and isnull(invoice_no_type,'') = 'N'
				--and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
				----and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month -- TEMPORARY
				--group by real_property_code

					
				set @next_invoice_no = isnull(@next_invoice_no,0) + 1
		
				set @next_invoice_no = right(ltrim(rtrim(@year)),2) + '-'+@month + '-'+ltrim(rtrim(replace(space(@invoice_len_settings - len(@next_invoice_no)),' ','0') + convert(varchar(4),@next_invoice_no)))
		
				set @next_invoice_no = ltrim(rtrim(@real_property_code_next)) + ' ' + ltrim(rtrim(@next_invoice_no))

				--set @next_invoice_no = 'FT A 17-05-0025'
		
				-- next doc no
				/*select @doc_no1 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_ar_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no2 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_payment_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no3 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_invoice_header where isnumeric(right(document_no,@next_doc_no_settings))=1
		
				if isnull(@doc_no1,0) >= isnull(@doc_no2,0) and isnull(@doc_no1,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no1		
				else if isnull(@doc_no2,0) >= isnull(@doc_no1,0) and isnull(@doc_no2,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no2	 
				else
					set @next_doc_no = @doc_no3

				if isnull(@next_doc_no,0) = 0
					set @next_doc_no = '1'
				else
					set @next_doc_no = @next_doc_no + 1

				set @next_doc_no = ltrim(rtrim(replace(space(@next_doc_no_settings - len(@next_doc_no)),' ','0') + convert(varchar(10),@next_doc_no)))
				--
				*/		
				exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@client_code,@real_property_code_next,@billing_from,@billing_to,
					'',@tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr
			end

		else
			begin
				select top 1 @next_invoice_no = invoice_no,@invoice_date = invoice_date,@next_doc_no = document_no
				from t_invoice_header where client_code = @client_code 
				and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next
				and convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
				and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
				and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
				and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month

				exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@client_code,@real_property_code_next,@billing_from,@billing_to, 
					'',@tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr
			end
		
	end


if @strMode = 'SAVE_PER_PROPERTY'
	begin
		set @real_property_code = upper(ltrim(rtrim(@real_property_code)))

		declare zzz cursor scroll for
		--select upper(ltrim(rtrim(real_property_code))) from m_real_property where upper(ltrim(rtrim(real_property_code))) like @real_property_code + '%'		
		select upper(ltrim(rtrim(real_property_code))) from m_real_property where upper(ltrim(rtrim(real_property_code))) = @real_property_code 

		open zzz
		fetch next from zzz into @real_property_code_next		
		while @@fetch_status = 0
			begin
				declare yyy cursor scroll for	
				select upper(ltrim(rtrim(tenant_code))) from m_tenant where 
					(
						(
						isnull(terminated,'') <> 'Y' and
						convert(datetime,convert(varchar(12),isnull(m_tenant.actual_move_in_date,'1/1/1900'),101)) 
						<= convert(datetime,convert(varchar(12),@billing_from,101))   
						)
					or 
					(isnull(terminated,'') = 'Y' and convert(datetime,convert(varchar(12),isnull(m_tenant.date_terminated,'1/1/1900'),101)) > convert(datetime,convert(varchar(12),@billing_from,101))   ) 
					)
					and (upper(ltrim(rtrim(tenant_type))) = 'OC' and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next)
					or (upper(ltrim(rtrim(tenant_type))) = 'C' and upper(ltrim(rtrim(tenant_code))) in (
						select upper(ltrim(rtrim(bill_to))) from m_tenant where upper(ltrim(rtrim(real_property_code))) = @real_property_code_next
					))
				order by m_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no,m_tenant.tenant_name	
		
				open yyy
				fetch next from yyy into @client_code
				while @@fetch_status = 0
				begin
		
					if not exists (select * from t_invoice_header where client_code = @client_code and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next
						and convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
						and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101)))

						begin
							set @next_invoice_no = 0
							select @next_invoice_no = max(
											convert(decimal(18,0),right(substring(invoice_no,@real_property_code_len_settings + 1,len(invoice_no)),@invoice_len_settings))
										)
							from t_invoice_header 
							where real_property_code = @real_property_code_next and isnull(invoice_no_type,'') = 'N'
							and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
							and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month
							group by real_property_code
							--print @next_invoice_no
							set @next_invoice_no = isnull(@next_invoice_no,0) + 1
					
							set @next_invoice_no = right(ltrim(rtrim(@year)),2) + '-'+@month + '-'+ltrim(rtrim(replace(space(@invoice_len_settings - len(@next_invoice_no)),' ','0') + convert(varchar(4),@next_invoice_no)))
					
							set @next_invoice_no = ltrim(rtrim(@real_property_code_next)) + ' ' + ltrim(rtrim(@next_invoice_no))
							
							/*-- next doc no
							select @doc_no1 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_ar_header where isnumeric(right(document_no,@next_doc_no_settings))=1
							select @doc_no2 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_payment_header where isnumeric(right(document_no,@next_doc_no_settings))=1
							select @doc_no3 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_invoice_header where isnumeric(right(document_no,@next_doc_no_settings))=1
					
							if isnull(@doc_no1,0) >= isnull(@doc_no2,0) and isnull(@doc_no1,0) >= isnull(@doc_no3,0)
								set @next_doc_no = @doc_no1		
							else if isnull(@doc_no2,0) >= isnull(@doc_no1,0) and isnull(@doc_no2,0) >= isnull(@doc_no3,0)
								set @next_doc_no = @doc_no2	 
							else
								set @next_doc_no = @doc_no3
			
							if isnull(@next_doc_no,0) = 0
								set @next_doc_no = '1'
							else
								set @next_doc_no = @next_doc_no + 1
			
							set @next_doc_no = ltrim(rtrim(replace(space(@next_doc_no_settings - len(@next_doc_no)),' ','0') + convert(varchar(10),@next_doc_no)))
							--
							*/
					
							declare ttt cursor scroll for 
							select tenant_code from m_tenant where upper(ltrim(rtrim(bill_to))) = @client_code
							and (isnull(terminated,'') <> 'Y' or 
							(isnull(terminated,'') = 'Y' and convert(datetime,convert(varchar(12),isnull(m_tenant.date_terminated,'1/1/1900'),101)) > convert(datetime,convert(varchar(12),@billing_from,101))   ) 
							)
							and (upper(ltrim(rtrim(tenant_type))) = 'OC' or upper(ltrim(rtrim(tenant_type))) = 'O')
							and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next

							open ttt
							fetch next from ttt into @tenant_code
							while @@fetch_status = 0
								begin
									exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@client_code,@real_property_code_next,@billing_from,@billing_to,
										@next_doc_no,@tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr
									fetch next from ttt into @tenant_code
								end
							close ttt
							deallocate ttt
						end
					
					else
						begin
							select top 1 @next_invoice_no = invoice_no,@invoice_date = invoice_date,@next_doc_no = document_no
							from t_invoice_header where client_code = @client_code 
							and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next
							and convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
							and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
							and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
							and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month
			
							declare ttt cursor scroll for 
							select tenant_code from m_tenant where upper(ltrim(rtrim(bill_to))) = @client_code
							and (isnull(terminated,'') <> 'Y' or 
							(isnull(terminated,'') = 'Y' and convert(datetime,convert(varchar(12),isnull(m_tenant.date_terminated,'1/1/1900'),101)) > convert(datetime,convert(varchar(12),@billing_from,101))   ) 
							)
							and (upper(ltrim(rtrim(tenant_type))) = 'OC' or upper(ltrim(rtrim(tenant_type))) = 'O')
							and upper(ltrim(rtrim(real_property_code))) = @real_property_code_next

							open ttt
							fetch next from ttt into @tenant_code
							while @@fetch_status = 0
								begin
									exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@client_code,@real_property_code_next,@billing_from,@billing_to,
										@next_doc_no,@tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr
									fetch next from ttt into @tenant_code
								end
							close ttt
							deallocate ttt
						end

					fetch next from yyy into @client_code
				end
				close yyy
				deallocate yyy
				fetch next from zzz into @real_property_code_next		
			end
			close zzz
			deallocate zzz
	end


if @strMode = 'RETRIEVE'
	begin
		select * from m_tenant
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		where upper(ltrim(m_tenant.real_property_code)) like upper(ltrim(@real_property_code)) + '%'
	end


if @strMode = 'VIEW'
	begin
		declare @a table (tenant_code varchar(200),tenant_name varchar(200),real_property_code varchar(50),
			building_code varchar(50),unit_no char(20),real_property_name varchar(100),x varchar(10))

		if ltrim(rtrim(@real_property_code)) = ''
			begin
				insert into @a (tenant_code,tenant_name,real_property_code,building_code,unit_no,real_property_name)
				select m_tenant.tenant_code,tenant_name,m_tenant.real_property_code,building_code,unit_no,
				m_real_property.real_property_name from m_tenant 
				left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
				where m_tenant.real_property_code like @real_property_code + '%' 
				and (tenant_code like @tenant_name + '%' or tenant_name like @tenant_name + '%'
				or m_tenant.building_code like @tenant_name + '%' or m_tenant.unit_no like @tenant_name + '%')
				and (
					(isnull(terminated,'') <> 'Y' 
					and 
					convert(datetime,convert(varchar(12),isnull(m_tenant.actual_move_in_date,'1/1/1900'),101)) 
					<= convert(datetime,convert(varchar(12),@billing_from,101))   
					)	
				or 
				(isnull(terminated,'') = 'Y' and convert(datetime,convert(varchar(12),isnull(m_tenant.date_terminated,'1/1/1900'),101)) > convert(datetime,convert(varchar(12),@billing_from,101))   ) 
				)
				and (tenant_type = 'OC' or tenant_type = 'O')
				order by ltrim(rtrim(m_tenant.real_property_code)), ltrim(rtrim(m_tenant.building_code)), ltrim(rtrim(m_tenant.unit_no)),m_tenant.tenant_name		
			end
		else
			begin
				insert into @a (tenant_code,tenant_name,real_property_code,building_code,unit_no,real_property_name)
				select m_tenant.tenant_code,tenant_name,m_tenant.real_property_code,building_code,unit_no,
				m_real_property.real_property_name from m_tenant 
				left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code
				where m_tenant.real_property_code = @real_property_code 
				and (tenant_code like @tenant_name + '%' or tenant_name like @tenant_name + '%'
				or m_tenant.building_code like @tenant_name + '%' or m_tenant.unit_no like @tenant_name + '%')
				and (
					(isnull(terminated,'') <> 'Y' 
					and 
					convert(datetime,convert(varchar(12),isnull(m_tenant.actual_move_in_date,'1/1/1900'),101)) 
					<= convert(datetime,convert(varchar(12),@billing_from,101))   
					)	
				or 
				(isnull(terminated,'') = 'Y' and convert(datetime,convert(varchar(12),isnull(m_tenant.date_terminated,'1/1/1900'),101)) > convert(datetime,convert(varchar(12),@billing_from,101))   ) 
				)
				and (tenant_type = 'OC' or tenant_type = 'O')
				order by ltrim(rtrim(m_tenant.real_property_code)), ltrim(rtrim(m_tenant.building_code)), ltrim(rtrim(m_tenant.unit_no)),m_tenant.tenant_name		
			end


		declare @tmp_tenant_code varchar(50),@y int,@tmp_unit_no varchar(50)
		select @y = max(len(unit_no)) from @a 
		
		declare xxx cursor scroll for
		select tenant_code,unit_no from @a 
		
		open xxx
		fetch next from xxx into @tmp_tenant_code,@tmp_unit_no
		while @@fetch_status = 0
			begin
				update @a set x = case when len(ltrim(rtrim(unit_no))) < @y then 
					replace(space(@y-len(ltrim(rtrim(unit_no)))) + ltrim(rtrim(unit_no)),' ','0')
					else ltrim(rtrim(unit_no)) end
				where tenant_code = @tmp_tenant_code
		
				fetch next from xxx into @tmp_tenant_code,@tmp_unit_no
			end
		close xxx
		deallocate xxx
		
		select * from @a order by  x
	end


/*

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_invoice_header where upper(ltrim(tenant_code)) = upper(ltrim(@code)))
			begin
				delete from m_tenant where upper(ltrim(tenant_code)) = upper(ltrim(@code))
				select 0 as x
			end
		else
			select 1 as x
	end
*/
GO
/****** Object:  StoredProcedure [dbo].[sp_t_GenerateInvoice_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_t_GenerateInvoice_Save]
	@invoice_no varchar(20),
	@invoice_date datetime,
	@client_code char(10),
	@real_property_code varchar(10),
	@billing_from datetime,
	@billing_to datetime,
	@doc_no varchar(20),
	@tenant_code varchar(10),
	@invoice_no_type char(1),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @tmp_invoice_no varchar(20),@tmp_invoice_detail_id decimal(9,0),@tmp_tenant_code char(10),@tmp_charge_code char(5)
declare @next_invoice_detail_id decimal(9,0),@total_usage decimal(9,0)
declare @data nvarchar(4000),@module_name varchar(50)

	if not exists (select * from t_invoice_header where invoice_no = @invoice_no)
		begin
			insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,status,created_by,date_created,updated_by,date_updated)	
			select top 1 @invoice_no,@invoice_no_type,@invoice_date,@client_code,@real_property_code,@billing_from,@billing_to,@doc_no,sap_code,'','',@uid,getdate(),@uid,getdate()
			from m_tenant where upper(ltrim(rtrim(tenant_code))) = @client_code

			set @module_name = 'INVOICE HEADER'
			set @data = 'insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to, ' +
				'document_no,sap_code,remarks,status,created_by,date_created) ' +
				'select top 1 ' + @invoice_no +','+ @invoice_no_type+','+convert(varchar(10),@invoice_date,101)+','+@client_code+','+@real_property_code+','+
				convert(varchar(10),@billing_from,101)+','+convert(varchar(10),@billing_to,101)+','+
				@doc_no+',sap_code,'''','''',' + @uid+','+ convert(varchar(20),getdate()) +
				'from m_tenant where upper(ltrim(rtrim(tenant_code))) = ' + @client_code
			exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
		end
	
	
	update t_tenant_reading_charges
		set trc_invoice_no = '',trc_invoice_detail_id = null,trc_invoice_detail_reading_id=null
	where trc_invoice_detail_id in
		(
		select invoice_detail_id  from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = @invoice_no 
		and invoice_detail_id in 
		(select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = @invoice_no and tenant_code = @tenant_code)
		)
	
	delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = @invoice_no and invoice_detail_id in 
		(select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = @invoice_no and tenant_code = @tenant_code)
	delete from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = @invoice_no and tenant_code = @tenant_code

	insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount)
	select @invoice_no,m_tenant_charges.tenant_code,m_tenant_charges.charge_code,charge_type,charge_amount 
	from m_tenant_charges
	left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
	where upper(ltrim(rtrim(tenant_code))) = @tenant_code 
	and m_tenant_charges.charge_code not in (
			select charge_code from t_invoice_detail where invoice_no = @invoice_no and upper(ltrim(rtrim(tenant_code))) = @tenant_code 
			)
	and (
		(m_tenant_charges.charge_code in (
			select trc_charge_code from t_tenant_reading 
			left join t_tenant_reading_charges on t_tenant_reading.reading_id = t_tenant_reading_charges.trc_reading_id
			where upper(ltrim(rtrim(tenant_code))) = @tenant_code
			and convert(datetime,convert(varchar(12),billing_date_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101))				
			and convert(datetime,convert(varchar(12),billing_date_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
			and isnull(trc_invoice_no,'') = ''		
			)
		and upper(ltrim(rtrim(isnull(m_charges.charge_type,'F')))) = 'U'
		) 
		or upper(ltrim(rtrim(isnull(m_charges.charge_type,'F')))) = 'F'
	)

	declare xxx cursor scroll for
	select invoice_no,invoice_detail_id,tenant_code,charge_code from t_invoice_detail where invoice_no = @invoice_no

	open xxx
	fetch next from xxx into @tmp_invoice_no,@tmp_invoice_detail_id,@tmp_tenant_code,@tmp_charge_code
	while @@fetch_status = 0
		begin
			declare @tmp_reading_id decimal(18,0)

			exec sp_t_Invoice_Detail_Save_Reading 1,@tmp_invoice_no,@tmp_invoice_detail_id,0,@tmp_tenant_code,@tmp_charge_code
					
			select top 1 @total_usage =  isnull(current_reading,0) - isnull(prev_reading,0)
			from t_tenant_reading 
			where reading_id 
			in (
			select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 
			and invoice_detail_id = @tmp_invoice_detail_id
			and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 
			and invoice_detail_id = @tmp_invoice_detail_id
			and charge_code = @tmp_charge_code and tenant_code = @tmp_tenant_code)
			)
				
			print @total_usage

			update t_invoice_detail set 				
				total_charge_amount = case when charge_type = 'U'  then (charge_amount * isnull(@total_usage,0)) else charge_amount end
			where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) and invoice_detail_id = @tmp_invoice_detail_id	
			and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tmp_tenant_code)))
			and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))	
			
			declare @tmp_actual_move_in_date datetime,@tmp_is_terminated char(2),@tmp_termination_date datetime,@tmp_days_diff int,@tmp_days_diff_billing int

			select top 1 @tmp_actual_move_in_date = isnull(actual_move_in_date,'1/1/1900'),@tmp_termination_date = isnull(date_terminated,'1/1/1900'),
				@tmp_is_terminated = isnull(terminated,'N')
			from m_tenant where upper(ltrim(rtrim(tenant_code))) = @tmp_tenant_code

			set @tmp_days_diff_billing = isnull(datediff(day,isnull(CONVERT(VARCHAR(25),DATEADD(dd,-(DAY(@billing_from)-1),@billing_from),101),'1/1/1900'),
					isnull(DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,@billing_to)+1,0)),'1/1/1900')) + 1,1)

			if @tmp_is_terminated = 'Y'
				begin
					if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to and isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
			
							update t_invoice_detail set 				
								total_charge_amount = case when charge_type = 'U'  then (charge_amount * @total_usage) 
									when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then charge_amount
									else (charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) and invoice_detail_id = @tmp_invoice_detail_id				
						end
					else if isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
			
							update t_invoice_detail set 				
								total_charge_amount = case when charge_type = 'U'  then (charge_amount * @total_usage) 
									when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then charge_amount
									else (charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) and invoice_detail_id = @tmp_invoice_detail_id				
						end
				end
			else if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to
				begin
					set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
					--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
	
					update t_invoice_detail set 				
						total_charge_amount = case when charge_type = 'U'  then (charge_amount * @total_usage) 
							when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then charge_amount
							else (charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
					where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) and invoice_detail_id = @tmp_invoice_detail_id				
				end
			else
				begin	
					update t_invoice_detail set 				
						total_charge_amount = case when charge_type = 'U'  then (charge_amount * @total_usage) else charge_amount end
					where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) and invoice_detail_id = @tmp_invoice_detail_id	
					and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tmp_tenant_code)))
					and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))	
				end

			fetch next from xxx into @tmp_invoice_no,@tmp_invoice_detail_id,@tmp_tenant_code,@tmp_charge_code
		end
	close xxx
	deallocate xxx

	if not exists (select * from t_invoice_detail where invoice_no = @invoice_no)
		begin
			delete from t_invoice_header where invoice_no = @invoice_no
			set @module_name = 'GENERATE INVOICE'			
			set @data ='delete from t_invoice_header where invoice_no =' + @invoice_no
			exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE (INVOICE W/OUT DETAIL)',@company_code
		end
	else
		begin
			set @module_name = 'INVOICE DETAIL'
			set @data = 'INSERT: ' + @invoice_no + ', ' + convert(varchar(10),@invoice_date,101) + ', ' + @client_code + ', ' + @real_property_code + ', ' +
					convert(varchar(10),@billing_from,101)+ ', ' +convert(varchar(10),@billing_to,101) + ', ' +@doc_no + ', ' +@tenant_code + ', ' +@invoice_no_type
			exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
		end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_date datetime,
	@tenant_code char(10),
	@billing_from datetime,
	@billing_to datetime,	
	@document_no varchar(20),
	@bill_to char(1),
	@account_no varchar(20),
	@remarks varchar(100),
	@status char(1),
	@real_property_code char(10),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_invoice_no varchar(20),@real_property_code_next char(10),@invoice_no_type char(1),@next_invoice_no_settings int
declare @next_doc_no_settings int,@doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0),@next_doc_no varchar(50)  
declare @tmp_tenant_code char(10)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)
declare @real_property_code_len_settings int

set @module_name = 'INVOICE HEADER'

set @invoice_no_type = ''
set @next_invoice_no_settings = 4
set @real_property_code_len_settings = 5
set @next_doc_no_settings = 10

if @strMode = 'SAVE_NEW'
	begin
		set @year = cast(datepart(year,@invoice_date) as varchar(4)) 
		set @month =  right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) 
		set @real_property_code_next = @real_property_code

		set @next_invoice_no = upper(ltrim(rtrim(@invoice_no)))
		if upper(ltrim(rtrim(@invoice_no))) = ''
			begin
				set @invoice_no_type = 'N'
				select @next_invoice_no = max(
								--convert(decimal(18,0),isnull(right(invoice_no,len(invoice_no) - (charindex(' ',invoice_no))),0))
								convert(decimal(18,0),right(substring(invoice_no,@real_property_code_len_settings + 1,len(invoice_no)),@next_invoice_no_settings))
							)
				from t_invoice_header 
				--left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				where t_invoice_header.real_property_code = @real_property_code --in (select real_property_code from m_tenant where tenant_code = @tenant_code and isnull(terminated,'') <> 'Y')
				and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
				and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month
				and isnull(invoice_no_type,'') = 'N'
				group by t_invoice_header.real_property_code
		
				set @next_invoice_no = isnull(@next_invoice_no,0)
		
				if isnull(@real_property_code_next,'') = ''
					select @real_property_code_next = real_property_code from m_tenant where tenant_code = @tenant_code
		
				set @next_invoice_no = @next_invoice_no + 1
				set @next_invoice_no = right(ltrim(rtrim(@year)),2) + '-'+@month + '-'+ ltrim(rtrim(replace(space(@next_invoice_no_settings - len(@next_invoice_no)),' ','0') + convert(varchar(4),@next_invoice_no)))
				set @next_invoice_no = ltrim(rtrim(@real_property_code_next)) + ' ' + ltrim(rtrim(@next_invoice_no))
			end

		set @document_no = ''

		/*if upper(ltrim(rtrim(isnull(@document_no,'')))) = ''
			begin	
				select @doc_no1 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_ar_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no2 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_payment_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no3 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_invoice_header where isnumeric(right(document_no,@next_doc_no_settings))=1
		
				if isnull(@doc_no1,0) >= isnull(@doc_no2,0) and isnull(@doc_no1,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no1		
				else if isnull(@doc_no2,0) >= isnull(@doc_no1,0) and isnull(@doc_no2,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no2	 
				else
					set @next_doc_no = @doc_no3

				if isnull(@next_doc_no,0) = 0
					set @next_doc_no = '1'
				else
					set @next_doc_no = @next_doc_no + 1

				set @document_no = ltrim(rtrim(replace(space(@next_doc_no_settings - len(@next_doc_no)),' ','0') + convert(varchar(10),@next_doc_no)))

			end
		*/

		if not exists (select * from t_invoice_header where upper(ltrim(invoice_no)) = upper(ltrim(@next_invoice_no)))
			begin
				insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,status,created_by,date_created,updated_by,date_updated)	
				select top 1 upper(@next_invoice_no),@invoice_no_type,@invoice_date,@tenant_code,@real_property_code_next,@billing_from,@billing_to,@document_no,sap_code,'','',@uid,getdate(),@uid,getdate()
				from m_tenant where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) 

				set @data = 'insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,status)	' +
					'select top 1 ' + upper(@next_invoice_no)+ ',' +@invoice_no_type+ ',' +convert(varchar(10),@invoice_date,101)+ ',' +
					@tenant_code+ ','+@real_property_code_next+',' + convert(varchar(10),@billing_from,101)+ ',' +convert(varchar(10),@billing_to,101)+ ',' + @document_no+ ',sap_code,'''',''''' +
					'from m_tenant where upper(ltrim(tenant_code)) =' + upper(ltrim(@tenant_code)) 
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		/*else
			begin
				update t_invoice_header set
					invoice_date = @invoice_date,
					client_code = @tenant_code,
					real_property_code = @real_property_code,
					billing_from = @billing_from,
					billing_to = @billing_to,
					document_no = @document_no,
					sap_code = @account_no,
					remarks = @remarks,
					status = @status
				where upper(ltrim(invoice_no)) = upper(ltrim(@next_invoice_no))

				set @data = 'update t_invoice_header set ' +
					'invoice_date =' + convert(varchar(10),@invoice_date,101) + ',' +
					'client_code =' + @tenant_code+ ',' +
					'real_property_code =' + @real_property_code+ ',' +
					'billing_from =' + convert(varchar(10),@billing_from,101)+ ',' +
					'billing_to =' + convert(varchar(10),@billing_to,101)+ ',' +
					'document_no =' + @document_no+ ',' +
					'sap_code =' + @account_no+ ',' +
					'remarks =' + @remarks+ ',' +
					'status =' + @status +
					'where upper(ltrim(invoice_no)) =' + upper(ltrim(@next_invoice_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		*/
			
		/*insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount,total_charge_amount)
		select distinct upper(@next_invoice_no),tenant_code,m_tenant_charges.charge_code,charge_type,charge_amount,
		case when upper(ltrim(rtrim(isnull(charge_type,'F')))) = 'U' then 0 else charge_amount end from m_tenant_charges
		left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
		where upper(ltrim(rtrim(tenant_code))) in (select upper(ltrim(rtrim(tenant_code))) from m_tenant where upper(ltrim(rtrim(bill_to))) = upper(ltrim(rtrim(@tenant_code))) )*/

		declare yyy cursor scroll for
		select distinct tenant_code from m_tenant_charges
		where upper(ltrim(rtrim(tenant_code))) in (select upper(ltrim(rtrim(tenant_code))) from m_tenant where upper(ltrim(rtrim(bill_to))) = upper(ltrim(rtrim(@tenant_code))))
		open yyy
		
		fetch next from yyy into @tmp_tenant_code
		while @@fetch_status = 0
			begin
				exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@tenant_code,@real_property_code_next,@billing_from,@billing_to,
							@document_no,@tmp_tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr

				fetch next from yyy into @tmp_tenant_code
			end
		close yyy
		deallocate yyy

		select upper(ltrim(@next_invoice_no)) as invoice_no

	end

if @strMode = 'SAVE_EDIT' 
	begin
		update t_invoice_header set
			invoice_date = @invoice_date,
			client_code = @tenant_code,
			real_property_code = @real_property_code,
			billing_from = @billing_from,
			billing_to = @billing_to,
			document_no = @document_no,
			sap_code = @account_no,
			remarks = @remarks,
			status = @status,
			updated_by = @uid,
			date_updated = getdate()
		where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no))

		if upper(ltrim(rtrim(isnull(@status,'')))) = 'V'
			begin
				if exists (select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
					begin
						update t_tenant_reading
							set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
						where reading_id in 
						(
						select reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
						)	
					end
				
				set @data = 'update t_invoice_header set status = ''V'' where upper(ltrim(invoice_no)) = ' + upper(ltrim(@invoice_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code
			end
	end


if @strMode = 'FIND'
	begin
		if exists (select * from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))) 
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'FIND_CLIENT_REAL_PROPERTY'
	begin
		if not exists (select * from m_tenant where bill_to = @tenant_code and real_property_code = @real_property_code)
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'FIND_SAME_BILLING'
	begin
		/*if exists (select * from t_invoice_header where convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
			and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and client_code = @tenant_code and real_property_code = @real_property_code
			) 
			select 1 as x
		else*/
			select 0 as x
	end

if @strMode = 'FIND_SAME_BILLING_EDIT'
	begin
		/*if exists (select * from t_invoice_header where convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
			and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and client_code = @tenant_code
			and upper(ltrim(rtrim(invoice_no))) <> upper(ltrim(rtrim(@invoice_no))) and real_property_code = @real_property_code
			) 
			select 1 as x
		else*/
			select 0 as x
	end

if @strMode = 'RETRIEVE'
	begin
		declare @tmp_invoice_no varchar(20),@total_amount decimal(9,2),@invoice_detail_cnt int
		
		select top 1 @tmp_invoice_no = invoice_no from t_invoice_header
		where upper(ltrim(rtrim(invoice_no))) like '%' +upper(ltrim(rtrim(@invoice_no))) + '%'

		select @total_amount = sum(isnull(total_charge_amount,0)),@invoice_detail_cnt = count(*) from t_invoice_detail
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 

		select top 1 t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,t_invoice_header.real_property_code,t_invoice_header.billing_from,
			t_invoice_header.billing_to,t_invoice_header.document_no,t_invoice_header.sap_code,t_invoice_header.remarks,upper(ltrim(rtrim(t_invoice_header.status))) as status,
			case when upper(ltrim(rtrim(t_invoice_header.status))) = 'P' then 'POSTED' else '' end as status_desc,
			tenant_name,real_property_name,isnull(@total_amount,0) as total_amount,@invoice_detail_cnt as invoice_detail_cnt
		from t_invoice_header
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code		
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 
	end

if @strMode = 'VIEW'
	begin
		select distinct t_invoice_header.invoice_no,CONVERT(VARCHAR(10), invoice_date, 101) as invoice_date,tenant_name,
			upper(ltrim(rtrim(t_invoice_header.status))) as status,
			case when upper(ltrim(rtrim(t_invoice_header.status))) = 'P' then 'POSTED' 
			when upper(ltrim(rtrim(t_invoice_header.status))) = 'V' then 'VOID'
			else '' end as status_desc
		from t_invoice_header 
		inner join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code		
		where invoice_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), invoice_date, 101) like '%' + @remarks + '%'
		order by invoice_no desc
	end

if @strMode = 'VIEW_STAT'
	begin
		select distinct t_invoice_header.invoice_no,CONVERT(VARCHAR(10), invoice_date, 101) as invoice_date,tenant_name,
			upper(ltrim(rtrim(t_invoice_header.status))) as status,
			case when upper(ltrim(rtrim(t_invoice_header.status))) = 'P' then 'POSTED' 
			when upper(ltrim(rtrim(t_invoice_header.status))) = 'V' then 'VOID'
			else '' end as status_desc
		from t_invoice_header 
		inner join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code		
		where (invoice_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), invoice_date, 101) like '%' + @remarks + '%')
		and upper(ltrim(rtrim(isnull(t_invoice_header.status,'')))) = ''
		order by invoice_no desc
	end

if @strMode = 'TENANT_SEARCH'
	begin
		select *,real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		where (tenant_name like @remarks + '%' or tenant_code like @remarks + '%'
		or building_code like @remarks + '%' or unit_no like @remarks + '%')
		--and isnull(terminated,'N') <> 'Y'
		and (upper(ltrim(rtrim(isnull(tenant_type,'')))) = 'C' or upper(ltrim(rtrim(isnull(tenant_type,'')))) = 'OC')
		and tenant_code in
			(
			select bill_to from m_tenant where tenant_code in
				(
				select tenant_code from m_tenant_charges
				)
			)
		order by tenant_name,real_property_name,building_code,unit_no
	end

if @strMode = 'VOID'
	begin
		if not exists (select * from t_invoice_header where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no)) and ltrim(rtrim(isnull(status,''))) <> '')
			begin
				if exists (select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
					begin
						update t_tenant_reading
							set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
						where reading_id in 
						(
						select reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
						)	
					end

				update t_invoice_header set status = 'V' where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no))
				set @data = 'update t_invoice_header set status = ''V'' where upper(ltrim(invoice_no)) = ' + upper(ltrim(@invoice_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Check]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Check]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_date datetime,
	@tenant_code char(10),
	@billing_from datetime,
	@billing_to datetime,	
	@document_no varchar(20),
	@bill_to char(1),
	@account_no varchar(20),
	@remarks varchar(100),
	@status char(1),
	@real_property_code char(10),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_invoice_no varchar(20),@real_property_code_next char(10),@invoice_no_type char(1),@next_invoice_no_settings int
declare @next_doc_no_settings int,@doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0),@next_doc_no varchar(50)  
declare @tmp_tenant_code char(10)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)
declare @real_property_code_len_settings int

set @module_name = 'INVOICE HEADER'

set @invoice_no_type = ''
set @next_invoice_no_settings = 4
set @real_property_code_len_settings = 5
set @next_doc_no_settings = 10

if @strMode = 'FIND'
	begin
		if exists (select * from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))) 
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'FIND_CLIENT_REAL_PROPERTY'
	begin
		if not exists (select * from m_tenant where bill_to = @tenant_code and real_property_code = @real_property_code)
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'FIND_SAME_BILLING'
	begin
		/*if exists (select * from t_invoice_header where convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
			and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and client_code = @tenant_code and real_property_code = @real_property_code
			) 
			select 1 as x
		else*/
			select 0 as x
	end

if @strMode = 'FIND_SAME_BILLING_EDIT'
	begin
		/*if exists (select * from t_invoice_header where convert(datetime,convert(varchar(12),billing_from,101)) = convert(datetime,convert(varchar(12),@billing_from,101)) 
			and convert(datetime,convert(varchar(12),billing_to,101)) = convert(datetime,convert(varchar(12),@billing_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and client_code = @tenant_code
			and upper(ltrim(rtrim(invoice_no))) <> upper(ltrim(rtrim(@invoice_no))) and real_property_code = @real_property_code
			) 
			select 1 as x
		else*/
			select 0 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Client_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Client_List]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	set @ssql = ' select m_tenant.*,m_real_property.real_property_name, ' +
		' case ' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''OC'' then ''OCCUPANT & CLIENT''' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''O'' then ''OCCUPANT''' +
			' when ltrim(rtrim(m_tenant.tenant_type)) = ''C'' then ''CLIENT''' +
			' else '''' ' +
		' end as tenant_type_desc,' +
		' case when ltrim(rtrim(m_tenant.tenant_type)) = ''O'' then bill_to.tenant_name else '''' end as bill_to_name, ' +
		' case when isnull(m_tenant.terminated,''N'') = ''N'' then ''NO'' else ''YES'' end as is_terminated ' +
		' from m_tenant ' +
		 'left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code' +
		' left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code ' 

		if ltrim(rtrim(@column_code)) <> '' 
			begin
				set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
			end

		if @strMode = 'VIEW_STAT'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (m_tenant.tenant_code not in (select tenant_code from m_tenant_charges) ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.terminated,'''')))) <> ''Y'' ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.tenant_type,''OC'')))) <> ''C'' ' +
					' ) '
			end

		if @strMode = 'TENANT_READING_LOOKUP'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (m_tenant.tenant_code  in (select tenant_code from m_tenant_charges left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code where isnull(charge_type,'''') = ''U'') ' +
					' and upper(ltrim(rtrim(isnull(m_tenant.tenant_type,''OC'')))) <> ''C'' ' +
					' ) '
			end

		if @strMode = 'INVOICE_CLIENT_LOOKUP'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'''')))) = ''C'' or upper(ltrim(rtrim(isnull(m_tenant.tenant_type,'''')))) = ''OC'')
							and m_tenant.tenant_code in (select bill_to from m_tenant where tenant_code in (select tenant_code from m_tenant_charges
							) and (upper(ltrim(rtrim(isnull(m_tenant.terminated,'''')))) <> ''Y'' 
								or (upper(ltrim(rtrim(isnull(m_tenant.terminated,'''')))) = ''Y'' and m_tenant.date_terminated >= dateadd(day,-15,getdate()))
								)
							)'
			end

		if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by m_tenant.tenant_code'
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Detail_Save_Reading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Detail_Save_Reading]
	@strMode int,
	@invoice_no varchar(20),
	@invoice_detail_id decimal(18,0),
	@invoice_detail_reading_id decimal(18,0),
	@tenant_code varchar(20),
	@charge_code char(10)
AS

declare @billing_from datetime,@billing_to datetime
declare @reading_id decimal(18,0)

select top 1 @billing_from = billing_from,@billing_to = billing_to
from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 

if @strMode = 1
	begin
		if not exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
			and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
			and charge_code = @charge_code and tenant_code = @tenant_code)
			)
			begin
				if exists (select * from t_tenant_reading 
				left join t_tenant_reading_charges on reading_id = trc_reading_id
				where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
				and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
				and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
				and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
				and isnull(trc_invoice_no,'') = ''			
				)			
				begin
					select top 1 @reading_id = reading_id
					from t_tenant_reading 					left join t_tenant_reading_charges on reading_id = trc_reading_id
					where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
					and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
					and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
					and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
					and isnull(trc_invoice_no,'') = ''

					insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
					select top 1 @invoice_no,@invoice_detail_id,@reading_id

					update t_tenant_reading_charges
						set trc_invoice_no = @invoice_no,trc_invoice_detail_id = @invoice_detail_id,
						trc_invoice_detail_reading_id=(
									select max(invoice_detail_reading_id) from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
									and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
									and charge_code = @charge_code and tenant_code = @tenant_code)
									)
					where trc_reading_id = @reading_id and trc_charge_code = @charge_code
					
				end	
			end
		else
			begin
				if exists (select * from t_tenant_reading 
				left join t_tenant_reading_charges on reading_id = trc_reading_id
				where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
				and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
				and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
				and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
				and isnull(trc_invoice_no,'') = ''
				)	
				begin
					select top 1 @reading_id = reading_id	
					 from t_tenant_reading 
					left join t_tenant_reading_charges on reading_id = trc_reading_id
					where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
					and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
					and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
					and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
					and isnull(trc_invoice_no,'') = ''				

					update t_tenant_reading_charges
						set trc_invoice_no = '',trc_invoice_detail_id = null,trc_invoice_detail_reading_id=null
					where trc_reading_id = @reading_id and trc_charge_code = @charge_code
	
					delete from t_invoice_detail_reading  where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
					and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
					and charge_code = @charge_code and tenant_code = @tenant_code)
	
					insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
					select top 1 @invoice_no,@invoice_detail_id,reading_id
					from t_tenant_reading 
					left join t_tenant_reading_charges on reading_id = trc_reading_id
					where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
					and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
					and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
					and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
					and isnull(trc_invoice_no,'') = ''

					update t_tenant_reading_charges
						set trc_invoice_no = @invoice_no,trc_invoice_detail_id = @invoice_detail_id,
						trc_invoice_detail_reading_id=(
									select max(invoice_detail_reading_id) from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
									and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
									and charge_code = @charge_code and tenant_code = @tenant_code)
									)
					where trc_reading_id = @reading_id and trc_charge_code = @charge_code
				end
			end
	end

if @strMode = 2
	begin
		if exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
			and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
			and charge_code = @charge_code and tenant_code = @tenant_code))
			begin
				select top 1 @reading_id = reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
				and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
				and charge_code = @charge_code and tenant_code = @tenant_code)

				update t_tenant_reading_charges
					set trc_invoice_no = '',trc_invoice_detail_id = null,trc_invoice_detail_reading_id=null
				where trc_reading_id =@reading_id and trc_charge_code = @charge_code 

				delete from t_invoice_detail_reading  where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
				and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
				and charge_code = @charge_code and tenant_code = @tenant_code)
			end
	end

if @strMode = 3
	begin
		select top 1 @reading_id = reading_id
		from t_tenant_reading 		left join t_tenant_reading_charges on reading_id = trc_reading_id
		where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
		and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
		and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
		and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
		and isnull(trc_invoice_no,'') = ''

		insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
		select top 1 @invoice_no,@invoice_detail_id,@reading_id

		update t_tenant_reading_charges
			set trc_invoice_no = upper(ltrim(rtrim(@invoice_no))),
			trc_invoice_detail_id = @invoice_detail_id,
			trc_invoice_detail_reading_id=(select max(invoice_detail_reading_id) from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
		where trc_reading_id = @reading_id and trc_charge_code = @charge_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_List]
	@strMode varchar(50),
	@column_code varchar(500),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000),@data_type char(1)
declare @tmp_column_code_year varchar(500)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @tmp_column_code_year = 'cast(datepart(year,t_invoice_header.invoice_date) as varchar(4))'

	set @ssql = 'select distinct t_invoice_header.invoice_no,CONVERT(VARCHAR(10), invoice_date, 101) as invoice_date,tenant_name, ' +
			' upper(ltrim(rtrim(t_invoice_header.status))) as status, ' +
			' case when upper(ltrim(rtrim(t_invoice_header.status))) = ''P'' then ''POSTED''  ' +
			' when upper(ltrim(rtrim(t_invoice_header.status))) = ''V'' then ''VOID'' ' +
			' else '''' end as status_desc, ' +
		'case when isnull(m_tenant.unit_no,'''') = '''' then '''' else
		ltrim(rtrim(m_tenant.real_property_code)) + '' / '' + ltrim(rtrim(m_tenant.building_code)) + '' / ''
		 + m_tenant.unit_no end as unit_no, ' + 
		'm_tenant.real_property_code,m_tenant.building_code ' +
		', CONVERT(datetime, t_invoice_header.invoice_date)  as invoice_date1,t_invoice_detail.total_inv_amt ' +
		' from t_invoice_header  ' +
		' inner join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code ' +
		' left join (select invoice_no, total_inv_amt = sum(isnull(total_charge_amount,0)) from t_invoice_detail group by invoice_no) ' +
		' t_invoice_detail on t_invoice_header.invoice_no=t_invoice_detail.invoice_no '

		/*select @total_amount = sum(isnull(total_charge_amount,0)),@invoice_detail_cnt = count(*) from t_invoice_detail
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) */

	if ltrim(rtrim(@column_code)) <> '' 
		begin
			if @data_type = 'S'
				set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
			else if @data_type = 'D' and @keyword <> ''
				set @ssql = @ssql + ' where convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
		end

	if @strMode = 'VIEW_STAT'
		begin
			if ltrim(rtrim(@column_code)) <> '' 
				set @ssql = @ssql + ' and '
			else
				set @ssql = @ssql + ' where '

			set @ssql = @ssql +' upper(ltrim(rtrim(isnull(t_invoice_header.status,'''')))) = '''' '
		end

	if @column_code <> @tmp_column_code_year and isnull(@keyword,'') = '' -- to display invoices of current year
		begin
			if ltrim(rtrim(@column_code)) <> '' 
				set @ssql = @ssql + ' and '
			else
				set @ssql = @ssql + ' where '

			set @ssql = @ssql +' cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = cast(datepart(year,getdate()) as varchar(4)) and
					cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)) = cast(datepart(month,getdate()) as varchar(2))'
		end
		

	if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by t_invoice_header.invoice_no desc'
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Retrieve]
	@strMode varchar(50),
	@invoice_no varchar(20)
AS

if @strMode = 'RETRIEVE'
	begin
		declare @tmp_invoice_no varchar(20),@total_amount decimal(18,2),@invoice_detail_cnt int
		
		select top 1 @tmp_invoice_no = invoice_no from t_invoice_header
		where upper(ltrim(rtrim(invoice_no))) like '%' +upper(ltrim(rtrim(@invoice_no))) + '%'

		select @total_amount = sum(isnull(total_charge_amount,0)),@invoice_detail_cnt = count(*) from t_invoice_detail
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 

		select top 1 t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,t_invoice_header.real_property_code,t_invoice_header.billing_from,
			t_invoice_header.billing_to,t_invoice_header.document_no,t_invoice_header.sap_code,t_invoice_header.remarks,upper(ltrim(rtrim(t_invoice_header.status))) as status,
			case when upper(ltrim(rtrim(t_invoice_header.status))) = 'P' then 'POSTED' else '' end as status_desc,
			tenant_name,real_property_name,isnull(@total_amount,0) as total_amount,@invoice_detail_cnt as invoice_detail_cnt
		from t_invoice_header
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code		
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@tmp_invoice_no))) 
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Save]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_date datetime,
	@tenant_code char(10),
	@billing_from datetime,
	@billing_to datetime,	
	@document_no varchar(20),
	@bill_to char(1),
	@account_no varchar(20),
	@remarks varchar(100),
	@status char(1),
	@real_property_code char(10),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @next_invoice_no varchar(20),@real_property_code_next char(10),@invoice_no_type char(1),@next_invoice_no_settings int
declare @next_doc_no_settings int,@doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0),@next_doc_no varchar(50)  
declare @tmp_tenant_code char(10)
declare @data nvarchar(max),@module_name varchar(50),@year varchar(4),@month varchar(2)
declare @real_property_code_len_settings int

set @module_name = 'INVOICE HEADER'

set @invoice_no_type = ''
set @next_invoice_no_settings = 4
set @real_property_code_len_settings = 5
set @next_doc_no_settings = 10

if @strMode = 'SAVE_NEW'
	begin
		set @year = cast(datepart(year,@invoice_date) as varchar(4)) 
		set @month =  right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) 
		set @real_property_code_next = @real_property_code

		set @next_invoice_no = upper(ltrim(rtrim(@invoice_no)))
		if upper(ltrim(rtrim(@invoice_no))) = ''
			begin
				set @invoice_no_type = 'N'
				select @next_invoice_no = max(
								--convert(decimal(18,0),isnull(right(invoice_no,len(invoice_no) - (charindex(' ',invoice_no))),0))
								convert(decimal(18,0),right(substring(invoice_no,@real_property_code_len_settings + 1,len(invoice_no)),@next_invoice_no_settings))
							)
				from t_invoice_header 
				--left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				where t_invoice_header.real_property_code = @real_property_code --in (select real_property_code from m_tenant where tenant_code = @tenant_code and isnull(terminated,'') <> 'Y')
				and cast(datepart(year,t_invoice_header.invoice_date) as varchar(4)) = @year
				and right('00'+ cast(datepart(month,t_invoice_header.invoice_date) as varchar(2)),2)  = @month
				and isnull(invoice_no_type,'') = 'N'
				group by t_invoice_header.real_property_code
		
				set @next_invoice_no = isnull(@next_invoice_no,0)
		
				if isnull(@real_property_code_next,'') = ''
					select @real_property_code_next = real_property_code from m_tenant where tenant_code = @tenant_code
		
				set @next_invoice_no = @next_invoice_no + 1
				set @next_invoice_no = right(ltrim(rtrim(@year)),2) + '-'+@month + '-'+ ltrim(rtrim(replace(space(@next_invoice_no_settings - len(@next_invoice_no)),' ','0') + convert(varchar(4),@next_invoice_no)))
				set @next_invoice_no = ltrim(rtrim(@real_property_code_next)) + ' ' + ltrim(rtrim(@next_invoice_no))
			end

		set @document_no = ''

		if not exists (select * from t_invoice_header where upper(ltrim(invoice_no)) = upper(ltrim(@next_invoice_no)))
			begin
				insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,status,created_by,date_created,updated_by,date_updated)	
				select top 1 upper(@next_invoice_no),@invoice_no_type,@invoice_date,@tenant_code,@real_property_code_next,@billing_from,@billing_to,@document_no,sap_code,'','',@uid,getdate(),@uid,getdate()
				from m_tenant where upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) 

				set @data = 'insert into t_invoice_header(invoice_no,invoice_no_type,invoice_date,client_code,real_property_code,billing_from,billing_to,document_no,sap_code,remarks,status)	' +
					'select top 1 ' + upper(@next_invoice_no)+ ',' +@invoice_no_type+ ',' +convert(varchar(10),@invoice_date,101)+ ',' +
					@tenant_code+ ','+@real_property_code_next+',' + convert(varchar(10),@billing_from,101)+ ',' +convert(varchar(10),@billing_to,101)+ ',' + @document_no+ ',sap_code,'''',''''' +
					'from m_tenant where upper(ltrim(tenant_code)) =' + upper(ltrim(@tenant_code)) 
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end

		declare yyy cursor scroll for
		select distinct tenant_code from m_tenant_charges
		where upper(ltrim(rtrim(tenant_code))) in (select upper(ltrim(rtrim(tenant_code))) from m_tenant where upper(ltrim(rtrim(bill_to))) = upper(ltrim(rtrim(@tenant_code))))
		and tenant_code in (select tenant_code from m_tenant where real_property_code = @real_property_code_next)
		open yyy
		
		fetch next from yyy into @tmp_tenant_code
		while @@fetch_status = 0
			begin
				exec sp_t_GenerateInvoice_Save @next_invoice_no,@invoice_date,@tenant_code,@real_property_code_next,@billing_from,@billing_to,
							@document_no,@tmp_tenant_code,@invoice_no_type,@uid,@company_code,@ip_addr

				fetch next from yyy into @tmp_tenant_code
			end
		close yyy
		deallocate yyy

		select upper(ltrim(@next_invoice_no)) as invoice_no

	end

if @strMode = 'SAVE_EDIT' 
	begin
		update t_invoice_header set
			invoice_date = @invoice_date,
			client_code = @tenant_code,
			real_property_code = @real_property_code,
			billing_from = @billing_from,
			billing_to = @billing_to,
			document_no = @document_no,
			sap_code = @account_no,
			remarks = @remarks,
			status = @status,
			updated_by = @uid,
			date_updated = getdate()
		where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no))

		if upper(ltrim(rtrim(isnull(@status,'')))) = 'V'
			begin
				if exists (select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
					begin
						update t_tenant_reading_charges
							set trc_invoice_no = '',trc_invoice_detail_id = null,trc_invoice_detail_reading_id=null
						where trc_reading_id in 
						(
						select reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
						)	
					end
				
				set @data = 'update t_invoice_header set status = ''V'' where upper(ltrim(invoice_no)) = ' + upper(ltrim(@invoice_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Invoice_Void]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Invoice_Void]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'INVOICE HEADER'

if @strMode = 'VOID'
	begin
		if not exists (select * from t_invoice_header where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no)) and ltrim(rtrim(isnull(status,''))) <> '')
			begin
				if exists (select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))))
					begin
						update t_tenant_reading_charges
							set trc_invoice_no = '',trc_invoice_detail_id = null,trc_invoice_detail_reading_id=null
						where trc_reading_id in 
						(
						select reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
						)	
					end

				update t_invoice_header set status = 'V' where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no))
				set @data = 'update t_invoice_header set status = ''V'' where upper(ltrim(invoice_no)) = ' + upper(ltrim(@invoice_no))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetail]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@tenant_code char(10),
	@charge_code char(5),
	@charge_type char(1),
	@charge_amount decimal(18,6),
	@remarks varchar(100),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @invoice_amount decimal(9,2),@total_usage decimal(9,0)
declare @billing_from datetime,@billing_to datetime,@tmp_tenant_code char(10),@tmp_charge_code char(5),@tmp_charge_type char(1),@tmp_actual_move_in_date datetime
declare @tmp_is_terminated char(2),@tmp_termination_date datetime,@tmp_days_diff int,@tmp_days_diff_billing int
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'INVOICE DETAIL'

select top 1 @charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))

select top 1 @billing_from = billing_from,@billing_to = billing_to
from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 

if @strMode = 'SAVE'
	begin
		if @invoice_detail_id = 0
			begin
				insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount)	
				select top 1 upper(ltrim(rtrim(@invoice_no))),upper(@tenant_code),upper(@charge_code),charge_type,@charge_amount from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))

				select @invoice_detail_id = max(invoice_detail_id) from t_invoice_detail
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
				and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
				and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))				

				set @data = 'insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount) ' +
					'select top 1 ' + upper(ltrim(rtrim(@invoice_no)))+',' +upper(@tenant_code)+',' +upper(@charge_code)+',charge_type,' +
					convert(varchar(10),@charge_amount) +' from m_charges where upper(ltrim(rtrim(charge_code))) ='+ upper(ltrim(rtrim(@charge_code)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code				
			end

		-- CHECK READING

		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))),@tmp_charge_code = upper(ltrim(rtrim(charge_code)))
		from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

		select top 1 @tmp_charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))

		if upper(ltrim(rtrim(@charge_code))) <> @tmp_charge_code 
			begin			
				if @tmp_charge_type = 'U' and @charge_type = 'U'
				begin
					if not exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
						begin
							if exists (select * from t_tenant_reading 
							where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
							and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
							and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
							and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
							and isnull(invoice_no,'') = ''			
							)			
							begin
								insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
								select top 1 @invoice_no,@invoice_detail_id,reading_id
								from t_tenant_reading 
								where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
								and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
								and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
								and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
								and isnull(invoice_no,'') = ''
							end
						end
					else
						begin
							if exists (select * from t_tenant_reading 
							where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
							and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
							and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
							and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
							and isnull(invoice_no,'') = ''
							)	
							begin
								update t_tenant_reading
									set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
								where reading_id in 
								(
								select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	
								)	
		
								delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id							

								insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
								select top 1 @invoice_no,@invoice_detail_id,reading_id
								from t_tenant_reading 
								where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
								and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
								and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
								and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
								and isnull(invoice_no,'') = ''
							end
						end
				end

				else if @tmp_charge_type = 'U' and @charge_type = 'F'
				begin
					if exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
						begin
							update t_tenant_reading
								set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
							where reading_id in 
							(
							select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	
							)	

							delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
						end
				end		
				
				else if @tmp_charge_type = 'F' and @charge_type = 'U'
				begin
					insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
					select top 1 @invoice_no,@invoice_detail_id,reading_id
					from t_tenant_reading 
					where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
					and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
					and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
					and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
					and isnull(invoice_no,'') = ''

					update t_tenant_reading
						set invoice_no = upper(ltrim(rtrim(@invoice_no))),invoice_detail_id = @invoice_detail_id,
						invoice_detail_reading_id=(select max(invoice_detail_reading_id) from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
					where reading_id in 
					(
					select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	
					)	
				end
			end

		else
			begin
				if @tmp_charge_type = 'U' and @charge_type = 'U'
				begin
					if not exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
						begin
							if exists (select * from t_tenant_reading 
							where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
							and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
							and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
							and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
							and isnull(invoice_no,'') = ''	
							)					
							begin
								insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
								select top 1 @invoice_no,@invoice_detail_id,reading_id
								from t_tenant_reading 
								where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
								and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
								and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
								and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
								and isnull(invoice_no,'') = ''
							end
						end
					else
						begin
							if exists (select * from t_tenant_reading 
							where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
							and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
							and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
							and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
							and isnull(invoice_no,'') = ''
							)	
							begin
								update t_tenant_reading
									set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
								where reading_id in 
								(
								select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	
								)	
		
								delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id							

								insert into  t_invoice_detail_reading(invoice_no,invoice_detail_id,reading_id)
								select top 1 @invoice_no,@invoice_detail_id,reading_id
								from t_tenant_reading 
								where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
								and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
								and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
								and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
								and isnull(invoice_no,'') = ''
							end
						end
				end
			end

		--END TENANT READING
		declare @reading_id decimal(9,0),@invoice_detail_reading_id decimal(9,0)
		set @total_usage = 0
		
		select top 1 @total_usage =  isnull(current_reading,0) - isnull(prev_reading,0),@reading_id = t_tenant_reading.reading_id,
			@invoice_detail_reading_id = t_invoice_detail_reading.invoice_detail_reading_id
		from t_tenant_reading 
		left join t_invoice_detail_reading on t_tenant_reading.reading_id = t_invoice_detail_reading.reading_id
		where upper(ltrim(rtrim(t_invoice_detail_reading.invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and t_invoice_detail_reading.invoice_detail_id = @invoice_detail_id

		update t_tenant_reading
			set invoice_no = @invoice_no,
			invoice_detail_id = @invoice_detail_id,
			invoice_detail_reading_id = @invoice_detail_reading_id
		where reading_id = @reading_id

		select top 1 @tmp_actual_move_in_date = isnull(actual_move_in_date,'1/1/1900'),@tmp_termination_date = isnull(date_terminated,'1/1/1900'),
			@tmp_is_terminated = isnull(terminated,'N')
		from m_tenant where upper(ltrim(rtrim(tenant_code))) = @tmp_tenant_code

		if @tmp_is_terminated = 'Y'
				begin
					if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to and isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
					
							update t_invoice_detail set 
								charge_code = @charge_code,
								charge_type = @charge_type,
								charge_amount = @charge_amount,
								total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
									else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

							set @data = 'update t_invoice_detail set ' +
								'charge_code =' + @charge_code + ',' +
								'charge_type =' + @charge_type + ',' +
								'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
								'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
									'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
								'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
						end

					else if isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)

							update t_invoice_detail set 
								charge_code = @charge_code,
								charge_type = @charge_type,
								charge_amount = @charge_amount,
								total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
									else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

							set @data = 'update t_invoice_detail set ' +
								'charge_code =' + @charge_code + ',' +
								'charge_type =' + @charge_type + ',' +
								'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
								'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
									'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
								'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
						end
				end
		else if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to
			begin
				set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
				set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)

				update t_invoice_detail set 
					charge_code = @charge_code,
					charge_type = @charge_type,
					charge_amount = @charge_amount,
					total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
						else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

				set @data = 'update t_invoice_detail set ' +
					'charge_code =' + @charge_code + ',' +
					'charge_type =' + @charge_type + ',' +
					'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
					'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
						'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
					'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
			end
		else
			begin
				update t_invoice_detail set 
					--tenant_code = @tenant_code,
					charge_code = @charge_code,
					charge_type = @charge_type,
					charge_amount = @charge_amount,
					total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) else @charge_amount end
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

				set @data = 'update t_invoice_detail set ' +
					'charge_code =' + @charge_code + ',' +
					'charge_type =' + @charge_type + ',' +
					'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
					'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
						'else ' + convert(varchar(10),@charge_amount) + ' end ' +
					'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
			end

		if ltrim(rtrim(isnull(@data,''))) <> ''
			begin
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end 
	end

		


if @strMode = 'FIND'
	begin
		--if exists (select * from t_invoice_detail where upper(ltrim(@invoice_no)) = upper(ltrim(@invoice_no)) and upper(ltrim(tenant_code)) = upper(ltrim(@tenant_code)) and upper(ltrim(charge_code)) = upper(ltrim(@charge_code)) and invoice_detail_id = @invoice_detail_id)
	
		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))),@tmp_charge_code = upper(ltrim(rtrim(charge_code)))
		from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

		select top 1 @tmp_charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))

		if @invoice_detail_id <> 0
			begin
				if upper(ltrim(rtrim(@charge_code))) <> @tmp_charge_code 
					begin			
						if @tmp_charge_type = 'U' and @charge_type = 'U'
						begin
							if not exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
								begin
									if not exists (select * from t_tenant_reading 
									where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
									and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
									and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
									and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
									and isnull(invoice_no,'') = ''
									)	
										begin
											select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
										end
									else
										begin
											select 0 as x,'' as msg
										end
								end
							else
								begin
									if not exists (select * from t_tenant_reading 
									where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
									and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
									and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
									and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
									and isnull(invoice_no,'') = ''
									)	
										begin
											select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
										end
									else
										begin
											select 0 as x,'' as msg
										end
								end
						end
		
						else if @tmp_charge_type = 'U' and @charge_type = 'F'
							begin
								select 0 as x,'' as msg
							end		
						
						else if @tmp_charge_type = 'F' and @charge_type = 'U'
							begin
								if not exists (select * from t_tenant_reading 
								where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
								and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
								and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
								and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
								and isnull(invoice_no,'') = ''
								)	
									begin
										select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
									end
								else
									begin
										select 0 as x,'' as msg
									end
							end
						
						else
							select 0 as x,'' as msg
					end
				else
					select 0 as x,'' as msg
			end
		else
			begin
				if @charge_type = 'U'
					begin
						if not exists (select * from t_tenant_reading 
						where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
						and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
						and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
						and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))
						and isnull(invoice_no,'') = ''
						)	
							begin
								select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not added.' as msg
							end
						else
							begin
								select 0 as x,'' as msg
							end
					end
				else
					select 0 as x,'' as msg
			end
	end


if @strMode = 'DELETE'		
	begin
		if exists (select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
			begin
				update t_tenant_reading
					set invoice_no = '',invoice_detail_id = null,invoice_detail_reading_id=null
				where reading_id in 
				(
				select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	
				)	
		
				delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	

				set @module_name = 'INVOICE DETAIL READING'
				set @data = 'delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = ' +upper(ltrim(rtrim(@invoice_no))) + ' and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				set @module_name = 'INVOICE DETAIL'
			end
		
		delete from t_invoice_detail
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id							

		set @data = 'delete from t_invoice_detail ' +
			'where upper(ltrim(rtrim(invoice_no))) = ' +upper(ltrim(rtrim(@invoice_no))) + ' and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

		select 0 as x
	end

if @strMode = 'RETRIEVE'
	begin
		select t_invoice_detail.invoice_detail_id,t_invoice_detail.invoice_no,t_invoice_detail.charge_code,charge_amount,total_charge_amount,
		'(' + convert(varchar(10),date_from,101) + '-' + convert(varchar(10),date_to,101) + ') '
		+
		case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then
			'Current Reading: ' + convert(varchar(10),current_reading) + '; Previous Reading: ' + convert(varchar(10),prev_reading) + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';' 
		else '' 
		end as remarks,m_tenant.tenant_name + ' (' + m_tenant.real_property_code + '/' + m_tenant.building_code + '/' + m_tenant.unit_no + ')' as tenant_name,
		t_invoice_detail.tenant_code,
		charge_desc,gl_code,upper(ltrim(rtrim(t_invoice_detail.charge_type))) as charge_type,
		case when upper(ltrim(rtrim(t_invoice_detail.charge_type))) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type_desc,
		t_invoice_detail_reading.invoice_detail_reading_id,
		t_invoice_detail_reading.reading_id
		from t_invoice_detail		
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		where upper(ltrim(t_invoice_detail.invoice_no)) like upper(ltrim(@invoice_no)) + '%'
		order by m_tenant.tenant_name,charge_desc
	end

if @strMode = 'RETRIEVE_INVOICE'
	begin
		select top 1 @invoice_no = invoice_no from t_invoice_header
		where upper(ltrim(invoice_no)) like  '%'  + upper(ltrim(@invoice_no)) + '%' 

		select @invoice_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail where upper(ltrim(invoice_no)) = upper(ltrim(@invoice_no)) 

		select top 1 t_invoice_header.invoice_no,tenant_name as client_name,isnull(@invoice_amount,0) as invoice_amount,
			upper(ltrim(rtrim(t_invoice_header.status))) as status,
			case when upper(ltrim(rtrim(t_invoice_header.status))) = 'P' then 'POSTED' else '' end as status_desc
		from t_invoice_header 
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		where upper(ltrim(invoice_no)) like upper(ltrim(@invoice_no)) 
	end


if @strMode = 'TENANT_SEARCH'
	begin
		select *,real_property_name from m_tenant 
		inner join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		where (tenant_name like @remarks + '%' or tenant_code like @remarks + '%'
		or building_code like @remarks + '%' or unit_no like @remarks + '%')
		and (isnull(terminated,'N') <> 'Y' or (isnull(terminated,'N') = 'Y' and m_tenant.date_terminated >= @billing_from and m_tenant.date_terminated <= @billing_to))
		and (tenant_type = 'OC' or tenant_type = 'O')
		and upper(ltrim(rtrim(bill_to))) in (select upper(ltrim(rtrim(client_code))) from t_invoice_header where  upper(ltrim(rtrim(invoice_no))) like upper(ltrim(rtrim(@invoice_no))) )
		order by tenant_name,real_property_name,building_code,unit_no
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetail_Check]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetail_Check]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(18,0),
	@invoice_detail_reading_id decimal(18,0),
	@tenant_code char(10),
	@charge_code char(5),
	@charge_type char(1),
	@reading_id decimal(18,0)
AS

declare @invoice_amount decimal(9,2),@total_usage decimal(9,0)
declare @billing_from datetime,@billing_to datetime,@tmp_tenant_code char(10),@tmp_charge_code char(5),@tmp_charge_type char(1),@tmp_actual_move_in_date datetime
declare @tmp_is_terminated char(2),@tmp_termination_date datetime,@tmp_days_diff int,@tmp_days_diff_billing int
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'INVOICE DETAIL'

select top 1 @charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))

select top 1 @billing_from = billing_from,@billing_to = billing_to
from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 

if @strMode = 'FIND'
	begin
		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))),@tmp_charge_code = upper(ltrim(rtrim(charge_code)))
		from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

		select top 1 @tmp_charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))

		if @invoice_detail_id <> 0
			begin
				if upper(ltrim(rtrim(@charge_code))) <> @tmp_charge_code 
					begin			
						if @tmp_charge_type = 'U' and @charge_type = 'U'
						begin
							if not exists(select * from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
								begin
									if not exists (select * from t_tenant_reading 
										left join t_tenant_reading_charges on reading_id = trc_reading_id
										where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
										and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
										and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
										and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
										and isnull(trc_invoice_no,'') = ''			
									)	
										begin
											select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
										end
									else
										begin
											select 0 as x,'' as msg
										end
								end
							else
								begin
									if not exists (select * from t_tenant_reading 
										left join t_tenant_reading_charges on reading_id = trc_reading_id
										where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
										and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
										and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
										and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
										and isnull(trc_invoice_no,'') = ''			
									)	
										begin
											select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
										end
									else
										begin
											select 0 as x,'' as msg
										end
								end
						end
		
						else if @tmp_charge_type = 'U' and @charge_type = 'F'
							begin
								select 0 as x,'' as msg
							end		
						
						else if @tmp_charge_type = 'F' and @charge_type = 'U'
							begin
								if not exists (select * from t_tenant_reading 
									left join t_tenant_reading_charges on reading_id = trc_reading_id
									where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
									and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
									and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
									and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
									and isnull(trc_invoice_no,'') = ''			
								)	
									begin
										select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not updated.' as msg
									end
								else
									begin
										select 0 as x,'' as msg
									end
							end
						
						else
							select 0 as x,'' as msg
					end
				else
					select 0 as x,'' as msg
			end
		else
			begin
				if @charge_type = 'U'
					begin
						if not exists (select * from t_tenant_reading 
							left join t_tenant_reading_charges on reading_id = trc_reading_id
							where convert(varchar(12),convert(datetime,billing_date_from),101) = convert(varchar(12),convert(datetime,@billing_from),101)
							and convert(varchar(12),convert(datetime,billing_date_to),101) = convert(varchar(12),convert(datetime,@billing_to),101)	
							and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
							and upper(ltrim(rtrim(trc_charge_code))) = upper(ltrim(rtrim(@charge_code)))
							and isnull(trc_invoice_no,'') = ''			
						)	
							begin
								select 1 as x,'No Reading can be assigned for this Charge. Check Tenant Reading module. Record not added.' as msg
							end
						else
							begin
								select 0 as x,'' as msg
							end
					end
				else
					select 0 as x,'' as msg
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetail_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetail_Delete]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(18,0),
	@invoice_detail_reading_id decimal(18,0),
	@tenant_code char(10),
	@charge_code char(5),
	@reading_id decimal(18,0),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'INVOICE DETAIL'

if @strMode = 'DELETE'		
	begin
		if exists (select top 1 reading_id from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
			begin
				exec sp_t_Invoice_Detail_Save_Reading 2,@invoice_no,@invoice_detail_id,@invoice_detail_reading_id,@tenant_code,@charge_code

				set @module_name = 'INVOICE DETAIL READING'
				set @data = 'delete from t_invoice_detail_reading where upper(ltrim(rtrim(invoice_no))) = ' +upper(ltrim(rtrim(@invoice_no))) + ' and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				set @module_name = 'INVOICE DETAIL'
			end
		
		delete from t_invoice_detail
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id							

		set @data = 'delete from t_invoice_detail ' +
			'where upper(ltrim(rtrim(invoice_no))) = ' +upper(ltrim(rtrim(@invoice_no))) + ' and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

		select 0 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetail_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetail_Retrieve]
	@strMode varchar(50),
	@invoice_no varchar(20)
AS


if @strMode = 'RETRIEVE'
	begin
		select t_invoice_detail.invoice_detail_id,t_invoice_detail.invoice_no,t_invoice_detail.charge_code,charge_amount,total_charge_amount,
		'(' + convert(varchar(10),date_from,101) + '-' + convert(varchar(10),date_to,101) + ') '
		+
		case when isnull(prev_reading,0) <> 0 or isnull(current_reading,0) <> 0 then
			'Current Reading: ' + convert(varchar(10),current_reading) + '; Previous Reading: ' + convert(varchar(10),prev_reading) + '; Usage: ' + convert(varchar(10),current_reading - prev_reading) + ';' 
		else '' 
		end as remarks,
		upper(m_tenant.tenant_name) as tenant_name,
		upper(ltrim(rtrim( m_tenant.real_property_code))) + '/' +upper(ltrim(rtrim( m_tenant.building_code))) + '/' + upper(ltrim(rtrim(m_tenant.unit_no))) as unit_no,
		t_invoice_detail.tenant_code,
		charge_desc,gl_code,upper(ltrim(rtrim(t_invoice_detail.charge_type))) as charge_type,
		--case when upper(ltrim(rtrim(t_invoice_detail.charge_type))) = 'U' then 'Based on Usage' else 'Fixed Rate' end as charge_type_desc,
		upper(ltrim(rtrim(t_invoice_detail.charge_type))) as charge_type_desc,
		isnull(t_invoice_detail_reading.invoice_detail_reading_id,0) as invoice_detail_reading_id,
		isnull(t_invoice_detail_reading.reading_id,0) as reading_id
		from t_invoice_detail		
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		where upper(ltrim(t_invoice_detail.invoice_no)) like upper(ltrim(@invoice_no)) + '%'
		order by m_tenant.tenant_name,
		upper(ltrim(rtrim( m_tenant.real_property_code))) + '/' +upper(ltrim(rtrim( m_tenant.building_code))) + '/' + upper(ltrim(rtrim(m_tenant.unit_no))) ,
		charge_desc
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetail_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetail_Save]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(18,0),
	@invoice_detail_reading_id decimal(18,0),
	@tenant_code char(10),
	@charge_code char(5),
	@charge_type char(1),
	@charge_amount decimal(18,6),
	@reading_id decimal(18,0),
	@remarks varchar(100),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @invoice_amount decimal(9,2),@total_usage decimal(9,0)
declare @billing_from datetime,@billing_to datetime,@tmp_tenant_code char(10),@tmp_charge_code char(5),@tmp_charge_type char(1),@tmp_actual_move_in_date datetime
declare @tmp_is_terminated char(2),@tmp_termination_date datetime,@tmp_days_diff int,@tmp_days_diff_billing int
declare @data nvarchar(max),@module_name varchar(50)
declare @tmp_reading_id decimal(18,0)

set @module_name = 'INVOICE DETAIL'

select top 1 @charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code))) 

select top 1 @billing_from = billing_from,@billing_to = billing_to
from t_invoice_header where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 

if @strMode = 'SAVE'
	begin
		if @invoice_detail_id = 0
			begin
				insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount)	
				select top 1 upper(ltrim(rtrim(@invoice_no))),upper(@tenant_code),upper(@charge_code),charge_type,@charge_amount from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))

				select @invoice_detail_id = max(invoice_detail_id) from t_invoice_detail
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
				and upper(ltrim(rtrim(tenant_code))) = upper(ltrim(rtrim(@tenant_code)))
				and upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@charge_code)))				

				set @data = 'insert into t_invoice_detail (invoice_no,tenant_code,charge_code,charge_type,charge_amount) ' +
					'select top 1 ' + upper(ltrim(rtrim(@invoice_no)))+',' +upper(@tenant_code)+',' +upper(@charge_code)+',charge_type,' +
					convert(varchar(10),@charge_amount) +' from m_charges where upper(ltrim(rtrim(charge_code))) ='+ upper(ltrim(rtrim(@charge_code)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code				
			end

		-- CHECK READING

		select top 1 @tmp_tenant_code = upper(ltrim(rtrim(tenant_code))),@tmp_charge_code = upper(ltrim(rtrim(charge_code)))
		from t_invoice_detail 
		where upper(ltrim(rtrim(t_invoice_detail.invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and t_invoice_detail.invoice_detail_id = @invoice_detail_id
		
		select top 1 @tmp_charge_type = upper(ltrim(rtrim(charge_type))) from m_charges where upper(ltrim(rtrim(charge_code))) = upper(ltrim(rtrim(@tmp_charge_code)))

		if upper(ltrim(rtrim(@charge_code))) <> @tmp_charge_code 
			begin			
				if @tmp_charge_type = 'U' and @charge_type = 'U'
				begin
					exec sp_t_Invoice_Detail_Save_Reading 1,@invoice_no,@invoice_detail_id,@invoice_detail_reading_id,@tenant_code,@charge_code
				end

				else if @tmp_charge_type = 'U' and @charge_type = 'F'
				begin
					exec sp_t_Invoice_Detail_Save_Reading 2,@invoice_no,@invoice_detail_id,@invoice_detail_reading_id,@tenant_code,@charge_code
				end		
				
				else if @tmp_charge_type = 'F' and @charge_type = 'U'
				begin
					exec sp_t_Invoice_Detail_Save_Reading 3,@invoice_no,@invoice_detail_id,@invoice_detail_reading_id,@tenant_code,@charge_code
				end
			end

		else
			begin
				if @tmp_charge_type = 'U' and @charge_type = 'U'
				begin
					exec sp_t_Invoice_Detail_Save_Reading 1,@invoice_no,@invoice_detail_id,@invoice_detail_reading_id,@tenant_code,@charge_code
				end
			end

		--END TENANT READING
		--declare @reading_id decimal(9,0),@invoice_detail_reading_id decimal(9,0)
		set @total_usage = 0
		
		select top 1 @total_usage =  isnull(current_reading,0) - isnull(prev_reading,0),@reading_id = t_tenant_reading.reading_id,
			@invoice_detail_reading_id = t_invoice_detail_reading.invoice_detail_reading_id
		from t_tenant_reading 
		left join t_invoice_detail_reading on t_tenant_reading.reading_id = t_invoice_detail_reading.reading_id
		where upper(ltrim(rtrim(t_invoice_detail_reading.invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and t_invoice_detail_reading.invoice_detail_id = @invoice_detail_id

		/*update t_tenant_reading_charges
			set trc_invoice_no = @invoice_no,
			 trc_invoice_detail_id = @invoice_detail_id,
			 trc_invoice_detail_reading_id = @invoice_detail_reading_id
		where trc_reading_id = @reading_id*/

		select top 1 @tmp_actual_move_in_date = isnull(actual_move_in_date,'1/1/1900'),@tmp_termination_date = isnull(date_terminated,'1/1/1900'),
			@tmp_is_terminated = isnull(terminated,'N')
		from m_tenant where upper(ltrim(rtrim(tenant_code))) = @tmp_tenant_code

		set @tmp_days_diff_billing = isnull(datediff(day,isnull(CONVERT(VARCHAR(25),DATEADD(dd,-(DAY(@billing_from)-1),@billing_from),101),'1/1/1900'),
					isnull(DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,@billing_to)+1,0)),'1/1/1900')) + 1,1)

		if @tmp_is_terminated = 'Y'
				begin
					if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to and isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
					
							update t_invoice_detail set 
								charge_code = @charge_code,
								charge_type = @charge_type,
								charge_amount = @charge_amount,
								total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
									when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then @charge_amount							
									else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
							

							set @data = 'update t_invoice_detail set ' +
								'charge_code =' + @charge_code + ',' +
								'charge_type =' + @charge_type + ',' +
								'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
								'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
									'when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then ' + convert(varchar(10),@charge_amount) +
									'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
								'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
						end

					else if isnull(@tmp_termination_date,'1/1/1900') >= @billing_from and isnull(@tmp_termination_date,'1/1/1900')  <= @billing_to
						begin
							set @tmp_days_diff = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@tmp_termination_date,'1/1/1900')) + 1,1)
							--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)

							update t_invoice_detail set 
								charge_code = @charge_code,
								charge_type = @charge_type,
								charge_amount = @charge_amount,
								total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
									when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then @charge_amount
									else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
							where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	

							set @data = 'update t_invoice_detail set ' +
								'charge_code =' + @charge_code + ',' +
								'charge_type =' + @charge_type + ',' +
								'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
								'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
									'when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then ' + convert(varchar(10),@charge_amount) + 
									'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
								'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
						end
				end
		else if isnull(@tmp_actual_move_in_date,'1/1/1900') >= @billing_from and isnull(@tmp_actual_move_in_date,'1/1/1900')  <= @billing_to
			begin

				set @tmp_days_diff = isnull(datediff(day,isnull(@tmp_actual_move_in_date,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
				--set @tmp_days_diff_billing = isnull(datediff(day,isnull(@billing_from,'1/1/1900'),isnull(@billing_to,'1/1/1900')) + 1,1)
				set @tmp_days_diff_billing = isnull(datediff(day,isnull(CONVERT(VARCHAR(25),DATEADD(dd,-(DAY(@billing_from)-1),@billing_from),101),'1/1/1900'),
					isnull(DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,@billing_to)+1,0)),'1/1/1900')) + 1,1)
				
				/*CONVERT(VARCHAR(25),DATEADD(dd,-(DAY('05/13/2011')-1),'05/13/2011'),101)
				DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,@billing_from)+1,0))*/

				update t_invoice_detail set 
					charge_code = @charge_code,
					charge_type = @charge_type,
					charge_amount = @charge_amount,
					total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) 
						when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then @charge_amount
						else (@charge_amount/@tmp_days_diff_billing) * @tmp_days_diff end
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id	

				set @data = 'update t_invoice_detail set ' +
					'charge_code =' + @charge_code + ',' +
					'charge_type =' + @charge_type + ',' +
					'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
					'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
						'when upper(ltrim(rtrim(charge_code))) in (select upper(ltrim(rtrim(charge_code))) from m_charges where isnull(exclude_prorate,0) = 1) then ' + convert(varchar(10),@charge_amount) +
						'else (' +convert(varchar(10),@charge_amount) + '/' +convert(varchar(10),@tmp_days_diff_billing) + ') *' +  convert(varchar(10),@tmp_days_diff) + ' end ' +
					'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
			end
		else
			begin
				update t_invoice_detail set 
					--tenant_code = @tenant_code,
					charge_code = @charge_code,
					charge_type = @charge_type,
					charge_amount = @charge_amount,
					total_charge_amount = case when @charge_type = 'U'  then (@charge_amount * @total_usage) else @charge_amount end
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id

				set @data = 'update t_invoice_detail set ' +
					'charge_code =' + @charge_code + ',' +
					'charge_type =' + @charge_type + ',' +
					'charge_amount =' + convert(varchar(10),@charge_amount) + ',' +
					'total_charge_amount = case when' + @charge_type +'= ''U''  then (' +convert(varchar(10),@charge_amount) + '*' +  convert(varchar(10),@total_usage) + ') ' +
						'else ' + convert(varchar(10),@charge_amount) + ' end ' +
					'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
			end

		if ltrim(rtrim(isnull(@data,''))) <> ''
			begin
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end 
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_InvoiceDetailReading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_InvoiceDetailReading]
	@strMode varchar(50),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@invoice_detail_reading_id decimal(9,0),
	@reading_id decimal(9,0),
	@reading_date_from datetime,
	@reading_date_to datetime,
	@prev_reading decimal(9,0),
	@current_reading decimal(9,0),
	@remarks varchar(100),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

if @strMode = 'SAVE'
	begin
		update t_tenant_reading set 
			date_from = @reading_date_from,
			date_to = @reading_date_to,	
			prev_reading = @prev_reading,		
			current_reading = @current_reading,
			remarks = @remarks
		where reading_id = @reading_id

		set @current_reading = isnull(@current_reading,0)
		set @prev_reading = isnull(@prev_reading,0)
		
		update t_invoice_detail set	
			total_charge_amount = charge_amount * (case when @current_reading - @prev_reading <=0 then 1 else @current_reading - @prev_reading end)
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id
		
		set @module_name = 'INVOICE DETAIL READING'
		set @data = 'update t_tenant_reading set ' +
				'date_from = ' +convert(varchar(10),@reading_date_from,101)+',' +
				'date_to = ' +convert(varchar(10),@reading_date_to,101)+',' +	
				'prev_reading =' + convert(varchar(20),@prev_reading)+',' +		
				'current_reading =' + convert(varchar(20),@current_reading)+',' +
				'remarks =' + @remarks +
				'where reading_id =' + convert(varchar(10),@reading_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

		set @module_name = 'INVOICE DETAIL'
		set @data = 'update t_invoice_detail set ' +	
			'total_charge_amount = charge_amount * (case when ' + convert(varchar(20),@current_reading) +' -' + convert(varchar(20),@prev_reading) + ' <=0 then 1 else ' +
			  convert(varchar(20),@current_reading) + '-' + convert(varchar(20),@prev_reading) + ' end) ' +
			'where upper(ltrim(rtrim(invoice_no))) =' + upper(ltrim(rtrim(@invoice_no))) + 'and invoice_detail_id =' + convert(varchar(10),@invoice_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
	end

if @strMode = 'RETRIEVE'
	begin
		select t_invoice_detail_reading.*,t_tenant_reading.date_from, t_tenant_reading.date_to,
			t_tenant_reading.prev_reading,t_tenant_reading.current_reading,t_tenant_reading.remarks,
			t_tenant_reading.tenant_code,t_tenant_reading.charge_code,m_tenant.tenant_name,m_charges.charge_desc,t_tenant_reading.remarks,
			ltrim(rtrim(isnull(t_invoice_header.status,''))) as status
		from t_invoice_detail_reading
		left join t_tenant_reading on t_invoice_detail_reading.reading_id = t_tenant_reading.reading_id
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		left join m_charges on t_tenant_reading.charge_code = m_charges.charge_code
		left join t_invoice_header on t_invoice_detail_reading.invoice_no = t_invoice_header.invoice_no
		where upper(ltrim(t_invoice_detail_reading.invoice_no)) = upper(ltrim(@invoice_no)) and t_invoice_detail_reading.invoice_detail_id = @invoice_detail_id 
			and t_invoice_detail_reading.invoice_detail_reading_id = @invoice_detail_reading_id 
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_List_Clients]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_t_List_Clients]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000),@data_type char(1)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @ssql = 'select *,real_property_name from m_tenant 
		left join m_real_property on m_tenant.real_property_code = m_real_property.real_property_code		
		where 
		upper(ltrim(rtrim(isnull(terminated,''N'')))) <> ''Y''
		and (upper(ltrim(rtrim(isnull(tenant_type,'''')))) = ''OC'' or upper(ltrim(rtrim(isnull(tenant_type,'''')))) = ''C'') '


	if ltrim(rtrim(@column_code)) <> '' 
		begin
			if @data_type = 'S'
				set @ssql = @ssql + ' and ' + @column_code + ' like ''%' + @keyword + '%'''				
			else if @data_type = 'D' and @keyword <> ''
				set @ssql = @ssql + ' and convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
		end

	if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by tenant_name,real_property_name,building_code,unit_no '
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_List_TenantsNotInInvoiceDetail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_List_TenantsNotInInvoiceDetail]
	@invoice_no varchar(20)
AS

	select distinct m_tenant.tenant_code,upper(ltrim(rtrim(tenant_name)) + ' (' + ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' + ltrim(rtrim(unit_no)) + ')') as tenant_name,
	ltrim(rtrim(real_property_code)) as real_property_code,ltrim(rtrim(building_code)) as building_code,ltrim(rtrim(unit_no)) as unit_no
	 from m_tenant where (tenant_type = 'O' or tenant_type='OC') 
	and bill_to in (select top 1 client_code from t_invoice_header where invoice_no = @invoice_no)
	--order by m_tenant.tenant_name
	order by upper(ltrim(rtrim(m_tenant.tenant_name)) 
	+ ' (' + ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' + ltrim(rtrim(unit_no)) + ')'),
	ltrim(rtrim(real_property_code)),ltrim(rtrim(building_code)),ltrim(rtrim(unit_no)),
	m_tenant.tenant_code
GO
/****** Object:  StoredProcedure [dbo].[sp_t_List_UnitsNotInPaymentDetail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_List_UnitsNotInPaymentDetail]
	@or_no varchar(20)
AS

	select distinct upper(ltrim(rtrim(tenant_name)) + ' (' + ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' + ltrim(rtrim(unit_no)) + ')') as tenant_name,
	ltrim(rtrim(real_property_code)) as real_property_code,ltrim(rtrim(building_code)) as building_code,ltrim(rtrim(unit_no)) as unit_no
	 from m_tenant where (tenant_type = 'O' or tenant_type='OC') 
	and bill_to in (select top 1 client_code from t_payment_header where or_no = @or_no)
	and upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) not in
	(
	select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
	from t_security_deposit_detail where or_no = @or_no
	)
	order by real_property_code,building_code,unit_no
GO
/****** Object:  StoredProcedure [dbo].[sp_t_ORProcessing_AdvancePayment_InvoiceList]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_ORProcessing_AdvancePayment_InvoiceList]
	@or_no varchar(20),
	@date_from varchar(10),
	@date_to varchar(10),
	@sort_by varchar(50)
AS

	-- update invoice paid amount value, all invoices not existing in payment tables
		update t_invoice_detail set paid_amount =0,balance_amount=total_charge_amount
		where invoice_detail_id not in
		(select invoice_detail_id from t_payment_detail where or_no in (select or_no from t_payment_header where isnull(status,'') <> 'V'))
	--
	
	declare @invoice_no varchar(20),@invoice_detail_id decimal(18,0)

	declare xxx cursor scroll for
	select invoice_no,invoice_detail_id from t_invoice_detail 
	where invoice_no in (select invoice_no from t_invoice_header where isnull(status,'') <> 'V'
	and client_code  in (
		select top 1 client_code from t_ar_header
		where or_no = @or_no))
	
	open xxx
	fetch next from xxx into @invoice_no,@invoice_detail_id
	while @@fetch_status = 0
		begin
			exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
			fetch next from xxx into @invoice_no,@invoice_detail_id
		end
	close xxx
	deallocate xxx

	declare @xxx table (invoice_no varchar(20),invoice_detail_id decimal(18,0),
		invoice_date varchar(10),client_code varchar(20),client_name varchar(200),unit_no varchar(200),charge_desc varchar(200),
		balance_amount decimal(18,2),paid_amount decimal(18,2),
		total_amount decimal(18,2))

	if @date_from = '' and @date_to = ''
		begin
			insert into @xxx
			select t_invoice_header.invoice_no,
			invoice_detail_id,
			convert(varchar(10),invoice_date,101) as invoice_date,
			t_invoice_detail.tenant_code as client_code,
			tenant_name as client_name,
			case when isnull(tenant_type,'OC') = 'C' then '' 
			else ltrim(rtrim(m_tenant.real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) end as unit_no,	
			m_charges.charge_desc,
			isnull(t_invoice_detail.balance_amount,0) as balance_amount,
			isnull(t_invoice_detail.paid_amount,0) as paid_amount,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount
			from t_invoice_header
			left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
			left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
			left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
			where
			upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and upper(ltrim(rtrim(isnull(status,'')))) = 'P'	
			and total_charge_amount-isnull(paid_amount,0) > 0	
			and t_invoice_header.client_code in (
				select top 1 client_code from t_ar_header
				where or_no = @or_no
			)
			and isnull(t_invoice_detail.balance_amount,0) <=
				(select top 1 amount - dbo.fn_GetORTotalDeductedAmount(or_no)  from t_ar_header where or_no = @or_no)
		end
	else
		begin
			insert into @xxx
			select t_invoice_header.invoice_no,invoice_detail_id,
			convert(varchar(10),invoice_date,101) as invoice_date,
			t_invoice_detail.tenant_code as client_code,
			tenant_name as client_name,
			case when isnull(tenant_type,'OC') = 'C' then '' 
			else ltrim(rtrim(m_tenant.real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) end as unit_no,	
			m_charges.charge_desc,
			isnull(t_invoice_detail.balance_amount,0) as balance_amount,
			isnull(t_invoice_detail.paid_amount,0) as paid_amount,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount
			from t_invoice_header
			left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
			left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
			left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
			where
			upper(ltrim(rtrim(isnull(status,'')))) <> 'V' and upper(ltrim(rtrim(isnull(status,'')))) = 'P'	
			and total_charge_amount-isnull(paid_amount,0) > 0	
			and t_invoice_header.client_code in (
				select top 1 client_code from t_ar_header
				where or_no = @or_no
			)
			and isnull(t_invoice_detail.balance_amount,0) <=
				(select top 1 amount - dbo.fn_GetORTotalDeductedAmount(or_no)  from t_ar_header where or_no = @or_no)
			and t_invoice_header.invoice_date >= convert(datetime,@date_from) and t_invoice_header.invoice_date <=  convert(datetime,@date_to)
		end

	if @sort_by = 'INVOICE NO.' or @sort_by = ''
		begin
			select * from @xxx order by invoice_no
		end
	else if @sort_by = 'INVOICE DATE'
		begin
			select* from @xxx order by convert(datetime,invoice_date)
		end
	else if @sort_by = 'TENANT'
		begin
			select* from @xxx order by client_name
		end
	else if @sort_by = 'UNIT NO.'
		begin
			select* from @xxx order by unit_no
		end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_ORProcessing_AdvancePayment_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_t_ORProcessing_AdvancePayment_List]
	@or_no varchar(20),
	@date_from varchar(10),
	@date_to varchar(10),
	@sort_by varchar(50)
AS
	declare @xxx table (or_no varchar(20),or_date varchar(10),client_code varchar(20),client_name varchar(200),unit_no varchar(200),
		total_amount decimal(18,2),deducted_amount decimal(18,2),
		balance_amount decimal(18,2))

	if ltrim(rtrim(isnull(@or_no,''))) = ''
		begin
			insert into @xxx
			select or_no,convert(varchar(10),or_date,101) as or_date,client_code,
				tenant_name  as client_name,
				case when isnull(tenant_type,'OC') = 'C' then '' 
				else ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) end as unit_no,
				isnull(amount,0) as total_amount,
				isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0) as deducted_amount,
				isnull(amount,0) - isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0) as balance_amount 	
			from t_ar_header
			left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code	
			where (isnull(amount,0) - isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0)) > 0
			and upper(ltrim(rtrim(status))) <> 'V'
			--and or_date >= @date_from and or_date <=@date_to
		end
	else
		begin
			insert into @xxx
			select top 1 or_no,convert(varchar(10),or_date,101) as or_date,client_code,
				tenant_name  as client_name,
				case when isnull(tenant_type,'OC') = 'C' then '' 
				else ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) end as unit_no,
				isnull(amount,0) as total_amount,
				isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0) as deducted_amount,
				isnull(amount,0) - isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0) as balance_amount 	
			from t_ar_header
			left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code	
			where (isnull(amount,0) - isnull(dbo.fn_GetORTotalDeductedAmount(or_no),0)) > 0
			and upper(ltrim(rtrim(status))) <> 'V'
			and or_no = @or_no 
		end

	if ltrim(rtrim(@date_from)) = '' and ltrim(rtrim(@date_to)) = ''
		begin
			if @sort_by = 'OR NO.' or @sort_by = ''
				begin
					select * from @xxx order by or_no
				end
			else if @sort_by = 'OR DATE'
				begin
					select* from @xxx order by convert(datetime,or_date)
				end
			else if @sort_by = 'CLIENT'
				begin
					select* from @xxx order by client_name
				end
			else if @sort_by = 'UNIT NO.'
				begin
					select* from @xxx order by unit_no
				end
		end
	else
		begin
			if @sort_by = 'OR NO.' or @sort_by = ''
				begin
					select * from @xxx 
					where or_date >= convert(datetime,@date_from) and or_date <=convert(datetime,@date_to)
					order by or_no
				end
			else if @sort_by = 'OR DATE'
				begin
					select* from @xxx 
					where or_date >= convert(datetime,@date_from) and or_date <=convert(datetime,@date_to)
					order by convert(datetime,or_date)
				end
			else if @sort_by = 'CLIENT'
				begin
					select* from @xxx 
					where or_date >= convert(datetime,@date_from) and or_date <=convert(datetime,@date_to)
					order by client_name
				end
			else if @sort_by = 'UNIT NO.'
				begin
					select* from @xxx 
					where or_date >= convert(datetime,@date_from) and or_date <=convert(datetime,@date_to)
					order by unit_no
				end
		end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_ORProcessing_AdvancePayment_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_ORProcessing_AdvancePayment_Save]
	@or_no varchar(20),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@amount decimal(18,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'OR PROCESSING - ADVANCE PAYMENT - APPLY INVOICE'

	if not exists(select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
		begin
			insert into t_payment_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,trans_type,date_updated,updated_by)
			select or_no,or_no_type,or_date,client_code,0,document_no,'','','','P','A',getdate(),@uid
			from t_ar_header where or_no = @or_no

			set @data = 'insert into t_payment_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by) ' +
				'select or_no,or_no_type,or_date,client_code,0,document_no,'''','''','''',''P'',''A,''`' + convert(varchar(20),getdate())+',' +@uid

			exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			
		end

	if not exists(select * from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
		begin
			insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,or_amount,total_charge_amount,date_created,created_by,is_selected,trans_type)	
			select top 1 @or_no,invoice_no,invoice_detail_id,@amount,total_charge_amount,getdate(),@uid,0,'A' 
			from t_invoice_detail
			where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 
		end
	else
		begin
			update t_payment_detail set 
				or_amount = or_amount + @amount,
				is_selected = 0,
				date_created = getdate(),
				created_by = @uid,
				trans_type = 'A'
			where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
			and invoice_detail_id = @invoice_detail_id

			if (select top 1 isnull(or_amount,0) from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
				and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
				and invoice_detail_id = @invoice_detail_id) > 
				(select top 1 isnull(total_charge_amount,0) from t_invoice_detail  
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
				and invoice_detail_id = @invoice_detail_id)
				begin
					update t_payment_detail set 
						or_amount = isnull((select top 1 isnull(or_amount,0) from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
								and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
								and invoice_detail_id = @invoice_detail_id),0),
						is_selected = 0,
						date_created = getdate(),
						created_by = @uid
					where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) 
					and invoice_detail_id = @invoice_detail_id
				end
		end

	--\\ update paid amount in invoice table
	exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
	--end update paid amount in invoice table
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_AdvancePayment_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_AdvancePayment_Delete]
	@strMode varchar(50),
	@or_no varchar(20),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @deducted_amount decimal(9,2),@tmp_or_no varchar(20),@detail_cnt decimal(9,0),@total_detail_amount decimal(9,2)
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'DELETE ADVANCE PAYMENT IN PAYMENT DETAIL'

if @strMode = 'DELETE'		
	begin
		if not exists (select * from t_payment_header left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
			where upper(ltrim(rtrim(t_payment_header.or_no))) = upper(ltrim(rtrim(@or_no))) 
			and isnull(t_payment_header.status,'') <> 'V' and isnull(trans_type,'') = 'A')
			begin
				delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				delete from t_ar_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				delete from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				--update payment mode table
				exec sp_t_Payment_UpdatePaymentMode @or_no
				--end update payment mode table

				set @data = 'delete from t_ar_header where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_AdvancePayment_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_AdvancePayment_Save]
	@strMode varchar(50),
	@or_no varchar(20),	
	@ar_detail_id decimal(18,0),
	@amount decimal(18,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'ADVANCE PAYMENT IN PAYMENT DETAIL'

--set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))

if @strMode = 'SAVE'
	begin
		if not exists(select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,trans_type,date_updated,updated_by)
				select top 1 or_no,or_no_type,or_date,client_code,@amount,document_no,'','',remarks,'','U',getdate(),@uid from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				if exists(select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					begin
						insert into t_ar_header_payment_mode(or_no,payment_mode_type,amount,account_no,bank_name)
						select top 1 or_no,payment_mode_type,@amount,account_no,bank_name 
						from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
						and isnull(amount,0) >= @amount
					end

				set @data = 'insert into t_ar_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,trans_type,date_updated,updated_by) ' +
					'select top 1 or_no,or_no_type,or_date,client_code,' + convert(varchar(10),@amount) + ',@document_no,'','',remarks,'',''U'',' +convert(varchar(20),getdate())+',' +@uid + ' from t_payment_header where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				--select @or_no as or_no
			end
		else
			begin
				update t_ar_header set		
					amount = @amount,			
					trans_type = 'U',
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(isnull(status,'')))) <> 'P'

				if exists(select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					begin
						delete from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
					end

				if exists(select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					begin
						insert into t_ar_header_payment_mode(or_no,payment_mode_type,amount,account_no,bank_name)
						select top 1 or_no,payment_mode_type,@amount,account_no,bank_name 
						from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
						and isnull(amount,0) >= @amount
					end

				set @data = 'update t_ar_header set ' +	
					'amount =' + convert(varchar(10),@amount) + ',' +
					'trans_type = U,' +
					'date_updated = ' + convert(varchar(20),getdate())+',' +
					'updated_by = ' + @uid +
					'where upper(ltrim(rtrim(or_no))) =  ' + upper(ltrim(rtrim(@or_no))) + 'and upper(ltrim(rtrim(isnull(status,'''')))) <> ''P'''
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		
		--update payment mode table
		exec sp_t_Payment_UpdatePaymentMode @or_no
		--end update payment mode table
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Detail]
	@strMode varchar(50),
	@payment_detail_id decimal(9,0),
	@or_no varchar(20),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@total_charge_amount decimal(9,2),
	@or_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'PAYMENT DETAIL'

if @strMode = 'SAVE'
	begin
		if @or_amount > 0 
			begin
				update t_payment_detail set 
					or_amount = @or_amount,
					is_selected = 0,
					date_created = getdate(),
					created_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_detail_id = @payment_detail_id

				--\\ update paid amount in invoice table
				exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
				--end update paid amount in invoice table

				set @data = 'update t_payment_detail set ' +
					'or_amount =' + convert(varchar(10),@or_amount)+','+
					'is_selected = 0,' +
					'date_created = ' + convert(varchar(20),getdate())+','+
					'created_by =' + @uid +
					'where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no))) + ' and payment_detail_id =' + convert(varchar(10),@payment_detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code				
			end	
		else
			begin
				delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_detail_id = @payment_detail_id
			end	

		--update payment mode table
		exec sp_t_Payment_UpdatePaymentMode @or_no
		--end update payment mode table

	end

/*
if @strMode = 'FIND'
	begin
		if exists (select * from t_payment_detail where upper(ltrim(or_no)) = upper(ltrim(@or_no)) and payment_detail_id = @payment_detail_id)
			select 1 as x
		else
			select 0 as x
	end

*/
if @strMode = 'RETRIEVE_HDR'
	begin
		select top 1 @or_no = or_no from t_payment_header
		where upper(ltrim(or_no)) like '%' + upper(ltrim(@or_no)) + '%' 

		select @total_or_amount = sum(isnull(or_amount,0)) from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @payment_detail_total_rec = count(*) from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))		

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name as client_name,@total_or_amount as total_or_amount,@payment_detail_total_rec as payment_detail_total_rec,
			isnull(t_payment_header.amount,0) as or_amount_header
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
	end

if @strMode = 'ADD_ENTRY'
	begin
		if not exists(select * from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
			begin
				insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,or_amount,total_charge_amount,date_created,created_by,is_selected)	
				select top 1 @or_no,invoice_no,invoice_detail_id,isnull(total_charge_amount,0)-isnull(paid_amount,0),total_charge_amount,getdate(),@uid,1 from t_invoice_detail
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 

				/*select @paid_amount = isnull(sum(isnull(or_amount,0)),0) from t_payment_detail	
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 
		
				update t_invoice_detail
					set paid_amount = isnull(@paid_amount,0),
					balance_amount = total_charge_amount - isnull(@paid_amount,0) 
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id */
				
				--\\ update paid amount in invoice table
				exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
				--end update paid amount in invoice table

				select 0 as x
			end
		else
			select 1 as x
	end

if @strMode = 'RETRIEVE_DTL'
	begin
		delete from t_payment_detail where isnull(is_selected,0) = 1 and isnull(or_amount,0) = 0 and upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
			and or_no in (select or_no from t_payment_header where upper(ltrim(rtrim(status))) = 'P')

		select t_payment_detail.payment_detail_id,t_payment_detail.or_no,
			t_payment_detail.invoice_no,t_payment_detail.invoice_detail_id,t_payment_detail.invoice_detail_id,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,isnull(t_payment_detail.or_amount,0) as or_amount,
			upper(ltrim(rtrim(tenant_name)) + ' (' + ltrim(rtrim(m_tenant.real_property_code)) + '/' +  ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)) + ')') as tenant_name,
			charge_desc,
			case when upper(ltrim(rtrim(isnull(t_payment_header.status,'')))) = '' then isnull(t_invoice_detail.paid_amount,0) - isnull(t_payment_detail.or_amount,0) 
			else isnull(t_invoice_detail.paid_amount,0) end as paid_amount,
			convert(varchar(10),t_invoice_header.invoice_date,101) as invoice_date,
			case when upper(ltrim(rtrim(isnull(t_payment_header.status,'')))) = '' 
			then (isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) + isnull(t_payment_detail.or_amount,0) 
			else (isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) end as balance_amount
		from t_payment_header
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join t_invoice_detail on t_payment_detail.invoice_no = t_payment_detail.invoice_no
			and t_payment_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id 
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_invoice_header on t_invoice_detail.invoice_no = t_invoice_header.invoice_no
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		where upper(ltrim(rtrim(t_payment_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
		order by t_payment_detail.invoice_no,tenant_name,charge_desc
	end

if @strMode = 'DELETE'		
	begin
		delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_detail_id = @payment_detail_id

		--\\ update paid amount in invoice table
		exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
		--end update paid amount in invoice table

		--update payment mode table
		select @total_or_amount = sum(isnull(or_amount,0)) from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @total_payment_mode_amount = sum(isnull(amount,0)),@payment_mode_detail_cnt = count(*) 
			from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

		if isnull(@total_or_amount,0) <> isnull(@total_payment_mode_amount,0)
			begin
				if @payment_mode_detail_cnt = 1
					begin
						update t_payment_header_payment_mode set					
						amount = @total_or_amount
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
						and payment_mode_id in (select top 1 payment_mode_id from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					end
				else
					begin
						delete from t_payment_header_payment_mode 
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

						insert into t_payment_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,'1',@total_or_amount,'',''
					end
			end
		--end update payment mode table

		set @data = 'delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = ' +upper(ltrim(rtrim(@or_no))) + ' and payment_detail_id = ' + convert(varchar(10),@payment_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code				
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Detail_AddEntries]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Detail_AddEntries]
	@strMode varchar(50),
	@or_no varchar(20),
	@invoice_no_from varchar(20),
	@invoice_no_to varchar(20),
	@invoice_date_from varchar(10),
	@invoice_date_to varchar(10)
AS

declare @ssql nvarchar(4000)

if @strMode = 'VIEW_ADD_ENTRIES'
	begin
		declare @client_code char(10)

		select top 1 @client_code = upper(ltrim(rtrim(isnull(client_code,'')))) from t_payment_header
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

		-- update invoice paid amount value, all invoices not existing in payment tables
			update t_invoice_detail set paid_amount =0,balance_amount=total_charge_amount
			where invoice_detail_id not in
			(select invoice_detail_id from t_payment_detail where or_no in (select or_no from t_payment_header where isnull(status,'') <> 'V'))
		--
		
		declare @invoice_no varchar(20),@invoice_detail_id decimal(18,0)
	
		declare xxx cursor scroll for
		select invoice_no,invoice_detail_id from t_invoice_detail 
		where invoice_no in (select invoice_no from t_invoice_header where isnull(status,'') <> 'V'
		and client_code = @client_code)
		
		open xxx
		fetch next from xxx into @invoice_no,@invoice_detail_id
		while @@fetch_status = 0
			begin
				exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
				fetch next from xxx into @invoice_no,@invoice_detail_id
			end
		close xxx
		deallocate xxx

		set @ssql = 'select t_invoice_header.invoice_no, ' +
					'convert(varchar(10),t_invoice_header.invoice_date,101) as invoice_date, ' +
					'convert(varchar(10),t_invoice_header.billing_from,101) as billing_from, ' +
					'convert(varchar(10),t_invoice_header.billing_to,101) as billing_to, ' +
					't_invoice_detail.invoice_detail_id, ' +
					't_invoice_header.client_code, ' +
					't_invoice_detail.tenant_code, ' +
					'upper(ltrim(rtrim(m_tenant.tenant_name)) + '' ('' + ltrim(rtrim(m_tenant.real_property_code)) + ''/'' +  ltrim(rtrim(m_tenant.building_code)) +''/'' +  ltrim(rtrim(m_tenant.unit_no)) + '')'') as tenant_name, ' +
					't_invoice_detail.charge_code, ' +
					'm_charges.charge_desc, ' +
					'isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount, ' +
					'isnull(t_invoice_detail.paid_amount,0) as paid_amount, ' +
					'isnull(t_invoice_detail.balance_amount,0) as balance_amount,upper(ltrim(rtrim(t_invoice_header.status))) as status ' +
				'from t_invoice_header ' +
				'left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no ' +
				'left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no ' +
					'and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id ' +					
				'left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code ' +
				'left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code ' +
				'where  upper(ltrim(rtrim(t_invoice_header.client_code))) = ''' + @client_code + '''  ' --and upper(ltrim(rtrim(t_invoice_header.status))) = ''P''

		set @ssql = @ssql + 'and (upper(ltrim(rtrim(t_invoice_detail.invoice_no)))  +''' + '*' + '''+ convert(varchar(10),t_invoice_detail.invoice_detail_id) +''' + '*' + ''') ' +
				       'not in ' +
				       '(select (upper(ltrim(rtrim(invoice_no))) + ''' + '*' + '''+ convert(varchar(10),invoice_detail_id) +''' + '*'  + ''') from t_payment_detail where upper(ltrim(rtrim(or_no)))  = ''' + upper(ltrim(rtrim(@or_no))) + ''') '

		if @invoice_no_from <> '' and @invoice_no_to <> ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,'+ convert(varchar(5),len(@invoice_no_from)) +') >= ''' + @invoice_no_from + ''' ' + 
					'and left(t_invoice_header.invoice_no,' + convert(varchar(5),len(@invoice_no_to)) + ') <= ''' + @invoice_no_to + ''' '
			end
		else if @invoice_no_from <> '' and @invoice_no_to = ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,'+ convert(varchar(5),len(@invoice_no_from)) +') >= ''' + @invoice_no_from + ''' ' 
			end
		else if @invoice_no_from = '' and @invoice_no_to <> ''
			begin
				set @ssql = @ssql + 'and left(t_invoice_header.invoice_no,' + convert(varchar(5),len(@invoice_no_to)) + ') <= ''' + @invoice_no_to + ''' '
			end

		if @invoice_date_from <> '' and @invoice_date_to <> ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_from = ''' + @invoice_date_from + ''' and t_invoice_header.billing_to = ''' + @invoice_date_to + ''' '
			end
		else if @invoice_date_from <> '' and @invoice_date_to = ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_from = ''' + @invoice_date_from + ''' '
			end
		else if @invoice_date_from = '' and @invoice_date_to <> ''
			begin
				set @ssql = @ssql + 'and t_invoice_header.billing_to = ''' + @invoice_date_to + ''' '
			end
		set @ssql = @ssql + 'order by t_invoice_header.invoice_no,tenant_name'
		--print @ssql
		exec sp_executesql @ssql
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Detail_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Detail_Delete]
	@strMode varchar(50),
	@payment_detail_id decimal(9,0),
	@or_no varchar(20),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(9,0),
	@total_charge_amount decimal(9,2),
	@or_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'PAYMENT DETAIL'

if @strMode = 'DELETE'		
	begin
		delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_detail_id = @payment_detail_id

		--\\ update paid amount in invoice table
		exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
		--end update paid amount in invoice table

		--update payment mode table
		exec sp_t_Payment_UpdatePaymentMode @or_no
		--end update payment mode table

		set @data = 'delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = ' +upper(ltrim(rtrim(@or_no))) + ' and payment_detail_id = ' + convert(varchar(10),@payment_detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code			

		select 0 as x	
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Detail_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Detail_Retrieve]
	@strMode varchar(50),
	@or_no varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	

if @strMode = 'RETRIEVE'
	begin
		declare @invoice_no varchar(20),@invoice_detail_id decimal(18,0)

		declare xxx cursor scroll for 
		select invoice_no,invoice_detail_id from t_payment_detail where isnull(is_selected,0) = 1 and isnull(or_amount,0) = 0 and upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
			and or_no in (select or_no from t_payment_header where upper(ltrim(rtrim(status))) = 'P')

		open xxx
		fetch next from xxx into @invoice_no,@invoice_detail_id
		while @@fetch_status = 0
			begin

				--\\ update paid amount in invoice table
				exec sp_t_Payment_UpdateInvoice '',@invoice_no,@invoice_detail_id
				--end update paid amount in invoice table
	
				delete from t_payment_detail where isnull(is_selected,0) = 1 and isnull(or_amount,0) = 0 and upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
					and or_no in (select or_no from t_payment_header where upper(ltrim(rtrim(status))) = 'P')

				fetch next from xxx into @invoice_no,@invoice_detail_id
			end
		close xxx
		deallocate xxx

		declare @xxx table (trans_type char(1),payment_detail_id decimal(18,0),or_no varchar(20),invoice_no varchar(20),invoice_detail_id decimal(18,0),
			total_charge_amount decimal(18,2),or_amount decimal(18,2),tenant_name varchar(300),charge_desc varchar(100),
			paid_amount decimal(18,2),invoice_date varchar(10),balance_amount decimal(18,2),
			real_property_code char(5),building_code char(10), unit_no char(10)
			)

		insert into @xxx
		select 'U',t_payment_detail.payment_detail_id,t_payment_detail.or_no,
			t_payment_detail.invoice_no,t_payment_detail.invoice_detail_id,
			isnull(t_invoice_detail.total_charge_amount,0) as total_charge_amount,isnull(t_payment_detail.or_amount,0) as or_amount,
			upper(ltrim(rtrim(tenant_name)) + ' (' + ltrim(rtrim(m_tenant.real_property_code)) + '/' +  ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)) + ')') as tenant_name,
			charge_desc,
			case when upper(ltrim(rtrim(isnull(t_payment_header.status,'')))) = '' then isnull(t_invoice_detail.paid_amount,0) - isnull(t_payment_detail.or_amount,0) 
			else isnull(t_invoice_detail.paid_amount,0) end as paid_amount,
			convert(varchar(10),t_invoice_header.invoice_date,101) as invoice_date,
			case when upper(ltrim(rtrim(isnull(t_payment_header.status,'')))) = '' 
			then (isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) + isnull(t_payment_detail.or_amount,0) 
			else (isnull(t_invoice_detail.total_charge_amount,0) - isnull(t_invoice_detail.paid_amount,0) ) end as balance_amount,
			'','',''
		from t_payment_header
		left join t_payment_detail on t_payment_header.or_no = t_payment_detail.or_no
		left join t_invoice_detail on t_payment_detail.invoice_no = t_payment_detail.invoice_no
			and t_payment_detail.invoice_detail_id = t_invoice_detail.invoice_detail_id 
		left join t_invoice_detail_reading on t_invoice_detail.invoice_no = t_invoice_detail_reading.invoice_no
			and t_invoice_detail.invoice_detail_id = t_invoice_detail_reading.invoice_detail_id
		left join t_invoice_header on t_invoice_detail.invoice_no = t_invoice_header.invoice_no
		left join m_tenant on t_invoice_detail.tenant_code = m_tenant.tenant_code
		left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
		where upper(ltrim(rtrim(t_payment_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
		order by t_payment_detail.invoice_no,tenant_name,charge_desc

		if exists (select * from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(trans_type,'') = 'U')
			begin
				insert into @xxx
				select top 1 'A',0,or_no,'',0,0,amount,'','ADVANCE PAYMENT',0,'',0,'','',''
				from t_ar_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(trans_type,'') = 'U'
			end

		if exists (select * from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(trans_type,'') = 'U')
			begin
				insert into @xxx
				select 'S',detail_id,t_security_deposit.or_no,'',0,0,t_security_deposit_detail.amount,
				 ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' + ltrim(rtrim(unit_no)),
				'SECURITY DEPOSIT',0,'',0,
				real_property_code,building_code, unit_no
				from t_security_deposit
				left join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no
				where upper(ltrim(rtrim(t_security_deposit.or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(trans_type,'') = 'U'
				order by real_property_code,building_code, unit_no
			end

		select * from @xxx --order by --trans_type,invoice_no,tenant_name,charge_desc
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Detail_Retrieve_Header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Detail_Retrieve_Header]
	@strMode varchar(50),
	@or_no varchar(20)

AS

declare @data nvarchar(4000),@module_name varchar(50)

if @strMode = 'RETRIEVE'
	begin
		select top 1 @or_no = or_no from t_payment_header
		where upper(ltrim(or_no)) like '%' + upper(ltrim(@or_no)) + '%' 

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name as client_name,
			dbo.fn_GetORTotalAmount(or_no) as total_or_amount,
			dbo.fn_GetORTotalRecordCount(or_no) as payment_detail_total_rec,
			isnull(t_payment_header.amount,0) as or_amount_header
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Header]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Header]
	@strMode varchar(50),
	@or_no varchar(20),
	@or_date datetime,
	@client_code char(10),
	@amount decimal(9,2),
	@document_no varchar(20),
	@mode_of_payment char(1),
	@bank_name varchar(100),
	@remarks varchar(100),
	@status char(1),
	@payment_account_no varchar(20),
	@payment_mode_id decimal(9,2),
	@payment_mode_amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @payment_detail_cnt int,@tmp_or_no varchar(20),@total_payment_detail_amount decimal(9,2)
declare @next_or_no varchar(20), @or_len_settings int,@or_no_type char(1)
declare @or_no1 decimal(18,0),@or_no2 decimal(18,0)
declare @next_doc_no_settings int,@doc_no1 decimal(18,0),@doc_no2 decimal(18,0),@doc_no3 decimal(18,0),@next_doc_no varchar(50)  
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)

set @module_name = 'PAYMENT HEADER'

set @or_len_settings = 4
set @or_no_type = ''
set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))
set @next_doc_no_settings = 10
set @document_no = ''

if @strMode = 'SAVE'
	begin
		if @or_no = '' 
			begin
				select @or_no = dbo.fn_GetNextORNo (@or_date)
				set @or_no_type = 'N'
			end

		/*if upper(ltrim(rtrim(isnull(@document_no,'')))) = ''
			begin	
				select @doc_no1 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_ar_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no2 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_payment_header where isnumeric(right(document_no,@next_doc_no_settings))=1
				select @doc_no3 = max(convert(decimal(18,0),right(document_no,@next_doc_no_settings))) from t_invoice_header where isnumeric(right(document_no,@next_doc_no_settings))=1
		
				if isnull(@doc_no1,0) >= isnull(@doc_no2,0) and isnull(@doc_no1,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no1		
				else if isnull(@doc_no2,0) >= isnull(@doc_no1,0) and isnull(@doc_no2,0) >= isnull(@doc_no3,0)
					set @next_doc_no = @doc_no2	 
				else
					set @next_doc_no = @doc_no3

				if isnull(@next_doc_no,0) = 0
					set @next_doc_no = '1'
				else
					set @next_doc_no = @next_doc_no + 1

				set @document_no = ltrim(rtrim(replace(space(@next_doc_no_settings - len(@next_doc_no)),' ','0') + convert(varchar(10),@next_doc_no)))

			end
		*/
		
		if not exists(select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_payment_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by)
				select @or_no,@or_no_type,@or_date,@client_code,0,@document_no,@mode_of_payment,@bank_name,@remarks,@status,getdate(),@uid

				set @data = 'insert into t_payment_header(or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,status,date_updated,updated_by) ' +
					'select ' + @or_no +',' +@or_no_type+',' +convert(varchar(10),@or_date,101)+',' +@client_code+',0,'+@document_no+',' +@mode_of_payment+',' +
					@bank_name+',' +@remarks+',' +@status+',' +convert(varchar(20),getdate())+',' +@uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
				
				select @or_no as or_no
			end
		else
			begin
				
				select @amount = sum(isnull(or_amount,0)) 
				from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))			

				update t_payment_header set
					or_date = @or_date,
					client_code = @client_code,
					amount = isnull(@amount,0),
					document_no = @document_no,
					mode_of_payment = @mode_of_payment,
					bank_name = @bank_name,
					remarks = @remarks,
					status = @status,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(isnull(status,'')))) <> 'P'
				
				set @data = 'update t_payment_header set ' +
						'or_date ='+ convert(varchar(10),@or_date,101)+','+
						'client_code ='+ @client_code+','+
						'amount ='+ convert(varchar(10),isnull(@amount,0))+','+
						'document_no ='+ @document_no+','+
						'mode_of_payment ='+ @mode_of_payment+','+
						'bank_name ='+ @bank_name+','+
						'remarks ='+ @remarks+','+
						'status ='+ @status+','+
						'date_updated ='+ convert(varchar(20),getdate())+','+
						'updated_by ='+ @uid +
						'where upper(ltrim(rtrim(or_no))) ='+ upper(ltrim(rtrim(@or_no))) +'and upper(ltrim(rtrim(isnull(status,'''')))) <> ''P'''
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		
	end

if @strMode = 'DELETE_PAYMENT_MODE'
	begin
		delete from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
	end

if @strMode = 'SAVE_PAYMENT_MODE'
	begin
		if @payment_mode_id = 0
			begin
				if @mode_of_payment = 1
					begin
						if not exists (select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = 1)
							begin
								insert into t_payment_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
								select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
							end		
						else	
							begin
								update t_payment_header_payment_mode set
									payment_mode_type = @mode_of_payment,
									amount = isnull(amount,0) + @payment_mode_amount,
									account_no = @payment_account_no,	
									bank_name = @bank_name
								where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = @mode_of_payment
							end
					end
				else
					begin
						insert into t_payment_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@bank_name		
					end		
			end
		else			
			begin
				update t_payment_header_payment_mode set
					payment_mode_type = @mode_of_payment,
					amount = @payment_mode_amount,
					account_no = @payment_account_no,	
					bank_name = @bank_name
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_id = @payment_mode_id
			end
		
	end

if @strMode = 'FIND'
	begin
		if exists(select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'RETRIEVE'
	begin
		delete from t_payment_detail where is_selected = 1

		select top 1 @tmp_or_no = or_no
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'

		select @payment_detail_cnt = count(*),@total_payment_detail_amount = isnull(sum(isnull(or_amount,0)),0) 
		from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,t_payment_header.remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name  as client_name,@payment_detail_cnt as payment_detail_cnt,dbo.fn_GetORTotalAmount(or_no) as total_payment_detail_amount 	
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		if isnull(@total_payment_detail_amount,0) = 0
			delete from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no))) 
	end

if @strMode = 'RETRIEVE_PAYMENT_MODE'
	begin		
		exec sp_t_Payment_UpdatePaymentMode @or_no
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_payment_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
		and (isnull(amount,0) > 0 or 
		(
		isnull(account_no,'') <> '' or isnull(bank_name,'') <> '')
		)
		order by payment_mode_type
	end

if @strMode = 'RETRIEVE_CASH_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_payment_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '1'
	end

if @strMode = 'RETRIEVE_CHARGE_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_payment_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '2'
	end

if @strMode = 'RETRIEVE_CHECK_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_payment_header_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '3'
	end


if @strMode = 'VIEW'
	begin
		select distinct t_payment_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_payment_header 
		inner join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%'
		order by t_payment_header.or_no desc
	end


if @strMode = 'VIEW_STAT'
	begin
		select distinct t_payment_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_payment_header 
		inner join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where (or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%')
		and ltrim(rtrim(isnull(status,''))) = ''
		order by or_date desc,t_payment_header.or_no desc
	end

if @strMode = 'CLIENT_SEARCH'
	begin
		select * from m_tenant 
		where (tenant_name like @remarks + '%' or tenant_code like @remarks + '%'
		or building_code like @remarks + '%' or unit_no like @remarks + '%')
		--and isnull(terminated,'N') <> 'Y'
		and (upper(ltrim(rtrim(tenant_type))) = 'OC' or upper(ltrim(rtrim(tenant_type))) = 'C')
		and tenant_code in 
			(
			select client_code from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'P'
			)
		order by tenant_name
	end


if @strMode = 'VOID'
	begin
		if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and upper(ltrim(rtrim(status))) = 'P')
			begin
				update t_payment_header set status = 'V' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				set @data = 'update t_payment_header set status = ''V'' where upper(ltrim(rtrim(or_no))) = ' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code

				select 0 as x
				--\\ update paid amount in invoice table
				exec sp_t_Payment_UpdateInvoice @or_no,'',0
				--end update paid amount in invoice table
			end
		else
			select 1 as x
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and upper(ltrim(rtrim(status))) = 'P' or or_no_type = 'N')
			begin
				update t_payment_header set status = 'V'
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))				
				--\\ update paid amount in invoice table
				exec sp_t_Payment_UpdateInvoice @or_no,'',0
				--end update paid amount in invoice table

				delete from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				delete from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				set @module_name = 'PAYMENT DETAIL'
				set @data = 'delete from t_payment_detail where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				set @module_name = 'PAYMENT HEADER'
				set @data = 'delete from t_payment_header where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code

				select 0 as x
			end
		else
			select 1 as x
	end

if @strMode = 'POST'
	begin
		update t_payment_header set status = 'P' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  
		set @data = 'update t_payment_header set status = ''P'' where upper(ltrim(rtrim(or_no))) ='+ upper(ltrim(rtrim(@or_no)))  
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'POST',@company_code
		select 0 as x			
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Header_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_t_Payment_Header_List]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000),@data_type char(1)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @ssql = 'select distinct t_payment_header.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'''')))) as status,
		case when upper(ltrim(rtrim(status))) = ''P'' then ''POSTED''
		when upper(ltrim(rtrim(status))) = ''V'' then ''VOID''
		else '''' end as status_desc
		from t_payment_header 
		inner join m_tenant on t_payment_header.client_code = m_tenant.tenant_code '

	if ltrim(rtrim(@column_code)) <> '' 
		begin
			if @data_type = 'S'
				set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
			else if @data_type = 'D' and @keyword <> ''
				set @ssql = @ssql + ' where convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
		end

	if @strMode = 'VIEW_STAT'
		begin
			if ltrim(rtrim(@column_code)) <> '' 
				set @ssql = @ssql + ' and '
			else
				set @ssql = @ssql + ' where '

			set @ssql = @ssql +' upper(ltrim(rtrim(isnull(t_payment_header.status,'''')))) = '''' '
		end

	if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by or_date desc,t_payment_header.or_no desc '
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_Header_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_Header_Retrieve]
	@strMode varchar(50),
	@or_no varchar(20)
AS

declare @tmp_or_no varchar(20)

set @or_no = upper(ltrim(rtrim(@or_no)))

if @strMode = 'RETRIEVE'
	begin
		delete from t_payment_detail where is_selected = 1

		select top 1 @tmp_or_no = or_no
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'

		set @or_no = @tmp_or_no

		--update payment mode table
		exec sp_t_Payment_UpdatePaymentMode @or_no
		--end update payment mode table

		select top 1 or_no,or_no_type,or_date,client_code,amount,document_no,mode_of_payment,bank_name,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			t_payment_header.date_updated,t_payment_header.updated_by,tenant_name  as client_name,
			dbo.fn_GetORTotalRecordCount(or_no) as payment_detail_cnt,dbo.fn_GetORTotalAmount(or_no) as total_payment_detail_amount 	
		from t_payment_header
		left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_SAP_Uploading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_SAP_Uploading]
	@strMode varchar(20),
	@or_no varchar(20),
	@or_date_from datetime,
	@or_date_to datetime,
--	@real_property_code varchar(10),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @office_unit_type varchar(10), @apt_rental_charge varchar(5), @off_rental_charge varchar(5), @whs_rental_charge varchar(5)

set @or_no = upper(ltrim(rtrim(isnull(@or_no,''))))
set @module_name = 'OR SAP UPLOADING'

if @strMode = 'DELETE'
	begin
		delete from t_payment_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)
	end

if @strMode = 'CREATE_FILE'
	begin
		select * from t_payment_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded) = convert(varchar(20),@date_uploaded)
		order by recid
	end

if @strMode = 'POST'
	begin
		update t_payment_header set status = 'P' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_payment_header.or_no))) = upper(ltrim(rtrim(@or_no)))

		set @data = 'update t_payment_header set status = ''P'' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_payment_header.or_no))) =' + upper(ltrim(rtrim(@or_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'POST',@s_company_code

		update t_ar_header set status = 'P' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_ar_header.or_no))) = upper(ltrim(rtrim(@or_no)))

		set @data = 'update t_ar_header set status = ''P'' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_ar_header.or_no))) =' + upper(ltrim(rtrim(@or_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'POST',@s_company_code

		update t_security_deposit set status = 'P' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_security_deposit.or_no))) = upper(ltrim(rtrim(@or_no)))

		set @data = 'update t_security_deposit set status = ''P'' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_security_deposit.or_no))) =' + upper(ltrim(rtrim(@or_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'POST',@s_company_code
	end

if @strMode = 'GENERATE'
	begin
		delete from t_payment_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) <> convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		set @data = 'GENERATE ' + @or_no + ',' +  convert(varchar(10),@or_date_from)+ ',' +  convert(varchar(10),@or_date_to) + ',' 
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'GENERATE',@s_company_code

		declare @company_code varchar (50),@currency_code varchar(10),@doc_type varchar(10),@posting_key_hdr varchar(10),@acct_code_non_aff varchar(50),@posting_key_dtl1 varchar(10),
			@posting_key_dtl2 varchar(10),@gl_code_dtl varchar(50),
			@posting_key_hdr_sub varchar(10),@posting_key_dtl_sub varchar(10),
			@posting_key_hdr_aff varchar(10),@acct_code_aff varchar(50),@tax_code varchar(10),
			@buss_area_non_aff varchar(10),@cost_center_hdr varchar(10),
			@cost_center_dtl varchar(10),@job_order varchar(50),@doc_date_len int,@post_date_len int,
			@ref_doc_no_len int,@company_code_len int,@currency_code_len int,
			@doc_type_len int,@posting_key_len int,@account_code_len int,@amount_len int,@tax_code_len int,
			@buss_area_len int,@cost_center_len int,@job_order_len int,@base_date_len int,@alloc_len int,@new_code_len int,@text_len int
		declare @tmp_invoice_no varchar(20),@invoice_date datetime,@charge_code varchar(5),@total_charge_amount decimal(9,2),@client_name varchar(100),@sap_code varchar(100),@gl_code varchar(100)
		declare @total_invoice_amount decimal(9,2),@is_sap_affiliate char(1),@client_code char(10),@company_code_aff char(4),@buss_area_aff char(4),@alloc_invoice_no varchar(20)
		declare @payment_modes varchar(50),@or_date datetime,@tmp_or_no varchar(20),@transaction_type int,@unit_no varchar(20)

		set @payment_modes = ''
		set @transaction_type = 0

		if exists (select * from t_payment_header where or_no = @or_no) 
			begin
				set @transaction_type = 1
				if exists (select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=1)
					begin
						set @payment_modes = @payment_modes + 'CASH'
					end 

				if exists (select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=2)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CC'
						else
							set @payment_modes = @payment_modes + 'CC'
					end 

				if exists (select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=3)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CHECK'
						else
							set @payment_modes = @payment_modes + 'CHECK'
					end 

				select top 1 @is_sap_affiliate = isnull(is_sap_affiliate,'N'),
					@client_code = ltrim(rtrim(isnull(t_payment_header.client_code,''))) ,@company_code_aff = upper(ltrim(rtrim(isnull(m_tenant.new_code,'')))),
					@buss_area_aff = upper(ltrim(rtrim(isnull(m_tenant.business_area,'')))),
					@unit_no = case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end,
					@sap_text = 
						/*case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end + 
						' ' + @payment_modes + ' ' +
						right('00'+ cast(datepart(month,or_date) as varchar(2)),2)  + '/' + cast(datepart(year,or_date) as varchar(4)) + ' ' +
						ltrim(rtrim(isnull(m_tenant.tenant_name,''))),*/
						replace(ltrim(rtrim(isnull(t_payment_header.remarks,''))),char(13)+char(10),' '),
					@or_date = or_date,
					@sap_code = isnull(m_tenant.sap_code,'') 
				from t_payment_header
				left join m_tenant on t_payment_header.client_code = m_tenant.tenant_code
				where upper(ltrim(rtrim(t_payment_header.or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		else if exists (select * from t_ar_header where or_no = @or_no) 
			begin
				set @transaction_type = 2
				if exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=1)
					begin
						set @payment_modes = @payment_modes + 'CASH'
					end 

				if exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=2)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CC'
						else
							set @payment_modes = @payment_modes + 'CC'
					end 

				if exists (select * from t_ar_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=3)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CHECK'
						else
							set @payment_modes = @payment_modes + 'CHECK'
					end 

				select top 1 @is_sap_affiliate = isnull(is_sap_affiliate,'N'),
					@client_code = ltrim(rtrim(isnull(t_ar_header.client_code,''))) ,@company_code_aff = upper(ltrim(rtrim(isnull(m_tenant.new_code,'')))),
					@buss_area_aff = upper(ltrim(rtrim(isnull(m_tenant.business_area,'')))),
					@unit_no = case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end,
					@sap_text = 
						/*case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end + 
						' ' + @payment_modes + ' ' +
						right('00'+ cast(datepart(month,or_date) as varchar(2)),2)  + '/' + cast(datepart(year,or_date) as varchar(4)) + ' ' +
						ltrim(rtrim(isnull(m_tenant.tenant_name,''))),*/
					replace(ltrim(rtrim(isnull(t_ar_header.remarks,''))),char(13)+char(10),' '),
					@or_date = or_date,
					@sap_code = isnull(m_tenant.sap_code,'')  
				from t_ar_header
				left join m_tenant on t_ar_header.client_code = m_tenant.tenant_code
				where upper(ltrim(rtrim(t_ar_header.or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		else if exists (select * from t_security_deposit where or_no = @or_no) 
			begin
				set @transaction_type = 3
				if exists (select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=1)
					begin
						set @payment_modes = @payment_modes + 'CASH'
					end 

				if exists (select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=2)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CC'
						else
							set @payment_modes = @payment_modes + 'CC'
					end 

				if exists (select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type=3)
					begin
						if ltrim(rtrim(@payment_modes)) <> ''
							set @payment_modes = @payment_modes + '/CHECK'
						else
							set @payment_modes = @payment_modes + 'CHECK'
					end 

				select top 1 @is_sap_affiliate = isnull(is_sap_affiliate,'N'),
					@client_code = ltrim(rtrim(isnull(t_security_deposit.client_code,''))) ,@company_code_aff = upper(ltrim(rtrim(isnull(m_tenant.new_code,'')))),
					@buss_area_aff = upper(ltrim(rtrim(isnull(m_tenant.business_area,'')))),
					@unit_no = case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end,
					@sap_text = 
						/*ltrim(rtrim(
						case when ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) = '' then '' else
						 ltrim(rtrim(isnull(m_tenant.real_property_code,''))) +'/'+ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) end + 
						' SECURITY DEPOSIT ' +
						ltrim(rtrim(isnull(m_tenant.tenant_name,''))))),*/
						replace(ltrim(rtrim(isnull(t_security_deposit.remarks,''))),char(13)+char(10),' '),
					@or_date = or_date,
					@sap_code = isnull(m_tenant.sap_code,'')  
				from t_security_deposit
				left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code
				where upper(ltrim(rtrim(t_security_deposit.or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		set @alloc_invoice_no =  upper(ltrim(rtrim(@or_no)))

		select @total_invoice_amount = dbo.fn_GetORTotalAmount(upper(ltrim(rtrim(@or_no))))
		set @sap_text = upper(ltrim(rtrim(isnull(@sap_text,''))))

		select top 1 
			@company_code = upper(ltrim(rtrim(company_code))),
			@currency_code = currency_code,
			@doc_type = doc_type,
			@posting_key_hdr = posting_key_hdr,
			@acct_code_non_aff = acct_code_non_aff,
			@posting_key_dtl1 = posting_key_dtl1,
			@posting_key_dtl2 = posting_key_dtl2,
			@gl_code_dtl = ltrim(rtrim(isnull(gl_code_dtl,''))),
			@posting_key_hdr_sub = posting_key_hdr_sub,
			@posting_key_dtl_sub = posting_key_dtl_sub,
			@posting_key_hdr_aff = posting_key_hdr_aff,
			@acct_code_aff = acct_code_aff,
			@tax_code = tax_code,
			@buss_area_non_aff = buss_area_non_aff,
			@cost_center_hdr = cost_center_hdr,
			@cost_center_dtl = isnull(cost_center_dtl,''),
			@job_order = job_order,
			@doc_date_len = doc_date_len,
			@post_date_len = post_date_len,
			@ref_doc_no_len = ref_doc_no_len,
			@company_code_len = company_code_len,
			@currency_code_len = currency_code_len,
			@doc_type_len = doc_type_len,
			@posting_key_len = posting_key_len,
			@account_code_len = account_code_len,
			@amount_len = amount_len,
			@tax_code_len = tax_code_len,
			@buss_area_len = buss_area_len,
			@cost_center_len = cost_center_len,
			@job_order_len = job_order_len,
			@base_date_len = base_date_len,@alloc_len = alloc_len,@new_code_len = new_code_len,@text_len = text_len
		from s_payment_sap_fields where trans_id = @transaction_type

		--debit
		insert into t_payment_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
					cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
		select top 1 
			cast(datepart(year,@or_date) as varchar(4)) + right('00'+ cast(datepart(month,@or_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@or_date) as varchar(2)),2) ,
			cast(datepart(year,@or_date) as varchar(4)) + right('00'+ cast(datepart(month,@or_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@or_date) as varchar(2)),2),
			upper(ltrim(rtrim(isnull(@or_no,'')))) + space(@ref_doc_no_len-len(upper(ltrim(rtrim(isnull(@or_no,'')))))),
			@company_code + space(@company_code_len-len(@company_code)),
			@currency_code + space(@currency_code_len-len(@currency_code)),
			@doc_type + space(@doc_type_len-len(@doc_type)),
			@posting_key_hdr + space(@posting_key_len-len(@posting_key_hdr)),
			@acct_code_non_aff + space(@account_code_len-len(@acct_code_non_aff)),
			space(@amount_len-len(cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)),
			space(@tax_code_len),
			--space(@buss_area_len),
			@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
			@cost_center_hdr + space(@cost_center_len-len(@cost_center_hdr)),
			@job_order + space(@job_order_len-len(@job_order)),
			space(@base_date_len),
			@company_code + space(@new_code_len-len(@company_code)),
			@alloc_invoice_no  + space(@alloc_len-len(upper(ltrim(rtrim(@alloc_invoice_no))) )),
			left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
			@date_uploaded,
			@uploaded_by				
	
		--credit
		insert into t_payment_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
			cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
		select top 1 
			space(@doc_date_len),
			space(@post_date_len),
			space(@ref_doc_no_len),
			space(@company_code_len),
			space(@currency_code_len),
			space(@doc_type_len),
			@posting_key_dtl1 + space(@posting_key_len-len(@posting_key_dtl1)),
			case when @transaction_type = 3 then @gl_code_dtl + space(@account_code_len-len(@gl_code_dtl))
			else @sap_code + space(@account_code_len-len(@sap_code)) end,
			space(@amount_len-len(cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)),
			@tax_code + space(@tax_code_len - len(@tax_code)),
			@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
			@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
			@job_order + space(@job_order_len-len(@job_order)),
			space(@base_date_len),
			@company_code + space(@new_code_len-len(@company_code)),
			case when @transaction_type = 3 then @unit_no  + space(@alloc_len-len(upper(ltrim(rtrim(@unit_no))) )) 
				else @alloc_invoice_no  + space(@alloc_len-len(upper(ltrim(rtrim(@alloc_invoice_no))) )) end,
			left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
			@date_uploaded,
			@uploaded_by								
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_SAP_Uploading_Build]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_SAP_Uploading_Build]
	@strMode varchar(20),
	@or_no varchar(20),
	@or_date_from datetime,
	@or_date_to datetime,
	@sort_by varchar(10),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000)

set @or_no = upper(ltrim(rtrim(isnull(@or_no,''))))
set @module_name = 'OR SAP UPLOADING'

if @strMode = 'BUILD'
	begin
		delete from t_payment_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		declare @xxx table (or_no varchar(20),or_date datetime,client_code varchar(20),total_amount decimal(18,2))

		insert into @xxx
		select t_payment_header.or_no,t_payment_header.or_date,t_payment_header.client_code,isnull(t_payment_detail.total_or_amount,0)
		from t_payment_header 
		left join 
			(select or_no,sum(isnull(or_amount,0)) as total_or_amount from t_payment_detail group by or_no) t_payment_detail
			on  t_payment_header.or_no = t_payment_detail.or_no
		where convert(datetime,convert(varchar(12),or_date,101)) >= convert(datetime,convert(varchar(12),@or_date_from,101)) 
			and convert(datetime,convert(varchar(12),or_date,101)) <= convert(datetime,convert(varchar(12),@or_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = ''
		
		insert into @xxx		
		select t_ar_header.or_no,t_ar_header.or_date,t_ar_header.client_code,isnull(t_ar_header.amount,0)
		from t_ar_header 
		where convert(datetime,convert(varchar(12),or_date,101)) >= convert(datetime,convert(varchar(12),@or_date_from,101)) 
			and convert(datetime,convert(varchar(12),or_date,101)) <= convert(datetime,convert(varchar(12),@or_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = ''
		
		insert into @xxx		
		select t_security_deposit.or_no,t_security_deposit.or_date,t_security_deposit.client_code,isnull(t_security_deposit_detail.total_or_amount,0)
		from t_security_deposit 
		left join 
			(select or_no,sum(isnull(amount,0)) as total_or_amount from t_security_deposit_detail group by or_no) t_security_deposit_detail
			on  t_security_deposit.or_no = t_security_deposit_detail.or_no
		where convert(datetime,convert(varchar(12),or_date,101)) >= convert(datetime,convert(varchar(12),@or_date_from,101)) 
			and convert(datetime,convert(varchar(12),or_date,101)) <= convert(datetime,convert(varchar(12),@or_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = ''

		if @sort_by = 'OR NO.' or @sort_by = ''
			begin
				select or_no,or_date,client_code,tenant_name as client_name,sum(total_amount) as total_amount,ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) as unit_no
				from @xxx a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				where isnull(total_amount,0) > 0
				group by or_no,or_date,client_code,tenant_name,real_property_code,building_code,unit_no 
				order by or_no
			end
		else if @sort_by = 'OR DATE'
			begin
				select or_no,or_date,client_code,tenant_name as client_name,sum(total_amount) as total_amount,ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) as unit_no
				from @xxx a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				where isnull(total_amount,0) > 0
				group by or_no,or_date,client_code,tenant_name,real_property_code,building_code,unit_no 
				order by or_date
			end
		else if @sort_by = 'CLIENT'
			begin
				select or_no,or_date,client_code,tenant_name as client_name,sum(total_amount) as total_amount,ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) as unit_no
				from @xxx a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				where isnull(total_amount,0) > 0
				group by or_no,or_date,client_code,tenant_name,real_property_code,building_code,unit_no 
				order by tenant_name
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select or_no,or_date,client_code,tenant_name as client_name,sum(total_amount) as total_amount,ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) as unit_no
				from @xxx a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				where isnull(total_amount,0) > 0
				group by or_no,or_date,client_code,tenant_name,real_property_code,building_code,unit_no 
				order by ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' +  ltrim(rtrim(unit_no)) 
			end

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_SecurityDeposit_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_SecurityDeposit_Delete]
	@strMode varchar(50),
	@or_no varchar(20),
	@detail_id decimal(18,0),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'DELETE SECURITY DEPOSIT IN PAYMENT DETAIL'

if @strMode = 'DELETE'		
	begin
		delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and detail_id = @detail_id

		if not exists (select * from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				delete from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
				delete from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
			end

		--update payment mode table
		exec sp_t_Payment_UpdatePaymentMode @or_no
		--end update payment mode table

		set @data = 'delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = ' +upper(ltrim(rtrim(@or_no))) + ' and detail_id = ' + convert(varchar(10),@detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code		

		select 0 as x		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_SecurityDeposit_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_SecurityDeposit_Save]
	@strMode varchar(50),
	@or_no varchar(20),
	@detail_id decimal(18,0),
	@real_property_code char(5),
	@building_code char(10),
	@unit_no char(10),
	@amount decimal(9,2),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'SECURITY DEPOSIT IN PAYMENT DETAIL'

if @strMode = 'SAVE'
	begin
		if not exists(select * from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_security_deposit(or_no,or_no_type,or_date,client_code,amount,remarks,status,trans_type,date_updated,updated_by)
				select top 1 or_no,or_no_type,or_date,client_code,0,remarks,status,'U',getdate(),@uid from t_payment_header
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				set @data = 't_security_deposit(or_no,or_no_type,or_date,client_code,amount,remarks,status,trans_type,date_updated,updated_by)) ' +
					'select top 1 or_no,or_no_type,or_date,client_code,0,remarks,status,''U'' ' +convert(varchar(20),getdate())+',' +@uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code				
			end
		
		else	
			begin
				update t_security_deposit set 
					remarks = (select top 1 remarks from t_payment_header where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))),
					trans_type = 'U',
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
			end

		if @detail_id <> 0
			begin
				update t_security_deposit_detail set 
					amount = @amount,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and detail_id = @detail_id				
	
				set @data = 'update t_security_deposit_detail set ' +
					'amount =' + convert(varchar(10),@amount)+','+
					'date_updated = ' + convert(varchar(20),getdate())+','+
					'updated_by =' + @uid +
					'where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no))) + ' and detail_id =' + convert(varchar(10),@detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code				
			end
		else
			begin
				if exists(select * from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
					and real_property_code = real_property_code and building_code = @building_code and unit_no = @unit_no)		
					begin
						delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
						and real_property_code = real_property_code and building_code = @building_code and unit_no = @unit_no							
					end

				insert into t_security_deposit_detail (or_no,real_property_code,building_code,unit_no,amount,date_updated,updated_by)
				select @or_no,@real_property_code,@building_code,@unit_no,@amount,getdate(),@uid

				set @data = 'insert into t_security_deposit_detail (or_no,real_property_code,building_code,unit_no,amount,date_updated,updated_by) ' + 
					' select ' + @or_no + ',' + @real_property_code + ',' + @building_code + ',' + @unit_no + ',' + convert(varchar(10),@amount) + ',' + ',' + convert(varchar(20),getdate()) + ',' + @uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code	
			end					

		--update payment mode table
		exec sp_t_Security_Deposit_UpdatePaymentMode @or_no
		--end update payment mode table

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_UpdateInvoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_UpdateInvoice]
	@or_no varchar(20),
	@invoice_no varchar(20),
	@invoice_detail_id decimal(18,0)
AS
declare @paid_amount decimal(9,2)				

if isnull(@or_no,'') <> ''
	begin
		--\\ update paid amount in invoice table
		set @invoice_no = ''
		set @invoice_detail_id = 0

		declare xxx cursor scroll for
		select invoice_no,invoice_detail_id from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		
		open xxx
		fetch next from xxx into @invoice_no,@invoice_detail_id
		while @@fetch_status = 0
			begin
				set @paid_amount = 0
		
				select @paid_amount = isnull(sum(isnull(or_amount,0)),0) from t_payment_detail	
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 
				and upper(ltrim(rtrim(or_no))) not in (select upper(ltrim(rtrim(or_no))) from t_payment_header where status='V')
		
				update t_invoice_detail
					set paid_amount = isnull(@paid_amount,0),
					balance_amount = total_charge_amount - isnull(@paid_amount,0) 
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 

				fetch next from xxx into @invoice_no,@invoice_detail_id
			end
		close xxx
		deallocate xxx

		--end update paid amount in invoice table

	end

else
	begin		
		--\\ update paid amount in invoice table		
		select @paid_amount = isnull(sum(isnull(or_amount,0)),0) from t_payment_detail	
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 
		and upper(ltrim(rtrim(or_no))) not in (select upper(ltrim(rtrim(or_no))) from t_payment_header where status='V')

		update t_invoice_detail
			set paid_amount = isnull(@paid_amount,0),
			balance_amount = total_charge_amount - isnull(@paid_amount,0) 
		where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 

		--end update paid amount in invoice table

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Payment_UpdatePaymentMode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Payment_UpdatePaymentMode]
	@or_no varchar(20)
AS
	declare @total_or_amount decimal(9,2),@total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt decimal(9,0)
	--update payment mode table
	delete from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and (isnull(amount,0) = 0 or isnull(payment_mode_type,'') ='')
	and (isnull(account_no,'') = '' or isnull(bank_name,'') = '')

	select @total_or_amount = dbo.fn_GetORTotalAmount(upper(ltrim(rtrim(@or_no))))
	select @total_payment_mode_amount = sum(isnull(amount,0)),@payment_mode_detail_cnt = count(*) 
		from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

	set @payment_mode_detail_cnt = isnull(@payment_mode_detail_cnt,0)

	if isnull(@total_or_amount,0) <> isnull(@total_payment_mode_amount,0)
		begin
			if @payment_mode_detail_cnt = 1
				begin
					update t_payment_header_payment_mode set					
					amount = @total_or_amount
					where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
					and payment_mode_id in (select top 1 payment_mode_id from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
				end
			else if @payment_mode_detail_cnt > 1
				begin
					declare @payment_mode_type char(1),@account_no varchar(50),@bank_name varchar(50)
				
					if exists(select * from t_payment_header_payment_mode
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and (isnull(account_no,'') <> '' or isnull(bank_name,'') <> ''))
						begin
							select top 1 
							@payment_mode_type = payment_mode_type,
							@account_no = account_no,
							@bank_name = bank_name
							 from t_payment_header_payment_mode 
							where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) > 0
							and (isnull(account_no,'') <> '' or isnull(bank_name,'') <> '')
							order by payment_mode_type 
							
						end
					else
						begin
							select top 1 
							@payment_mode_type = payment_mode_type,
							@account_no = account_no,
							@bank_name = bank_name
							 from t_payment_header_payment_mode 
							where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) > 0							
							order by payment_mode_type 
							
						end

					update t_payment_header_payment_mode set amount = 0
					where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 

					delete from t_payment_header_payment_mode 
					where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and (isnull(account_no,'') = '' or isnull(bank_name,'') = '')

					if not exists(select * from t_payment_header_payment_mode
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))		
						begin			
							insert into t_payment_header_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
							select @or_no,@payment_mode_type,@total_or_amount,@account_no,@bank_name
						end
					else
						update t_payment_header_payment_mode set
							amount = @total_or_amount
							 from t_payment_header_payment_mode 
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = @payment_mode_type
						and @account_no = account_no and @bank_name = bank_name
				end

			else if @payment_mode_detail_cnt = 0
				begin
					insert into t_payment_header_payment_mode(or_no,payment_mode_type,amount,account_no,bank_name)
					select @or_no,'1',@total_or_amount,'',''
				end
		end
	--end update payment mode table
GO
/****** Object:  StoredProcedure [dbo].[sp_t_SAP_Uploading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO



CREATE PROCEDURE [dbo].[sp_t_SAP_Uploading]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@invoice_date_from datetime,
	@invoice_date_to datetime,
	@real_property_code varchar(10),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
/*
Fix:
	05/03/2016 Aldrich - Added Space Type condition when Generating SAP FILE (eg. APT, WHS, OFC)
*/
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @office_unit_type varchar(10), @apt_rental_charge varchar(5), @off_rental_charge varchar(5), @whs_rental_charge varchar(5)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'SAP UPLOADING'

select top 1 @office_unit_type =  isnull(off_unit_type,''), @apt_rental_charge = isnull(apt_rental_charge,''), @off_rental_charge = isnull(off_rental_charge,''),
	@whs_rental_charge = isnull(whs_rental_charge,'') from s_settings

if @strMode = 'BUILD'
	begin
		delete from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		select t_invoice_header.*,m_tenant.tenant_name as client_name
		from t_invoice_header 
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		where upper(ltrim(rtrim(t_invoice_header.real_property_code))) like @real_property_code + '%'
			and convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@invoice_date_from,101)) 
			and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@invoice_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = ''
	end

if @strMode = 'DELETE'
	begin
		delete from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)
	end

if @strMode = 'CREATE_FILE'
	begin
		select * from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded) = convert(varchar(20),@date_uploaded)
		order by recid
	end

if @strMode = 'POST'
	begin
		update t_invoice_header set status = 'P' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

		set @data = 'update t_invoice_header set status = ''P'' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_invoice_header.invoice_no))) =' + upper(ltrim(rtrim(@invoice_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'POST',@s_company_code
	end

if @strMode = 'GENERATE'
	begin
		delete from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) <> convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		set @data = 'GENERATE ' + @invoice_no + ',' +  convert(varchar(10),@invoice_date_from)+ ',' +  convert(varchar(10),@invoice_date_to) + ',' + @real_property_code
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'GENERATE',@s_company_code

		declare @company_code varchar (50),@currency_code varchar(10),@doc_type varchar(10),@posting_key_hdr varchar(10),@posting_key_dtl1 varchar(10),
			@posting_key_dtl2 varchar(10),@posting_key_hdr_sub varchar(10),@posting_key_dtl_sub varchar(10),
			@posting_key_hdr_aff varchar(10),@acct_code_aff varchar(50),@tax_code varchar(10),
			@buss_area_non_aff varchar(10),@cost_center_hdr varchar(10),
			@cost_center_dtl varchar(10),@job_order varchar(50),@doc_date_len int,@post_date_len int,
			@ref_doc_no_len int,@company_code_len int,@currency_code_len int,
			@doc_type_len int,@posting_key_len int,@account_code_len int,@amount_len int,@tax_code_len int,
			@buss_area_len int,@cost_center_len int,@job_order_len int,@base_date_len int,@alloc_len int,@new_code_len int,@text_len int
		declare @tmp_invoice_no varchar(20),@invoice_date datetime,@charge_code varchar(5),@total_charge_amount decimal(9,2),@client_name varchar(100),@sap_code varchar(100),@gl_code varchar(100)
		declare @total_invoice_amount decimal(9,2),@is_sap_affiliate char(1),@is_employee_benefit char(1),@employee_benefit_cc varchar(50),@client_code char(10),@company_code_aff char(4),@buss_area_aff char(4),@alloc_invoice_no varchar(20)
		declare @real_property_code_tmp varchar(20)

		select top 1 
			@company_code = upper(ltrim(rtrim(company_code))),
			@currency_code = currency_code,
			@doc_type = doc_type,
			@posting_key_hdr = posting_key_hdr,
			@posting_key_dtl1 = posting_key_dtl1,
			@posting_key_dtl2 = posting_key_dtl2,
			@posting_key_hdr_sub = posting_key_hdr_sub,
			@posting_key_dtl_sub = posting_key_dtl_sub,
			@posting_key_hdr_aff = posting_key_hdr_aff,
			@acct_code_aff = acct_code_aff,
			@tax_code = tax_code,
			@buss_area_non_aff = buss_area_non_aff,
			@cost_center_hdr = cost_center_hdr,
			--@cost_center_dtl = cost_center_dtl,
			@job_order = job_order,
			@doc_date_len = doc_date_len,
			@post_date_len = post_date_len,
			@ref_doc_no_len = ref_doc_no_len,
			@company_code_len = company_code_len,
			@currency_code_len = currency_code_len,
			@doc_type_len = doc_type_len,
			@posting_key_len = posting_key_len,
			@account_code_len = account_code_len,
			@amount_len = amount_len,
			@tax_code_len = tax_code_len,
			@buss_area_len = buss_area_len,
			@cost_center_len = cost_center_len,
			@job_order_len = job_order_len,
			@base_date_len = base_date_len,@alloc_len = alloc_len,@new_code_len = new_code_len,@text_len = text_len
		from s_sap_fields

		select top 1 @is_sap_affiliate = isnull(is_sap_affiliate,'N'),@cost_center_dtl = ltrim(rtrim(isnull(m_real_property.cost_center,''))),
			@client_code = ltrim(rtrim(isnull(t_invoice_header.client_code,''))) ,@company_code_aff = upper(ltrim(rtrim(isnull(m_tenant.new_code,'')))),
			@buss_area_aff = upper(ltrim(rtrim(isnull(m_tenant.business_area,'')))),
			--@sap_text = 'APT RENT ' + ltrim(rtrim(isnull(t_invoice_header.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) + ' ' + 			
			/* FIX 05/04/2016 */
			@sap_text = ISNULL(s.space_type_code,'') + ' RENT ' + ltrim(rtrim(isnull(t_invoice_header.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) + ' ' + 			
			/* FIX 05/03/2016 */
			--@sap_text = (CASE 
			--	WHEN m_real_property.space_type='W' THEN 'WHS'  
			--	WHEN m_real_property.space_type='O' THEN 'OFC'
			--	ELSE 'APT'
			--	END) 
			--	+ ' RENT ' + ltrim(rtrim(isnull(t_invoice_header.real_property_code,''))) +ltrim(rtrim(isnull(m_tenant.UNIT_NO,''))) + ' ' + 
				right('00'+ cast(datepart(month,invoice_date) as varchar(2)),2)  + '/' + cast(datepart(year,invoice_date) as varchar(4)) + ' ' +
				ltrim(rtrim(isnull(m_tenant.tenant_name,''))),
			@is_employee_benefit = isnull(is_employee_benefit,'N'),@employee_benefit_cc = ltrim(rtrim(isnull(employee_benefit_cc,''))),
			@real_property_code_tmp = t_invoice_header.real_property_code
		from t_invoice_header
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		left join m_real_property on t_invoice_header.real_property_code = m_real_property.real_property_code
		left join m_space_type s on s.space_type=m_real_property.space_type
		where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

		set @alloc_invoice_no =  upper(ltrim(rtrim(@invoice_no)))
		set @alloc_invoice_no = substring(@alloc_invoice_no,1,len(@alloc_invoice_no)-10)

		if len(ltrim(rtrim(@alloc_invoice_no))) > 3
			set @alloc_invoice_no = left(@alloc_invoice_no,3) + ' ' + right( upper(ltrim(rtrim(@invoice_no))),10)
		else
			set @alloc_invoice_no =  upper(ltrim(rtrim(@invoice_no)))

		set @alloc_invoice_no = @company_code + @alloc_invoice_no

		--if ltrim(rtrim(isnull(@is_employee_benefit,'N'))) = 'Y' 
			--select @total_invoice_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and charge_code <> 'REF'
		--else
			select @total_invoice_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

		declare @invoice_ctr int
		declare @tenant_cost_center_specific varchar(10) 

		if ltrim(rtrim(isnull(@is_employee_benefit,'N'))) = 'Y'
			begin
				--header
				insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
							cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
				select top 1 
					cast(datepart(year,invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,invoice_date) as varchar(2)),2) ,
					cast(datepart(year,invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,invoice_date) as varchar(2)),2),
					upper(ltrim(rtrim(isnull(t_invoice_header.invoice_no,'')))) + space(@ref_doc_no_len-len(upper(ltrim(rtrim(isnull(t_invoice_header.invoice_no,'')))))),
					@company_code + space(@company_code_len-len(@company_code)),
					@currency_code + space(@currency_code_len-len(@currency_code)),
					@doc_type + space(@doc_type_len-len(@doc_type)),
					@posting_key_hdr_aff + space(@posting_key_len-len(@posting_key_hdr_aff)),
					upper(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end 
						+ space(@account_code_len-len(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end))),
					space(@amount_len-len(cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)),
					space(@tax_code_len),
					--space(@buss_area_len),
					@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
					@employee_benefit_cc + space(@cost_center_len-len(@employee_benefit_cc)),
					@job_order + space(@job_order_len-len(@job_order)),
					space(@base_date_len),
					@company_code + space(@new_code_len-len(@company_code)),
					upper(ltrim(rtrim(t_invoice_header.invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(t_invoice_header.invoice_no))) )),
					left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
					@date_uploaded,
					@uploaded_by
				from t_invoice_header
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

				--detail
				declare xxx cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_detail.charge_code,
				abs(t_invoice_detail.total_charge_amount) as total_charge_amount,m_tenant.tenant_name as client_name,
				case when isnull(t_invoice_header.sap_code,'') = '' then m_tenant.sap_code end as sap_code,m_charges.gl_code
				from t_invoice_header
				left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
				where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
		
				open xxx
				fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
				while @@fetch_status = 0
					begin
						set @tmp_invoice_no = upper(ltrim(rtrim(@tmp_invoice_no)))
						set @gl_code = upper(ltrim(rtrim(isnull(@gl_code,''))))
						
						if @charge_code <> 'REF'
							begin
								insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
									cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
								select top 1 
									space(@doc_date_len),
									space(@post_date_len),
									space(@ref_doc_no_len),
									space(@company_code_len),
									space(@currency_code_len),
									space(@doc_type_len),
									@posting_key_dtl1 + space(@posting_key_len-len(@posting_key_dtl1)),
									@gl_code + space(@account_code_len-len(@gl_code)),
									space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
									case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
										then @tax_code + space(@tax_code_len - len(@tax_code))
									else space(@tax_code_len) end,
									@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
									@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
									@job_order + space(@job_order_len-len(@job_order)),
									space(@base_date_len),
									@company_code + space(@new_code_len-len(@company_code)),
									upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
									left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
									@date_uploaded,
									@uploaded_by						
								end
							
						else
							begin
								insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
									cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
								select top 1 
									space(@doc_date_len),
									space(@post_date_len),
									space(@ref_doc_no_len),
									space(@company_code_len),
									space(@currency_code_len),
									space(@doc_type_len),
									@posting_key_dtl2 + space(@posting_key_len-len(@posting_key_dtl2)),
									@gl_code + space(@account_code_len-len(@gl_code)),
									space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
									case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
										then @tax_code + space(@tax_code_len - len(@tax_code))
									else space(@tax_code_len) end,
									@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
									@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
									@job_order + space(@job_order_len-len(@job_order)),
									space(@base_date_len),
									@company_code + space(@new_code_len-len(@company_code)),
									upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
									left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
									@date_uploaded,
									@uploaded_by						
								end
								
						fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
					end
				close xxx
				deallocate xxx
			end

		else if ltrim(rtrim(isnull(@is_sap_affiliate,'N'))) <> 'Y'
			begin
				--header
				insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
							cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
				select top 1 
					cast(datepart(year,invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,invoice_date) as varchar(2)),2) ,
					cast(datepart(year,invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,invoice_date) as varchar(2)),2),
					upper(ltrim(rtrim(isnull(t_invoice_header.invoice_no,'')))) + space(@ref_doc_no_len-len(upper(ltrim(rtrim(isnull(t_invoice_header.invoice_no,'')))))),
					@company_code + space(@company_code_len-len(@company_code)),
					@currency_code + space(@currency_code_len-len(@currency_code)),
					@doc_type + space(@doc_type_len-len(@doc_type)),
					@posting_key_hdr + space(@posting_key_len-len(@posting_key_hdr)),
					upper(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end 
						+ space(@account_code_len-len(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end))),
					space(@amount_len-len(cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)),
					space(@tax_code_len),
					--space(@buss_area_len),
					@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
					@cost_center_hdr + space(@cost_center_len-len(@cost_center_hdr)),
					@job_order + space(@job_order_len-len(@job_order)),
					space(@base_date_len),
					@company_code + space(@new_code_len-len(@company_code)),
					upper(ltrim(rtrim(t_invoice_header.invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(t_invoice_header.invoice_no))) )),
					left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
					@date_uploaded,
					@uploaded_by
				from t_invoice_header
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

				--detail
				declare xxx cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_detail.charge_code,
				abs(t_invoice_detail.total_charge_amount) as total_charge_amount,m_tenant.tenant_name as client_name,
				case when isnull(t_invoice_header.sap_code,'') = '' then m_tenant.sap_code end as sap_code,m_charges.gl_code
				from t_invoice_header
				left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
				where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
		
				open xxx
				fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
				while @@fetch_status = 0
					begin
						set @tmp_invoice_no = upper(ltrim(rtrim(@tmp_invoice_no)))
						set @gl_code = upper(ltrim(rtrim(isnull(@gl_code,''))))
						
						if @charge_code <> 'REF'
							begin
								insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
									cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
								select top 1 
									space(@doc_date_len),
									space(@post_date_len),
									space(@ref_doc_no_len),
									space(@company_code_len),
									space(@currency_code_len),
									space(@doc_type_len),
									@posting_key_dtl1 + space(@posting_key_len-len(@posting_key_dtl1)),
									@gl_code + space(@account_code_len-len(@gl_code)),
									space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
									case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
										then @tax_code + space(@tax_code_len - len(@tax_code))
									else space(@tax_code_len) end,
									@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
									@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
									@job_order + space(@job_order_len-len(@job_order)),
									space(@base_date_len),
									@company_code + space(@new_code_len-len(@company_code)),
									upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
									left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
									@date_uploaded,
									@uploaded_by		
							end	
						else
							begin
								insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
									cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
								select top 1 
									space(@doc_date_len),
									space(@post_date_len),
									space(@ref_doc_no_len),
									space(@company_code_len),
									space(@currency_code_len),
									space(@doc_type_len),
									@posting_key_dtl2 + space(@posting_key_len-len(@posting_key_dtl2)),
									@gl_code + space(@account_code_len-len(@gl_code)),
									space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
									case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
										upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
										then @tax_code + space(@tax_code_len - len(@tax_code))
									else space(@tax_code_len) end,
									@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
									@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
									@job_order + space(@job_order_len-len(@job_order)),
									space(@base_date_len),
									@company_code + space(@new_code_len-len(@company_code)),
									upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
									left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
									@date_uploaded,
									@uploaded_by		
							end				
								
						fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
					end
				close xxx
				deallocate xxx
			end
		else
			begin
				--detail
				declare xxx cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_detail.charge_code,
				abs(t_invoice_detail.total_charge_amount) as total_charge_amount,m_tenant.tenant_name as client_name,
				case when isnull(t_invoice_header.sap_code,'') = '' then m_tenant.sap_code end as sap_code,m_charges.gl_code
				from t_invoice_header
				left join t_invoice_detail on t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				left join m_charges on t_invoice_detail.charge_code = m_charges.charge_code
				where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
		
				set @invoice_ctr = 1

				open xxx
				fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
				while @@fetch_status = 0
					begin
						set @tmp_invoice_no = upper(ltrim(rtrim(@tmp_invoice_no)))
						set @gl_code = upper(ltrim(rtrim(isnull(@gl_code,''))))
						
						if @invoice_ctr = 1
							begin
								if @charge_code <> 'REF'
									begin
										insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
											cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
										select top 1 
											cast(datepart(year,@invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@invoice_date) as varchar(2)),2) ,
											cast(datepart(year,@invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@invoice_date) as varchar(2)),2),
											upper(ltrim(rtrim(@tmp_invoice_no))) + space(@ref_doc_no_len-len(upper(ltrim(rtrim(@tmp_invoice_no))))),
											@company_code + space(@company_code_len-len(@company_code)),
											@currency_code + space(@currency_code_len-len(@currency_code)),
											@doc_type + space(@doc_type_len-len(@doc_type)),
											@posting_key_dtl1 + space(@posting_key_len-len(@posting_key_dtl1)),
											@gl_code + space(@account_code_len-len(@gl_code)),
											space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
											--space(@tax_code_len),
											case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
												then @tax_code + space(@tax_code_len - len(@tax_code))
											else space(@tax_code_len) end,
											@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
											@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
											@job_order + space(@job_order_len-len(@job_order)),
											space(@base_date_len),
											@company_code + space(@new_code_len-len(@company_code)),
											--@company_code + upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(@company_code +upper(ltrim(rtrim(@tmp_invoice_no))) )),
											left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
											--left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
											left(@company_code  + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
											@date_uploaded,
											@uploaded_by	
									end
								else
									begin
										insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
											cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
										select top 1 
											cast(datepart(year,@invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@invoice_date) as varchar(2)),2) ,
											cast(datepart(year,@invoice_date) as varchar(4)) + right('00'+ cast(datepart(month,@invoice_date) as varchar(2)),2) + right('00'+ cast(datepart(day,@invoice_date) as varchar(2)),2),
											upper(ltrim(rtrim(@tmp_invoice_no))) + space(@ref_doc_no_len-len(upper(ltrim(rtrim(@tmp_invoice_no))))),
											@company_code + space(@company_code_len-len(@company_code)),
											@currency_code + space(@currency_code_len-len(@currency_code)),
											@doc_type + space(@doc_type_len-len(@doc_type)),
											@posting_key_dtl2 + space(@posting_key_len-len(@posting_key_dtl2)),
											@gl_code + space(@account_code_len-len(@gl_code)),
											space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
											--space(@tax_code_len),
											case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
												then @tax_code + space(@tax_code_len - len(@tax_code))
											else space(@tax_code_len) end,
											@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
											@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
											@job_order + space(@job_order_len-len(@job_order)),
											space(@base_date_len),
											@company_code + space(@new_code_len-len(@company_code)),
											--@company_code + upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(@company_code +upper(ltrim(rtrim(@tmp_invoice_no))) )),
											left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
											--left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
											left(@company_code  + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
											@date_uploaded,
											@uploaded_by		
									end	
							end		

						else
							begin
								if @charge_code <> 'REF'
									begin
										insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
											cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
										select top 1 
											space(@doc_date_len),
											space(@post_date_len),
											space(@ref_doc_no_len),
											space(@company_code_len),
											space(@currency_code_len),
											space(@doc_type_len),
											@posting_key_dtl1 + space(@posting_key_len-len(@posting_key_dtl1)),
											@gl_code + space(@account_code_len-len(@gl_code)),
											space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
											--space(@tax_code_len),
											case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
												then @tax_code + space(@tax_code_len - len(@tax_code))
											else space(@tax_code_len) end,
											@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
											@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
											@job_order + space(@job_order_len-len(@job_order)),
											space(@base_date_len),
											@company_code + space(@new_code_len-len(@company_code)),
											--upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
											left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
											--left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
											left(@company_code + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
											@date_uploaded,
											@uploaded_by	
									end	
								else
									begin
										insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
											cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
										select top 1 
											space(@doc_date_len),
											space(@post_date_len),
											space(@ref_doc_no_len),
											space(@company_code_len),
											space(@currency_code_len),
											space(@doc_type_len),
											@posting_key_dtl2 + space(@posting_key_len-len(@posting_key_dtl2)),
											@gl_code + space(@account_code_len-len(@gl_code)),
											space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
											--space(@tax_code_len),
											case when upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@apt_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@off_rental_charge))) or
												upper(ltrim(rtrim(@charge_code))) = upper(ltrim(rtrim(@whs_rental_charge))) 
												then @tax_code + space(@tax_code_len - len(@tax_code))
											else space(@tax_code_len) end,
											@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
											@cost_center_dtl + space(@cost_center_len-len(@cost_center_dtl)),
											@job_order + space(@job_order_len-len(@job_order)),
											space(@base_date_len),
											@company_code + space(@new_code_len-len(@company_code)),
											--upper(ltrim(rtrim(@tmp_invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(@tmp_invoice_no))) )),
											left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
											--left(@sap_text,@text_len) + space(@text_len-len(left(@sap_text,@text_len))),
											left(@company_code + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
											@date_uploaded,
											@uploaded_by	
									end	
							end		

							if exists (select * from m_tenant_cost_center where tenant_code = @client_code)
							begin
								select top 1 @tenant_cost_center_specific = cost_center from m_tenant_cost_center where tenant_code = @client_code
								set @tenant_cost_center_specific = ltrim(rtrim(isnull(@tenant_cost_center_specific,'')))

								insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
											cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
								select top 1 
									space(@doc_date_len),
									space(@post_date_len),
									space(@ref_doc_no_len),
									space(@company_code_len),
									space(@currency_code_len),
									space(@doc_type_len),
									@posting_key_hdr_aff + space(@posting_key_len-len(@posting_key_hdr_aff)),
									@gl_code + space(@account_code_len-len(@gl_code)),
									space(@amount_len-len(cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_charge_amount,0) as decimal(15,2)) as varchar(15)),
									space(@tax_code_len),
									--space(@buss_area_len),
									@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
									--space(@cost_center_len),
									@tenant_cost_center_specific + space(@cost_center_len-len(@tenant_cost_center_specific)),
									@job_order + space(@job_order_len-len(@job_order)),
									space(@base_date_len),
									@company_code + space(@new_code_len-len(@company_code)),
									--upper(ltrim(rtrim(t_invoice_header.invoice_no)))  + space(@alloc_len-len(upper(ltrim(rtrim(t_invoice_header.invoice_no))) )),
									left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
									left(@company_code + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
									@date_uploaded,
									@uploaded_by
								from t_invoice_header
								left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
								where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
							end		
										
						set @invoice_ctr = @invoice_ctr + 1
						fetch next from xxx into @tmp_invoice_no,@invoice_date,@charge_code,@total_charge_amount,@client_name,@sap_code,@gl_code
					end
				close xxx
				deallocate xxx

				if not exists (select * from m_tenant_cost_center where tenant_code = @client_code)
					begin
						--select @total_invoice_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and charge_code <> 'REF'
						
						insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
									cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
						select top 1 
							space(@doc_date_len),
							space(@post_date_len),
							space(@ref_doc_no_len),
							--space(@company_code_len),
							@company_code_aff + space(@company_code_len-len(@company_code_aff)),
							space(@currency_code_len),
							space(@doc_type_len),
							@posting_key_hdr_aff + space(@posting_key_len-len(@posting_key_hdr_aff)),
							--case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') end + space(@account_code_len-len(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') end)),
							upper(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end 
								+ space(@account_code_len-len(case when isnull(t_invoice_header.sap_code,'') = '' then isnull(m_tenant.sap_code,'') else isnull(t_invoice_header.sap_code,'') end))),
							space(@amount_len-len(cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(@total_invoice_amount,0) as decimal(15,2)) as varchar(15)),
							space(@tax_code_len),
							--space(@buss_area_len),
							@buss_area_aff + space(@buss_area_len - len(@buss_area_aff)),
							space(@cost_center_len),
							@job_order + space(@job_order_len-len(@job_order)),
							space(@base_date_len),
							@company_code_aff + space(@new_code_len-len(@company_code_aff)),
							left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
							left(@company_code + ' ' + @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' ' +@sap_text,@text_len))),
							@date_uploaded,
							@uploaded_by
						from t_invoice_header
						left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
						where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))
					end
			end
		
		-- // IF TENANT HAS SUB-LEASE, ADD 2 ENTRIES IN SAP FACILITIES RENTAL 515010000, 249.52 (FOR CTSI, SUBLEASE OF COO)
		--commented by resalie usi 2021.10.14 as advised by acctg
		/*
		if @client_code = 'T000000038' and @real_property_code_tmp = 'LB 2'--CTSI CLIENT PAYING FOR LB 2 (TO NOT DECLARE IN BGRT)
			begin
				insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
					cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
				select top 1 
					space(@doc_date_len),
					space(@post_date_len),
					space(@ref_doc_no_len),
					space(@company_code_len),
					space(@currency_code_len),
					space(@doc_type_len),
					'50' + space(@posting_key_len-len('50')),
					'515010000' + space(@account_code_len-len('515010000')),
					space(@amount_len-len(cast(cast(249.52 as decimal(15,2)) as varchar(15)))) + cast(cast(249.52 as decimal(15,2)) as varchar(15)),
					space(@tax_code_len),
					@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
					'64121' + space(@cost_center_len-len('64121' )),
					@job_order + space(@job_order_len-len(@job_order)),
					space(@base_date_len),
					@company_code + space(@new_code_len-len(@company_code)),
					left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
					left(@company_code  + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
					@date_uploaded,
					@uploaded_by	

				insert into t_sap_upload (doc_date,posting_date,refdocno,company_code,currency,doctype,postingkey,account_code,amount,tax_code,buss_area,
					cost_center,job_order,baselndate,new_code,alloc,stext,date_uploaded,uploaded_by)
				select top 1 
					space(@doc_date_len),
					space(@post_date_len),
					space(@ref_doc_no_len),
					space(@company_code_len),
					space(@currency_code_len),
					space(@doc_type_len),
					'40' + space(@posting_key_len-len('40')),
					'515010000' + space(@account_code_len-len('515010000')),
					space(@amount_len-len(cast(cast(isnull(249.52,0) as decimal(15,2)) as varchar(15)))) + cast(cast(isnull(249.52,0) as decimal(15,2)) as varchar(15)),
					space(@tax_code_len),
					@buss_area_non_aff + space(@buss_area_len - len(@buss_area_non_aff)),
					'67031' + space(@cost_center_len-len('67031')),
					@job_order + space(@job_order_len-len(@job_order)),
					space(@base_date_len),
					@company_code + space(@new_code_len-len(@company_code)),
					left(@alloc_invoice_no,@alloc_len) + space(@alloc_len-len(left(@alloc_invoice_no,@alloc_len))),
					left(@company_code  + ' '+ @sap_text,@text_len) + space(@text_len-len(left(@company_code + ' '+@sap_text,@text_len))),
					@date_uploaded,
					@uploaded_by	
			end
		*/
		-- //		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_SAP_Uploading_Build]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_SAP_Uploading_Build]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000),@charge_code varchar(50)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'INVOICE SAP UPLOADING'

if @strMode = 'BUILD'
	begin
		delete from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_sap_uploading]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_sap_uploading]

		--\\ create temp table

		set @ssql = ' invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20) '

		declare xxx cursor scroll for
		select charge_code from m_charges order by charge_desc
		
		open xxx
		fetch next from xxx into @charge_code
		while @@fetch_status = 0
			begin
				set @charge_code = '[' + @charge_code + '] decimal(18,2)'
				if @ssql = ''
					set @ssql = @ssql + @charge_code 
				else
					set @ssql = @ssql + ',' +  @charge_code 
		
				fetch next from xxx into @charge_code
			end
		close xxx
		deallocate xxx
	
		set @ssql = 'create table z_tmp_sap_uploading (' + @ssql + ')'
		--print @ssql
		exec sp_executesql @ssql
		set @ssql = ''

		--\\

		declare @tmp_invoice_no varchar(20),@tmp_tenant_code varchar(20),@tmp_charge_code varchar(20),@tmp_total_charge_amount decimal(18,2)

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = ''
			end
		else
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = ''
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end

		open yyy
		fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
		while @@fetch_status = 0
			begin
				if not exists (select * from z_tmp_sap_uploading where invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code)
					begin
						insert into z_tmp_sap_uploading (invoice_no,invoice_date ,client_code ,tenant_code  )	
						select top 1 t_invoice_header.invoice_no,invoice_date,client_code,t_invoice_detail.tenant_code
						from t_invoice_header 
						left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
						where t_invoice_header.invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code
					end
		
				set @ssql = 'update z_tmp_sap_uploading set [' + @tmp_charge_code + ']= ' + convert(varchar(10),@tmp_total_charge_amount)
				set @ssql = @ssql + ' where invoice_no =  ''' + @tmp_invoice_no + ''' and tenant_code = ''' + @tmp_tenant_code + ''''
				exec sp_executesql @ssql
				
				fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
			end
		close yyy
		deallocate yyy
		
		if @sort_by = 'INVOICE NO.' or @sort_by = ''
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_no
			end
		else if @sort_by = 'INVOICE DATE'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_date,a.invoice_no
			end
		else if @sort_by = 'TENANT'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by tenant.tenant_name,a.invoice_no
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_no, ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no))
			end

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_SAP_Uploading_Build_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_SAP_Uploading_Build_View]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'INVOICE SAP UPLOADING'

if @strMode = 'BUILD'
	begin
		delete from t_sap_upload where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_sap_uploading_view]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_sap_uploading_view]

		create table z_tmp_sap_uploading_view (invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20),charge_code varchar(10),total_amount decimal(18,2))

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				insert into z_tmp_sap_uploading_view
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = ''
			end
		else
			begin
				insert into z_tmp_sap_uploading_view
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = ''
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end
		
		/*if @sort_by = 'INVOICE NO.' or @sort_by = ''
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				total_amount,ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.charge_code,charge_desc
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				left join m_charges on a.charge_code = m_charges.charge_code
				where isnull(total_amount,0) > 0
				order by invoice_no
			end
		else if @sort_by = 'INVOICE DATE'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				total_amount,ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.charge_code,charge_desc
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				left join m_charges on a.charge_code = m_charges.charge_code
				where isnull(total_amount,0) > 0
				order by invoice_date,invoice_no
			end
		else if @sort_by = 'TENANT'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				total_amount,ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.charge_code,charge_desc
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				left join m_charges on a.charge_code = m_charges.charge_code
				where isnull(total_amount,0) > 0
				order by tenant.tenant_name,invoice_no
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				total_amount,ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.charge_code,charge_desc
				from z_tmp_sap_uploading a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				left join m_charges on a.charge_code = m_charges.charge_code
				where isnull(total_amount,0) > 0
				order by invoice_no, ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no))
			end
		*/

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Security_Deposit]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Security_Deposit]
	@strMode varchar(50),
	@or_no varchar(20),
	@or_date datetime,
	@client_code char(10),
	@amount decimal(9,2),
	@remarks varchar(100),
	@status char(1),
	@payment_mode_id decimal(9,2),
	@mode_of_payment char(1),
	@payment_mode_amount decimal(9,2),
	@payment_bank_name varchar(100),
	@payment_account_no varchar(20),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @payment_detail_cnt int,@tmp_or_no varchar(20),@total_payment_detail_amount decimal(9,2)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)
declare @or_no_type char(1)

set @module_name = 'TENANT SECURITY DEPOSIT'

set @or_no_type = 'N'
set @client_code = upper(ltrim(rtrim(@client_code)))
set @or_no = upper(ltrim(rtrim(@or_no)))


if @strMode = 'SAVE'
	begin
		if @or_no = '' 
			begin
				select @or_no = dbo.fn_GetNextORNo (@or_date)
				set @or_no_type = 'N'
			end
		
		if not exists(select * from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_security_deposit(or_no,or_no_type,or_date,client_code,amount,remarks,status,date_updated,updated_by)
				select @or_no,@or_no_type,@or_date,@client_code,@amount,@remarks,@status,getdate(),@uid

				set @data = 't_security_deposit(or_no,or_no_type,or_date,client_code,amount,remarks,status,date_updated,updated_by)) ' +
					'select ' + @or_no +',' +@or_no_type+',' +convert(varchar(10),@or_date,101)+',' +@client_code+',' + convert(varchar(10),@amount) + ','+
					@remarks+',' +@status+',' +convert(varchar(20),getdate())+',' +@uid

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
				
				select @or_no as or_no
			end
		else
			begin
				update t_security_deposit set
					or_date = @or_date,
					client_code = @client_code,
					amount = isnull(@amount,0),					
					remarks = @remarks,
					status = @status,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(isnull(status,'')))) <> 'P'
				
				set @data = 'update t_security_deposit set ' +
						'or_date ='+ convert(varchar(10),@or_date,101)+','+
						'client_code ='+ @client_code+','+
						'amount ='+ convert(varchar(10),isnull(@amount,0))+','+						
						'remarks ='+ @remarks+','+
						'status ='+ @status+','+
						'date_updated ='+ convert(varchar(20),getdate())+','+
						'updated_by ='+ @uid +
						'where upper(ltrim(rtrim(or_no))) ='+ upper(ltrim(rtrim(@or_no))) +'and upper(ltrim(rtrim(isnull(status,'''')))) <> ''P'''
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
		
	end



if @strMode = 'DELETE_PAYMENT_MODE'
	begin
		delete from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
	end


if @strMode = 'SAVE_PAYMENT_MODE'
	begin
		if @payment_mode_id = 0
			begin
				if @mode_of_payment = 1
					begin
						if not exists (select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = 1)
							begin
								insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
								select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@payment_bank_name		
							end		
						else	
							begin
								update t_security_deposit_payment_mode set
									payment_mode_type = @mode_of_payment,
									amount = isnull(amount,0) + @payment_mode_amount,
									account_no = @payment_account_no,	
									bank_name = @payment_bank_name
								where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_type = @mode_of_payment
							end
					end
				else
					begin
						insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,@mode_of_payment,@payment_mode_amount,@payment_account_no,@payment_bank_name		
					end		
			end
		else			
			begin
				update t_security_deposit_payment_mode set
					payment_mode_type = @mode_of_payment,
					amount = @payment_mode_amount,
					account_no = @payment_account_no,	
					bank_name = @payment_bank_name
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and payment_mode_id = @payment_mode_id
			end
		
	end


if @strMode = 'FIND'
	begin
		select top 1 x =  dbo.fn_CheckIfORNoExists (@or_no)
	end

if @strMode = 'RETRIEVE'
	begin
		select top 1 @tmp_or_no = or_no
		from t_security_deposit
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'

		select @payment_detail_cnt = count(*),@total_payment_detail_amount = isnull(sum(isnull(amount,0)),0) 
		from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		select top 1 or_no,or_no_type,or_date,client_code,amount,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name  as client_name,@payment_detail_cnt as payment_detail_cnt,isnull(@total_payment_detail_amount,0) as total_payment_detail_amount 	
		from t_security_deposit
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
	end

if @strMode = 'RETRIEVE_PAYMENT_MODE'
	begin
		delete from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) = 0

		if not exists (select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
			begin
				insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
				select top 1 or_no,'1',amount,'','' from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))			
			end

		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_security_deposit_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and isnull(amount,0) > 0 order by payment_mode_type
	end



if @strMode = 'RETRIEVE_CASH_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_security_deposit_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '1'
	end

if @strMode = 'RETRIEVE_CHARGE_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_security_deposit_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '2'
	end

if @strMode = 'RETRIEVE_CHECK_PAYMENT'
	begin
		select payment_mode_id,payment_mode_type,amount,account_no,bank_name,
			case when upper(ltrim(rtrim(payment_mode_type))) = '1' then 'CASH'
			when upper(ltrim(rtrim(payment_mode_type))) = '2' then 'CHARGE'
			when upper(ltrim(rtrim(payment_mode_type))) = '3' then 'CHECK'
			else '' end as payment_mode_type_desc
		from t_security_deposit_payment_mode	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(payment_mode_type))) = '3'
	end


if @strMode = 'VIEW'
	begin
		select distinct t_security_deposit.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_security_deposit 
		inner join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%'
		order by or_date desc,t_security_deposit.or_no desc
	end


if @strMode = 'VIEW_STAT'
	begin
		select distinct t_security_deposit.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'')))) as status,
		case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
		when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
		else '' end as status_desc
		from t_security_deposit 
		inner join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where (or_no like '%' + @remarks + '%' or tenant_name like '%' + @remarks + '%'
		or CONVERT(VARCHAR(10), or_date, 101) like '%' + @remarks + '%')
		and ltrim(rtrim(isnull(status,''))) = ''
		order by or_date desc,t_security_deposit.or_no desc
	end

if @strMode = 'CLIENT_SEARCH'
	begin
		select * from m_tenant 
		where (tenant_name like @remarks + '%' or tenant_code like @remarks + '%'
		or building_code like @remarks + '%' or unit_no like @remarks + '%')
		and isnull(terminated,'N') <> 'Y'
		and (upper(ltrim(rtrim(tenant_type))) = 'OC' or upper(ltrim(rtrim(tenant_type))) = 'C')
		order by tenant_name
	end


if @strMode = 'VOID'
	begin
		if not exists (select * from t_security_deposit where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  and upper(ltrim(rtrim(status))) = 'P')
			begin
				update t_security_deposit set status = 'V' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

				set @data = 'update t_security_deposit set status = ''V'' where upper(ltrim(rtrim(or_no))) = ' + upper(ltrim(rtrim(@or_no)))
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'VOID',@company_code

				select 0 as x
				
			end
		else
			select 1 as x
	end



if @strMode = 'POST'
	begin
		update t_security_deposit set status = 'P' where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))  
		set @data = 'update t_security_deposit set status = ''P'' where upper(ltrim(rtrim(or_no))) ='+ upper(ltrim(rtrim(@or_no)))  
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'POST',@company_code
		select 0 as x			
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Security_Deposit_Detail]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Security_Deposit_Detail]
	@strMode varchar(50),
	@or_no varchar(20),
	@detail_id decimal(18,0),
	@real_property_code char(5),
	@building_code char(10),
	@unit_no char(10),
	@amount decimal(9,2),
	@remarks varchar(100),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @total_or_amount decimal(9,2),@payment_detail_total_rec decimal(9,0)
declare @paid_amount decimal(9,2)
declare @total_payment_mode_amount decimal(9,2),@payment_mode_detail_cnt int
declare @tmp_amount decimal(9,2)	
declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'SECURITY DEPOSIT DETAIL'

if @strMode = 'SAVE'
	begin
		if @detail_id = 0 
			begin
				if exists (select * from t_security_deposit_detail
					where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and real_property_code = @real_property_code and building_code = @building_code 
					and unit_no = @unit_no
					)
					begin
						delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and real_property_code = @real_property_code 
						and building_code = @building_code 
						and unit_no = @unit_no
					end

				insert into t_security_deposit_detail (or_no,real_property_code,building_code,unit_no,amount,date_updated,updated_by)
				select @or_no,@real_property_code,@building_code,@unit_no,@amount,getdate(),@uid

				set @data = 'insert into t_security_deposit_detail (or_no,real_property_code,building_code,unit_no,amount,date_updated,updated_by) ' + 
					' select ' + @or_no + ',' + @real_property_code + ',' + @building_code + ',' + @unit_no + ',' + convert(varchar(10),@amount) + ',' + ',' + convert(varchar(20),getdate()) + ',' + @uid
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code				
			end
		else	
			begin
				update t_security_deposit_detail set 
					amount = @amount,
					date_updated = getdate(),
					updated_by = @uid
				where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and detail_id = @detail_id				
	
				set @data = 'update t_security_deposit_detail set ' +
					'amount =' + convert(varchar(10),@amount)+','+
					'date_created = ' + convert(varchar(20),getdate())+','+
					'created_by =' + @uid +
					'where upper(ltrim(rtrim(or_no))) =' + upper(ltrim(rtrim(@or_no))) + ' and detail_id =' + convert(varchar(10),@detail_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code				
			end	

		--update payment mode table
		select @total_or_amount = sum(isnull(amount,0)) from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @total_payment_mode_amount = sum(isnull(amount,0))
			from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @payment_mode_detail_cnt = count(*) 
			from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

		if isnull(@total_or_amount,0) <> isnull(@total_payment_mode_amount,0)
			begin
				if @payment_mode_detail_cnt = 1
					begin
						update t_security_deposit_payment_mode set					
						amount = @total_or_amount
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
						and payment_mode_id in (select top 1 payment_mode_id from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					end
				else
					begin
						delete from t_security_deposit_payment_mode 
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
						
						insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,'1',@total_or_amount,'',''
					end
			end
		--end update payment mode table

	end


if @strMode = 'RETRIEVE_HDR'
	begin
		select top 1 @or_no = or_no from t_security_deposit
		where upper(ltrim(or_no)) like '%' + upper(ltrim(@or_no)) + '%' 

		select @total_or_amount = sum(isnull(amount,0)) from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @payment_detail_total_rec = count(*) from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))		

		select top 1 or_no,or_no_type,or_date,client_code,amount,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			date_updated,updated_by,tenant_name as client_name,isnull(@total_or_amount,0) as total_or_amount,@payment_detail_total_rec as payment_detail_total_rec,
			isnull(t_security_deposit.amount,0) as or_amount_header
		from t_security_deposit
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code	
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
	end
/*
if @strMode = 'ADD_ENTRY'
	begin
		if not exists(select * from t_payment_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id)
			begin
				insert into t_payment_detail (or_no,invoice_no,invoice_detail_id,or_amount,total_charge_amount,date_created,created_by,is_selected)	
				select top 1 @or_no,invoice_no,invoice_detail_id,isnull(total_charge_amount,0)-isnull(paid_amount,0),total_charge_amount,getdate(),@uid,1 from t_invoice_detail
				where upper(ltrim(rtrim(invoice_no))) = upper(ltrim(rtrim(@invoice_no))) and invoice_detail_id = @invoice_detail_id 

				
				
				select 0 as x
			end
		else
			select 1 as x
	end
*/

if @strMode = 'RETRIEVE_DTL'
	begin
		select t_security_deposit_detail.*,m_real_property.real_property_name
		from t_security_deposit
		left join t_security_deposit_detail on t_security_deposit.or_no = t_security_deposit_detail.or_no		
		left join m_real_property on t_security_deposit_detail.real_property_code = m_real_property.real_property_code
		where upper(ltrim(rtrim(t_security_deposit_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
		order by t_security_deposit_detail.real_property_code,t_security_deposit_detail.building_code,t_security_deposit_detail.unit_no
	end


if @strMode = 'VACANT_UNITS'
	begin
		select m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @remarks + '%' or building_code like '%' + @remarks + '%' or unit_no like '%' + @remarks + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O'
			)
			and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from t_security_deposit_detail where upper(ltrim(rtrim(t_security_deposit_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
			)
		order by real_property_name,building_code,unit_no
	end

if @strMode = 'OCCUPIED_UNITS'
	begin
		select m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @remarks + '%' or building_code like '%' + @remarks + '%' or unit_no like '%' + @remarks + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from m_tenant where upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'OC' or upper(ltrim(rtrim(isnull(tenant_type,'OC')))) = 'O'
			)
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from t_security_deposit_detail where upper(ltrim(rtrim(t_security_deposit_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
			)
		order by real_property_name,building_code,unit_no
	end

if @strMode = 'ALL_UNITS'
	begin
		select m_units.*,real_property_name from m_units 
		left join m_real_property on m_units.real_property_code = m_real_property.real_property_code
		where (real_property_name like '%' + @remarks + '%' or building_code like '%' + @remarks + '%' or unit_no like '%' + @remarks + '%')
		and upper(ltrim(rtrim(isnull(is_reserved,'N')))) <> 'Y' 	
		and upper(ltrim(rtrim(isnull(m_units.real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(m_units.unit_no,'')))) not in
			(
			select upper(ltrim(rtrim(isnull(real_property_code,'')))) + '*' + upper(ltrim(rtrim(isnull(building_code,'')))) + '*' + upper(ltrim(rtrim(isnull(unit_no,'')))) 
			from t_security_deposit_detail where upper(ltrim(rtrim(t_security_deposit_detail.or_no))) = upper(ltrim(rtrim(@or_no)))
			)	
		order by real_property_name,building_code,unit_no
	end


if @strMode = 'DELETE'		
	begin
		delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) and detail_id = @detail_id

		--update payment mode table
		select @total_or_amount = sum(isnull(amount,0)) from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
		select @total_payment_mode_amount = sum(isnull(amount,0)),@payment_mode_detail_cnt = count(*) 
			from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

		if isnull(@total_or_amount,0) <> isnull(@total_payment_mode_amount,0)
			begin
				if @payment_mode_detail_cnt = 1
					begin
						update t_security_deposit_payment_mode set					
						amount = @total_or_amount
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))) 
						and payment_mode_id in (select top 1 payment_mode_id from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
					end
				else
					begin
						delete from t_security_deposit_payment_mode 
						where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

						insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
						select @or_no,'1',@total_or_amount,'',''
					end
			end
		--end update payment mode table

		set @data = 'delete from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = ' +upper(ltrim(rtrim(@or_no))) + ' and detail_id = ' + convert(varchar(10),@detail_id)
		exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code		

		select 0 as x		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Security_Deposit_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO

CREATE PROCEDURE [dbo].[sp_t_Security_Deposit_List]
	@strMode varchar(50),
	@column_code varchar(50),
	@keyword varchar(100),
	@function_id int

AS

--FUNCTION ID = 1
declare @ssql nvarchar(4000),@module_name varchar(50),@order_by varchar(1000),@data_type char(1)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @ssql = 'select distinct t_security_deposit.or_no,CONVERT(VARCHAR(10), or_date, 101) as or_date,tenant_name as client_name,
		upper(ltrim(rtrim(isnull(status,'''')))) as status,
		case when upper(ltrim(rtrim(status))) = ''P'' then ''POSTED''
		when upper(ltrim(rtrim(status))) = ''V'' then ''VOID''
		else '''' end as status_desc
		from t_security_deposit 
		inner join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code 
		and isnull(trans_type,'''') <> ''U'' '

	if ltrim(rtrim(@column_code)) <> '' 
		begin
			if @data_type = 'S'
				set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
			else if @data_type = 'D' and @keyword <> ''
				set @ssql = @ssql + ' where convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
		end

	if @strMode = 'VIEW_STAT'
		begin
			if ltrim(rtrim(@column_code)) <> '' 
				set @ssql = @ssql + ' and '
			else
				set @ssql = @ssql + ' where '

			set @ssql = @ssql +' upper(ltrim(rtrim(isnull(t_security_deposit.status,'''')))) = '''' '
		end

	if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' order by t_security_deposit.or_no desc'
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Security_Deposit_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Security_Deposit_Retrieve]
	@strMode varchar(50),
	@or_no varchar(20)
AS

declare @payment_detail_cnt int,@tmp_or_no varchar(20),@total_payment_detail_amount decimal(9,2)
declare @data nvarchar(4000),@module_name varchar(50),@year varchar(4),@month varchar(2)

set @module_name = 'TENANT SECURITY DEPOSIT'

if @strMode = 'RETRIEVE'
	begin
		select top 1 @tmp_or_no = or_no
		from t_security_deposit
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) like '%' +upper(ltrim(rtrim(@or_no))) + '%'
		and isnull(trans_type,'') <> 'U'

		select @payment_detail_cnt = count(*),@total_payment_detail_amount = isnull(sum(isnull(amount,0)),0) 
		from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))

		select top 1 or_no,or_no_type,or_date,client_code,amount,remarks,upper(ltrim(rtrim(status))) as status,
			case when upper(ltrim(rtrim(status))) = 'P' then 'POSTED'
			when upper(ltrim(rtrim(status))) = 'V' then 'VOID'
			else '' end as status_desc,
			t_security_deposit.date_updated,t_security_deposit.updated_by,tenant_name  as client_name,@payment_detail_cnt as payment_detail_cnt,isnull(@total_payment_detail_amount,0) as total_payment_detail_amount 	
		from t_security_deposit
		left join m_tenant on t_security_deposit.client_code = m_tenant.tenant_code		
		where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@tmp_or_no)))
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_Security_Deposit_UpdatePaymentMode]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_Security_Deposit_UpdatePaymentMode]
	@or_no varchar(20)
AS

declare @total_or_amount decimal(9,2)

	select @total_or_amount = sum(isnull(amount,0)) from t_security_deposit_detail where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

	update t_security_deposit set amount = @total_or_amount where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))

	if exists(select * from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
		begin
			delete from t_security_deposit_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))		
		end

	if exists(select * from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no))))
		begin
			insert into t_security_deposit_payment_mode (or_no,payment_mode_type,amount,account_no,bank_name)
			select top 1 or_no,payment_mode_type,@total_or_amount,account_no,bank_name 
			from t_payment_header_payment_mode where upper(ltrim(rtrim(or_no))) = upper(ltrim(rtrim(@or_no)))
			and isnull(amount,0) >= @total_or_amount
		end

GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading]
	@strMode varchar(50),
	@reading_id decimal(9,0),
	@tenant_code varchar(20),
	@charge_code char(5),
	@reading_date_from datetime,
	@reading_date_to datetime,
	@prev_reading decimal(9,0),
	@current_reading decimal(9,0),
	@remarks varchar(100),
	@billing_date_from datetime,
	@billing_date_to datetime,
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING'

if @strMode = 'SAVE'
	begin
		if not exists (select * from t_tenant_reading where reading_id = @reading_id)
			begin
				insert into t_tenant_reading (tenant_code,charge_code,date_from,date_to,prev_reading,current_reading,remarks,billing_date_from,billing_date_to)	
				select @tenant_code,@charge_code,@reading_date_from,@reading_date_to,@prev_reading,@current_reading,@remarks,@billing_date_from,@billing_date_to

				set @data = 'insert into t_tenant_reading (tenant_code,charge_code,date_from,date_to,prev_reading,current_reading,remarks,billing_date_from,billing_date_to) ' +	
					'select ' + @tenant_code +','+@charge_code+','+ convert(varchar(10),@reading_date_from,101)+','+convert(varchar(10),@reading_date_to,101)+','+convert(varchar(10),@prev_reading)+','+
					convert(varchar(10),@current_reading)+','+@remarks+','+convert(varchar(10),@billing_date_from,101)+','+convert(varchar(10),@billing_date_to,101)

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code
			end
		else
			begin
				update t_tenant_reading set 
					tenant_code = @tenant_code,
					charge_code = @charge_code,
					date_from = @reading_date_from,
					date_to = @reading_date_to,
					prev_reading = @prev_reading,
					current_reading = @current_reading,
					remarks = @remarks,
					billing_date_from = @billing_date_from,
					billing_date_to = @billing_date_to
				where reading_id = @reading_id

				set @data = 'update t_tenant_reading set ' + 
					'tenant_code = ' + @tenant_code+','+
					'charge_code =' + @charge_code+','+
					'date_from = ' + convert(varchar(10),@reading_date_from,101) +','+
					'date_to = ' + convert(varchar(10),@reading_date_to,101) +','+
					'prev_reading =' + convert(varchar(10),@prev_reading) +','+
					'current_reading =' + convert(varchar(10),@current_reading) +','+
					'remarks =' + @remarks+','+
					'billing_date_from =' + convert(varchar(10),@billing_date_from,101) +','+
					'billing_date_to =' + convert(varchar(10),@billing_date_to,101) +
					'where reading_id =' + convert(varchar(10),@reading_id)

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code
			end
	end

if @strMode = 'RETRIEVE'
	begin
		if exists (select * from t_invoice_detail_reading where reading_id = @reading_id and invoice_no not in (select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'V'))
		and exists (select * from t_tenant_reading where reading_id = @reading_id and ltrim(rtrim(isnull(invoice_no,''))) = '')
			begin
				declare @invoice_no varchar(20),@invoice_detail_id decimal(9,0),@invoice_detail_reading_id decimal(9,0)

				select top 1 
					@invoice_no = invoice_no,
					@invoice_detail_id = invoice_detail_id,
					@invoice_detail_reading_id = invoice_detail_reading_id
				from t_invoice_detail_reading where reading_id = @reading_id and invoice_no not in (select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'V')

				update t_tenant_reading set 
					invoice_no = @invoice_no,
					invoice_detail_id = @invoice_detail_id,
					invoice_detail_reading_id = @invoice_detail_reading_id
				where reading_id = @reading_id and ltrim(rtrim(isnull(invoice_no,''))) = ''
			end
		
		select top 1 t_tenant_reading.*,m_tenant.tenant_name,m_charges.charge_desc from t_tenant_reading
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		left join m_charges on t_tenant_reading.charge_code = m_charges.charge_code
		where reading_id = @reading_id
	end

if @strMode = 'FIND'
	begin
		if exists(select t_tenant_reading.*,m_tenant.tenant_name,m_charges.charge_desc from t_tenant_reading
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		left join m_charges on t_tenant_reading.charge_code = m_charges.charge_code
		where upper(t_tenant_reading.tenant_code) = upper(@tenant_code) and t_tenant_reading.charge_code = @charge_code
		and date_from = @reading_date_from and date_to = @reading_date_to
		and billing_date_from = @billing_date_from and billing_date_to = @billing_date_to)
			select 1 as x
		else
			select 0 as x
	end

if @strMode = 'VIEW'
	begin
		select t_tenant_reading.reading_id,t_tenant_reading.tenant_code,t_tenant_reading.charge_code,convert(varchar(10),t_tenant_reading.date_from,101) as date_from,
		convert(varchar(10),t_tenant_reading.date_to,101) as date_to,convert(varchar(10),t_tenant_reading.billing_date_from,101) as billing_date_from,
		convert(varchar(10),t_tenant_reading.billing_date_to,101) as billing_date_to,
		m_tenant.tenant_name,m_charges.charge_desc,ltrim(rtrim(isnull(invoice_no,''))) as invoice_no
		from t_tenant_reading
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		left join m_charges on t_tenant_reading.charge_code = m_charges.charge_code
		where tenant_name like @remarks + '%' or t_tenant_reading.tenant_code like @remarks + '%'
		or charge_desc like @remarks + '%' or convert(varchar(10),date_from,101) like @remarks + '%' 
		or convert(varchar(10),date_to,101) like @remarks + '%' or convert(varchar(10),billing_date_from,101) like @remarks + '%' 
		or convert(varchar(10),billing_date_to,101) like @remarks + '%' 
	end



if @strMode = 'VIEW_STAT'
	begin
		select t_tenant_reading.reading_id,t_tenant_reading.tenant_code,t_tenant_reading.charge_code,convert(varchar(10),t_tenant_reading.date_from,101) as date_from,
		convert(varchar(10),t_tenant_reading.date_to,101) as date_to,convert(varchar(10),t_tenant_reading.billing_date_from,101) as billing_date_from,
		convert(varchar(10),t_tenant_reading.billing_date_to,101) as billing_date_to,
		m_tenant.tenant_name,m_charges.charge_desc,ltrim(rtrim(isnull(invoice_no,''))) as invoice_no
		from t_tenant_reading
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		left join m_charges on t_tenant_reading.charge_code = m_charges.charge_code
		where (tenant_name like @remarks + '%' or t_tenant_reading.tenant_code like @remarks + '%'
		or charge_desc like @remarks + '%' or convert(varchar(10),date_from,101) like @remarks + '%' 
		or convert(varchar(10),date_to,101) like @remarks + '%' or convert(varchar(10),billing_date_from,101) like @remarks + '%' 
		or convert(varchar(10),billing_date_to,101) like @remarks + '%')
		and reading_id not in
		(
		select reading_id from t_invoice_detail_reading where invoice_no not in (select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'V')
		) 
	end

if @strMode = 'TENANT_CHARGES_SEARCH'
	begin
		select m_tenant.tenant_code,tenant_name,isnull(real_property_code,'') + ' / ' + isnull(building_code,'') + ' / ' + isnull(unit_no,'') as property,
		m_tenant_charges.charge_code,charge_desc
		from m_tenant
		left join m_tenant_charges on m_tenant.tenant_code = m_tenant_charges.tenant_code
		left join m_charges on m_tenant_charges.charge_code = m_charges.charge_code
		where (m_tenant.tenant_code  like  '%' + @remarks + '%' or tenant_name like  '%' + @remarks + '%' or charge_desc  like  '%' + @remarks + '%')
		and isnull(terminated,'N') <> 'Y' and charge_type = 'U'
		order by tenant_name,charge_desc
	end

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_invoice_detail_reading where reading_id = @reading_id)
			begin
				delete from t_tenant_reading where reading_id = @reading_id
				set @data = 'delete from t_tenant_reading where reading_id = ' + convert(varchar(10),@reading_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Charges_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Charges_Delete]
	@strMode varchar(50),
	@reading_id decimal(18,0),
	@charge_id decimal(18,0),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING - CHARGES'

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_invoice_detail_reading where reading_id = @reading_id 
			and invoice_detail_id in (select invoice_detail_id from t_invoice_detail where charge_code in 
			(select trc_charge_code from t_tenant_reading_charges where trc_charge_id = @charge_id and trc_reading_id = @reading_id)))
			begin
				delete from t_tenant_reading_charges where trc_reading_id = @reading_id and trc_charge_id = @charge_id
				
				set @data = 'delete from t_tenant_reading_charges where trc_reading_id = ' + convert(varchar(10),@reading_id) +
					'and trc_charge_id =' + convert(varchar(10),@charge_id) 
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Charges_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Charges_Retrieve]
	@strMode varchar(50),
	@reading_id decimal(18,0)
AS

if @strMode = 'RETRIEVE'
	begin
		select  t_tenant_reading_charges.*,m_charges.*  from t_tenant_reading_charges
		left join m_charges on t_tenant_reading_charges.trc_charge_code = m_charges.charge_code
		where trc_reading_id = @reading_id
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Charges_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Charges_Save]
	@strMode varchar(50),
	@reading_id decimal(18,0),
	@reading_charge_id decimal(18,0),
	@charge_code char(5),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING'

if @strMode = 'SAVE'
	begin
		if not exists (select * from t_tenant_reading_charges where trc_reading_id = @reading_id and  trc_charge_code = @charge_code)
			begin
				if exists (select * from m_tenant_charges where charge_code = @charge_code and tenant_code in (select top 1 tenant_code from t_tenant_reading 
					where reading_id = @reading_id))
					begin
						insert into t_tenant_reading_charges (trc_reading_id,trc_charge_code)	
						select @reading_id,@charge_code
		
						set @data = 'insert into t_tenant_reading_charges (trc_reading_id,trc_charge_code)	) ' +	
							'select '+ convert(varchar(10),@reading_id,101)+','+ @charge_code
		
						exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

						select 0 as x
					end
				else
					select 1 as x
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Delete]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Delete]
	@strMode varchar(50),
	@reading_id decimal(9,0),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING'

if @strMode = 'DELETE'
	begin
		if not exists (select * from t_invoice_detail_reading where reading_id = @reading_id)
			begin
				delete from t_tenant_reading_charges where trc_reading_id = @reading_id
				delete from t_tenant_reading where reading_id = @reading_id
				set @data = 'delete from t_tenant_reading where reading_id = ' + convert(varchar(10),@reading_id)
				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'DELETE',@company_code
				select 0 as x
			end
		else
			select 1 as x
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_List]
	@strMode varchar(50),
	@column_code varchar(500),
	@keyword varchar(100),
	@function_id decimal(18,0)
AS
/*
FIXES:
	2024-12-03	Aldrich
	- Added condition to filter only 1000 rows and date created worht 3 years worth of data
*/

declare @ssql nvarchar(4000),@module_name varchar(50),@data_type char(1),@order_by varchar(1000)

	select top 1 @order_by = isnull(order_by,'') from s_module_functions_search_list where function_id = @function_id and column_code = @column_code
	set @order_by = isnull(@order_by,'')

	select top 1 @data_type = data_type from  s_module_functions_search_list where column_code = @column_code
	and function_id = @function_id

	set @ssql = ' select top 1000 t_tenant_reading.reading_id,t_tenant_reading.tenant_code,m_tenant.tenant_name, ' +
		'm_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no, ' +
		' case when ltrim(rtrim(m_tenant.tenant_type)) = ''O'' then bill_to.tenant_name else '''' end as bill_to, ' +
		'convert(varchar(10),t_tenant_reading.date_from,101) as date_from, ' +
		'convert(varchar(10),t_tenant_reading.date_to,101) as date_to,convert(varchar(10),t_tenant_reading.billing_date_from,101) as billing_date_from, ' +
		'convert(varchar(10),t_tenant_reading.billing_date_to,101) as billing_date_to,  ' +
		'case when (select count(*) from t_tenant_reading_charges where isnull(trc_invoice_no,'''') <> '''' and trc_reading_id = t_tenant_reading.reading_id) > 0 then 1 else 0 end as with_invoice ' +
		',date_from1 = t_tenant_reading.date_from,date_to1 = t_tenant_reading.date_to ' +
		'from t_tenant_reading ' +
		'left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code ' +
		'left join m_tenant bill_to on m_tenant.bill_to = bill_to.tenant_code ' 

		if ltrim(rtrim(@column_code)) <> '' 
			begin
				if @data_type = 'S'
					set @ssql = @ssql + ' where ' + @column_code + ' like ''%' + @keyword + '%'''				
				else if @data_type = 'D' and @keyword <> ''
					set @ssql = @ssql + ' where convert(varchar(10),' + @column_code + ')=''' + @keyword + ''''
			end

		if @strMode = 'VIEW_STAT'
			begin
				if ltrim(rtrim(@column_code)) <> '' 
					set @ssql = @ssql + ' and '
				else
					set @ssql = @ssql + ' where '

				set @ssql = @ssql +' (and reading_id not in ( ' +
					'select reading_id from t_invoice_detail_reading where invoice_no not in  ' +
					'(select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = ''V''))  ' +
					' ) '
			end


		if ltrim(rtrim(@column_code)) = '' 
			set @ssql = @ssql + ' and t_tenant_reading.date_created >= DATEADD(YEAR, -3, GETDATE()) order by m_tenant.real_property_code,m_tenant.building_code,m_tenant.unit_no,m_tenant.tenant_name,date_from1 desc'
		else
			if @order_by = ''
				set @ssql = @ssql + ' order by ' + @column_code 
			else
				set @ssql = @ssql + ' order by ' + @order_by
		
	print @ssql
	exec sp_executesql @ssql
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Retrieve]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Retrieve]
	@strMode varchar(50),
	@reading_id decimal(18,0)
AS

if @strMode = 'RETRIEVE'
	begin
		/*if exists (select * from t_invoice_detail_reading where reading_id = @reading_id 
			and invoice_no not in (select invoice_no from t_invoice_header where upper(ltrim(rtrim(isnull(status,'')))) = 'V')
			and exists (select * from t_tenant_reading_charges where trc_reading_id = @reading_id and ltrim(rtrim(isnull(trc_invoice_no,''))) = ''))
			begin
				declare @charge_code char(10)

				declare xxx cursor scroll for				
				select trc_charge_code 
				from t_tenant_reading_charges where trc_reading_id = @reading_id 				
				and trc_reading_id in (select reading_id from t_invoice_detail_reading)
				and ltrim(rtrim(isnull(trc_invoice_no,''))) = ''

				open xxx
				fetch next from xxx into @charge_code
				while @@fetch_status = 0
					begin
						declare @invoice_no varchar(20),@invoice_detail_id decimal(18,0),@invoice_detail_reading_id decimal(18,0)

						set @invoice_no = ''
						set @invoice_detail_id = 0
						set @invoice_detail_reading_id = 0

						select top 1 
							@invoice_no = invoice_no,
							@invoice_detail_id = invoice_detail_id,
							@invoice_detail_reading_id = invoice_detail_reading_id
						from t_invoice_detail_reading where reading_id = @reading_id 						
		
						update t_tenant_reading_charges set 
							trc_invoice_no = @invoice_no,
							trc_invoice_detail_id = @invoice_detail_id,
							trc_invoice_detail_reading_id = @invoice_detail_reading_id
						where trc_reading_id = @reading_id and ltrim(rtrim(isnull(trc_invoice_no,''))) = '' and trc_charge_code = @charge_code

						fetch next from xxx into @charge_code
					end
				close xxx
				deallocate xxx
				
			end*/
		
		select top 1 t_tenant_reading.*,m_tenant.tenant_name,
		case when (select count(*) from t_tenant_reading_charges where isnull(trc_invoice_no,'') <> '' and trc_reading_id = t_tenant_reading.reading_id) >0 then 1 else 0 end as with_invoice 
		from t_tenant_reading
		left join m_tenant on t_tenant_reading.tenant_code = m_tenant.tenant_code
		where reading_id = @reading_id
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_t_TenantReading_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_t_TenantReading_Save]
	@strMode varchar(50),
	@reading_id decimal(18,0),
	@tenant_code varchar(20),
	@reading_date_from datetime,
	@reading_date_to datetime,
	@prev_reading decimal(9,0),
	@current_reading decimal(9,0),
	@remarks varchar(100),
	@billing_date_from datetime,
	@billing_date_to datetime,
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
AS

declare @data nvarchar(4000),@module_name varchar(50)

set @module_name = 'TENANT READING'

if @strMode = 'SAVE'
	begin
		if not exists (select * from t_tenant_reading where reading_id = @reading_id)
			begin
				insert into t_tenant_reading (tenant_code,date_from,date_to,prev_reading,current_reading,remarks,billing_date_from,billing_date_to,created_by,date_created)	
				select @tenant_code,@reading_date_from,@reading_date_to,@prev_reading,@current_reading,@remarks,@billing_date_from,@billing_date_to,@uid,getdate()

				set @data = 'insert into t_tenant_reading (tenant_code,date_from,date_to,prev_reading,current_reading,remarks,billing_date_from,billing_date_to) ' +	
					'select ' + @tenant_code +','+ convert(varchar(10),@reading_date_from,101)+','+convert(varchar(10),@reading_date_to,101)+','+convert(varchar(10),@prev_reading)+','+
					convert(varchar(10),@current_reading)+','+@remarks+','+convert(varchar(10),@billing_date_from,101)+','+convert(varchar(10),@billing_date_to,101)

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'INSERT',@company_code

				select max(reading_id) as reading_id from t_tenant_reading where tenant_code = @tenant_code
			end
		else
			begin
				update t_tenant_reading set 
					tenant_code = @tenant_code,
					date_from = @reading_date_from,
					date_to = @reading_date_to,
					prev_reading = @prev_reading,
					current_reading = @current_reading,
					remarks = @remarks,
					billing_date_from = @billing_date_from,
					billing_date_to = @billing_date_to,
					updated_by = @uid,
					date_updated = getdate()
				where reading_id = @reading_id

				set @data = 'update t_tenant_reading set ' + 
					'tenant_code = ' + @tenant_code+','+
					'date_from = ' + convert(varchar(10),@reading_date_from,101) +','+
					'date_to = ' + convert(varchar(10),@reading_date_to,101) +','+
					'prev_reading =' + convert(varchar(10),@prev_reading) +','+
					'current_reading =' + convert(varchar(10),@current_reading) +','+
					'remarks =' + @remarks+','+
					'billing_date_from =' + convert(varchar(10),@billing_date_from,101) +','+
					'billing_date_to =' + convert(varchar(10),@billing_date_to,101) +
					'where reading_id =' + convert(varchar(10),@reading_id)

				exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,'UPDATE',@company_code

				select top 1 @reading_id as reading_id
			end
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_MassUpdateChargeAmount_Process]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_u_MassUpdateChargeAmount_Process]
	@real_property_code varchar(10),
	@charge_code varchar(10),
	@is_all tinyint,
	@dblOldAmount decimal(18,6),
	@dblNewAmount decimal(18,6),
	@uid varchar(100),
	@company_code varchar(5),
	@ip_addr varchar(20)
	
AS
	declare @ssql nvarchar(4000)
	
	set @ssql = 'update m_unit_charges set charge_amount = ' + convert(varchar(15),@dblNewAmount) + ' '
	set @ssql = @ssql + ' where charge_code = ''' + @charge_code + ''''

	--//added by resalie usi 20180628
	set @ssql = @ssql + ' and charge_code not in (''APT'') '
	--//end added by resalie usi 20180628
	
	if (ISNULL(@real_property_code,'') <> '')
		set @ssql = @ssql + ' and real_property_code = ''' + @real_property_code + ''' '
	
	if isnull(@is_all,0) = 0
		set @ssql = @ssql + ' and charge_amount = ' + convert(varchar(15),@dblOldAmount) + ' '
	
	print @ssql
	exec sp_executesql @ssql
	exec sp_s_EventLog 'CHARGE AMOUNT MASS UPDATE',@uid,@ip_addr,@ssql,'UPDATE',@company_code
	
	set @ssql = ''
	
	set @ssql = 'update m_tenant_charges set charge_amount = ' + convert(varchar(15),@dblNewAmount) + ' '
	set @ssql = @ssql + ' where charge_code = ''' + @charge_code + ''''

	--//added by resalie usi 20180628
	set @ssql = @ssql + ' and charge_code not in (''APT'') '
	--//end added by resalie usi 20180628
	
	if (ISNULL(@real_property_code,'') <> '')
		set @ssql = @ssql + ' and tenant_code in (select tenant_code from m_tenant where ISNULL(terminated,''N'') <> ''Y'' and real_property_code = ''' + @real_property_code + ''') '
	else
		set @ssql = @ssql + ' and tenant_code in (select tenant_code from m_tenant where ISNULL(terminated,''N'') <> ''Y'') '
	
	
	if isnull(@is_all,0) = 0
		set @ssql = @ssql + ' and charge_amount = ' + convert(varchar(15),@dblOldAmount) + ' '
	
	print @ssql
	exec sp_executesql @ssql
	exec sp_s_EventLog 'CHARGE AMOUNT MASS UPDATE',@uid,@ip_addr,@ssql,'UPDATE',@company_code
	
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Alert_List]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO
-- Batch submitted through debugger: SQLQuery1.sql|7|0|C:\Users\ADMINI~1\AppData\Local\Temp\~vsC182.sql


CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Alert_List]

AS
--exec sp_u_Send_Invoice_Alert_List

	select a.invoice_no,tenant_name,
	
	--//updated by resalie usi 20180430, requested by j. maximo
	--email_add,
	email_add = case when isnull(c.email_add,'') = '' then 'rms_admin@tanholdings.com' else email_add end,
	--email_add = 'resalie_usi@tanholdings.com',
	
	report_title = upper(ltrim(rtrim(isnull(tenant_name,'')))) + ' - ' + upper(d.real_property_name) + ' - ' + upper(dbo.fn_GetMonthName(month(invoice_date))) + ' ' + convert(varchar(4),year(invoice_date)) + ' BILLING ',
	client_code,pdf_file_name = replace(replace(upper(ltrim(rtrim(isnull(tenant_name,'')))),'/',' '),'"','') + '-INVOICE-' + upper(a.invoice_no)
	from u_invoice_alert a
	left join t_invoice_header b on a.invoice_no = b.invoice_no
	left join m_tenant c on b.client_code = c.tenant_code
	left join m_real_property d on b.real_property_code = d.real_property_code
	where isnull(tagged,0) = 1 and isnull(email_sent,0) = 0 
	
	--//updated by resalie usi 20180430, requested by j. maximo
	--and isnull(c.email_add,'') <> ''
	--and isnull(c.email_add,'') = ''
	
	and ltrim(rtrim(isnull(terminated,'N')))  <> 'Y'
	--and datediff(month,invoice_date,getdate()) <=1
	and datediff(month,invoice_date,getdate()) = 0
	--and isnull(c.email_add,'') = 'aldrich_delossantos@tanholdings.com'
	--and isnull(c.email_add,'') = 'aldrich.bhurn@gmail.com'
	--and c.tenant_code='LNH 24-05-0045'
	order by a.invoice_no
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Alert_List_Sent]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Alert_List_Sent]
	@date_email_sent_fr datetime, 
	@date_email_sent_to datetime
AS

	select c.sap_code,a.invoice_no,tenant_name,email_addr = isnull(a.email_addr,''),
	real_property_name = upper(d.real_property_name),
	date_email_sent = isnull(convert(varchar(25),date_email_sent),''),b.invoice_date
	from u_invoice_alert a
	left join t_invoice_header b on a.invoice_no = b.invoice_no
	left join m_tenant c on b.client_code = c.tenant_code
	left join m_real_property d on b.real_property_code = d.real_property_code
	where isnull(tagged,0) = 1 
	and convert(datetime,convert(varchar(12),date_email_sent)) >= dateadd(day,-7,convert(datetime,convert(varchar(12),getdate())))
	and convert(datetime,convert(varchar(12),date_email_sent))  < dateadd(day,1,convert(datetime,convert(varchar(12),getdate())))
		--between convert(datetime,convert(varchar(12),@date_email_sent_fr)) 
		--and convert(datetime,convert(varchar(12),@date_email_sent_to))
	order by isnull(email_sent,0) desc,real_property_name,a.invoice_no
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Alert_Update]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Alert_Update]
	@invoice_no varchar(20),
	@email_addr varchar(200)
AS

	update u_invoice_alert 
		set email_sent = case when isnull(@email_addr,'') = 'rms_admin@tanholdings.com' then 0 else 1 end, 
		email_addr = case when isnull(@email_addr,'') = 'rms_admin@tanholdings.com' then '' else @email_addr end,

		--set email_sent = 1,
		--email_addr = case when isnull(@email_addr,'') = 'rms_admin@tanholdings.com' then 'rms_admin@tanholdings.com' else @email_addr end,

	date_email_sent = getdate()
	where invoice_no = @invoice_no
	
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Save]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@uid varchar(50),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50),@tagged tinyint

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'SEND INVOICE ALERT TAGGING'
set @tagged = 0
	
	if @strMode = 'TAG'	
		set @tagged = 1

	if exists (select top 1 invoice_no from u_invoice_alert where upper(ltrim(rtrim(invoice_no))) = @invoice_no)
		begin
			update u_invoice_alert set tagged = @tagged , updated_by = @uid,date_updated = getdate()
			where upper(ltrim(rtrim(invoice_no))) = @invoice_no

			set @data = 'update u_invoice_alert set tagged = ' + convert(varchar(1), @tagged)  + ' , updated_by =' + @uid+',' +
				'where upper(ltrim(rtrim(invoice_no))) =' + @invoice_no
		end
	else
		begin
			insert into u_invoice_alert (invoice_no,tagged,updated_by)
			select @invoice_no,@tagged,@uid

			set @data = 'insert into u_invoice_alert (invoice_no,tagged,updated_by) ' +
				' select ' + convert(varchar(20),@invoice_no) + ',' + convert(varchar(1), @tagged) + ',' + @uid

		end

	if isnull(@tagged,0) = 0
		update u_invoice_alert set email_sent = 0 , email_addr = '',date_email_sent = null
		where upper(ltrim(rtrim(invoice_no))) = @invoice_no			

	exec sp_s_EventLog @module_name,@uid,@ip_addr,@data,@strMode,@s_company_code
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Search]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@tagged tinyint,
	@email_sent tinyint,
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @sap_text varchar(50), @ssql nvarchar(4000),@charge_code varchar(50)
declare @intStatus1 tinyint,@intStatus2 tinyint

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))

if @tagged = 0
	begin
		set @intStatus1 = 0
		set @intStatus2 = 1
	end
else if @tagged = 1
	begin
		set @intStatus1 = 1
		set @intStatus2 = 1
	end
else if @tagged = 2
	begin
		set @intStatus1 = 0
		set @intStatus2 = 0
	end

if @strMode = 'SEARCH'
	begin
		--delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_send_invoice]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_send_invoice]

		--\\ create temp table

		set @ssql = ' invoice_no varchar(20),invoice_date datetime,client_code varchar(20),total_amount decimal(18,6),client_name varchar(200),unit_no varchar(20),tagged tinyint,
				email_sent tinyint,email_addr varchar(100),date_email_sent datetime'
	
		set @ssql = 'create table z_tmp_send_invoice (' + @ssql + ')'
		--print @ssql
		exec sp_executesql @ssql
		set @ssql = ''

		--\\

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				insert into z_tmp_send_invoice (invoice_no,invoice_date ,client_code,total_amount,client_name,unit_no,tagged,email_sent,email_addr,date_email_sent)	
				select t_invoice_header.invoice_no,invoice_date,client_code,isnull(t_invoice_detail.total_charge_amount,0),
					m_tenant.tenant_name,
					ltrim(rtrim(m_tenant.real_property_code)) + '/' + ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)),
					isnull(tagged,0),isnull(email_sent,0),isnull(email_addr,''),isnull(date_email_sent,'')
				from t_invoice_header 
				left join
					(
						select invoice_no,total_charge_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail group by invoice_no
					)  
					t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				left join u_invoice_alert on t_invoice_header.invoice_no = u_invoice_alert.invoice_no
				where convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
					and isnull(tagged,0) in (@intStatus1,@intStatus2)
					and isnull(email_sent,0) = @email_sent
			end
		else
			begin
				insert into z_tmp_send_invoice (invoice_no,invoice_date ,client_code,total_amount,client_name,unit_no,tagged,email_sent,email_addr,date_email_sent)	
				select t_invoice_header.invoice_no,invoice_date,client_code,isnull(t_invoice_detail.total_charge_amount,0),
					m_tenant.tenant_name,
					ltrim(rtrim(m_tenant.real_property_code)) + '/' + ltrim(rtrim(m_tenant.building_code)) + '/' +  ltrim(rtrim(m_tenant.unit_no)),
					isnull(tagged,0),isnull(email_sent,0),isnull(email_addr,''),isnull(date_email_sent,'')
				from t_invoice_header 
				left join
					(
						select invoice_no,total_charge_amount = sum(isnull(total_charge_amount,0)) from t_invoice_detail group by invoice_no
					)  
					t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
				left join u_invoice_alert on t_invoice_header.invoice_no = u_invoice_alert.invoice_no
				where convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
					and isnull(tagged,0) in (@intStatus1,@intStatus2)
					and isnull(email_sent,0) = @email_sent
			end
		
		if @sort_by = 'INVOICE NO.' or @sort_by = ''
			begin
				select * from z_tmp_send_invoice order by invoice_no
			end
		else if @sort_by = 'INVOICE DATE'
			begin
				select * from z_tmp_send_invoice order by invoice_date,invoice_no
			end
		else if @sort_by = 'CLIENT'
			begin
				select * from z_tmp_send_invoice order by client_name,invoice_no
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select * from z_tmp_send_invoice order by invoice_no, unit_no
			end
		else if @sort_by = 'STATUS'
			begin
				select * from z_tmp_send_invoice order by tagged desc, invoice_no
			end

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Send_Invoice_Search_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_u_Send_Invoice_Search_View]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'INVOICE ALERT TAGGING'

if @strMode = 'SEARCH'
	begin
		--delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_send_invoice_search]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_send_invoice_search]

		create table z_tmp_send_invoice_search (invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20),charge_code varchar(10),total_amount decimal(18,2))

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				insert into z_tmp_send_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
			end
		else
			begin
				insert into z_tmp_send_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) <> 'V'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unpost_Invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Unpost_Invoice]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@invoice_date_from datetime,
	@invoice_date_to datetime,
	@real_property_code varchar(10),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @office_unit_type varchar(10), @apt_rental_charge varchar(5), @off_rental_charge varchar(5), @whs_rental_charge varchar(5)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'UNPOST INVOICE'

select top 1 @office_unit_type =  isnull(off_unit_type,''), @apt_rental_charge = isnull(apt_rental_charge,''), @off_rental_charge = isnull(off_rental_charge,''),
	@whs_rental_charge = isnull(whs_rental_charge,'') from s_settings

if @strMode = 'SEARCH'
	begin
		delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		select t_invoice_header.*,m_tenant.tenant_name as client_name
		from t_invoice_header 
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		where upper(ltrim(rtrim(t_invoice_header.real_property_code))) like @real_property_code + '%'
			and convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@invoice_date_from,101)) 
			and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@invoice_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = 'P'
	end

if @strMode = 'DELETE'
	begin
		delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)
	end

if @strMode = 'UNPOST'
	begin
		update t_invoice_header set status = '' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

		set @data = 'update t_invoice_header set status = '''' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_invoice_header.invoice_no))) =' + upper(ltrim(rtrim(@invoice_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'UNPOST',@s_company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unpost_Invoice_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_u_Unpost_Invoice_Search]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000),@charge_code varchar(50)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'UNPOST INVOICE'

if @strMode = 'SEARCH'
	begin
		delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_unpost_invoice]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_unpost_invoice]

		--\\ create temp table

		set @ssql = ' invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20) '

		declare xxx cursor scroll for
		select charge_code from m_charges order by charge_desc
		
		open xxx
		fetch next from xxx into @charge_code
		while @@fetch_status = 0
			begin
				set @charge_code = '[' + @charge_code + '] decimal(18,2)'
				if @ssql = ''
					set @ssql = @ssql + @charge_code 
				else
					set @ssql = @ssql + ',' +  @charge_code 
		
				fetch next from xxx into @charge_code
			end
		close xxx
		deallocate xxx
	
		set @ssql = 'create table z_tmp_unpost_invoice (' + @ssql + ')'
		--print @ssql
		exec sp_executesql @ssql
		set @ssql = ''

		--\\

		declare @tmp_invoice_no varchar(20),@tmp_tenant_code varchar(20),@tmp_charge_code varchar(20),@tmp_total_charge_amount decimal(18,2)

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'P'
			end
		else
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'P'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end

		open yyy
		fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
		while @@fetch_status = 0
			begin
				if not exists (select * from z_tmp_unpost_invoice where invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code)
					begin
						insert into z_tmp_unpost_invoice (invoice_no,invoice_date ,client_code ,tenant_code  )	
						select top 1 t_invoice_header.invoice_no,invoice_date,client_code,t_invoice_detail.tenant_code
						from t_invoice_header 
						left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
						where t_invoice_header.invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code
					end
		
				set @ssql = 'update z_tmp_unpost_invoice set [' + @tmp_charge_code + ']= ' + convert(varchar(10),@tmp_total_charge_amount)
				set @ssql = @ssql + ' where invoice_no =  ''' + @tmp_invoice_no + ''' and tenant_code = ''' + @tmp_tenant_code + ''''
				exec sp_executesql @ssql
				
				fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
			end
		close yyy
		deallocate yyy
		
		if @sort_by = 'INVOICE NO.' or @sort_by = ''
			begin
				select a.invoice_no,invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unpost_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_no
			end
		else if @sort_by = 'INVOICE DATE'
			begin
				select a.invoice_no,a.invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unpost_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_date,a.invoice_no
			end
		else if @sort_by = 'TENANT'
			begin
				select a.invoice_no,a.invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unpost_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by tenant.tenant_name,a.invoice_no
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select a.invoice_no,a.invoice_date,client_code,m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unpost_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by a.invoice_no, ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no))
			end

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unpost_Invoice_Search_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Unpost_Invoice_Search_View]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'INVOICE SAP UPLOADING'

if @strMode = 'SEARCH'
	begin
		delete from u_unpost_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_unpost_invoice_search]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_unpost_invoice_search]

		create table z_tmp_unpost_invoice_search (invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20),charge_code varchar(10),total_amount decimal(18,2))

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				insert into z_tmp_unpost_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'P'
			end
		else
			begin
				insert into z_tmp_unpost_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'P'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unvoid_Invoice]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO



CREATE PROCEDURE [dbo].[sp_u_Unvoid_Invoice]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@invoice_date_from datetime,
	@invoice_date_to datetime,
	@real_property_code varchar(10),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @office_unit_type varchar(10), @apt_rental_charge varchar(5), @off_rental_charge varchar(5), @whs_rental_charge varchar(5)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'UNVOID INVOICE'

select top 1 @office_unit_type =  isnull(off_unit_type,''), @apt_rental_charge = isnull(apt_rental_charge,''), @off_rental_charge = isnull(off_rental_charge,''),
	@whs_rental_charge = isnull(whs_rental_charge,'') from s_settings

if @strMode = 'SEARCH'
	begin
		delete from u_unvoid_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		select t_invoice_header.*,m_tenant.tenant_name as client_name
		from t_invoice_header 
		left join m_tenant on t_invoice_header.client_code = m_tenant.tenant_code
		where upper(ltrim(rtrim(t_invoice_header.real_property_code))) like @real_property_code + '%'
			and convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@invoice_date_from,101)) 
			and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@invoice_date_to,101))
			and upper(ltrim(rtrim(isnull(status,'')))) = 'V'
	end

if @strMode = 'DELETE'
	begin
		delete from u_unvoid_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)
	end

if @strMode = 'UNVOID'
	begin
		update t_invoice_header set status = '' , updated_by = @uploaded_by,date_updated = getdate()
		where upper(ltrim(rtrim(t_invoice_header.invoice_no))) = upper(ltrim(rtrim(@invoice_no)))

		set @data = 'update t_invoice_header set status = '''' , updated_by =' + @uploaded_by+',date_updated =' + convert(varchar(20), getdate()) +
			'where upper(ltrim(rtrim(t_invoice_header.invoice_no))) =' + upper(ltrim(rtrim(@invoice_no)))
		exec sp_s_EventLog @module_name,@uploaded_by,@ip_addr,@data,'UNVOID',@s_company_code
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unvoid_Invoice_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Unvoid_Invoice_Search]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000),@charge_code varchar(50)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'UNVOID INVOICE'

if @strMode = 'SEARCH'
	begin
		delete from u_unvoid_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_unvoid_invoice]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_unvoid_invoice]

		--\\ create temp table

		set @ssql = ' invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20) '

		declare xxx cursor scroll for
		select charge_code from m_charges order by charge_desc
		
		open xxx
		fetch next from xxx into @charge_code
		while @@fetch_status = 0
			begin
				set @charge_code = '[' + @charge_code + '] decimal(18,2)'
				if @ssql = ''
					set @ssql = @ssql + @charge_code 
				else
					set @ssql = @ssql + ',' +  @charge_code 
		
				fetch next from xxx into @charge_code
			end
		close xxx
		deallocate xxx
	
		set @ssql = 'create table z_tmp_unvoid_invoice (' + @ssql + ')'
		--print @ssql
		exec sp_executesql @ssql
		set @ssql = ''

		--\\

		declare @tmp_invoice_no varchar(20),@tmp_tenant_code varchar(20),@tmp_charge_code varchar(20),@tmp_total_charge_amount decimal(18,2)

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'V'
			end
		else
			begin
				declare yyy cursor scroll for
				select t_invoice_header.invoice_no,t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),t_invoice_header.invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'V'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end

		open yyy
		fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
		while @@fetch_status = 0
			begin
				if not exists (select * from z_tmp_unvoid_invoice where invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code)
					begin
						insert into z_tmp_unvoid_invoice (invoice_no,invoice_date ,client_code ,tenant_code  )	
						select top 1 t_invoice_header.invoice_no,t_invoice_header.invoice_date,client_code,t_invoice_detail.tenant_code
						from t_invoice_header 
						left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
						where t_invoice_header.invoice_no = @tmp_invoice_no and tenant_code = @tmp_tenant_code
					end
		
				set @ssql = 'update z_tmp_unvoid_invoice set [' + @tmp_charge_code + ']= ' + convert(varchar(10),@tmp_total_charge_amount)
				set @ssql = @ssql + ' where invoice_no =  ''' + @tmp_invoice_no + ''' and tenant_code = ''' + @tmp_tenant_code + ''''
				exec sp_executesql @ssql
				
				fetch next from yyy into @tmp_invoice_no,@tmp_tenant_code,@tmp_charge_code,@tmp_total_charge_amount
			end
		close yyy
		deallocate yyy
		
		if @sort_by = 'INVOICE NO.' or @sort_by = ''
			begin
				select --invoice_no,invoice_date,client_code,
				m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unvoid_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by invoice_no
			end
		else if @sort_by = 'INVOICE DATE'
			begin
				select --invoice_no,invoice_date,client_code,
				m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unvoid_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by invoice_date,invoice_no
			end
		else if @sort_by = 'TENANT'
			begin
				select --invoice_no,invoice_date,client_code,
				m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unvoid_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by tenant.tenant_name,invoice_no
			end
		else if @sort_by = 'UNIT NO.'
			begin
				select --invoice_no,invoice_date,client_code,
				m_tenant.tenant_name as client_name,	
				tenant.tenant_name as tenant_name,tenant.tenant_code,	
				ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no)) as unit_no,
				a.*
				from z_tmp_unvoid_invoice a 
				left join m_tenant on a.client_code = m_tenant.tenant_code
				left join m_tenant tenant on a.tenant_code = tenant.tenant_code
				order by invoice_no, ltrim(rtrim(tenant.real_property_code)) + '/' + ltrim(rtrim(tenant.building_code)) + '/' +  ltrim(rtrim(tenant.unit_no))
			end

	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_Unvoid_Invoice_Search_View]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_Unvoid_Invoice_Search_View]
	@strMode varchar(20),
	@invoice_no varchar(20),
	@date_from datetime,
	@date_to datetime,
	@real_property_code varchar(10),
	@sort_by varchar(50),
	@date_uploaded datetime,
	@uploaded_by varchar(100),
	@s_company_code varchar(5),
	@ip_addr varchar(20)

AS
declare @data nvarchar(4000),@module_name varchar(50)
declare @sap_text varchar(50), @ssql nvarchar(4000)

set @invoice_no = upper(ltrim(rtrim(isnull(@invoice_no,''))))
set @module_name = 'UNVOID INVOICE'

if @strMode = 'SEARCH'
	begin
		delete from u_unvoid_invoice where uploaded_by = @uploaded_by and convert(varchar(20),date_uploaded,112) + convert(varchar(20),date_uploaded,114) = convert(varchar(20),@date_uploaded,112) + convert(varchar(20),@date_uploaded,114)

		if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[z_tmp_unvoid_invoice_search]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
			drop table [dbo].[z_tmp_unvoid_invoice_search]

		create table z_tmp_unvoid_invoice_search (invoice_no varchar(20),invoice_date datetime,client_code varchar(20),tenant_code varchar(20),charge_code varchar(10),total_amount decimal(18,2))

		if ltrim(rtrim(isnull(@real_property_code,''))) = ''
			begin
				insert into z_tmp_unvoid_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'V'
			end
		else
			begin
				insert into z_tmp_unvoid_invoice_search
				select t_invoice_header.invoice_no,t_invoice_header.invoice_date,t_invoice_header.client_code,
				t_invoice_detail.tenant_code,t_invoice_detail.charge_code,isnull(t_invoice_detail.total_charge_amount,0)
				from t_invoice_header 
				left join  t_invoice_detail on  t_invoice_header.invoice_no = t_invoice_detail.invoice_no
				where convert(datetime,convert(varchar(12),invoice_date,101)) >= convert(datetime,convert(varchar(12),@date_from,101)) 
					and convert(datetime,convert(varchar(12),invoice_date,101)) <= convert(datetime,convert(varchar(12),@date_to,101))
					and upper(ltrim(rtrim(isnull(status,'')))) = 'V'
					and upper(ltrim(rtrim(t_invoice_header.real_property_code))) = @real_property_code 
			end
		
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_UploadAging_Search]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_u_UploadAging_Search]
	@date_as_of datetime,
	@sort_by varchar(30),
	@search_value varchar(100)
AS

if @sort_by = 'TENANT'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_tenant_name like '%' + @search_value + '%'
		order by wta_tenant_name
	end

else if @sort_by = 'REAL PROPERTY'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_real_property_name like '%' + @search_value + '%'
		order by wta_real_property_name
	end

else if @sort_by = 'SAP ACCOUNT CODE'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_sap_code like '%' + @search_value + '%'
		order by wta_sap_code
	end

else if @sort_by = 'NOTICE NO.'
	begin
		select * from w_tenant_aging_header
		left join w_tenant_aging_detail on wth_hdr_id = wta_hdr_id
		where wth_as_of = @date_as_of
		and wta_notice_no like '%' + @search_value + '%'
		order by wta_notice_no
	end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_UploadAgingDetail_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[sp_u_UploadAgingDetail_Save]
	@wta_hdr_id decimal(18,0),
	@wta_sap_code varchar(20),
	@wta_tenant_name varchar(200),
	@wta_real_property_name varchar(200),
	@wta_total_balance decimal(18,2),
	@wta_curr_balance decimal(18,2),
	@wta_total_overdue decimal(18,2),
	@wta_aging_1_30 decimal(18,2),
	@wta_aging_31_60 decimal(18,2),
	@wta_aging_61_90 decimal(18,2),
	@wta_aging_91_120 decimal(18,2),
	@wta_aging_121_150 decimal(18,2),
	@wta_aging_over_151 decimal(18,2),
	@wta_write_off decimal(18,2),
	@wta_remarks varchar(500),
	@wta_notice_no varchar(100)
AS

	insert into w_tenant_aging_detail (wta_hdr_id,wta_sap_code,wta_tenant_name,wta_real_property_name,wta_total_balance,wta_curr_balance,
			wta_total_overdue,wta_aging_1_30,wta_aging_31_60,wta_aging_61_90,wta_aging_91_120,wta_aging_121_150,
			wta_aging_over_151,wta_write_off,wta_remarks,wta_notice_no)
	select top 1 @wta_hdr_id,@wta_sap_code,@wta_tenant_name,@wta_real_property_name,@wta_total_balance,@wta_curr_balance,
			@wta_total_overdue,@wta_aging_1_30,@wta_aging_31_60,@wta_aging_61_90,@wta_aging_91_120,@wta_aging_121_150,
			@wta_aging_over_151,@wta_write_off,@wta_remarks,@wta_notice_no
GO
/****** Object:  StoredProcedure [dbo].[sp_u_UploadAgingHeader_CheckIfAlertSent]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_UploadAgingHeader_CheckIfAlertSent]
	@wth_as_of datetime
AS
	if not exists (select top 1 * from w_tenant_aging_header where wth_as_of = @wth_as_of)
		begin
			select 0 as x,'' as msg
		end
	else
		begin
			if exists (select top 1 * from w_tenant_aging_header where wth_as_of = @wth_as_of and (isnull(wth_email_sent,0)=1 or isnull(wth_email_sent_final,0)=1))
				select 1 as x,'E-mail alert already sent on the specified As Of date. Overwrite not allowed.' as msg
		end
GO
/****** Object:  StoredProcedure [dbo].[sp_u_UploadAgingHeader_Save]    Script Date: 8/6/2025 3:30:13 PM ******/
SET ANSI_NULLS OFF
GO
SET QUOTED_IDENTIFIER OFF
GO


CREATE PROCEDURE [dbo].[sp_u_UploadAgingHeader_Save]
	@wth_hdr_id decimal(18,0),
	@wth_filename varchar(100),
	@wth_as_of datetime,
	@uid varchar(100),
	@company_code char(5),
	@ip_addr varchar(20)
AS
	if exists (select top 1 * from w_tenant_aging_header where wth_as_of = @wth_as_of and isnull(wth_email_sent,0)=0 and isnull(wth_email_sent_final,0)=0)
		begin
			delete from w_tenant_aging_detail where wta_hdr_id in
				 (select wta_hdr_id from w_tenant_aging_header where wth_as_of = @wth_as_of)
				 
			delete from w_tenant_aging_header where wth_as_of = @wth_as_of
		end
		
	if not exists (select top 1 * from w_tenant_aging_header where wth_as_of = @wth_as_of)
		begin
			insert into w_tenant_aging_header (wth_filename,wth_as_of,wth_date_uploaded,wth_uploaded_by)
			select top 1 @wth_filename,@wth_as_of,getdate(),@uid
	
			set @wth_hdr_id = @@identity
		end
	else
		begin
			update w_tenant_aging_header set
				wth_filename = @wth_filename,
				wth_uploaded_by = @uid
			where wth_as_of = @wth_as_of

			select top 1 @wth_hdr_id = wth_hdr_id from w_tenant_aging_header where wth_as_of = @wth_as_of

			delete from w_tenant_aging_detail where wta_hdr_id = @wth_hdr_id
			and wta_hdr_id not in 
			(
			select wth_hdr_id from w_tenant_aging_header where wth_as_of = @wth_as_of and (isnull(wth_email_sent,0)=1 or isnull(wth_email_sent_final,0)=1)
			)
		end

	select wth_hdr_id = @wth_hdr_id
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'This is the table used to add cost center of affiliate real property, to be used in SAP PRN file.' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'm_tenant_cost_center'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_DiagramPane1', @value=N'[0E232FF0-B466-11cf-A24F-00AA00A3EFFF, 1.00]
Begin DesignProperties = 
   Begin PaneConfigurations = 
      Begin PaneConfiguration = 0
         NumPanes = 4
         Configuration = "(H (1[40] 4[20] 2[20] 3) )"
      End
      Begin PaneConfiguration = 1
         NumPanes = 3
         Configuration = "(H (1 [50] 4 [25] 3))"
      End
      Begin PaneConfiguration = 2
         NumPanes = 3
         Configuration = "(H (1 [50] 2 [25] 3))"
      End
      Begin PaneConfiguration = 3
         NumPanes = 3
         Configuration = "(H (4 [30] 2 [40] 3))"
      End
      Begin PaneConfiguration = 4
         NumPanes = 2
         Configuration = "(H (1 [56] 3))"
      End
      Begin PaneConfiguration = 5
         NumPanes = 2
         Configuration = "(H (2 [66] 3))"
      End
      Begin PaneConfiguration = 6
         NumPanes = 2
         Configuration = "(H (4 [50] 3))"
      End
      Begin PaneConfiguration = 7
         NumPanes = 1
         Configuration = "(V (3))"
      End
      Begin PaneConfiguration = 8
         NumPanes = 3
         Configuration = "(H (1[56] 4[18] 2) )"
      End
      Begin PaneConfiguration = 9
         NumPanes = 2
         Configuration = "(H (1 [75] 4))"
      End
      Begin PaneConfiguration = 10
         NumPanes = 2
         Configuration = "(H (1[66] 2) )"
      End
      Begin PaneConfiguration = 11
         NumPanes = 2
         Configuration = "(H (4 [60] 2))"
      End
      Begin PaneConfiguration = 12
         NumPanes = 1
         Configuration = "(H (1) )"
      End
      Begin PaneConfiguration = 13
         NumPanes = 1
         Configuration = "(V (4))"
      End
      Begin PaneConfiguration = 14
         NumPanes = 1
         Configuration = "(V (2))"
      End
      ActivePaneConfig = 0
   End
   Begin DiagramPane = 
      Begin Origin = 
         Top = 0
         Left = 0
      End
      Begin Tables = 
         Begin Table = "m_tenant_charges"
            Begin Extent = 
               Top = 6
               Left = 38
               Bottom = 118
               Right = 210
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "m_tenant"
            Begin Extent = 
               Top = 6
               Left = 248
               Bottom = 135
               Right = 469
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "m_real_property"
            Begin Extent = 
               Top = 6
               Left = 507
               Bottom = 135
               Right = 755
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "m_charges"
            Begin Extent = 
               Top = 6
               Left = 793
               Bottom = 135
               Right = 965
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "s_company"
            Begin Extent = 
               Top = 138
               Left = 38
               Bottom = 267
               Right = 263
            End
            DisplayFlags = 280
            TopColumn = 0
         End
      End
   End
   Begin SQLPane = 
   End
   Begin DataPane = 
      Begin ParameterDefaults = ""
      End
   End
   Begin CriteriaPane = 
      Begin ColumnWidths = 11
         Column = 1440
         Alias = 900
         Table = 1170
         Output = 720
         Append = 1400
         NewValue = 1170
         SortType = 1350
         SortOrder = 1410
         GroupBy = 1350
         Filter = 1350
         Or = 1350
         Or = 1350
' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'vw_TenantChargesListing_Active'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_DiagramPane2', @value=N'
         Or = 1350
      End
   End
End
' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'vw_TenantChargesListing_Active'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_DiagramPaneCount', @value=2 , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'vw_TenantChargesListing_Active'
GO
