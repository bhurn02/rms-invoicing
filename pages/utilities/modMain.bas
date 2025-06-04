Attribute VB_Name = "modMain"

Public Const PRJNAME = "RMS System Email Alert"
Public Const FDATE = "mm/dd/yyyy"
Public Const TIMEFORMAT = "HH:MM"
Public Const FTIME = "hh:mm AMPM"
Public Const FDATETIME = "mm/dd/yyyy hh:mm:ss AMPM"

Public strTLSCpy, strTLSServer, strTLSDB, strTLSUser, strTLSPwd As String
Public strHR2Cpy, strHR2Server, strHR2DB, strHR2User, strHR2Pwd As String

Public cnTLS As ADODB.Connection
Public cnHR2 As ADODB.Connection
Public strCnTLS, strCnHR2 As String
Public strAutoMode, strPath, strLogPath, strAlertType, strReportPath, strPDFPath As String
Public intPaymentDueDays As Integer
Public dblMaxHrs

Private oExcel As Excel.Application
Private oWrkBook As Excel.Workbook
Private oWrkSheet As Excel.Worksheet
Dim nRow As Long, nCol As Integer, nSheet As Integer

Dim rsEmailSettings As ADODB.Recordset
Dim sEmailAttach, sEmailFile
Dim sEMailFrom, sEmailTo, sEmailCC, SEmailBcc, sEmailSubject, sEmailBody, sEmailAlert
Dim sSubject1, sSubject2
Dim dtExport

Dim dtDay1, dtDay7 As Date

Public pScrFSO As New Scripting.FileSystemObject
Public pTxtStr As TextStream

Sub Main()
'On Error GoTo ErrHandler
On Error Resume Next

    Call SetCn
    
    If strAutoMode = "Y" Then
        Call ProcEmail(DateAdd("d", -1, Date), DateAdd("d", -1, Date))
    Else
        frmDtRange.Show 1
    End If
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Sub SetCn()
On Error GoTo ErrHandler
    Call LoadIni
    
     If strTLSServer = "" Or strAutoMode = "" Then
        MsgBox "Connection Failed. Please configure your connection settings.", vbCritical + vbOKOnly, "Server Connection"
        End
    End If
    
    strCnTLS = "Driver={SQL Server};Server=" & strTLSServer & ";Database=" & strTLSDB & ";Uid=" & strTLSUser & ";Pwd=" & strTLSPwd & ""
    Set cnTLS = New ADODB.Connection
    
    With cnTLS
        .ConnectionString = strCnTLS
        .CursorLocation = adUseClient
        .ConnectionTimeout = 0
        .CommandTimeout = 0
        .Open
    End With
    
Exit Sub
ErrHandler:
    If Err.Number <> 0 Then
        MsgBox "Connection Failed. Please configure your connection settings.", vbCritical + vbOKOnly, "Server Connection"
        End
    End If
End Sub

Public Sub LoadIni()
'On Error GoTo ErrHandler
On Error Resume Next
    Dim strTxtLine As String
    Dim strTxtLineArr
    
    Open App.Path & "\RMS.INI" For Input As #1
    Do While Not EOF(1)
        Line Input #1, strTxtLine
        strTxtLineArr = Split(strTxtLine, ";")
        If UCase(strTxtLineArr(0)) = "CPY" Then
            strTLSCpy = strTxtLineArr(1)
            strTLSServer = strTxtLineArr(2)
            strTLSDB = strTxtLineArr(3)
            strTLSUser = strTxtLineArr(4)
            strTLSPwd = strTxtLineArr(5)
        ElseIf UCase(strTxtLineArr(0)) = "AUTO (Y/N)" Then
            strAutoMode = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "PATH" Then
            strPath = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "LOG" Then
            strLogPath = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "REPORTS" Then
            strReportPath = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "PDF" Then
            strPDFPath = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "ALERT" Then
            strAlertType = strAlertType & strTxtLineArr(1) & ";"
        ElseIf UCase(strTxtLineArr(0)) = "PAYMENT_DUE_DAYS" Then
            intPaymentDueDays = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "TO" Then
            sEmailTo = sEmailTo & IIf(sEmailTo = "", "", ";") & strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "FROM" Then
            sEMailFrom = sEMailFrom & IIf(sEMailFrom = "", "", ";") & strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "CC" Then
            sEmailCC = sEmailCC & IIf(sEmailCC = "", "", ";") & strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "BCC" Then
            SEmailBcc = SEmailBcc & IIf(SEmailBcc = "", "", ";") & strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "SUBJECT1" Then
            sSubject1 = strTxtLineArr(1)
        ElseIf UCase(strTxtLineArr(0)) = "SUBJECT2" Then
            sSubject2 = strTxtLineArr(1)
        End If
    Loop
    Close #1
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Sub GetCurrWeek(dtFr)
    Dim dtCurr As Date
    
    dtCurr = dtFr
    
    Select Case Weekday(dtCurr)
        Case 1
            dtDay1 = dtCurr
            dtDay7 = DateAdd("d", 6, dtCurr)
        Case 2
            dtDay1 = DateAdd("d", -1, dtCurr)
            dtDay7 = DateAdd("d", 5, dtCurr)
        Case 3
            dtDay1 = DateAdd("d", -2, dtCurr)
            dtDay7 = DateAdd("d", 4, dtCurr)
        Case 4
            dtDay1 = DateAdd("d", -3, dtCurr)
            dtDay7 = DateAdd("d", 3, dtCurr)
        Case 5
            dtDay1 = DateAdd("d", -4, dtCurr)
            dtDay7 = DateAdd("d", 2, dtCurr)
        Case 6
            dtDay1 = DateAdd("d", -5, dtCurr)
            dtDay7 = DateAdd("d", 1, dtCurr)
        Case 7
            dtDay1 = DateAdd("d", -6, dtCurr)
            dtDay7 = dtCurr
    End Select
End Sub

