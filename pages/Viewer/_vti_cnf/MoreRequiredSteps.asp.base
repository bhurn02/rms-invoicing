<%

'====================================================================================
' Retrieve the Records and Create the "Page on Demand" Engine Object
'====================================================================================

On Error Resume Next                                                  
session("oRpt").ReadRecords                                           
If Err.Number <> 0 Then                                               
  Response.Write "The Error Message was: " & Err.Description & " from: " & Err.Source
Else
  If IsObject(session("oPageEngine")) Then                              
  	set session("oPageEngine") = nothing
  End If
  set session("oPageEngine") = session("oRpt").PageEngine
End If

%>