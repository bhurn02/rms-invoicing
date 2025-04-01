<html>
<body>

<?php
$dsn="personnel";
$username="sa";
$password="rgarcia@3@8";
$sqlconnect=odbc_connect($dsn,$username,$password);
$sqlquery="SELECT * FROM a_emp_master;";
$process=odbc_exec($sqlconnect, $sqlquery);

while(odbc_fetch_row($process)){
$companyName = odbc_result($process,"last_name");
echo "$companyName<br>"; }
odbc_close($sqlconnect);
?>

</body>
</html> 


//$strCode = $_POST["txtCode"];
//$strName = $_POST["txtName"];
$query = mssql_init("sp_Apartment",$sqlconnect); 
mssql_bind($query, "@strMode","ADD", SQLVARCHAR); 
mssql_bind($query, "@apart_code","b", SQLVARCHAR); 
mssql_bind($query, "@apart_name","b", SQLVARCHAR); 
mssql_bind($query, "@address1","", SQLVARCHAR); 
mssql_bind($query, "@address2","", SQLVARCHAR); 
mssql_bind($query, "@contactno","", SQLVARCHAR); 
mssql_bind($query, "@totalunits",1, SQLINT4); 
mssql_bind($query, "@remarks","", SQLVARCHAR); 
$result = mssql_execute($query);
if (!$result) { echo mysql_error();} 