Public Sub ProcEmail(dtFr, dtTo)
'On Error GoTo ErrHandler
On Error Resume Next
    Dim rsTimeLog As ADODB.Recordset
    Dim rsTLSSummary As ADODB.Recordset
    Dim strDeptCode, sSQLTimeLog, varInf
    Dim iCtr As Integer
    Dim strAlertTypeArr
    
    strAlertTypeArr = Split(strAlertType, ";")
    iCtr = 0
    
    While iCtr <> UBound(strAlertTypeArr)
        Select Case strAlertTypeArr(iCtr)
            
            Case "RENEWAL LIST"
                strDeptCode = ""
                Set rsTimeLog = New ADODB.Recordset
                If rsTimeLog.State = 1 Then rsTimeLog.Close
                sSQLTimeLog = "exec sp_rpt_TenantForRenewal '" & Format(dtFr, FDATE) & "','" & Format(dtTo, FDATE) & "'"
                rsTimeLog.Open sSQLTimeLog, cnTLS, adOpenKeyset, adLockOptimistic
                If rsTimeLog.EOF = False Then
                    Call CreateExcelFile
                    nRow = 2
                    oWrkSheet.Name = Left("TENANTS FOR RENEWAL AS OF " & Replace(Format(dtFr, FDATE), "/", ""), 31)
                    While Not rsTimeLog.EOF
                            With oWrkSheet
                                .Cells(nRow, 1) = Trim(UCase(rsTimeLog!real_property_code & ""))
                                .Cells(nRow, 2) = Trim(UCase(rsTimeLog!building_code & ""))
                                .Cells(nRow, 3) = Trim(UCase(rsTimeLog!unit_no & ""))
                                .Cells(nRow, 4) = Trim(UCase(rsTimeLog!tenant_name & ""))
                                .Cells(nRow, 5) = Trim(UCase(rsTimeLog!tenant_code & ""))
                                .Cells(nRow, 6) = Trim(UCase(rsTimeLog!sap_code & ""))
                                .Cells(nRow, 7) = IIf(IsNull(rsTimeLog!contract_eff_date), "", Format(rsTimeLog!contract_eff_date, FDATE))
                                .Cells(nRow, 8) = IIf(IsNull(rsTimeLog!contract_expiry_date), "", Format(rsTimeLog!contract_expiry_date, FDATE))
                                .Cells(nRow, 9) = IIf(IsNull(rsTimeLog!actual_move_in_date), "", Format(rsTimeLog!actual_move_in_date, FDATE))
                                .Columns.AutoFit
                            End With
                            nRow = nRow + 1
                        rsTimeLog.MoveNext
                    Wend
                End If
                
                Call PopulateExcelFile
                Call SendEMail("RMS System - Tenants for Renewal As Of " & CStr(Format(dtFr, FDATE)), "Tenants for Renewal", sEmailTo, sEmailCC, SEmailBcc, dtFr, dtTo)
            
            Case "RENEWAL NOTICE"
                Dim objCrystal As CRAXDRT.Application
                Dim objReport As CRAXDRT.Report
                
                'cnTLS.Execute "exec sp_rpt_TenantForRenewalNotice_AlertSave 'SAVE','" & dtFr & "','" & dtTo & "'"
        
                Dim rsRenewal As New ADODB.Recordset
                Dim strRenewalPDFFileName As String
                
