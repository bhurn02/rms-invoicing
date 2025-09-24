USE [RMS]
GO
/****** Object:  View [dbo].[vw_TenantChargesListing_Active]    Script Date: 8/28/2025 3:59:58 PM ******/
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
/****** Object:  View [dbo].[vw_TenantReadingHistory]    Script Date: 8/28/2025 3:59:58 PM ******/
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
         Or = 1350' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'vw_TenantChargesListing_Active'
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
