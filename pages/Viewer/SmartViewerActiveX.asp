<%
%>
<HTML>
<HEAD>
<TITLE>Seagate ActiveX Viewer</TITLE>
<style>
   TD {font-size: 10pt;
       font-family: verdana,helvetica;
	   text-decoration: none;
	   white-space:nowrap;}
</style>
</HEAD>
<BODY BGCOLOR=#c6c6c6 LANGUAGE=VBScript ONLOAD="Page_Initialize" topMargin=0 leftMargin=0 rightMargin=0 bottomMargin=0>
<OBJECT id=CRViewer style="HEIGHT: 100%"
codeBase=/viewer/activeXViewer/activexviewer.cab#Version=8,0,0,224 height="100%"
width="100%" classid=CLSID:C4847596-972C-11D0-9567-00A0C9273C2A>
<PARAM NAME="DisplayGroupTree" VALUE="0">
<PARAM NAME="DisplayToolbar" VALUE="-1">
<PARAM NAME="EnableGroupTree" VALUE="-1">
<PARAM NAME="EnableNavigationControls" VALUE="-1">
<PARAM NAME="EnableStopButton" VALUE="-1">
<PARAM NAME="EnablePrintButton" VALUE="-1">
<PARAM NAME="EnableZoomControl" VALUE="-1">
<PARAM NAME="EnableCloseButton" VALUE="0">
<PARAM NAME="EnableProgressControl" VALUE="-1">
<PARAM NAME="EnableSearchControl" VALUE="-1">
<PARAM NAME="EnableRefreshButton" VALUE="0">
<PARAM NAME="EnableDrillDown" VALUE="-1">
<PARAM NAME="EnableAnimationControl" VALUE="-1">
<PARAM NAME="EnableSelectExpertButton" VALUE="0">
<PARAM NAME="EnableToolbar" VALUE="-1">
<PARAM NAME="DisplayBorder" VALUE="-1">
<PARAM NAME="DisplayTabs" VALUE="-1">
<PARAM NAME="DisplayBackgroundEdge" VALUE="-1">
<PARAM NAME="SelectionFormula" VALUE="">
<PARAM NAME="EnablePopupMenu" VALUE="-1">
<PARAM NAME="EnableExportButton" VALUE="-1">
<PARAM NAME="EnableSearchExpertButton" VALUE="0">
<PARAM NAME="EnableHelpButton" VALUE="0">
</OBJECT>

<SCRIPT LANGUAGE="VBScript">
<!--
Sub Page_Initialize
	On Error Resume Next
	Dim webBroker
	Set webBroker = CreateObject("WebReportBroker.WebReportBroker")
	if ScriptEngineMajorVersion < 2 then
		window.alert "IE 3.02 users on NT4 need to get the latest version of VBScript or install IE 4.01 SP1. IE 3.02 users on Win95 need DCOM95 and latest version of VBScript, or install IE 4.01 SP1. These files are available at Microsoft's web site."
		CRViewer.ReportName = "rptserver.asp"
	else
		Dim webSource
		Set webSource = CreateObject("WebReportSource.WebReportSource")
		webSource.ReportSource = webBroker
		webSource.URL = Location.Protocol + "//" + Location.host + "/cfclms/Viewer/rptserver.asp"
		webSource.PromptOnRefresh = True
		CRViewer.ReportSource = webSource
	end if
	CRViewer.ViewReport
End Sub
-->
</SCRIPT>
</BODY>
</HTML>