'                rsRenewal.Open "select a.tenant_code,tenant_name from z_tmp_tenantforrenewalnotice_tenant a " & _
'                            " left join m_tenant on a.tenant_code = m_tenant.tenant_code " & _
'                            " order by m_tenant.real_property_code,building_code,unit_no,tenant_name", cnTLS, adOpenKeyset, adLockOptimistic
                rsRenewal.Open "exec sp_rpt_TenantForRenewalNotice_AlertSave '" & dtFr & "','" & dtTo & "'", cnTLS, adOpenKeyset, adLockOptimistic
                
                If rsRenewal.EOF = False Then
                    With rsRenewal
                        Do While Not .EOF
                            Set objCrystal = New CRAXDRT.Application
                            Set objReport = objCrystal.OpenReport(strReportPath & "\t_tenant_for_renewal.rpt", 1)
                            
                            strRenewalPDFFileName = strPDFPath & "\" & Replace(.Fields(1), "/", "") & "-RENEWAL-" & Format(Now, "mmddyyyyhhmm") & ".pdf"
                            ExportReportToPDF dtFr, dtTo, .Fields(0), strRenewalPDFFileName, objReport, strRenewalPDFFileName, "Letter of Expiration"
                            
                            sEmailAttach = strRenewalPDFFileName
                            Call SendEMail("Notice of Lease Agreement Renewal", "Notice of Lease Agreement Renewal", .Fields(2), "", "", dtFr, dtTo)
                            
                            cnTLS.Execute "exec sp_s_EmailAlert_Log '" & Now & "','" & strAlertTypeArr(iCtr) & "',0,'','',0,'" & .Fields(0) & "','" & .Fields(1) & "','" & .Fields(2) & "','" & strRenewalPDFFileName & "'"
                            
                            .MoveNext
                        Loop
                    End With
                End If
                
            Case "FIRST PAYMENT NOTICE"
                Dim objCrystalNoticeFirst As CRAXDRT.Application
                Dim objReportNoticeFirst As CRAXDRT.Report
                
                Dim rsNoticeFirst As New ADODB.Recordset
                Dim strPDFFileNameNoticeFirst As String
                
                rsNoticeFirst.Open "exec sp_rpt_TenantForPaymentNotice_AlertSave", cnTLS, adOpenKeyset, adLockOptimistic
                
                If rsNoticeFirst.EOF = False Then
                    With rsNoticeFirst
                        Do While Not .EOF
                            Set objCrystalNoticeFirst = New CRAXDRT.Application
                            Set objReportNoticeFirst = objCrystalNoticeFirst.OpenReport(strReportPath & "\t_tenant_demand_notice_first.rpt", 1)
                            
                            strPDFFileNameNoticeFirst = strPDFPath & "\" & Replace(.Fields(1), "/", "") & "-" & UCase(sSubject1) & "-" & Format(Now, "mmddyyyyhhmm") & ".pdf"
                            ExportReportToPDFNoticeFirst CStr(.Fields(0)), strPDFFileNameNoticeFirst, objReportNoticeFirst, strPDFFileNameNoticeFirst, sSubject1
                            
                            sEmailAttach = strPDFFileNameNoticeFirst
                            Call SendEMail(sSubject1 & "-" & .Fields(1), sSubject1 & "-" & .Fields(1), .Fields(2), .Fields(5), SEmailBcc, dtFr, dtTo)
                            cnTLS.Execute "exec sp_rpt_TenantForPaymentNotice_AlertUpdate " & .Fields(0) & ",'" & .Fields(2) & "'"
                            cnTLS.Execute "exec sp_s_EmailAlert_Log '" & Now & "','" & strAlertTypeArr(iCtr) & "',1,'',''," & .Fields(0) & ",'" & .Fields(3) & "','" & .Fields(1) & "','" & .Fields(2) & "','" & strPDFFileNameNoticeFirst & "'"
                                                        
                            .MoveNext
                        Loop
                    End With
                End If
                
            Case "FINAL PAYMENT NOTICE"
                Dim objCrystalNoticeFinal As CRAXDRT.Application
                Dim objReportNoticeFinal As CRAXDRT.Report
                
                Dim rsNoticeFinal As New ADODB.Recordset
                Dim strPDFFileNameNoticeFinal As String
                
                rsNoticeFinal.Open "exec sp_rpt_TenantForPaymentNoticeFinal_AlertSave", cnTLS, adOpenKeyset, adLockOptimistic
                
                If rsNoticeFinal.EOF = False Then
                    With rsNoticeFinal
                        Do While Not .EOF
                            Set objCrystalNoticeFinal = New CRAXDRT.Application
                            Set objReportNoticeFinal = objCrystalNoticeFinal.OpenReport(strReportPath & "\t_tenant_demand_notice_final.rpt", 1)
                            
                            strPDFFileNameNoticeFinal = strPDFPath & "\" & Replace(.Fields(1), "/", "") & "-" & UCase(sSubject2) & "-" & Format(Now, "mmddyyyyhhmm") & ".pdf"
                            ExportReportToPDFNoticeFinal CStr(.Fields(0)), strPDFFileNameNoticeFinal, objReportNoticeFinal, strPDFFileNameNoticeFinal, sSubject2
                            
                            sEmailAttach = strPDFFileNameNoticeFinal
                            Call SendEMail(sSubject2 & "-" & .Fields(1), sSubject2 & "-" & .Fields(1), .Fields(2), .Fields(5), SEmailBcc, dtFr, dtTo)
                            cnTLS.Execute "exec sp_rpt_TenantForPaymentNotice_AlertUpdate " & .Fields(0) & ",'" & .Fields(2) & "'"
                            cnTLS.Execute "exec sp_s_EmailAlert_Log '" & Now & "','" & strAlertTypeArr(iCtr) & "',2,'',''," & .Fields(0) & ",'" & .Fields(3) & "','" & .Fields(1) & "','" & .Fields(2) & "','" & strPDFFileNameNoticeFinal & "'"
                            
                            .MoveNext
                        Loop
                    End With
                End If
                
            Case "INVOICE"
                Dim objCrystalInvoice As CRAXDRT.Application
                Dim objReportInvoice As CRAXDRT.Report
                
                Dim rsInvoice As New ADODB.Recordset
                Dim strPDFFileNameInvoice As String
                
                rsInvoice.Open "exec sp_u_Send_Invoice_Alert_List", cnTLS, adOpenKeyset, adLockOptimistic
                
                If rsInvoice.EOF = False Then
                    With rsInvoice
                        Do While Not .EOF
                            Set objCrystalInvoice = New CRAXDRT.Application
                            Set objReportInvoice = objCrystalInvoice.OpenReport(strReportPath & "\t_invoice.rpt", 1)
                            
                            strPDFFileNameInvoice = strPDFPath & "\" & .Fields("pdf_file_name") & ".pdf"
                            ExportReportToPDFInvoice CStr(.Fields(0)), strPDFFileNameInvoice, objReportInvoice, strPDFFileNameInvoice, .Fields("report_title")
                            
                            sEmailAttach = strPDFFileNameInvoice
                            Call SendEMail(.Fields("report_title"), .Fields("report_title") + " " + .Fields(0), .Fields(2), "", SEmailBcc, dtFr, dtTo)
                            cnTLS.Execute "exec sp_u_Send_Invoice_Alert_Update '" & .Fields("invoice_no") & "','" & .Fields("email_add") & "'"
                            cnTLS.Execute "exec sp_s_EmailAlert_Log '" & Now & "','" & strAlertTypeArr(iCtr) & "-" & .Fields("invoice_no") & "',0,'','',0,'" & .Fields("client_code") & "','" & .Fields(tenant_name) & "','" & .Fields("email_add") & "','" & strPDFFileNameInvoice & "'"
                            
                            .MoveNext
                        Loop
                    End With
                End If
            
            Case "SENT INVOICES SUMMARY"
                Set rsTimeLog = New ADODB.Recordset
                If rsTimeLog.State = 1 Then rsTimeLog.Close
                sSQLTimeLog = "exec sp_u_Send_Invoice_Alert_List_Sent '" & DateAdd("d", 1, dtFr) & "','" & DateAdd("d", 1, dtTo) & "'"
                rsTimeLog.Open sSQLTimeLog, cnTLS, adOpenKeyset, adLockOptimistic
                If rsTimeLog.EOF = False Then
                    Call CreateExcelFileInvoiceList
                    nRow = 2
                    oWrkSheet.Name = Left("INVOICES ALERT SUMMARY AS OF " & Replace(Format(DateAdd("d", 1, dtFr), FDATE), "/", ""), 31)
                    While Not rsTimeLog.EOF
                            With oWrkSheet
                                .Cells(nRow, 1) = Trim(UCase(rsTimeLog!sap_code & ""))
                                .Cells(nRow, 2) = Trim(UCase(rsTimeLog!tenant_name & ""))
                                .Cells(nRow, 3) = Format(rsTimeLog!invoice_date, FDATE)
                                .Cells(nRow, 4) = Trim(UCase(rsTimeLog!invoice_no & ""))
                                .Cells(nRow, 5) = rsTimeLog!date_email_sent & ""
                                .Cells(nRow, 6) = Trim(rsTimeLog!email_addr & "")
                                .Columns.AutoFit
                            End With
                            nRow = nRow + 1
                        rsTimeLog.MoveNext
                    Wend
                    Call PopulateInvoiceExcelFile
                    Call SendEMail("RMS System - Invoices Alert Summary As Of " & CStr(Format(DateAdd("d", 1, dtFr), FDATE)), "Sent Invoices Summary", sEmailTo, sEmailCC, SEmailBcc, dtFr, dtTo)
                End If
            
            Case "SENT NOTICE/S SUMMARY"
                strDeptCode = ""
                Set rsTimeLog = New ADODB.Recordset
                If rsTimeLog.State = 1 Then rsTimeLog.Close
                sSQLTimeLog = "exec sp_rpt_TenantForPaymenNotice_AlertListSent"
                rsTimeLog.Open sSQLTimeLog, cnTLS, adOpenKeyset, adLockOptimistic
                If rsTimeLog.EOF = False Then
                    Call CreateExcelFileNoticeList
                    nRow = 2
                    oWrkSheet.Name = Left("SENT PAYMENT NOTICE/S SUMMARY AS OF " & Replace(Format(Date, FDATE), "/", ""), 31)
                    While Not rsTimeLog.EOF
                            With oWrkSheet
                                .Cells(nRow, 1) = Trim(UCase(rsTimeLog!wta_sap_code & ""))
                                .Cells(nRow, 2) = Trim(UCase(rsTimeLog!wta_tenant_name & ""))
                                .Cells(nRow, 3) = Trim(UCase(rsTimeLog!wta_real_property_name & ""))
                                .Cells(nRow, 4) = Trim(UCase(rsTimeLog!wta_notice_no & ""))
                                .Cells(nRow, 5) = IIf(IsNull(rsTimeLog!wta_date_email_sent), "", Format(rsTimeLog!wta_date_email_sent, FDATE))
                                .Cells(nRow, 6) = Trim(rsTimeLog!wta_email_add & "")
                                .Columns.AutoFit
                            End With
                            nRow = nRow + 1
                        rsTimeLog.MoveNext
                    Wend
                    Call PopulateNoticeExcelFile
                    Call SendEMail("RMS System - Sent Demand for Payment Notices Summary As Of " & CStr(Format(Date, FDATE)), "Sent Demand for Payment Notices Summary", sEmailTo, sEmailCC, SEmailBcc, dtFr, dtTo)
                End If
                
        End Select
        
        iCtr = iCtr + 1
    Wend
    End
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Private Sub ExportReportToPDFInvoice(strCode As String, strPDFFileName, ReportObject As CRAXDRT.Report, ByVal FileName As String, ByVal ReportTitle As String)
'On Error GoTo ErrHandler
On Error Resume Next

   Dim objExportOptions As CRAXDRT.ExportOptions
   Dim dtNow As Date
   
   ReportObject.ReportTitle = ReportTitle
    With ReportObject
        .Database.Tables(1).SetLogOnInfo strTLSServer, strTLSDB, strTLSUser, strTLSPwd
        .EnableParameterPrompting = False
        .MorePrintEngineErrorMessages = True
        .ParameterFields(1).AddCurrentValue strCode
        .ParameterFields(2).AddCurrentValue ""
        .ParameterFields(3).AddCurrentValue ""
    End With

   Set objExportOptions = ReportObject.ExportOptions
    With objExportOptions
        .DestinationType = crEDTDiskFile
        .DiskFileName = strPDFFileName
        .FormatType = crEFTPortableDocFormat
        .PDFExportAllPages = True
    End With

    ReportObject.Export False
    
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Private Sub ExportReportToPDFNoticeFinal(strCode As String, strPDFFileName, ReportObject As CRAXDRT.Report, ByVal FileName As String, ByVal ReportTitle As String)
'On Error GoTo ErrHandler
On Error Resume Next

   Dim objExportOptions As CRAXDRT.ExportOptions
   Dim dtNow As Date
   
   ReportObject.ReportTitle = "Final Demand Notice to Pay Rent"
    With ReportObject
        .Database.Tables(1).SetLogOnInfo strTLSServer, strTLSDB, strTLSUser, strTLSPwd
        .EnableParameterPrompting = False
        .MorePrintEngineErrorMessages = True
        .ParameterFields(1).AddCurrentValue strCode
        dtNow = Date
        .ParameterFields(2).AddCurrentValue CStr(Format(dtNow, FDATE))
        .ParameterFields(3).AddCurrentValue CStr(Format(DateAdd("d", intPaymentDueDays, dtNow), FDATE))
    End With

   Set objExportOptions = ReportObject.ExportOptions
    With objExportOptions
        .DestinationType = crEDTDiskFile
        .DiskFileName = strPDFFileName
        .FormatType = crEFTPortableDocFormat
        .PDFExportAllPages = True
    End With

    ReportObject.Export False
    
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Private Sub ExportReportToPDFNoticeFirst(strCode As String, strPDFFileName, ReportObject As CRAXDRT.Report, ByVal FileName As String, ByVal ReportTitle As String)
'On Error GoTo ErrHandler
On Error Resume Next

   Dim objExportOptions As CRAXDRT.ExportOptions
   Dim dtNow As Date
   
   ReportObject.ReportTitle = "Demand Notice to Pay Rent"
    With ReportObject
        .Database.Tables(1).SetLogOnInfo strTLSServer, strTLSDB, strTLSUser, strTLSPwd
        .EnableParameterPrompting = False
        .MorePrintEngineErrorMessages = True
        .ParameterFields(1).AddCurrentValue strCode
        dtNow = Date
        .ParameterFields(2).AddCurrentValue CStr(Format(dtNow, FDATE))
        .ParameterFields(3).AddCurrentValue CStr(Format(DateAdd("d", intPaymentDueDays, dtNow), FDATE))
    End With

   Set objExportOptions = ReportObject.ExportOptions
    With objExportOptions
        .DestinationType = crEDTDiskFile
        .DiskFileName = strPDFFileName
        .FormatType = crEFTPortableDocFormat
        .PDFExportAllPages = True
    End With

    ReportObject.Export False
    
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Private Sub ExportReportToPDF(dtFr, dtTo, strTenantCode As String, strRenewalPDFFileName, ReportObject As CRAXDRT.Report, ByVal FileName As String, ByVal ReportTitle As String)
'On Error GoTo ErrHandler
On Error Resume Next

   Dim objExportOptions As CRAXDRT.ExportOptions
   ReportObject.ReportTitle = "Letter of Expiration"
    With ReportObject
        .Database.Tables(1).SetLogOnInfo strTLSServer, strTLSDB, strTLSUser, strTLSPwd
        .EnableParameterPrompting = False
        .MorePrintEngineErrorMessages = True
        .ParameterFields(1).AddCurrentValue strTenantCode
        .ParameterFields(2).AddCurrentValue CStr(Format(Date, FDATE))
        .ParameterFields(3).AddCurrentValue CStr(dtFr)
        .ParameterFields(4).AddCurrentValue CStr(dtTo)
    End With

   Set objExportOptions = ReportObject.ExportOptions
    With objExportOptions
        .DestinationType = crEDTDiskFile
        .DiskFileName = strRenewalPDFFileName
        .FormatType = crEFTPortableDocFormat
        .PDFExportAllPages = True
    End With

    ReportObject.Export False
    
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Private Sub ProcTotalHrsForTheWeek(dtTo)
On Error GoTo ErrHandler
    Dim rsRemarks As ADODB.Recordset
    Dim rsTotalHrs As ADODB.Recordset
    Dim strCardNo, sSQLTimeLog2, dtFrom, strRemarks
    
    cnTLS.Execute "delete from tmp_totalhrs"
    strCardNo = ""
    Call GetCurrWeek(dtTo)
    dtFrom = dtDay1
    Set rsRemarks = New ADODB.Recordset
    If rsRemarks.State = 1 Then rsRemarks.Close
    sSQLTimeLog2 = "select * from vtimelognew where date_log between '" & Format(dtFrom, FDATE) & "' and '" & Format(dtTo, FDATE) & "' and paytype='H' and status = 'A' and company_code = '" & ReplaceSingleQuote(strTLSCpy) & "' " & _
                " and ((isnull(time_in1,'1/1/1900') = '1/1/1900' and isnull(sched_in1,'1/1/1900') <> '1/1/1900') or (isnull(time_out1,'1/1/1900') = '1/1/1900' and isnull(sched_out1,'1/1/1900') <> '1/1/1900') or (isnull(time_in2,'1/1/1900') = '1/1/1900' and " & _
                " isnull(sched_in2,'1/1/1900') <> '1/1/1900') or (isnull(time_out2,'1/1/1900') = '1/1/1900' and isnull(sched_out2,'1/1/1900') <> '1/1/1900') or (isnull(time_in3,'1/1/1900') = '1/1/1900' and isnull(sched_in3,'1/1/1900') <> '1/1/1900') or " & _
                " (isnull(time_out3,'1/1/1900') = '1/1/1900' and isnull(sched_out3,'1/1/1900') <> '1/1/1900') or (isnull(time_in4,'1/1/1900') = '1/1/1900' and isnull(sched_in4,'1/1/1900') <> '1/1/1900') or (isnull(time_out4,'1/1/1900') = '1/1/1900' and " & _
                " isnull(sched_out4,'1/1/1900') <> '1/1/1900') or (isnull(time_in5,'1/1/1900') = '1/1/1900' and isnull(sched_in5,'1/1/1900') <> '1/1/1900') or (isnull(time_out5,'1/1/1900') = '1/1/1900' and  isnull(sched_out5,'1/1/1900') <> '1/1/1900') )" & _
                " order by deptcode,last_name,first_name,middle_name,cardno,date_log"
    rsRemarks.Open sSQLTimeLog2, cnTLS, adOpenKeyset, adLockOptimistic
    
    If rsRemarks.EOF = False Then
        While Not rsRemarks.EOF
            If strCardNo <> Trim(UCase(rsRemarks!cardno & "")) Then
                If strCardNo <> "" Then
                    cnTLS.Execute "insert into tmp_totalhrs (cardno,remarks) " & _
                                " select '" & strCardNo & "" & "','" & strRemarks & "'"
                End If
                strCardNo = Trim(UCase(rsRemarks!cardno & ""))
                strRemarks = ""
            End If
            strRemarks = strRemarks & CStr(IIf(IsNull(rsRemarks!date_log), "", Format(rsRemarks!date_log, FDATE))) & ";"
            rsRemarks.MoveNext
        Wend
    End If
    
    'get summary
    Set rsTotalHrs = New ADODB.Recordset
    If rsTotalHrs.State = 1 Then rsTotalHrs.Close
    rsTotalHrs.Open "select deptname,l_name,f_name,m_name,sum(no_of_hrs) as total_no_of_hrs,tmp_totalhrs.remarks from tmp_loghrs " & _
                        " inner join temployee on tmp_loghrs.cardno=temployee.cardno " & _
                        " left join tmp_totalhrs on tmp_loghrs.cardno=tmp_totalhrs.cardno " & _
                        " inner join tldepartment on temployee.deptcode=tldepartment.deptcode " & _
                        " where temployee.company_code='" & ReplaceSingleQuote(strTLSCpy) & "' " & _
                        " and date_log between '" & Format(dtFrom, FDATE) & "' and '" & Format(dtTo, FDATE) & "'" & _
                        " group by deptname,l_name,f_name,m_name,tmp_totalhrs.remarks " & _
                        " order by deptname,l_name,f_name,m_name", cnTLS, adOpenKeyset, adLockOptimistic
    
    If rsTotalHrs.EOF = False Then
        Set oWrkSheet = oWrkBook.Sheets.Add
        oWrkSheet.Cells.Font.Name = "Verdana"
        oWrkSheet.Cells.Font.Size = 8
        oWrkSheet.Cells(1, 1) = "DEPARTMENT"
        oWrkSheet.Cells(1, 2) = "LAST NAME"
        oWrkSheet.Cells(1, 3) = "FIRST NAME"
        oWrkSheet.Cells(1, 4) = "MIDDLE NAME"
        oWrkSheet.Cells(1, 5) = "TOTAL NO OF HRS"
        oWrkSheet.Cells(1, 6) = "DATE/S OF THE WEEK WITH INCOMPLETE SWIPE"
        oWrkSheet.Range("A1:F1").Font.Bold = True
        oWrkSheet.Columns.AutoFit
        nRow = 2
        oWrkSheet.Name = "TOTAL HRS (" & Format(dtFrom, "mmddyy") & "-" & Format(dtTo, "mmddyy") & ")"
        
        While rsTotalHrs.EOF = False
            With oWrkSheet
                .Cells(nRow, 1) = Trim(UCase(rsTotalHrs!deptname & ""))
                .Cells(nRow, 2) = Trim(UCase(rsTotalHrs!l_name & ""))
                .Cells(nRow, 3) = Trim(UCase(rsTotalHrs!f_name & ""))
                .Cells(nRow, 4) = Trim(UCase(rsTotalHrs!m_name & ""))
                .Cells(nRow, 5) = IIf(IsNull(rsTotalHrs!total_no_of_hrs), 0, rsTotalHrs!total_no_of_hrs)
                .Cells(nRow, 6) = Trim(rsTotalHrs!remarks & "")
                .Columns.AutoFit
            End With
            nRow = nRow + 1
            rsTotalHrs.MoveNext
        Wend
    End If
