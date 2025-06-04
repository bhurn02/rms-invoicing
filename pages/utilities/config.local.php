<?php
/*
  Author:         Aldrich Delos Santos
  Project Name:   eTime Adjustment Request System
  Version:        v1.0
  Date:           12/21/2018
*/
define('DEBUG', false );        // Set DEBUG MODE
define('COMPANY_CODE', 'thc' );        // Set company_code
define('COMPANY_NAME', 'Tan Holdings Corporation' );        // Set company_code
define('PARENT_URL', 'http://aldrich.projects.local/rms/' );        // Set home url
define('ROOT_URL', 'http://aldrich.projects.local/rms/utilities/' );        // Set home url
define('SITE_LOGO', 'http://aldrich.projects.local/rms/assets/images/thc-logo-icon.png' );        // Set home url

// Set Application Name
define('PARENT_APP_NAME', 'RMS' );
define('APP_NAME', 'RMS Invoicing System' );
define('APP_CODE', 'RMSIS' );
// Set Application Author
define('APP_AUTHOR', 'Aldrich Delos Santos' );
define('STAFF_POSITION', 'Business Analyst / Developer' );
// Set Copyright
define('APP_COPYRIGHT', 'L&T Group of Companies' );
define('APP_HELPDESK_MAIL', 'etlar.helpdesk@tanholdings.com' );

date_default_timezone_set("Pacific/Guam");
/*
APPLICATION CONFIG
*/
// Set Application Email
define('APP_MAIL_SENDER', 'hr2admin@tanholdings.com' );
// define('APP_MAIL_BCC', 'hr2_devsupport@tanholdings.com' );
// define('APP_MAIL_SENDER', 'lnt.etlar.helpdesk@gmail.com' );
// define('APP_MAIL_SENDER', 'aldrich_delossantos@tanholdings.com' );
// Set Theme Color [blue, blue-dark, default-dark, default, green-dark, green, megna-dark, megna, purple-dark, purple, red-dark, red]
define('THEME_COLOR', 'green' );
// SET APIs
define('LOCAL_API', ROOT_URL.'api/' );
// SET SAP directory
define('SAP_DIRECTORY', $_SERVER['APPL_PHYSICAL_PATH'].'sap'.DIRECTORY_SEPARATOR );
define('LOGS_DIRECTORY', $_SERVER['APPL_PHYSICAL_PATH'].'logs'.DIRECTORY_SEPARATOR );
define('ATTACHMENT_DIRECTORY', $_SERVER['APPL_PHYSICAL_PATH'].'pdf'.DIRECTORY_SEPARATOR );
// Include / exclude invalid records from SAP File
define('SAP_INCLUDE_INVALID', true );
define('SOURCEAPP', 'WEB' );

// Database Parameters
define('DSN_NAME', 'etlar_approval' );
define('HR2V2_DSN_NAME', 'hr2v2' );
define('HR2V2_DB_NAME', 'hr2v2_main');	
define('ETLAR_DB_NAME', 'THC_ApprovalSystem');	
define('TLS_DB_NAME', 'TLSNA_PPPI.dbo.' );

/* 
Set all Database information here.
*/
// Set your database host
define('DB_HOST', 'localhost');			
// Set your database username
define('DB_USERNAME', 'web_app' ); 
// Set your database password
define('DB_PASSWORD', '@webapp123' ); 
// Set your database name
define('DB_NAME', 'RMS');	
// define('DB_NAME', 'EmployeeManagementSystem_EFG');	
// Set your database type (mysql, mssql or sqlite)
define('DB_TYPE', 'mssql');					
// Set your DSN
define('DB_DSN', 'sqlsrv:Server='.DB_HOST.';Database='.DB_NAME); 

define('MENU_TYPE', 'drilldown' );					// Set menu type [drilldown, accordion-menu]
define('PAGENOTFOUND_TEMPLATE', 'machine/under-construction/dark' ); // Set the 404-page not found template [under-construction, cerberus, villains, space-invaders]
define('ACCESSDENIED_TEMPLATE', 'machine/coming-soon/dark' );        // Set the Access Denied page template [beaver]
define('VIEWS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR );			      // Set the Access Denied page template [beaver]




// ini_set("mssql.textlimit" , "2147483647");
// ini_set("mssql.textsize" , "2147483647");
// ini_set("odbc.defaultlrl", "0");


/* DEV TEST */
define('CARDNO', '90143' );
define('UserID', '2' );
define('UserName', 'dev1' );
define('IsAdmin', false );
define('UNDER_MAINTENANCE', false );
// define('UNDER_MAINTENANCE_PAGE_EXCLUDED', array() ); // only available in PHP v7+
// define('UNDER_MAINTENANCE_PAGE_EXCLUDED', array('home') ); // only available in PHP v7+
define('UNDER_MAINTENANCE_PAGE_EXCLUDED', array() ); // only available in PHP v7+
// define('UNDER_MAINTENANCE_PAGE_EXCLUDED', array() ); // only available in PHP v7+
define('UNDER_MAINTENANCE_SPECIFIC_PAGES', array() ); // only available in PHP v7+
// define('UNDER_MAINTENANCE_SPECIFIC_PAGES', array('request-details') ); // only available in PHP v7+
// define('UNDER_MAINTENANCE_USERID_EXCLUDED', array(5,1058,4) ); // only available in PHP v7+
define('UNDER_MAINTENANCE_USERID_EXCLUDED', array() ); // only available in PHP v7+
define('FULL_PAGES', array("home","work-scheduler","scheduler-prototype","timeclock-gps","timekeeper-util","premium-hours") ); // only available in PHP v7+
define('ETLA_FORMS', array('daily-time-correction','change-work-sched','extended-hours') ); // only available in PHP v7+
?>