Exit Sub
ErrHandler:
    If Err.Number <> 0 Then
        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
    End If
End Sub

Private Sub SendEMail(strEmailSubject, strTextLog, strEmailTo, strEmailCC, strEmailBCC, dtFr, dtTo)
'On Error GoTo ErrHandler
On Error Resume Next
  Dim objMail
  Dim strIsComplete, strDeptSummary
  Dim rsDept As ADODB.Recordset
  
  'sEMailFrom = ""
  'sEmailTo = ""
  'sEmailCC = ""
  'sEmailSubject = "RMS System - Tenants for Renewal As Of " & CStr(Format(dtFr, FDATE))
  sEmailSubject = strEmailSubject
  sEmailBody = ""
  
  Set objMail = CreateObject("CDONTS.NewMail")

  objMail.From = sEMailFrom
  objMail.To = strEmailTo
  objMail.Cc = strEmailCC
  objMail.Bcc = strEmailBCC
  objMail.Subject = sEmailSubject
  objMail.Body = sEmailBody
  objMail.AttachFile sEmailAttach
  
  objMail.Send
  Set objMail = Nothing
  
  'MsgBox "TLS email alert sent at " & Format(Now, FDATETIME) & vbCrLf & "(File: " & sEmailAttach & ")", vbInformation + vbOKOnly, PRJNAME
  
  Set pTxtStr = pScrFSO.CreateTextFile(strLogPath & "\l" & Format(Now, "mmddyyhhmmss") & ".txt")
  pTxtStr.WriteLine "RMS System - " & strTextLog & " email alert sent at " & Format(Now, FDATETIME) & vbCrLf & "(File: " & sEmailAttach & ")"
  
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Sub CreateExcelFileNoticeList()
'On Error GoTo ErrHandler
On Error Resume Next
    'Me.MousePointer = vbHourglass
    
    Set oExcel = CreateObject("Excel.Application")
    Set oWrkBook = oExcel.Workbooks.Add
    Set oWrkSheet = oWrkBook.Sheets(1)
    
    With oWrkSheet
        .Cells.Font.Name = "Verdana"
        .Cells.Font.Size = 8
        .Cells(1, 1) = "SAP ACCOUNT CODE"
        .Cells(1, 2) = "TENANT NAME"
        .Cells(1, 3) = "REAL PROPERTY CODE"
        .Cells(1, 4) = "NOTICE TYPE"
        .Cells(1, 5) = "DATE SENT"
        .Cells(1, 6) = "E-MAIL ADDRESS"
        .Range("A1:F1").Font.Bold = True
        .Columns.AutoFit
        
        nRow = 2
        nSheet = 1
    End With
    
    'Me.MousePointer = vbDefault
    
    'oExcel.Visible = True
Exit Sub
''ErrHandler:
''    If Err.Number <> 0 Then
''        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
''    End If
End Sub

Public Sub CreateExcelFileInvoiceList()
'On Error GoTo ErrHandler
On Error Resume Next
    'Me.MousePointer = vbHourglass
    
    Set oExcel = CreateObject("Excel.Application")
    Set oWrkBook = oExcel.Workbooks.Add
    Set oWrkSheet = oWrkBook.Sheets(1)
    
    With oWrkSheet
        .Cells.Font.Name = "Verdana"
        .Cells.Font.Size = 8
        .Cells(1, 1) = "SAP ACCOUNT CODE"
        .Cells(1, 2) = "CLIENT NAME"
        .Cells(1, 3) = "INVOICE DATE"
        .Cells(1, 4) = "INVOICE NO."
        .Cells(1, 5) = "DATE & TIME SENT"
        .Cells(1, 6) = "E-MAIL ADDRESS"
        .Range("A1:F1").Font.Bold = True
        .Columns.AutoFit
        
        nRow = 2
        nSheet = 1
    End With
    
    'Me.MousePointer = vbDefault
    
    'oExcel.Visible = True
Exit Sub
''ErrHandler:
''    If Err.Number <> 0 Then
''        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
''    End If
End Sub

Public Sub CreateExcelFile()
'On Error GoTo ErrHandler
On Error Resume Next
    'Me.MousePointer = vbHourglass
    
    Set oExcel = CreateObject("Excel.Application")
    Set oWrkBook = oExcel.Workbooks.Add
    Set oWrkSheet = oWrkBook.Sheets(1)
    
    With oWrkSheet
        .Cells.Font.Name = "Verdana"
        .Cells.Font.Size = 8
        .Cells(1, 1) = "REAL PROPERTY CODE"
        .Cells(1, 2) = "BUILDING CODE"
        .Cells(1, 3) = "UNIT NO."
        .Cells(1, 4) = "TENANT NAME"
        .Cells(1, 5) = "SAP ACCOUNT CODE"
        .Cells(1, 6) = "TENANT CODE"
        .Cells(1, 7) = "CONTRACT EFF DATE"
        .Cells(1, 8) = "CONTRACT EXPIRY DATE"
        .Cells(1, 9) = "ACTUAL MOVE IN DATE"
        .Range("A1:I1").Font.Bold = True
        .Columns.AutoFit
        
        nRow = 2
        nSheet = 1
    End With
    
    'Me.MousePointer = vbDefault
    
    'oExcel.Visible = True
Exit Sub
''ErrHandler:
''    If Err.Number <> 0 Then
''        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
''    End If
End Sub

Public Sub PopulateInvoiceExcelFile()
'On Error GoTo ErrHandler
On Error Resume Next
    sEmailFile = Trim(strTLSCpy) & "-SentInvoicesSummary" & Format(Now, "mmddyyyyhhmm") & ".xls"
    sEmailAttach = strPath & "\" & sEmailFile
    oWrkBook.SaveAs sEmailAttach
    oWrkBook.Close savechanges:=False
    oExcel.Quit
    Set oExcel = Nothing
    Set oWrkBook = Nothing
    Set oWrkSheet = Nothing
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Sub PopulateNoticeExcelFile()
'On Error GoTo ErrHandler
On Error Resume Next
    sEmailFile = Trim(strTLSCpy) & "-SentNoticesSummary" & Format(Now, "mmddyyyyhhmm") & ".xls"
    sEmailAttach = strPath & "\" & sEmailFile
    oWrkBook.SaveAs sEmailAttach
    oWrkBook.Close savechanges:=False
    oExcel.Quit
    Set oExcel = Nothing
    Set oWrkBook = Nothing
    Set oWrkSheet = Nothing
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Sub PopulateExcelFile()
'On Error GoTo ErrHandler
On Error Resume Next
    sEmailFile = Trim(strTLSCpy) & "-TenantsForRenewal" & Format(Now, "mmddyyyyhhmm") & ".xls"
    sEmailAttach = strPath & "\" & sEmailFile
    oWrkBook.SaveAs sEmailAttach
    oWrkBook.Close savechanges:=False
    oExcel.Quit
    Set oExcel = Nothing
    Set oWrkBook = Nothing
    Set oWrkSheet = Nothing
Exit Sub
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Sub

Public Function ReplaceSingleQuote(strParm) As String
'On Error GoTo ErrHandler
On Error Resume Next
    ReplaceSingleQuote = Trim(Replace(strParm, "'", "''"))
Exit Function
'ErrHandler:
'    If Err.Number <> 0 Then
'        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
'    End If
End Function

Public Sub ProcTotalNoOfHrs()
On Error GoTo ErrHandler
    Dim rsLogHrs As ADODB.Recordset
    Dim intNoHrs1, intNoHrs2, intNoHrs3, intNoHrs4 As Double
    Dim dblA, dblB, dblTotalNoOfHrs
    
    cnTLS.Execute "delete from tmp_loghrs"
    
    Set rsLogHrs = New ADODB.Recordset
    If rsLogHrs.State = 1 Then rsLogHrs.Close
    rsLogHrs.Open "select * from vtimelognew where date_log between '" & Format(dtDay1, FDATE) & "' and '" & Format(dtDay7, FDATE) & "' and paytype='H' and status = 'A' and company_code = '" & ReplaceSingleQuote(strTLSCpy) & "' order by deptcode,last_name,first_name,middle_name,date_log", cnTLS, adOpenKeyset, adLockOptimistic

    If rsLogHrs.EOF Then
        Exit Sub
    End If
    With rsLogHrs
        .MoveFirst
        Do While Not .EOF
           intNoHrs1 = 0
           intNoHrs2 = 0
           intNoHrs3 = 0
           intNoHrs4 = 0
           
           Dim rsTotal As ADODB.Recordset
           
           Set rsTotal = New ADODB.Recordset
           If rsTotal.State = 1 Then rsTotal.Close
           rsTotal.Open "select * from vtimelognew where cardno =  '" & Trim(!cardno) & "' and date_log = '" & !date_log & "' and company_code = '" & strTLSCpy & "'", cnTLS, adOpenKeyset, adLockOptimistic

           If rsTotal.EOF = False Then
                With rsTotal
                   ' shift 1
                   If !ischk_ws & "" = "Y" Then
                        dblA = ProcDeptWithChkWS(!correction_out1, !time_out1_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_out1, !correction_time_out1)
                        dblB = ProcDeptWithChkWS(!correction_in1, !time_in1_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_in1, !correction_time_in1)
                   Else
                        dblA = ProcessTimeLog(!sched_out1_hr, !change_sched_out1, !correction_out1, !time_out1_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "O")
                        dblB = ProcessTimeLog(!sched_in1_hr, !change_sched_in1, !correction_in1, !time_in1_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "I")
                   End If
                   If (dblA >= 0 And dblB >= 0) And (dblA <> "" And dblB <> "") And (IIf(IsNull(!correction_out1), !time_out1_hr, !correction_out1) <> IIf(IsNull(!correction_in1), !time_in1_hr, !correction_in1)) Then
                        intNoHrs1 = IIf(dblB > dblA, dblA + 24, dblA) - dblB
                   Else
                        intNoHrs1 = 0
                   End If
                   intNoHrs1 = IIf(intNoHrs1 <= 0, 0, intNoHrs1)
                   
                   ' shift 2
                   If !ischk_ws & "" = "Y" Then
                        dblA = ProcDeptWithChkWS(!correction_out2, !time_out2_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_out2, !correction_time_out2)
                        dblB = ProcDeptWithChkWS(!correction_in2, !time_in2_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_in2, !correction_time_in2)
                   Else
                        dblA = ProcessTimeLog(!sched_out2_hr, !change_sched_out2, !correction_out2, !time_out2_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "O")
                        dblB = ProcessTimeLog(!sched_in2_hr, !change_sched_in2, !correction_in2, !time_in2_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "I")
                   End If
                   If (dblA >= 0 And dblB >= 0) And (dblA <> "" And dblB <> "") And (IIf(IsNull(!correction_out2), !time_out2_hr, !correction_out2) <> IIf(IsNull(!correction_in2), !time_in2_hr, !correction_in2)) Then
                        intNoHrs2 = IIf(dblB > dblA, dblA + 24, dblA) - dblB
                   Else
                        intNoHrs2 = 0
                   End If
                   intNoHrs2 = IIf(intNoHrs2 <= 0, 0, intNoHrs2)
             
                   ' shift 3
                   If !ischk_ws & "" = "Y" Then
                        dblA = ProcDeptWithChkWS(!correction_out3, !time_out3_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_out3, !correction_time_out3)
                        dblB = ProcDeptWithChkWS(!correction_in3, !time_in3_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_in3, !correction_time_in3)
                   Else
                        dblA = ProcessTimeLog(!sched_out3_hr, !change_sched_out3, !correction_out3, !time_out3_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "O")
                        dblB = ProcessTimeLog(!sched_in3_hr, !change_sched_in3, !correction_in3, !time_in3_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "I")
                   End If
                   If (dblA >= 0 And dblB >= 0) And (dblA <> "" And dblB <> "") And (IIf(IsNull(!correction_out3), !time_out3_hr, !correction_out3) <> IIf(IsNull(!correction_in3), !time_in3_hr, !correction_in3)) Then
                        intNoHrs3 = IIf(dblB > dblA, dblA + 24, dblA) - dblB
                   Else
                        intNoHrs3 = 0
                   End If
                   intNoHrs3 = IIf(intNoHrs3 <= 0, 0, intNoHrs3)

                   ' shift 4
                   If !ischk_ws & "" = "Y" Then
                        dblA = ProcDeptWithChkWS(!correction_out4, !time_out4_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_out4, !correction_time_out4)
                        dblB = ProcDeptWithChkWS(!correction_in4, !time_in4_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), !time_in4, !correction_time_in4)
                   Else
                        dblA = ProcessTimeLog(!sched_out4_hr, !change_sched_out4, !correction_out4, !time_out4_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "O")
                        dblB = ProcessTimeLog(!sched_in4_hr, !change_sched_in4, !correction_in4, !time_in4_hr, IIf(IsNull(!allowed_in), 0, !allowed_in), IIf(IsNull(!grace_period), 0, !grace_period), "I")
                   End If
                   
                   If (dblA >= 0 And dblB >= 0) And (dblA <> "" And dblB <> "") And (IIf(IsNull(!correction_out4), !time_out4_hr, !correction_out4) <> IIf(IsNull(!correction_in4), !time_in4_hr, !correction_in4)) Then
                        intNoHrs4 = IIf(dblB > dblA, dblA + 24, dblA) - dblB
                   Else
                        intNoHrs4 = 0
                   End If
                   intNoHrs4 = IIf(intNoHrs4 <= 0, 0, intNoHrs4)

                End With
           End If
           
           dblTotalNoOfHrs = Round(Round(IIf(IsNull(intNoHrs1), 0, intNoHrs1), 2) + Round(IIf(IsNull(intNoHrs2), 0, intNoHrs2), 2) + Round(IIf(IsNull(intNoHrs3), 0, intNoHrs3), 2) + Round(IIf(IsNull(intNoHrs4), 0, intNoHrs4), 2), 2)
           
           cnTLS.Execute "insert into tmp_loghrs(cardno,no_of_hrs,date_log) select '" & !cardno & "" & "'," & dblTotalNoOfHrs & ",'" & !date_log & "'"
       
           .MoveNext
        Loop
        
    End With
Exit Sub
ErrHandler:
    If Err.Number <> 0 Then
        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
    End If
End Sub

Public Function ProcessTimeLog(sched_log_hr, change_sched_log, correction_log, time_log_hr, allowed_log, grace_period, strInOut)
On Error GoTo ErrHandler
    If IsNull(sched_log_hr) And IsNull(change_sched_log) Then
        If IsNull(correction_log) Then
            ProcessTimeLog = time_log_hr
        Else
            ProcessTimeLog = correction_log
        End If
    ElseIf Not IsNull(change_sched_log) Then
        If IsNull(correction_log) Then
            If strInOut = "I" Then
                If (Round(time_log_hr, 2) >= Round(change_sched_log - allowed_log / 60, 2)) And (Round(time_log_hr, 2) <= Round(change_sched_log + grace_period / 60, 2)) Then
                    ProcessTimeLog = change_sched_log
                Else
                    ProcessTimeLog = time_log_hr
                End If
            Else
                If (Round(time_log_hr, 2) >= Round(change_sched_log, 2)) And (Round(time_log_hr, 2) <= Round(change_sched_log + grace_period / 60, 2)) Then
                    ProcessTimeLog = change_sched_log
                Else
                    ProcessTimeLog = time_log_hr
                End If
            End If
        Else
            If strInOut = "I" Then
                If (Round(correction_log, 2) >= Round(change_sched_log - allowed_log / 60, 2)) And (Round(correction_log, 2) <= Round(change_sched_log + grace_period / 60, 2)) Then
                    ProcessTimeLog = change_sched_log
                Else
                    ProcessTimeLog = correction_log
                End If
            Else
                If (Round(correction_log, 2) >= Round(change_sched_log, 2)) And (Round(correction_log, 2) <= Round(change_sched_log + grace_period / 60, 2)) Then
                    ProcessTimeLog = change_sched_log
                Else
                    ProcessTimeLog = correction_log
                End If
            End If
        End If
    ElseIf Not IsNull(time_log_hr) Or Not IsNull(correction_log) Then
        If IsNull(correction_log) Then
            If strInOut = "I" Then
                If (Round(time_log_hr, 2) >= Round(sched_log_hr - allowed_log / 60, 2)) And (Round(time_log_hr, 2) <= Round(sched_log_hr + grace_period / 60, 2)) Then
                    ProcessTimeLog = sched_log_hr
                Else
                    ProcessTimeLog = time_log_hr
                End If
            Else
                If (Round(time_log_hr, 2) >= Round(sched_log_hr, 2)) And (Round(time_log_hr, 2) <= Round(sched_log_hr + grace_period / 60, 2)) Then
                    ProcessTimeLog = sched_log_hr
                Else
                    ProcessTimeLog = time_log_hr
                End If
            End If
        Else
            If strInOut = "I" Then
                If (Round(correction_log, 2) >= Round(sched_log_hr - allowed_log / 60, 2)) And (Round(correction_log, 2) <= Round(sched_log_hr + grace_period / 60, 2)) Then
                    ProcessTimeLog = sched_log_hr
                Else
                    ProcessTimeLog = correction_log
                End If
            Else
                If (Round(correction_log, 2) >= Round(sched_log_hr, 2)) And (Round(correction_log, 2) <= Round(sched_log_hr + grace_period / 60, 2)) Then
                    ProcessTimeLog = sched_log_hr
                Else
                    ProcessTimeLog = correction_log
                End If
            End If
        End If
    Else
        If IsNull(correction_log) Then
            ProcessTimeLog = time_log_hr
        Else
            ProcessTimeLog = correction_log
        End If
    End If
    ProcessTimeLog = IIf(IsNull(ProcessTimeLog), "", ProcessTimeLog)
Exit Function
ErrHandler:
    If Err.Number <> 0 Then
        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
    End If
End Function

Public Function ProcDeptWithChkWS(correction_log, time_log_hr, allowed_log, grace_period, time_log, correction_log_time)
On Error GoTo ErrHandler
    'process time logs for departments whose ischk_ws is tagged as Y regardless of work sched
    Dim dtTmp
    
    If Not IsNull(time_log_hr) Or Not IsNull(correction_log) Then
        If IsNull(correction_log) Then
            dtTmp = DateAdd("n", allowed_log, time_log)
            If DatePart("n", dtTmp) >= 0 And DatePart("n", dtTmp) <= 10 Then
                If DatePart("n", dtTmp) >= 0 And DatePart("n", dtTmp) < 5 Then
                    ProcDeptWithChkWS = DatePart("h", DateAdd("h", 1, time_log))
                ElseIf DatePart("n", dtTmp) >= 5 And DatePart("n", dtTmp) <= 10 Then
                    ProcDeptWithChkWS = DatePart("h", time_log)
                End If
            ElseIf (DatePart("n", dtTmp) >= 30 And DatePart("n", dtTmp) <= 40) Then
                ProcDeptWithChkWS = DatePart("h", time_log) + 0.5
            Else
                ProcDeptWithChkWS = time_log_hr
            End If
        Else
            dtTmp = DateAdd("n", allowed_log, correction_log_time)
            If DatePart("n", dtTmp) >= 0 And DatePart("n", dtTmp) <= 10 Then
                If DatePart("n", dtTmp) >= 0 And DatePart("n", dtTmp) < 5 Then
                    ProcDeptWithChkWS = DatePart("h", DateAdd("h", 1, correction_log_time))
                ElseIf (DatePart("n", dtTmp) >= 5 And DatePart("n", dtTmp) <= 10) Then
                    ProcDeptWithChkWS = DatePart("h", correction_log_time)
                End If
            ElseIf DatePart("n", dtTmp) >= 30 And DatePart("n", dtTmp) <= 40 Then
                ProcDeptWithChkWS = DatePart("h", correction_log_time) + 0.5
            Else
                ProcDeptWithChkWS = correction_log
            End If
        End If
    Else
        If IsNull(correction_log) Then
            ProcDeptWithChkWS = time_log_hr
        Else
            ProcDeptWithChkWS = correction_log
        End If
    End If
    ProcDeptWithChkWS = IIf(IsNull(ProcDeptWithChkWS), "", ProcDeptWithChkWS)
Exit Function
ErrHandler:
    If Err.Number <> 0 Then
        MsgBox Err.Number & ": " & Err.Description, vbCritical + vbOKOnly, PRJNAME
    End If
End Function

