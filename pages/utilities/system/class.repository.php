<?php

/*
Project:    Class Repository 
Author:     Aldrich Delos Santos
Updated:    02/15/2017

How to use:
    $register = new RegisterUser();
    if(!empty($_POST))    
        $register->start();

    or

    $register-><Function Name>(<optional || parameter>);
    $register->myFunction(myParameters);
*/

/* Initialize SMTP Plugin */
require_once("mail_smtp.php");

define('IPAddress',$_SERVER['REMOTE_ADDR']);
define('UserAgent',$_SERVER['HTTP_USER_AGENT']);
// $UserAgent = $_SERVER['HTTP_USER_AGENT'];
$ErrorMessage = "<div class='alert alert-danger text-center'><strong>We're Sorry!</strong> Online reservation is currently unavailable. Please <a href='#' class='alert-link' onclick='location.reload(); return false;'>try again</a> later or you may want to call our reservation hotline below. Thank you.</div>";

class RegisterUser {
    private $staffname;
    private $emailaddress;
    private $username;
    private $password;
    private $groupid;
    private $imagepath;
    private $status;
    private $userid;

    function __construct() {
        $this->staffname = isset($_POST['u_name']) ? $_POST['u_name'] : null;
        $this->emailaddress = isset($_POST['u_email']) ? $_POST['u_email'] : null;
        $this->username = isset($_POST['u_username']) ? $_POST['u_username'] : null;
        $this->password = isset($_POST['u_password']) ? $_POST['u_password'] : null;
        $this->groupid = isset($_POST['u_groupid']) ? $_POST['u_groupid'] : "0";
        $this->imagepath = isset($_POST['u_imagepath']) ? $_POST['u_imagepath'] : null;
        $this->status = isset($_POST['u_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->staffname) || empty($this->emailaddress) || empty($this->username) || empty($this->password)) {
            // throw new Exception("Empty Post not allowed");
            echo "Empty Post not allowed";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            ================
            Append to Staffs
            ================
            */

            try {
                $sqlquery = "INSERT INTO Staffs(staff_name, staff_email, staff_group_id, staff_status)
                            SELECT '".ms_escape_string($this->staffname)."'
                                    ,'".ms_escape_string($this->emailaddress)."'
                                    ,".$this->groupid."
                                    ,".$this->status;

                $inserted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }
            
            /*
            ===========================
            Fetch New Staff Information
            ===========================            
            */
            $staffinfo = new UserInfo();
            $staffinfo->fetchdatabyemail($this->emailaddress);

            /*
            ===============
            Append to User
            ===============
            */
            try {
                $sqlquery = "INSERT INTO Users(staff_id, username, password)
                            SELECT ".$staffinfo->staffid."
                                ,'".ms_escape_string($this->username)."'
                                ,'".ms_escape_string($this->password)."'";

                $inserted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>added</b> staff <a href="'.ROOT_URL.'admin/staff-edit.php?id='.$staffinfo->staffid.'"><b>'.$staffinfo->staffname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'staffs', 'staff', ".$userinfo->staffid.", 'added', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Staff <strong>".$staffinfo->staffname."</strong> 
                    was added successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

class UpdateUser {
    private $staffid;
    private $staffname;
    private $emailaddress;
    private $username;
    private $password;
    private $groupid;
    private $imagepath;
    private $status;
    private $userid;

    function __construct() {
        $this->staffid = isset($_POST['u_staffid']) ? $_POST['u_staffid'] : "0";
        $this->staffname = isset($_POST['u_name']) ? $_POST['u_name'] : null;
        $this->emailaddress = isset($_POST['u_email']) ? $_POST['u_email'] : null;
        $this->username = isset($_POST['u_username']) ? $_POST['u_username'] : null;
        $this->password = isset($_POST['u_password']) ? $_POST['u_password'] : null;
        $this->groupid = isset($_POST['u_groupid']) ? $_POST['u_groupid'] : "0";
        $this->imagepath = isset($_POST['u_imagepath']) ? $_POST['u_imagepath'] : null;
        $this->status = isset($_POST['u_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->staffname) || empty($this->emailaddress) || empty($this->password)) {
            // throw new Exception("Empty Post not allowed");
            echo "Empty Post not allowed";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            ================
            Update Staffs
            ================
            */

            try {
                $sqlquery = "UPDATE Staffs SET 
                            staff_name='".ms_escape_string($this->staffname)."'
                            ,staff_email='".ms_escape_string($this->emailaddress)."'
                            ,staff_group_id=".$this->groupid."
                            ,staff_status=".$this->status."
                            WHERE staff_id=".$this->staffid;
                            

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }
            
            /*
            
            /*
            ===========
            Update User
            ===========            
            */
            try {
                $sqlquery = "UPDATE Users SET 
                            password='".ms_escape_string($this->password)."'
                            WHERE username='".ms_escape_string($this->username)."'";

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> staff <a href="'.ROOT_URL.'admin/staff-edit.php?id='.$this->staffid.'"><b>'.$this->staffname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'staffs', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Staff <strong>".$this->staffname."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class UserInfo {

    public $id
            ,$staffid
            ,$username
            ,$password
            ,$staffname
            ,$emailaddress
            ,$groupid            
            ,$groupname            
            ,$customeraccountaccess            
            ,$locationaccess            
            ,$imagepath            
            ,$status            
            ,$numrows;

    function fetchdatabyusername($username) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_UserProfile WHERE username='$username'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['user_id'];            
            $this->staffid = (int) $row['staff_id'];     
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->staffname = $row['staff_name'];
            $this->emailaddress = $row['staff_email'];            
            $this->groupid = (int) $row['staff_group_id'];     
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = (int) $row['customer_account_access'];
            $this->locationaccess = (int) $row['location_access'];
            $this->imagepath = $row['staff_image'];
            $this->status = (int) $row['staff_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyuserid($userid) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_UserProfile WHERE user_id=$userid";
        // die($sqlquery);

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {            
            $this->id = (int) $row['user_id'];            
            $this->staffid = (int) $row['staff_id'];     
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->staffname = $row['staff_name'];
            $this->emailaddress = $row['staff_email'];            
            $this->groupid = (int) $row['staff_group_id'];     
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = (int) $row['customer_account_access'];
            $this->locationaccess = (int) $row['location_access'];
            $this->imagepath = $row['staff_image'];
            $this->status = (int) $row['staff_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabystaffid($staffid) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_UserProfile WHERE staff_id=$staffid";
        // die($sqlquery);

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {            
            $this->id = (int) $row['user_id'];            
            $this->staffid = (int) $row['staff_id'];     
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->staffname = $row['staff_name'];
            $this->emailaddress = $row['staff_email'];            
            $this->groupid = (int) $row['staff_group_id'];     
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = (int) $row['customer_account_access'];
            $this->locationaccess = (int) $row['location_access'];
            $this->imagepath = $row['staff_image'];
            $this->status = (int) $row['staff_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyemail($email) {
        $count=0;
        
        $sqlquery = "SELECT TOP 1 
                        U.user_id
                        ,S.staff_id
                        ,staff_name
                        ,staff_email
                        ,username
                        ,password
                        ,S.staff_group_id
                        ,staff_group_name
                        ,customer_account_access
                        ,location_access
                        ,staff_image
                        ,staff_status
                    FROM Staffs S 
                    LEFT JOIN StaffGroups G ON G.staff_group_id=S.staff_group_id
                    LEFT JOIN Users U ON U.staff_id=S.staff_id
                    WHERE staff_email='$email' ORDER BY staff_id DESC";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {            
            $this->id = (int) $row['user_id'];            
            $this->staffid = (int) $row['staff_id'];     
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->staffname = $row['staff_name'];
            $this->emailaddress = $row['staff_email'];            
            $this->groupid = (int) $row['staff_group_id'];     
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = (int) $row['customer_account_access'];
            $this->locationaccess = (int) $row['location_access'];
            $this->imagepath = $row['staff_image'];
            $this->status = (int) $row['staff_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function checkpassword($username,$password) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_UserProfile WHERE username='$username' and password='$password'";
        // die($sqlquery);

        $rows = mssql_resultset($sqlquery);
        
        $this->numrows= sizeof($rows);
    }
}

class RegisterStaffGroup {
    private $groupname;
    private $customeraccountaccess;
    private $locationaccess;    
    private $status;
    private $userid;

    function __construct() {
        $this->groupname = isset($_POST['u_group_name']) ? $_POST['u_group_name'] : null;
        $this->customeraccountaccess = isset($_POST['u_customer_account_access']) ? "1" : "0";
        $this->locationaccess = isset($_POST['u_location_access']) ? "1" : "0";
        $this->status = isset($_POST['u_group_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->groupname)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            =====================
            Append to Staff Group
            =====================            
            */

            try {
                $sqlquery = "INSERT INTO StaffGroups(staff_group_name, customer_account_access, location_access, staff_group_status)
                            SELECT '".ms_escape_string($this->groupname)."'                                    
                                    ,".$this->customeraccountaccess."
                                    ,".$this->locationaccess."
                                    ,".$this->status;

                $inserted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }
            
            /*
            ===========================
            Fetch New Staff Information
            ===========================            
            */
            $staffgroupinfo = new StaffGroupInfo();
            $staffgroupinfo->fetchdatabyname($this->groupname);
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>added</b> staff group <a href="'.ROOT_URL.'admin/staffgroup-edit.php?id='.$staffgroupinfo->id.'"><b>'.$staffgroupinfo->groupname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'staffs', 'staff', ".$userinfo->staffid.", 'added', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center' style='color:gray'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Staff Group <strong>".$staffgroupinfo->groupname."</strong> 
                    was added successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

class UpdateStaffGroup {
    public $id;
    public $groupname;
    public $customeraccountaccess;
    public $locationaccess;    
    public $status;

    function __construct() {
        $this->id = isset($_POST['u_groupid']) ? $_POST['u_groupid'] : null;
        $this->groupname = isset($_POST['u_group_name']) ? $_POST['u_group_name'] : null;
        $this->customeraccountaccess = isset($_POST['u_customer_account_access']) ? "1" : "0";
        $this->locationaccess = isset($_POST['u_location_access']) ? "1" : "0";
        $this->status = isset($_POST['u_group_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->groupname)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            ==================
            Update Staff Group
            ==================            
            */

            try {
                $sqlquery = "UPDATE StaffGroups SET 
                            staff_group_name='".ms_escape_string($this->groupname)."'
                            ,customer_account_access=".$this->customeraccountaccess."
                            ,location_access=".$this->locationaccess."
                            ,staff_group_status=".$this->status."
                            WHERE staff_group_id=".$this->id;
                            

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }           
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> staff group <a href="'.ROOT_URL.'admin/staffgroup-edit.php?id='.$this->id.'"><b>'.$this->groupname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'staffs', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Staff Group <strong>".$this->groupname."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class StaffGroupInfo {
    public $id;
    public $groupname;
    public $customeraccountaccess;
    public $locationaccess;    
    public $status;
    public $numrows;

    function fetchdatabyid($groupid) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_StaffGroups WHERE staff_group_id=$groupid";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            // $this->id = (int) $row['staff_group_id'];
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = $row['customer_account_access'];
            $this->locationaccess = $row['strict_location'];
            $this->status = $row['staff_group_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyname($groupname) {
        $count=0;
        
        $sqlquery = "SELECT * FROM vw_StaffGroups WHERE staff_group_name='$groupname'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['staff_group_id'];
            $this->groupname = $row['staff_group_name'];
            $this->customeraccountaccess = $row['customer_account_access'];
            $this->locationaccess = $row['strict_location'];
            $this->status = $row['staff_group_status'];            
            $count++;
        }        
        $this->numrows= $count;
    }

}

class RegisterMember {
    private $firstname;
    private $lastname;
    private $emailaddress;
    private $middleinitial;
    private $suffix;
    private $nickname;
    private $gender;
    private $birthdate;
    private $yearsofstay;
    private $address;
    private $villageresidence;
    private $homephone;
    private $mobilephone;
    private $businessphone;
    private $companyname;
    private $memberimage;
    private $acceptterms;

    function __construct() {

        $this->firstname = isset($_POST['FirstName']) ? $_POST['FirstName'] : null;
        $this->lastname = isset($_POST['LastName']) ? $_POST['LastName'] : null;
        $this->emailaddress = isset($_POST['EmailAddress']) ? $_POST['EmailAddress'] : null;
        $this->middleinitial = isset($_POST['MiddleName']) ? $_POST['MiddleName'] : null;
        $this->suffix = isset($_POST['Suffix']) ? $_POST['Suffix'] : null;
        $this->nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;
        $this->gender = isset($_POST['Gender']) ? $_POST['Gender'] : null;
        $this->birthdate = isset($_POST['Birthdate']) ? $_POST['Birthdate'] : null;
        $this->yearsofstay = isset($_POST['YearsOfStay']) ? $_POST['YearsOfStay'] : null;
        $this->address = isset($_POST['Address']) ? $_POST['Address'] : null;
        $this->villageresidence = isset($_POST['VillageResidence']) ? $_POST['VillageResidence'] : null;
        $this->homephone = isset($_POST['HomePhone']) ? $_POST['HomePhone'] : null;
        $this->mobilephone = isset($_POST['MobilePhone']) ? $_POST['MobilePhone'] : null;
        $this->businessphone = isset($_POST['BusinessPhone']) ? $_POST['BusinessPhone'] : null;
        $this->companyname = isset($_POST['CompanyName']) ? $_POST['CompanyName'] : null;
        $this->memberimage = isset($_POST['memberimage']) ? $_POST['memberimage'] : null;
        $this->acceptterms = isset($_POST['AcceptTerms']) ? "1" : "0";
    }

    function start() {
        if (empty($this->firstname) || empty($this->lastname) || empty($this->emailaddress)) {
            // throw new Exception("Empty Post not allowed");
            echo "Empty Post not allowed";
        }

        else
        {
            // Save and Fetch Company Info            
            $companyid=null;

            if(!empty($this->companyname))
            {
                $registerCompany = new RegisterCompany();
                $registerCompany->start();

                /* TODO: check numrow then register if 0 */
                $companyInfo = new CompanyInfo();
                $companyInfo->fetchdatabyname($_POST["CompanyName"]);  
                $companyid = $companyInfo->id;
            }
            
            // $hello=(!empty($this->firstname))?("'".ms_escape_string($this->firstname)."'"):'null';
            // die($hello);

            /* Append and Fetch Member Info */
            $sqlquery="INSERT INTO Members(
                            [first_name]
                            ,[last_name]
                            ,[middle_initial]
                            ,[nick_name]
                            ,[suffix]
                            ,[gender]
                            ,[birthdate]
                            ,[years_of_stay]
                            ,[home_phone]
                            ,[mobile_phone]
                            ,[business_phone]
                            ,[member_email]
                            ,[company_id]
                            ,[accept_terms]
                            ,[member_group_id]
                            ,[ip_address]
                            ,[user_agent]
                            ,[member_status])
                        SELECT 
                            '".ms_escape_string($this->firstname)."'
                            ,'".ms_escape_string($this->lastname)."'
                            ,".((!empty($this->middleinitial))?("'".ms_escape_string($this->middleinitial)."'"):"NULL")
                            .",".((!empty($this->nickname))?("'".ms_escape_string($this->nickname)."'"):"NULL")
                            .",".((!empty($this->suffix))?("'".ms_escape_string($this->suffix)."'"):"NULL")
                            .",".((!empty($this->gender))?("'".ms_escape_string($this->gender)."'"):"NULL")
                            .",".((!empty($this->birthdate))?("'".ms_escape_string($this->birthdate)."'"):"NULL")
                            .",".((!empty($this->yearsofstay))?("'".$this->yearsofstay."'"):"NULL")
                            .",".((!empty($this->homephone))?("'".ms_escape_string($this->homephone)."'"):"NULL")
                            .",".((!empty($this->mobilephone))?("'".ms_escape_string($this->mobilephone)."'"):"NULL")
                            .",".((!empty($this->businessphone))?("'".ms_escape_string($this->businessphone)."'"):"NULL")
                            .",".((!empty($this->emailaddress))?("'".ms_escape_string($this->emailaddress)."'"):"NULL")
                            .",".((!empty($companyid))?$companyid:"NULL")
                            .",".$this->acceptterms
                            .",1
                            ,'".IPAddress."'
                            ,'".UserAgent."'
                            ,1
                        WHERE '".$this->emailaddress."' NOT IN(SELECT DISTINCT member_email from Members)";
                        // AND '".$this->lastname."' NOT IN(SELECT DISTINCT last_name FROM Members)";

            
            $inserted = mssql_executequery($sqlquery);
            // die($sqlquery);
            $memberInfo = new MemberInfo();
            $memberInfo->fetchdatabyemail($this->emailaddress);              

            if (!empty($memberInfo->id))
            {
                /* Activity log */  
                $ActivityMessage = '<a href="'.ROOT_URL.'admin/members/edit?id='.$memberInfo->id.'">'.ms_escape_string($memberInfo->fullname).'</a> has submitted an <b>application.</b>';
                $sqlquery="EXEC sp_s_Activities 'main', 'personaldata', 'member', ".$memberInfo->id.", 'submitted', '$ActivityMessage'";
                // die($sqlquery);
                $rows = mssql_resultset($sqlquery);            
                foreach ($rows as $row) {        
                    $ResultStatus = (int) $row['Result'];
                    $ResultMessage = $row['ResultMessage'];        
                }

                if ($ResultStatus==0)
                    die($ResultMessage);
                
                /* APPEND AND FETCH ADDRESS */
                $addressid=null;

                if(!empty($this->address))
                {
                    $sqlquery="INSERT INTO Addresses(member_id, address_address1, address_address2, address_country_id)
                                SELECT ".$memberInfo->id."
                                ,".((!empty($this->address))?("'".ms_escape_string($this->address)."'"):"NULL")."
                                ,".((!empty($this->villageresidence))?("'".ms_escape_string($this->villageresidence)."'"):"NULL")."
                                ,159";
                    $inserted = mssql_executequery($sqlquery);
                }

                $addressInfo = new AddressInfo();
                $addressInfo->fetchdatabymemberid($memberInfo->id);

                /* UPDATE Member's Address ID from main table */
                $sqlquery="UPDATE Members SET address_id=".$addressInfo->id." WHERE member_id=".$memberInfo->id;
                $updated = mssql_executequery($sqlquery);

                /* APPEND Member's Educational Background */
                $registerEducations = new RegisterEducations();
                $registerEducations->appenddatabymemberid($memberInfo->id);

                /* APPEND Member's Employment Data */
                $registerEmployments = new RegisterEmployments();
                foreach (array_values($_POST['employername']) as $index => $value) {                    
                    if (!empty($value))
                        $registerEmployments->appenddatabymemberid($memberInfo->id,$index);                                        
                }

                /* APPEND Member's Presonal References */
                $registerReferences = new RegisterReferences();
                foreach (array_values($_POST['mreferencename']) as $index => $value) {                    
                        // echo "$index : $value";
                    if (!empty($value))
                        $registerReferences->appenddatabymemberid($memberInfo->id,$index);                                        
                }

                /* APPEND Member's Organizations */
                $registerOrganizations = new RegisterOrganizations();
                foreach (array_values($_POST['organizationname']) as $index => $value) {                    
                        // echo "$index : $value";
                    if (!empty($value))
                        $registerOrganizations->appenddatabymemberid($memberInfo->id,$index);                                        
                }

                /* APPEND Member's Questionaire Answers */
                $registerAnswers = new RegisterAnswers();
                $registerAnswers->appenddatabymemberid($memberInfo->id);

                /* APPEND Payment Information */
                $registerPayments = new RegisterPayments();
                $registerPayments->appenddatabymemberid($memberInfo->id);                

                
                /* Return MemberID for client side manipulation */
                echo ($memberInfo->id);
                
            }


            

            // fnPostToHTML();
            
            // echo " Registration Done";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }

    function updatesinglefieldbymemberid($memberid,$field,$value) {

        $count=0;
        $sqlquery = "UPDATE Members SET $field='".ms_escape_string($value)."' WHERE member_id=$memberid";
                        

        // echo ($sqlquery."<br><br>");

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/member/edit?id='.$memberid.'">Member #'.$memberid.'</a> updated<b> personal data.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'members', 'member', $memberid, 'modify', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
        }
        
    } 
}


Class MemberInfo {

    public $fullname
            ,$firstname
            ,$lastname
            ,$emailaddress
            ,$middleinitial
            ,$suffix
            ,$nickname
            ,$gender
            ,$birthdate
            ,$yearsofstay
            ,$address
            ,$villageresidence
            ,$homephone
            ,$mobilephone
            ,$businessphone
            ,$companyname
            ,$numrows;

    function fetchdatabyemail($email) {
        $count=0;
        
        $sqlquery = "SELECT M.member_id AS id
                        ,[first_name]
                        ,[last_name]
                        ,[middle_initial]
                        ,[nick_name]
                        ,[suffix]
                        ,[gender]
                        ,[birthdate]
                        ,[address_address1] AS address
                        ,[years_of_stay]
                        ,[home_phone]
                        ,[mobile_phone]
                        ,[business_phone]
                        ,[member_email]
                        ,[company_name]
                        ,[accept_terms]
        FROM Members M        
        LEFT JOIN Companies C ON C.company_id=M.company_id
        LEFT JOIN Addresses A ON A.address_id=M.address_id AND A.member_id=M.member_id
        WHERE UPPER(LTRIM(RTRIM(member_email)))=UPPER('".ms_escape_string($email)."')";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];            
            $this->firstname = $row['first_name'];
            $this->lastname = $row['last_name'];
            $this->middleinitial = $row['middle_initial'];
            $this->emailaddress = $row['member_email'];
            $this->nickname = $row['nick_name'];
            $this->suffix = $row['suffix'];
            $this->gender = $row['gender'];
            $this->birthdate = $row['birthdate'];
            $this->address = $row['address'];
            $this->yearsofstay = $row['years_of_stay'];
            $this->homephone = $row['home_phone'];
            $this->mobilephone = $row['mobile_phone'];
            $this->businessphone = $row['business_phone'];
            $this->companyname = $row['company_name'];
            $count++;
        }
        $this->fullname=$this->firstname.' '.$this->lastname;
        $this->numrows= $count;
    }

}


class RegisterCompany {
    private $companyName;
    private $companyWebsite;
    private $companyAddress;
    private $companyFax;
    

    function __construct() {
        $this->companyName = isset($_POST['CompanyName']) ? $_POST['CompanyName'] : null;
        $this->companyWebsite = isset($_POST['CompanyWebsite']) ? $_POST['CompanyWebsite'] : null;
        $this->companyAddress = isset($_POST['CompanyAddress']) ? $_POST['CompanyAddress'] : null;
        $this->companyFax = isset($_POST['CompanyFax']) ? $_POST['CompanyFax'] : null;
    }

    function start() {

        if (empty($this->companyName)) {
            // throw new Exception("Empty Post not allowed");
            // return 0;
            echo "Empty Post not allowed";
        } else {                        
            /* 
            ** APPEND TO Companies 
            */
            try {
                $sqlquery="INSERT INTO Companies(company_name,company_address1,company_fax,company_website)
                            SELECT 
                            '".ms_escape_string($this->companyName)."'
                            , '".ms_escape_string($this->companyAddress)."'
                            , '".ms_escape_string($this->companyFax)."'
                            , '".ms_escape_string($this->companyWebsite)."'
                            WHERE '".$this->companyName."' NOT IN(SELECT DISTINCT company_name FROM Companies)";

                $inserted = mssql_executequery($sqlquery);

                if ($inserted==200) {
                    $sqlquery = "SELECT company_id AS id, 'success' AS [text] FROM Companies WHERE UPPER(LTRIM(RTRIM(company_name)))=UPPER('".ms_escape_string($this->companyName)."')";
                    $rows = mssql_resultset($sqlquery);
                    foreach ($rows as $row) {               
                        $companyID = (int) $row['id'];
                        $ResultMessage = $row['text'];        
                    }
                }

                if ($companyID==0)
                    die($ResultMessage);
                else {
                    /* Activity log */  
                    $ActivityMessage = '<a href="'.ROOT_URL.'admin/company/edit?id='.$companyID.'">'.ms_escape_string($this->companyName).'</a> has been <b>registered.</b>';
                    $sqlquery="EXEC sp_s_Activities 'main', 'company', 'guest', 0, 'registered', '$ActivityMessage'";
                    // die($sqlquery);
                    $rows = mssql_resultset($sqlquery);            
                    foreach ($rows as $row) {        
                        $ResultStatus = (int) $row['Result'];
                        $ResultMessage = $row['ResultMessage'];        
                    }

                    if ($ResultStatus==0)
                        die($ResultMessage);
                }

            } catch(Exception $e) {
                echo "<pre>";
                    print_r($_POST);
                    print_r($e);
                // if (defined('DEBUG') && DEBUG) {
                // } else {
                //     echo $ErrorMessage;
                // }
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }  

            // return 1;

        }//end if       
            
    }
}


Class CompanyInfo {

    public $id,$name,$address,$fax,$numrows;

    function fetchdatabyname($name) {
        $count=0;
        $sqlquery = "SELECT company_id AS id, company_name, company_address1, company_fax FROM Companies WHERE UPPER(LTRIM(RTRIM(company_name)))=UPPER('".ms_escape_string($name)."')";
        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];            
            $this->name = $row['company_name'];
            $this->address = $row['company_address1'];
            $this->fax = $row['company_fax'];
            $count++;
        }
        $this->numrows= $count;
    }

}

Class AddressInfo {

    public $id,$address1,$address2,$city,$state,$postcode,$country,$isprimary,$numrows;

    function fetchdatabyemail($email) {
        $count=0;
        $sqlquery = "SELECT 
                        A.address_id AS id
                        ,address_address1 AS address1
                        ,address_address2 AS address2
                        ,address_city AS city
                        ,address_state AS state
                        ,address_postcode AS postcode
                        ,C.nice_name AS country
                        ,ISNULL(is_primary,0) AS isprimary
                    FROM Members M
                    LEFT JOIN Addresses A ON A.member_id=M.member_id
                    LEFT JOIN Countries C ON C.country_id=A.address_country_id
                    WHERE member_email='".$email."'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];            
            $this->address1 = $row['address1'];
            $this->address2 = $row['address2'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->postcode = $row['postcode'];
            $this->country = $row['country'];
            $this->isprimary = $row['isprimary'];            
            $count++;
        }
        $this->numrows= $count;
    }

    function fetchdatabymemberid($memberid) {
        $count=0;
        $sqlquery = "SELECT 
                        A.address_id AS id
                        ,address_address1 AS address1
                        ,address_address2 AS address2
                        ,address_city AS city
                        ,address_state AS state
                        ,address_postcode AS postcode
                        ,C.nice_name AS country
                        ,ISNULL(is_primary,0) AS isprimary
                    FROM Members M
                    LEFT JOIN Addresses A ON A.member_id=M.member_id
                    LEFT JOIN Countries C ON C.country_id=A.address_country_id
                    WHERE M.member_id=".$memberid;

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];            
            $this->address1 = $row['address1'];
            $this->address2 = $row['address2'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->postcode = $row['postcode'];
            $this->country = $row['country'];
            $this->isprimary = $row['isprimary'];            
            $count++;
        }
        $this->numrows= $count;
    }

}


Class RegisterEducations {

    public $id;
    public $highschoolname;
    public $highschooldates;
    public $highschooladdress;
    public $highschooldegree;
    public $hsgraddate;
    public $institutionname;
    public $institutiondates;
    public $institutionaddress;
    public $institutiondegree;
    public $institutionmajor;
    public $institution1name;
    public $institution1dates;
    public $institution1address;
    public $institution1degree;
    public $institution1major;

    function __construct() {
        $this->highschoolname = isset($_POST['highschoolname']) ? $_POST['highschoolname'] : null;
        $this->highschooldates = isset($_POST['highschooldates']) ? $_POST['highschooldates'] : null;
        $this->highschooladdress = isset($_POST['highschooladdress']) ? $_POST['highschooladdress'] : null;
        $this->highschooldegree = isset($_POST['highschooldegree']) ? $_POST['highschooldegree'] : null;
        $this->hsgraddate = isset($_POST['hsgraddate']) ? $_POST['hsgraddate'] : null;
        $this->institutionname = isset($_POST['institutionname']) ? $_POST['institutionname'] : null;
        $this->institutiondates = isset($_POST['institutiondates']) ? $_POST['institutiondates'] : null;
        $this->institutionaddress = isset($_POST['institutionaddress']) ? $_POST['institutionaddress'] : null;
        $this->institutiondegree = isset($_POST['institutiondegree']) ? $_POST['institutiondegree'] : null;
        $this->institutionmajor = isset($_POST['institutionmajor']) ? $_POST['institutionmajor'] : null;
        $this->institution1name = isset($_POST['institution1name']) ? $_POST['institution1name'] : null;
        $this->institution1dates = isset($_POST['institution1dates']) ? $_POST['institution1dates'] : null;
        $this->institution1address = isset($_POST['institution1address']) ? $_POST['institution1address'] : null;
        $this->institution1degree = isset($_POST['institution1degree']) ? $_POST['institution1degree'] : null;
        $this->institution1major = isset($_POST['institution1major']) ? $_POST['institution1major'] : null;        
    }

    function appenddatabymemberid($memberid) {
        $count=0;
        $sqlquery = "INSERT INTO MemberEducations(
                        member_id
                        ,high_school_name 
                        ,high_school_dates
                        ,high_school_address
                        ,high_school_degree
                        ,hs_grad_date
                        ,institution_name
                        ,institution_dates
                        ,institution_address
                        ,institution_degree
                        ,institution_major
                        ,institution1_name
                        ,institution1_dates
                        ,institution1_address
                        ,institution1_degree
                        ,institution1_major
                    )
                    SELECT 
                        ".$memberid."
                        ,".((!empty($this->highschoolname))?("'".ms_escape_string($this->highschoolname)."'"):"NULL")
                        .",".((!empty($this->highschooldates))?("'".ms_escape_string($this->highschooldates)."'"):"NULL")
                        .",".((!empty($this->highschooladdress))?("'".ms_escape_string($this->highschooladdress)."'"):"NULL")                        
                        .",".((!empty($this->highschooldegree))?("'".ms_escape_string($this->highschooldegree)."'"):"NULL")
                        .",".((!empty($this->hsgraddate))?("'".ms_escape_string($this->hsgraddate)."'"):"NULL")
                        .",".((!empty($this->institutionname))?("'".ms_escape_string($this->institutionname)."'"):"NULL")
                        .",".((!empty($this->institutiondates))?("'".ms_escape_string($this->institutiondates)."'"):"NULL")
                        .",".((!empty($this->institutionaddress))?("'".ms_escape_string($this->institutionaddress)."'"):"NULL")
                        .",".((!empty($this->institutiondegree))?("'".ms_escape_string($this->institutiondegree)."'"):"NULL")
                        .",".((!empty($this->institutionmajor))?("'".ms_escape_string($this->institutionmajor)."'"):"NULL")
                        .",".((!empty($this->institution1name))?("'".ms_escape_string($this->institution1name)."'"):"NULL")
                        .",".((!empty($this->institution1dates))?("'".ms_escape_string($this->institution1dates)."'"):"NULL")
                        .",".((!empty($this->institution1address))?("'".ms_escape_string($this->institution1address)."'"):"NULL")
                        .",".((!empty($this->institution1degree))?("'".ms_escape_string($this->institution1degree)."'"):"NULL")
                        .",".((!empty($this->institution1major))?("'".ms_escape_string($this->institution1major)."'"):"NULL");

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            $sqlquery = "SELECT education_id AS id, 'success' AS [text] FROM MemberEducations WHERE member_id=$memberid";
            $rows = mssql_resultset($sqlquery);
            $educationid=0;
            foreach ($rows as $row) {               
                $educationid = (int) $row['id'];
                $ResultMessage = $row['text'];        
            }
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/education/edit?id='.$educationid.'">'.ms_escape_string($this->highschoolname).'</a> has been <b>added.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'education', 'member', $memberid, 'registered', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
        }
        
    }    
    

}

Class MemberEducationInfo {

    private $id;
    private $highschoolname;
    private $highschooldates;
    private $highschooladdress;
    private $highschooldegree;
    private $hsgraddate;
    private $institutionname;
    private $institutiondates;
    private $institutionaddress;
    private $institutiondegree;
    private $institutionmajor;
    private $institution1name;
    private $institution1dates;
    private $institution1address;
    private $institution1degree;
    private $institution1major;
    private $numrows;

    function fetchdatabymemberid($memberid) {
        $count=0;
        $sqlquery = "SELECT 
                        education_id AS id
                        ,high_school_name 
                        ,high_school_dates
                        ,high_school_address
                        ,high_school_degree
                        ,hs_grad_date
                        ,institution_name
                        ,institution_dates
                        ,institution_address
                        ,institution_degree
                        ,institution_major
                        ,institution1_name
                        ,institution1_dates
                        ,institution1_address
                        ,institution1_degree
                        ,institution1_major
                    FROM MemberEducations
                    WHERE member_id=$memberid";
        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];
            $this->highschoolname = $row['high_school_name'];
            $this->highschooldates = $row['high_school_dates'];
            $this->highschooladdress = $row['high_school_address'];
            $this->highschooldegree = $row['high_school_degree'];
            $this->hsgraddate = $row['hs_grad_date'];
            $this->institutionname = $row['institution_name'];
            $this->institutiondates = $row['institution_dates'];
            $this->institutionaddress = $row['institution_address'];
            $this->institutiondegree = $row['institution_degree'];
            $this->institutionmajor = $row['institution_major'];
            $this->institution1name = $row['institution1_name'];
            $this->institution1dates = $row['institution1_dates'];
            $this->institution1address = $row['institution1_address'];
            $this->institution1degree = $row['institution1_degree'];
            $this->institution1major = $row['institution1_major'];            
            $count++;
        }
        $this->numrows= $count;
    }

}

/* Employment Data */
Class RegisterEmployments {

    public $id;
    public $employername;
    public $employeraddress;
    public $jobtitle;
    public $employmentstartdate;
    public $employmenttermdate;
    public $employmentduties;
    public $reasonforleaving;

    function __construct() {        
        $this->employername = isset($_POST['employername']) ? $_POST['employername'] : null;
        $this->employeraddress = isset($_POST['employeraddress']) ? $_POST['employeraddress'] : null;
        $this->jobtitle = isset($_POST['jobtitle']) ? $_POST['jobtitle'] : null;
        $this->employmentstartdate = isset($_POST['employmentstartdate']) ? $_POST['employmentstartdate'] : null;
        $this->employmenttermdate = isset($_POST['employmenttermdate']) ? $_POST['employmenttermdate'] : null;
        $this->employmentduties = isset($_POST['employmentduties']) ? $_POST['employmentduties'] : null;
        $this->reasonforleaving = isset($_POST['reasonforleaving']) ? $_POST['reasonforleaving'] : null;
    }

    function appenddatabymemberid($memberid,$index) {
        
        /* Guide to accessing array */
        // echo "<pre>";
        // print_r($this->employername);
        // echo($this->employername[0]);        
        // echo "</pre>";
        // die();

        $count=0;
        $sqlquery = "INSERT INTO MemberEmployments(
                        member_id
                        ,employer_name
                        ,employer_address
                        ,job_title
                        ,employment_startdate
                        ,employment_termdate
                        ,employment_duties
                        ,reason_for_leaving
                    )
                    SELECT 
                        ".$memberid."                        
                        ,".((!empty($this->employername[$index]))?("'".ms_escape_string($this->employername[$index])."'"):"NULL")
                        .",".((!empty($this->employeraddress[$index]))?("'".ms_escape_string($this->employeraddress[$index])."'"):"NULL")
                        .",".((!empty($this->jobtitle[$index]))?("'".ms_escape_string($this->jobtitle[$index])."'"):"NULL")
                        .",".((!empty($this->employmentstartdate[$index]))?("'".ms_escape_string($this->employmentstartdate[$index])."'"):"NULL")
                        .",".((!empty($this->employmenttermdate[$index]))?("'".ms_escape_string($this->employmenttermdate[$index])."'"):"NULL")
                        .",".((!empty($this->employmentduties[$index]))?("'".ms_escape_string($this->employmentduties[$index])."'"):"NULL")
                        .",".((!empty($this->reasonforleaving[$index]))?("'".ms_escape_string($this->employername[$index])."'"):"NULL");                        

        // echo ($sqlquery."<br><br>");

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            $sqlquery = "SELECT TOP 1 employment_id AS id, 'success' AS [text] FROM MemberEmployments WHERE member_id=$memberid ORDER BY employment_id DESC";
            $rows = mssql_resultset($sqlquery);
            $employmentid=0;
            foreach ($rows as $row) {               
                $employmentid = (int) $row['id'];
                $ResultMessage = $row['text'];        
            }
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/employment/edit?id='.$employmentid.'">'.ms_escape_string($this->employername[$index]).'</a> has been <b>added to Employment.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'employment', 'member', $memberid, 'registered', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
        }
        
    }    
    

}

/* Personal References */
Class RegisterReferences {

    public $id;
    public $mreferencename;
    public $mreferencetitle;
    public $mreferenceemail;
    public $mreferencephone;
    public $relationtoapplicant;
    public $mreferencedocument;

    function __construct() {        
        $this->mreferencename = isset($_POST['mreferencename']) ? $_POST['mreferencename'] : null;
        $this->mreferencetitle = isset($_POST['mreferencetitle']) ? $_POST['mreferencetitle'] : null;
        $this->relationtoapplicant = isset($_POST['relationtoapplicant']) ? $_POST['relationtoapplicant'] : null;
        $this->mreferenceemail = isset($_POST['mreferenceemail']) ? $_POST['mreferenceemail'] : null;
        $this->mreferencephone = isset($_POST['mreferencephone']) ? $_POST['mreferencephone'] : null;
        $this->mreferencedocument = isset($_POST['mreferencedocument']) ? $_POST['mreferencedocument'] : null;        
    }

    function appenddatabymemberid($memberid,$index) {
        
        /* Guide to accessing array */
        // echo "<pre>";
        // print_r($this->employername);
        // echo($this->employername[0]);        
        // echo "</pre>";
        // die();

        $count=0;
        $sqlquery = "INSERT INTO MemberReferences(
                        member_id
                        ,m_reference_name
                        ,m_reference_title
                        ,m_reference_email
                        ,m_reference_phone
                        ,relation_to_applicant
                        ,m_reference_document
                    )
                    SELECT 
                        ".$memberid."                        
                        ,".((!empty($this->mreferencename[$index]))?("'".ms_escape_string($this->mreferencename[$index])."'"):"NULL")
                        .",".((!empty($this->mreferencetitle[$index]))?("'".ms_escape_string($this->mreferencetitle[$index])."'"):"NULL")
                        .",".((!empty($this->mreferenceemail[$index]))?("'".ms_escape_string($this->mreferenceemail[$index])."'"):"NULL")
                        .",".((!empty($this->mreferencephone[$index]))?("'".ms_escape_string($this->mreferencephone[$index])."'"):"NULL")
                        .",".((!empty($this->relationtoapplicant[$index]))?("'".ms_escape_string($this->relationtoapplicant[$index])."'"):"NULL")
                        .",".((!empty($this->mreferencedocument[$index]))?("'".ms_escape_string($this->mreferencedocument[$index])."'"):"NULL");
                        

        // echo ($sqlquery."<br><br>");

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            $sqlquery = "SELECT TOP 1 m_reference_id AS id, 'success' AS [text] FROM MemberReferences WHERE member_id=$memberid ORDER BY m_reference_id DESC";
            $rows = mssql_resultset($sqlquery);
            $referenceid=0;
            foreach ($rows as $row) {               
                $referenceid = (int) $row['id'];
                $ResultMessage = $row['text'];        
            }
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/reference/edit?id='.$referenceid.'">'.ms_escape_string($this->mreferencename[$index]).'</a> has been <b>added as personal reference.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'reference', 'member', $memberid, 'registered', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
        }
        
    }  

}


/* Member's Organizations */
Class RegisterOrganizations {

    public $id;
    public $organizationname;
    public $organizationaddress;
    public $officepositionheld;
    public $membershipdates;
    public $dutiesprojects;
    public $monthlyhoursserviced;

    function __construct() {        
        $this->organizationname = isset($_POST['organizationname']) ? $_POST['organizationname'] : null;
        $this->organizationaddress = isset($_POST['organizationaddress']) ? $_POST['organizationaddress'] : null;
        $this->officepositionheld = isset($_POST['officepositionheld']) ? $_POST['officepositionheld'] : null;
        $this->membershipdates = isset($_POST['membershipdates']) ? $_POST['membershipdates'] : null;
        $this->dutiesprojects = isset($_POST['dutiesprojects']) ? $_POST['dutiesprojects'] : null;
        $this->monthlyhoursserviced = isset($_POST['monthlyhoursserviced']) ? $_POST['monthlyhoursserviced'] : null;        
    }

    function appenddatabymemberid($memberid,$index) {
        
        /* Guide to accessing array */
        // echo "<pre>";
        // print_r($this->employername);
        // echo($this->employername[0]);        
        // echo "</pre>";
        // die();

        $count=0;
        $sqlquery = "INSERT INTO MemberOrganizations(
                        member_id
                        ,organization_name
                        ,organization_address
                        ,office_position_held
                        ,membership_dates
                        ,duties_projects
                        ,monthly_hours_serviced
                    )
                    SELECT 
                        ".$memberid."                        
                        ,".((!empty($this->organizationname[$index]))?("'".ms_escape_string($this->organizationname[$index])."'"):"NULL")
                        .",".((!empty($this->organizationaddress[$index]))?("'".ms_escape_string($this->organizationaddress[$index])."'"):"NULL")
                        .",".((!empty($this->officepositionheld[$index]))?("'".ms_escape_string($this->officepositionheld[$index])."'"):"NULL")
                        .",".((!empty($this->membershipdates[$index]))?("'".ms_escape_string($this->membershipdates[$index])."'"):"NULL")
                        .",".((!empty($this->dutiesprojects[$index]))?("'".ms_escape_string($this->dutiesprojects[$index])."'"):"NULL")
                        .",".((!empty($this->monthlyhoursserviced[$index]))?("'".ms_escape_string($this->monthlyhoursserviced[$index])."'"):"NULL");
                        

        // echo ($sqlquery."<br><br>");

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            $sqlquery = "SELECT TOP 1 organization_id AS id, 'success' AS [text] FROM MemberOrganizations WHERE member_id=$memberid ORDER BY organization_id DESC";
            $rows = mssql_resultset($sqlquery);
            $organizationid=0;
            foreach ($rows as $row) {               
                $organizationid = (int) $row['id'];
                $ResultMessage = $row['text'];        
            }
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/organization/edit?id='.$organizationid.'">'.ms_escape_string($this->organizationname[$index]).'</a> has been <b>added organization.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'organization', 'member', $memberid, 'registered', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
        }
        
    }  

}


/* Questionaires */
Class RegisterAnswers {
    private $question_1;
    private $question_2;
    private $question_3;
    private $question_4;
    private $question_5; // checkbox / switch
    private $question_6; // checkbox / switch
    private $question_7; // checkbox / switch
    private $question_8; // checkbox / switch
    private $question_9;

    function __construct() {
        $this->question_1 = isset($_POST['question_id_1']) ? $_POST['question_id_1'] : null;
        $this->question_2 = isset($_POST['question_id_2']) ? $_POST['question_id_2'] : null;
        $this->question_3 = isset($_POST['question_id_3']) ? $_POST['question_id_3'] : null;
        $this->question_4 = isset($_POST['question_id_4']) ? $_POST['question_id_4'] : null;
        $this->question_5 = isset($_POST['question_id_5']) ? "'yes'" : "'no'";
        $this->question_6 = isset($_POST['question_id_6']) ? "'yes'" : "'no'";
        $this->question_7 = isset($_POST['question_id_7']) ? "'yes'" : "'no'";
        $this->question_8 = isset($_POST['question_id_8']) ? "'yes'" : "'no'";        
        $this->question_9 = isset($_POST['question_id_9']) ? $_POST['question_id_9'] : null;
        
        
    }

    function appenddatabymemberid($memberid) {

        $sqlquery = "INSERT INTO MemberAnswers(
                        member_id
                        ,qustion_id
                        ,member_answer                        
                    ) VALUES 
                    ($memberid,1,".((!empty($this->question_1))?("'".ms_escape_string($this->question_1)."'"):"NULL")."),"
                    ."($memberid,2,".((!empty($this->question_2))?("'".ms_escape_string($this->question_2)."'"):"NULL")."),"
                    ."($memberid,3,".((!empty($this->question_3))?("'".ms_escape_string($this->question_3)."'"):"NULL")."),"
                    ."($memberid,4,".((!empty($this->question_4))?("'".ms_escape_string($this->question_4)."'"):"NULL")."),"
                    ."($memberid,5,".$this->question_5."),"
                    ."($memberid,6,".$this->question_6."),"
                    ."($memberid,7,".$this->question_7."),"
                    ."($memberid,8,".$this->question_8."),"
                    ."($memberid,9,".((!empty($this->question_9))?("'".ms_escape_string($this->question_9)."'"):"NULL").")";
                        
        // echo "<pre>";
        // print_r($_POST);        
        // echo "</pre>";
        // die($sqlquery);        

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/member/edit?id='.$memberid.'">Member #'.$memberid.'</a> answered<b> a questions.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'question', 'member', ".$memberid.", 'submitted', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
            
        }     
            
    }
}

/* Payments */
Class RegisterPayments {
    private $billtoemployer;
    private $representativename;
    private $representativetitle;
    private $representativephone;
    private $amount;
    private $statusid;

    function __construct() {
        $this->billtoemployer = isset($_POST['billtoemployer']) ? "1" : "0";
        $this->representativename = isset($_POST['representativename']) ? $_POST['representativename'] : null;                
        $this->representativetitle = isset($_POST['representativetitle']) ? $_POST['representativetitle'] : null;                
        $this->representativephone = isset($_POST['representativephone']) ? $_POST['representativephone'] : null;                
        $this->amount = isset($_POST['amount']) ? $_POST['amount'] : "500.00";                
        $this->statusid = isset($_POST['statusid']) ? $_POST['statusid'] : null;                
    }

    function appenddatabymemberid($memberid) {

        $sqlquery = "INSERT INTO MemberPayments(
                        member_id
                        ,bill_to_employer
                        ,representative_name
                        ,representative_title
                        ,representative_phone
                        ,amount
                        ,status_id
                    )  
                    SELECT 
                        ".$memberid."                        
                        ,".$this->billtoemployer
                        .",".((!empty($this->representativename))?("'".ms_escape_string($this->representativename)."'"):"NULL")
                        .",".((!empty($this->representativetitle))?("'".ms_escape_string($this->representativetitle)."'"):"NULL")
                        .",".((!empty($this->representativephone))?("'".ms_escape_string($this->representativephone)."'"):"NULL")
                        .",".$this->amount
                        .",".((!empty($this->statusid))?$this->status_id:"NULL");
                    
        // die($sqlquery)                        ;
        // echo "<pre>";
        // print_r($_POST);        
        // echo "</pre>";
        // die($sqlquery);        

        $inserted = mssql_executequery($sqlquery);

        if ($inserted==200) {
            /* Activity log */  
            $ActivityMessage = '<a href="'.ROOT_URL.'admin/member/edit?id='.$memberid.'">Member #'.$memberid.'</a> added<b> a payment information.</b>';
            $sqlquery="EXEC sp_s_Activities 'main', 'payment', 'member', ".$memberid.", 'submitted', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);
            
        }     
            
    }
}

/* Send Reservation Notification */
Class ReservationNotification {
    private $sitename;
    private $siteurl;
    private $sitelogo;
    private $fullname;
    private $bookingnumber;
    private $reservationnumber;
    private $locationname;
    private $reservationheadcount;
    private $adultnum;
    private $childrennum;
    private $reservationdate;
    private $reservationtime;
    private $mealname;
    private $signature;    
    private $senderemail;    
    private $sendername;    
    private $sendtomail;    
    private $sendtoname;        
    private $bccemail;    
    private $companyphone;    
    private $statusname;    
    private $statuscomment;    

    function __construct() {
        $this->sitename = $_POST["sitename"];
        $this->siteurl = $_POST["siteurl"];
        $this->senderemail = $_POST["senderemail"];
        $this->sendername = $_POST["sendername"];
        $this->bccemail = $_POST["bccemail"];
        $this->companyphone = (isset($_POST["companyphone"])?$_POST["companyphone"]:"");

        foreach ($_POST["ReservationDetails"] as $result) {              
            $this->sitelogo = ROOT_URL ."assets/images/".((defined('COMPANY_CODE'))?COMPANY_CODE:'default')."-logo-dark-full.png";
            $this->fullname = $result["reservation_name"];                
            $this->bookingnumber = $result["booking_id"];
            $this->reservationnumber = $result["reservation_id"];
            $this->locationname = $result["outlet_name"];                
            $this->reservationheadcount = (isset($result["adult_num"])?$result["adult_num"]:0) + (isset($result["children_num"])?$result["children_num"]:0);
            $this->adultnum = (isset($result["adult_num"])?$result["adult_num"]:0);
            $this->childrennum = (isset($result["children_num"])?$result["children_num"]:0);
            $this->reservationdate = isset($result["reservation_date"]) ? $result["reservation_date"] : null;                
            $this->reservationtime = isset($result["reservation_time"]) ? date_format(date_create($result["reservation_time"]),'g:i A') : null;                
            $this->mealname = $result["mealtime_name"];
            $this->statusname = isset($result["status_name"])?$result["status_name"]:null;
            $this->statuscomment = $result["reservation_remarks"];
            $this->signature = isset($result["CompanyName"]) ? $result["CompanyName"] : null;
            $this->sendtomail = $result["reservation_email"];
            $this->sendtoname = $result["reservation_name"];
            // $this->signature = $result["CompanyName"];
            
        }
        
    }

    function ReserservationConfirmation() {   
     
        // die($this->sitelogo);
        /*
        ===============
        Customer's Copy
        ===============
        */              

        try {                       
            
            $dummyMailRecipient = array();
            
            $Subject =  "Your Reservation Confirmation - ".$this->bookingnumber;

            /* Open and use email template */           
            $templateName = "reservation";
            $emailMessage = file_get_contents(ROOT_URL . 'admin/mail-templates/' . $templateName . '.html');
            // $emailMessage = str_replace('{site_name}', $this->sitename, $emailMessage);            
            $emailMessage = str_replace('{site_name}', $this->signature, $emailMessage);            
            $emailMessage = str_replace('{site_url}', $this->siteurl, $emailMessage);            
            $emailMessage = str_replace('{site_logo}', $this->sitelogo, $emailMessage);            
            $emailMessage = str_replace('{fullname}', $this->fullname, $emailMessage);            
            $emailMessage = str_replace('{booking_number}', $this->bookingnumber, $emailMessage);            
            $emailMessage = str_replace('{location_name}', $this->locationname, $emailMessage);            
            $emailMessage = str_replace('{reservation_guest_no}', $this->reservationheadcount, $emailMessage);            
            $emailMessage = str_replace('{reservation_date}', $this->reservationdate, $emailMessage);
            $emailMessage = str_replace('{reservation_time}', $this->reservationtime, $emailMessage);
            // $emailMessage = str_replace('{meal_time}', $this->mealname, $emailMessage);
            $emailMessage = str_replace('{year}', date("Y"), $emailMessage);
            $emailMessage = str_replace('{signature}', $this->signature, $emailMessage);

            // die($emailMessage);
            $CustomerNotified = sendSMTPMail($this->senderemail,$this->sendername,$this->sendtomail,$this->sendtoname,$dummyMailRecipient,$this->senderemail,$Subject,$emailMessage);

            if ($CustomerNotified) {
                $sqlquery = "UPDATE Bookings SET notify=1 WHERE booking_id=".$this->bookingnumber;
                $updated = mssql_executequery($sqlquery);
                
                $sqlquery = "UPDATE Reservations SET notify=1 WHERE reservation_id=".$this->reservationnumber;                    
                $updated = mssql_executequery($sqlquery);
                

            }
            

        } 
        catch(Exception $e) {   
            echo "<pre>";            
            print_r($e);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
            die();
        }
            
    }

    function ReserservationAlert() {   
     

        /*
        ===============
        Helpdesk Copy
        ===============
        */              

        try {                       
            
            $dummyMailRecipient = array();
            
            $Subject =  "New reservation at ".$this->locationname.". (#".$this->bookingnumber.")";

            /* Open and use email template */           
            $templateName = "reservation-alert";
            $emailMessage = file_get_contents(ROOT_URL . 'admin/mail-templates/' . $templateName . '.html');
            // $emailMessage = str_replace('{site_name}', $this->sitename, $emailMessage);            
            $emailMessage = str_replace('{site_name}', $this->signature, $emailMessage);            
            $emailMessage = str_replace('{site_url}', $this->siteurl, $emailMessage);            
            $emailMessage = str_replace('{site_logo}', $this->sitelogo, $emailMessage);
            $emailMessage = str_replace('{fullname}', $this->fullname, $emailMessage);            
            $emailMessage = str_replace('{booking_number}', $this->bookingnumber, $emailMessage);            
            $emailMessage = str_replace('{location_name}', $this->locationname, $emailMessage);            
            $emailMessage = str_replace('{adult_no}', $this->adultnum, $emailMessage);            
            $emailMessage = str_replace('{children_no}', $this->childrennum, $emailMessage);            
            $emailMessage = str_replace('{reservation_date}', $this->reservationdate, $emailMessage);
            $emailMessage = str_replace('{reservation_time}', $this->reservationtime, $emailMessage);
            // $emailMessage = str_replace('{meal_time}', $this->mealname, $emailMessage);
            $emailMessage = str_replace('{year}', date("Y"), $emailMessage);
            $emailMessage = str_replace('{signature}', $this->signature, $emailMessage);

            // die($emailMessage);
            $HelpdeskNotified = sendSMTPMail($this->senderemail,$this->sendername,$this->senderemail,$this->sendername,$dummyMailRecipient,$this->bccemail,$Subject,$emailMessage);
            

        } 
        catch(Exception $e) {   
            echo "<pre>";            
            print_r($e);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
            die();
        }
            
    }

    function ReserservationUpdate() {        

        /*
        ===============
        Customer's Copy
        ===============
        */              

        try {                       
            
            $dummyMailRecipient = array();
            
            $Subject =  "Your Reservation Update - ".$this->bookingnumber;

            /* Open and use email template */           
            $templateName = "reservation-update";
            $emailMessage = file_get_contents(ROOT_URL . 'admin/mail-templates/' . $templateName . '.html');
            // $emailMessage = str_replace('{site_name}', $this->sitename, $emailMessage);            
            $emailMessage = str_replace('{site_name}', $this->signature, $emailMessage);            
            $emailMessage = str_replace('{site_url}', $this->siteurl, $emailMessage);            
            $emailMessage = str_replace('{site_logo}', $this->sitelogo, $emailMessage);            
            $emailMessage = str_replace('{status_name}', $this->statusname, $emailMessage);            
            $emailMessage = str_replace('{booking_number}', $this->bookingnumber, $emailMessage);            
            $emailMessage = str_replace('{location_name}', $this->locationname, $emailMessage);            
            $emailMessage = str_replace('{status_comment}', ($this->statuscomment)?$this->statuscomment:"N/A", $emailMessage);            
            $emailMessage = str_replace('{year}', date("Y"), $emailMessage);
            $emailMessage = str_replace('{signature}', $this->signature, $emailMessage);

            // die($emailMessage);
            $this->sendtomail = isset($_POST['notify_customer'])?$this->sendtomail:$this->senderemail;
            $this->sendtoname = isset($_POST['notify_customer'])?$this->sendtoname:$this->sendername;

            $CustomerNotified = sendSMTPMail($this->senderemail,$this->sendername,$this->sendtomail,$this->sendtoname,$dummyMailRecipient,$this->senderemail,$Subject,$emailMessage);

            if ($CustomerNotified) {
                $sqlquery = "UPDATE Bookings SET notify=1 WHERE booking_id=".$this->bookingnumber;
                // $sqlquery = "UPDATE Reservations SET notify=1 WHERE reservation_id=".$this->reservationnumber;                    
                

                $updated = mssql_executequery($sqlquery);
            }
            

        } 
        catch(Exception $e) {   
            echo "<pre>";            
            print_r($e);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
            die();
        }
            
    }

    function ReserservationDeclined() {        
       
        try {                       
            
            $dummyMailRecipient = array();
            
            $Subject =  "Your Reservation Update - ".$this->bookingnumber;

            /* Open and use email template */           
            $templateName = "reservation-declined";
            $emailMessage = file_get_contents(ROOT_URL . 'admin/mail-templates/' . $templateName . '.html');
            // $emailMessage = str_replace('{site_name}', $this->sitename, $emailMessage);            
            $emailMessage = str_replace('{site_name}', $this->signature, $emailMessage);            
            $emailMessage = str_replace('{site_url}', $this->siteurl, $emailMessage);            
            $emailMessage = str_replace('{site_logo}', $this->sitelogo, $emailMessage);                        
            $emailMessage = str_replace('{company_number}', $this->companyphone, $emailMessage);            
            $emailMessage = str_replace('{year}', date("Y"), $emailMessage);
            $emailMessage = str_replace('{signature}', $this->signature, $emailMessage);

            $CustomerNotified = sendSMTPMail($this->senderemail,$this->sendername,$this->sendtomail,$this->sendtoname,$dummyMailRecipient,$this->senderemail,$Subject,$emailMessage);

            if ($CustomerNotified) {
                $sqlquery = "UPDATE Bookings SET notify=1 WHERE booking_id=".$this->bookingnumber;
                // $sqlquery = "UPDATE Reservations SET notify=1 WHERE reservation_id=".$this->reservationnumber;                    

                $updated = mssql_executequery($sqlquery);
            }
            

        } 
        catch(Exception $e) {   
            echo "<pre>";            
            print_r($e);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
            die();
        }
            
    }
}

Class ReservationInfo {

    public $id;
    public $bookingid;
    public $reservationtypeid;
    public $reservationtype;
    public $adultnum;
    public $childrennum;
    public $infantnum;
    public $childrenremarks;
    public $infantremarks;
    public $meatnum;
    public $fishnum;
    public $roundid;
    public $reservationdate;
    public $reservationdateend;
    public $reservationdays;
    public $reservationtime;
    public $reservationtimeend;
    public $mealtimeid;
    public $mealtime;
    public $mealtime_for;
    public $mealaddon_breakfast;
    public $mealaddon_lunch;
    public $mealaddon_dinner;
    public $mealaddon_snack_am;
    public $mealaddon_snack_pm;
    public $company;
    public $occassion;
    public $customerid;
    public $customername;
    public $customeremail;
    public $customerphone;
    public $nationalityid;
    public $customernationality;
    public $dateadded;
    public $datemodified;
    public $notify;
    public $ipaddress;
    public $useragent;
    public $outletid;
    public $outletname;
    public $staytime;
    public $outletaddress1;
    public $outletaddress2;
    public $comment;
    public $remarks;
    public $staffid;
    public $staffname;
    public $status;    
    public $numrows;

    function fetchdatabybookingid($bookingid) {
        $count=0;
        $sqlquery = "SELECT TOP 1 * FROM vw_ReservationInfo WHERE booking_id=$bookingid ORDER BY reservation_id ASC";
        // die($sqlquery);
        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = $row['reservation_id'];
            $this->bookingid = $row['booking_id'];
            $this->reservationtypeid = $row['reservationtype_id'];
            $this->reservationtype = $row['reservationtype_description'];
            $this->adultnum = $row['adult_num'];
            $this->childrennum = $row['children_num'];
            $this->infantnum = $row['infant_num'];
            $this->childrenremarks = $row['children_remarks'];
            $this->infantremarks = $row['infant_remarks'];
            $this->meatnum = $row['meat_num'];
            $this->fishnum = $row['fish_num'];
            $this->roundid = $row['round_id'];
            $this->reservationdate = $row['reservation_date'];
            $this->reservationdateend = $row['reservation_date_end'];
            $this->reservationdays = $row['reservation_days'];
            $this->reservationtime = $row['reservation_time'];
            $this->reservationtimeend = $row['reservation_time_end'];
            $this->mealtimeid = $row['mealtime_id'];
            $this->mealtime = $row['mealtime_name'];
            $this->mealtime_for = $row['mealtime_for'];
            $this->mealaddon_breakfast = $row['mealaddon_breakfast'];
            $this->mealaddon_lunch = $row['mealaddon_lunch'];
            $this->mealaddon_dinner = $row['mealaddon_dinner'];
            $this->mealaddon_snack_am = $row['mealaddon_snack_am'];
            $this->mealaddon_snack_pm = $row['mealaddon_snack_pm'];
            $this->occassion = $row['reservation_occasion'];
            $this->customerid = $row['customer_id'];
            $this->customername = $row['reservation_name'];
            $this->customeremail = $row['reservation_email'];
            $this->customerphone = $row['reservation_telephone'];
            $this->nationalityid = $row['nationality_id'];
            $this->customernationality = $row['customer_nationality'];
            $this->dateadded = $row['date_added'];
            $this->datemodified = $row['date_modified'];
            $this->notify = $row['notify'];
            $this->ipaddress = $row['ip_address'];
            $this->useragent = $row['user_agent'];
            $this->company = $row['reservation_company'];
            $this->outletid = $row['outlet_id'];
            $this->outletname = $row['outlet_name'];
            $this->outletaddress1 = $row['outlet_address1'];
            $this->outletaddress2 = $row['outlet_address2'];
            $this->staytime = $row['reservation_stay_time'];
            $this->comment = $row['reservation_comment'];
            $this->remarks = $row['reservation_remarks'];
            $this->staffid = $row['assignee_id'];
            $this->staffname = $row['staff_name'];
            $this->status = $row['status_name'];
            $count++;
        }
        $this->numrows= $count;
    }

    function fetchdatabyreservationid($reservationid) {
        $count=0;
        $sqlquery = "SELECT * FROM vw_ReservationInfo WHERE reservation_id=$reservationid";
        // die($sqlquery);
        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {
            $this->id = $row['reservation_id'];
            $this->bookingid = $row['booking_id'];
            $this->reservationtypeid = $row['reservationtype_id'];
            $this->reservationtype = $row['reservationtype_description'];
            $this->adultnum = $row['adult_num'];
            $this->childrennum = $row['children_num'];
            $this->infantnum = $row['infant_num'];
            $this->childrenremarks = $row['children_remarks'];
            $this->infantremarks = $row['infant_remarks'];
            $this->meatnum = $row['meat_num'];
            $this->fishnum = $row['fish_num'];
            $this->roundid = $row['round_id'];
            $this->reservationdate = $row['reservation_date'];
            $this->reservationtime = $row['reservation_time'];
            $this->reservationtimeend = $row['reservation_time_end'];
            $this->mealtimeid = $row['mealtime_id'];
            $this->mealtime = $row['mealtime_name'];
            $this->mealtime_for = $row['mealtime_for'];
            $this->mealaddon_breakfast = $row['mealaddon_breakfast'];
            $this->mealaddon_lunch = $row['mealaddon_lunch'];
            $this->mealaddon_dinner = $row['mealaddon_dinner'];
            $this->mealaddon_snack_am = $row['mealaddon_snack_am'];
            $this->mealaddon_snack_pm = $row['mealaddon_snack_pm'];
            $this->occassion = $row['reservation_occasion'];
            $this->customerid = $row['customer_id'];
            $this->customername = $row['reservation_name'];
            $this->customeremail = $row['reservation_email'];
            $this->customerphone = $row['reservation_telephone'];
            $this->nationalityid = $row['nationality_id'];
            $this->customernationality = $row['customer_nationality'];
            $this->dateadded = $row['date_added'];
            $this->datemodified = $row['date_modified'];
            $this->notify = $row['notify'];
            $this->ipaddress = $row['ip_address'];
            $this->useragent = $row['user_agent'];
            $this->company = $row['reservation_company'];
            $this->outletid = $row['outlet_id'];
            $this->outletname = $row['outlet_name'];
            $this->outletaddress1 = $row['outlet_address1'];
            $this->outletaddress2 = $row['outlet_address2'];
            $this->comment = $row['reservation_comment'];
            $this->remarks = $row['reservation_remarks'];
            $this->staffid = $row['assignee_id'];
            $this->staffname = $row['staff_name'];
            $this->status = $row['status_name'];
            $count++;
        }
        $this->numrows= $count;
    }

}

Class UpdateReservation {
    public $bookingid;
    public $reservationid;
    public $assigneename;
    public $staffid;
    public $oldstaffid;
    public $statusid;
    public $oldstatusid;    
    public $comment;
    public $notifycustomer;
    public $userid;

    function __construct() {
        $this->bookingid = isset($_POST['bookingid']) ? $_POST['bookingid'] : null;                
        $this->reservationid = isset($_POST['reservationid']) ? $_POST['reservationid'] : null;                
        $this->assigneename = isset($_POST['assigneename']) ? $_POST['assigneename'] : null;                
        $this->staffid = (isset($_POST['staff_id'])&&$_POST['staff_id']) ? $_POST['staff_id'] : (isset($_POST['old_staff_id']) ? $_POST['old_staff_id']:null); // if staff id is 0 then retain previous value                
        $this->oldstaffid = isset($_POST['old_staff_id']) ? $_POST['old_staff_id'] : null;                
        $this->statusid = isset($_POST['status_id']) ? $_POST['status_id'] : null;                
        $this->oldstatusid = isset($_POST['old_status_id']) ? $_POST['old_status_id'] : null;                        
        $this->comment = isset($_POST['comment']) ? ms_escape_string($_POST['comment']) : null;                
        $this->notifycustomer = isset($_POST['notify_customer']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function updatebyreservationid($reservationid) {
        try {
            $sqlquery = "UPDATE Reservations SET 
                            assignee_id=".(($this->staffid)?$this->staffid:'assignee_id')." 
                            ,reservation_status=".$this->statusid."
                            ,reservation_remarks='".$this->comment."'
                            WHERE reservation_id=".$reservationid;
            $updated = mssql_executequery($sqlquery);
        } catch(Exception $e) {
            echo "<pre>";
            print_r($e);            
            print_r($_POST);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;         
        }
    }
    
    function updatebybookingid($bookingid) {
        try {
            $sqlquery = "UPDATE Reservations SET 
                            assignee_id=".(($this->staffid)?$this->staffid:'assignee_id')." 
                            ,reservation_status=".$this->statusid."
                            ,reservation_remarks='".$this->comment."'
                            WHERE booking_id=".$bookingid;
            $updated = mssql_executequery($sqlquery);
        } catch(Exception $e) {
            echo "<pre>";
            print_r($e);            
            print_r($_POST);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;         
        }

        try {
            $sqlquery = "UPDATE Bookings SET 
                            reservation_status=".$this->statusid." 
                            WHERE booking_id=".$bookingid;
            $updated = mssql_executequery($sqlquery);
        } catch(Exception $e) {
            echo "<pre>";
            print_r($e);            
            print_r($_POST);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;         
        }
    }

    function updatefieldbybookingid($bookingid,$field,$value) {
        try {
            $sqlquery = "UPDATE Reservations SET 
                            $field=".((gettype($value)=='string')?"'$value'":$value)."                             
                            WHERE booking_id=$bookingid";
            // die($sqlquery);
            $updated = mssql_executequery($sqlquery);
        } catch(Exception $e) {
            echo "<pre>";
            print_r($e);            
            print_r($_POST);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;         
        }
    }
    

    function appendstatushistory() {

        try {
            $sqlquery = "INSERT INTO StatusHistory (reference_id, staff_id, assignee_id, status_id, notify, status_for, comment) VALUES 
                        ("
                            .$this->bookingid.", "
                            .$this->staffid.", "
                            .$this->userid.", "
                            .$this->statusid.", "
                            .$this->notifycustomer.", 'reserve','"
                            .$this->comment.
                        "')";
            // die($sqlquery);
            $inserted = mssql_executequery($sqlquery);
        } catch(Exception $e) {
            echo "<pre>";
            print_r($e);
            print_r($_POST);            
            echo "</pre>";          
            echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;         
        }
    }
}

class UpdateReservationType {
    public $id;
    public $description;
    public $checkavailabilty;    
    public $status;

    function __construct() {
        $this->id = isset($_POST['rtype_id']) ? $_POST['rtype_id'] : null;
        $this->description = isset($_POST['rtype_description']) ? $_POST['rtype_description'] : null;
        $this->checkavailabilty = isset($_POST['rtype_checkavailabilty']) ? "1" : "0";        
        $this->status = isset($_POST['rtype_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->description)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            =======================
            Update Reservation Type
            =======================                   
            */

            try {
                $sqlquery = "UPDATE ReservationType SET 
                            reservationtype_description='".ms_escape_string($this->description)."'
                            ,checkavailabilty=".$this->checkavailabilty."                            
                            ,reservationtype_status=".$this->status."
                            WHERE reservationtype_id=".$this->id;
                            

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }           
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> reservation type <a href="'.ROOT_URL.'admin/reservationtype-edit.php?id='.$this->id.'"><b>'.$this->description.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'staffs', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>".$this->description."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class ReservationTypeInfo {
    public $id;
    public $description;
    public $checkavailabilty;    
    public $status;
    public $numrows;

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM ReservationType WHERE reservationtype_id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['reservationtype_id'];
            $this->description = $row['reservationtype_description'];
            $this->checkavailabilty = $row['checkavailabilty'];            
            $this->status = $row['reservationtype_status'];        
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabydescription($description) {
        $count=0;
        
        $sqlquery = "SELECT * FROM ReservationType WHERE reservationtype_description='$description'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['reservationtype_id'];
            $this->description = $row['reservationtype_description'];
            $this->checkavailabilty = $row['checkavailabilty'];            
            $this->status = $row['reservationtype_status'];
            $count++;
        }        
        $this->numrows= $count;
    }

}

class RegisterOutlet {    
    private $outletname;
    private $emailaddress;    
    private $phonenumber;    
    private $address1;    
    private $address2;    
    private $city;    
    private $state;    
    private $postcode;    
    private $countryid;    
    private $maxcapacity;    
    private $offermenu;    
    private $offerroundvalidation;    
    private $status;
    private $userid;

    function __construct() {
        $this->outletname = isset($_POST['o_name']) ? $_POST['o_name'] : null;
        $this->emailaddress = isset($_POST['o_email']) ? $_POST['o_email'] : null;
        $this->phonenumber = isset($_POST['o_telephone']) ? $_POST['o_telephone'] : null;
        $this->address1 = isset($_POST['o_address1']) ? $_POST['o_address1'] : null;
        $this->address2 = isset($_POST['o_address2']) ? $_POST['o_address2'] : null;
        $this->city = isset($_POST['o_city']) ? $_POST['o_city'] : null;
        $this->state = isset($_POST['o_state']) ? $_POST['o_state'] : null;
        $this->postcode = isset($_POST['o_postcode']) ? $_POST['o_postcode'] : null;
        $this->countryid = isset($_POST['o_country']) ? $_POST['o_country'] : "0";
        $this->maxcapacity = isset($_POST['o_max_capacity']) ? $_POST['o_max_capacity'] : "0";
        $this->offermenu = isset($_POST['o_offer_menu']) ? "1" : "0";
        $this->offerroundvalidation = isset($_POST['o_offer_round_validation']) ? "1" : "0";
        $this->status = isset($_POST['o_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->outletname)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            =====================
            Append to Staff Group
            =====================            
            */

            try {
                $sqlquery = "INSERT INTO Outlets(
                            outlet_name
                            ,outlet_email
                            ,outlet_telephone
                            ,outlet_address1
                            ,outlet_address2
                            ,outlet_city
                            ,outlet_state
                            ,outlet_postcode
                            ,outlet_country_id
                            ,outlet_max_capacity
                            ,offer_menu
                            ,offer_round_validation
                            ,outlet_status
                            )
                            SELECT '".ms_escape_string($this->outletname)."'                                    
                                    ,'".ms_escape_string($this->emailaddress)."'
                                    ,'".ms_escape_string($this->phonenumber)."'
                                    ,'".ms_escape_string($this->address1)."'
                                    ,'".ms_escape_string($this->address2)."'
                                    ,'".ms_escape_string($this->city)."'
                                    ,'".ms_escape_string($this->state)."'                                    
                                    ,'".ms_escape_string($this->postcode)."'
                                    ,".$this->countryid."
                                    ,".$this->maxcapacity."
                                    ,".$this->offermenu."
                                    ,".$this->offerroundvalidation."
                                    ,".$this->status;
                // die($sqlquery);
                $inserted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }
            
            /*
            ===========================
            Fetch New Outlet Information
            ===========================            
            */
            $outletinfo = new OutletInfo();
            $outletinfo->fetchdatabyname($this->outletname);
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>added</b> outlet <a href="'.ROOT_URL.'admin/outlet-edit.php?id='.$outletinfo->id.'"><b>'.$outletinfo->outletname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'outlets', 'staff', ".$userinfo->staffid.", 'added', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center' style='color:gray'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>".$outletinfo->outletname."</strong> 
                    was added successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

class UpdateOutlet {
    public $id;
    public $outletname;
    public $emailaddress;    
    public $phonenumber;    
    public $address1;    
    public $address2;    
    public $city;    
    public $state;    
    public $postcode;    
    public $countryid;    
    public $maxcapacity;    
    public $offermenu;    
    public $offerroundvalidation;    
    public $status;
    public $userid;

    function __construct() {
        $this->id = isset($_POST['o_outletid']) ? $_POST['o_outletid'] : null;
        $this->outletname = isset($_POST['o_name']) ? $_POST['o_name'] : null;
        $this->emailaddress = isset($_POST['o_email']) ? $_POST['o_email'] : null;
        $this->phonenumber = isset($_POST['o_telephone']) ? $_POST['o_telephone'] : null;
        $this->address1 = isset($_POST['o_address1']) ? $_POST['o_address1'] : null;
        $this->address2 = isset($_POST['o_address2']) ? $_POST['o_address2'] : null;
        $this->city = isset($_POST['o_city']) ? $_POST['o_city'] : null;
        $this->state = isset($_POST['o_state']) ? $_POST['o_state'] : null;
        $this->postcode = isset($_POST['o_postcode']) ? $_POST['o_postcode'] : null;
        $this->countryid = isset($_POST['o_country']) ? $_POST['o_country'] : "0";
        $this->maxcapacity = isset($_POST['o_max_capacity']) ? $_POST['o_max_capacity'] : "0";
        $this->offermenu = isset($_POST['o_offer_menu']) ? "1" : "0";
        $this->offerroundvalidation = isset($_POST['o_offer_round_validation']) ? "1" : "0";
        $this->status = isset($_POST['o_status']) ? "1" : "0";
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->outletname)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            ==================
            Update Staff Group
            ==================            
            */

            try {
                $sqlquery = "UPDATE Outlets SET 
                            outlet_name='".ms_escape_string($this->outletname)."'
                            ,outlet_email='".ms_escape_string($this->emailaddress)."'
                            ,outlet_telephone='".ms_escape_string($this->phonenumber)."'
                            ,outlet_address1='".ms_escape_string($this->address1)."'
                            ,outlet_address2='".ms_escape_string($this->address2)."'
                            ,outlet_city='".ms_escape_string($this->city)."'
                            ,outlet_state='".ms_escape_string($this->state)."'
                            ,outlet_postcode='".ms_escape_string($this->postcode)."'
                            ,outlet_country_id=".$this->countryid."
                            ,outlet_max_capacity=".$this->maxcapacity."
                            ,offer_menu=".$this->offermenu."
                            ,offer_round_validation=".$this->offerroundvalidation."
                            ,outlet_status=".$this->status."
                            WHERE outlet_id=".$this->id;
                            

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }           
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> outlet <a href="'.ROOT_URL.'admin/outlet-edit.php?id='.$this->id.'"><b>'.$this->outletname.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'outlets', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>".$this->outletname."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class OutletInfo {
    public $id;
    public $outletname;
    public $emailaddress;    
    public $phonenumber;    
    public $address1;    
    public $address2;    
    public $city;    
    public $state;    
    public $postcode;    
    public $countryid;    
    public $country;    
    public $maxcapacity;    
    public $offerroundvalidation;    
    public $offermenu;    
    public $status;
    public $numrows;
    public $outlets = array();    

    function fetchactiveoutlets() {
        $count=0;
        
        $sqlquery = "SELECT O.*, C.nice_name AS country FROM Outlets O LEFT JOIN Countries C ON C.country_id=O.outlet_country_id WHERE ISNULL(outlet_status,0)=1 ORDER BY outlet_name";

        $this->outlets = mssql_resultset($sqlquery);
        $count = count($this->outlets);
        $this->numrows= $count;
    }

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT O.*, C.nice_name AS country FROM Outlets O LEFT JOIN Countries C ON C.country_id=O.outlet_country_id WHERE outlet_id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['outlet_id'];
            $this->outletname = $row['outlet_name'];
            $this->emailaddress = $row['outlet_email'];
            $this->phonenumber = $row['outlet_telephone'];
            $this->address1 = $row['outlet_address1'];
            $this->address2 = $row['outlet_address2'];
            $this->city = $row['outlet_city'];
            $this->state = $row['outlet_state'];
            $this->postcode = $row['outlet_postcode'];
            $this->countryid = $row['outlet_country_id'];
            $this->country = $row['country'];
            $this->maxcapacity = $row['outlet_max_capacity'];
            $this->offerroundvalidation = $row['offer_round_validation'];
            $this->offermenu = $row['offer_menu'];
            $this->status = $row['outlet_status'];
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyname($name) {
        $count=0;
        
        $sqlquery = "SELECT O.*, C.nice_name AS country FROM Outlets O LEFT JOIN Countries C ON C.country_id=O.outlet_country_id WHERE outlet_name='$name'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['outlet_id'];
            $this->outletname = $row['outlet_name'];
            $this->emailaddress = $row['outlet_email'];
            $this->phonenumber = $row['outlet_telephone'];
            $this->address1 = $row['outlet_address1'];
            $this->address2 = $row['outlet_address2'];
            $this->city = $row['outlet_city'];
            $this->state = $row['outlet_state'];
            $this->postcode = $row['outlet_postcode'];
            $this->countryid = $row['outlet_country_id'];
            $this->country = $row['country'];
            $this->maxcapacity = $row['outlet_max_capacity'];
            $this->offerroundvalidation = $row['offer_round_validation'];
            $this->offermenu = $row['offer_menu'];
            $this->status = $row['outlet_status'];
            $count++;
        }        
        $this->numrows= $count;
    }

}

class UpdateConfiguration {
    public $id;
    public $key;
    public $value;    
    public $note;
    public $userid;

    function __construct() {
        $this->id = isset($_POST['configuration_id']) ? $_POST['configuration_id'] : null;
        $this->key = isset($_POST['config_key']) ? $_POST['config_key'] : null;
        $this->value = isset($_POST['config_value']) ? $_POST['config_value'] : null;        
        $this->note = isset($_POST['config_note']) ? $_POST['config_note'] : null;        
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function start() {
        if (empty($this->key)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Empty post not allowed                    
                </div>";
        }

        else
        {
            // Do some stuiff
            // fnPostToHTML();

            /*
            =======================
            Update Reservation Type
            =======================                   
            */

            try {
                $sqlquery = "UPDATE Config SET 
                            [value]='".ms_escape_string($this->value)."'
                            ,[note]='".ms_escape_string($this->note)."'                            
                            ,date_modified=GETDATE()
                            WHERE id=".$this->id;
                            

                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }           
            

            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> settings <a href="'.ROOT_URL.'admin/setting-edit.php?id='.$this->id.'"><b>'.$this->key.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'settings', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>".$this->key."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class ConfigurationInfo {
    public $id;
    public $key;
    public $value;    
    public $note;    
    public $dateadded;    
    public $numrows;

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Config WHERE id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];
            $this->key = $row['key'];
            $this->value = $row['value'];
            $this->note = $row['note'];            
            $this->dateadded = $row['date_added'];
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabykey($key) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Config WHERE [key]='$key'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['id'];
            $this->key = $row['key'];
            $this->value = $row['value'];
            $this->note = $row['note'];            
            $this->dateadded = $row['date_added'];
            $count++;
        }        
        $this->numrows= $count;
    }

}

Class ProposalInfo {
    public $id;
    public $booking_id;
    public $proposal_date;
    public $proposal_time;
    public $proposal_venue;
    public $proposal_guaranteed;
    public $proposal_guaranteed_remarks;
    public $proposal_signatory_paragraph;
    public $proposal_event_menu;
    public $proposal_regular_lunch;
    public $proposal_lunch_remarks;
    public $proposal_sunday_lunch;
    public $proposal_refreshment;
    public $proposal_beverages;
    public $proposal_venue_fee;
    public $proposal_venue_arrangement;
    public $proposal_package_breakdown;
    public $proposal_file_path;
    public $proposal_menu_attachments;
    public $proposal_price;
    public $proposal_discount_amount;
    public $staff_id;
    public $date_added;
    public $date_updated;        
    public $numrows;

    function fetchdatabybookingid($bookingid) {
        /* Long Field Handeler manipulation */      
        $LongFieldLimit=10240;
        $DefaultLongFieldLimit=ini_get('odbc.defaultlrl');
        ini_set('odbc.defaultlrl',$LongFieldLimit);

        $count=0;

        $sqlquery = "SELECT P.*, U.staff_name FROM Proposals P LEFT JOIN vw_UserProfile U ON U.staff_id=P.staff_id WHERE booking_id=$bookingid";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['proposal_id'];
            $this->booking_id = $row['booking_id'];
            $this->proposal_date = $row['proposal_date'];
            $this->proposal_time = $row['proposal_time'];            
            $this->proposal_venue = $row['proposal_venue'];            
            $this->proposal_guaranteed = $row['proposal_guaranteed'];            
            $this->proposal_guaranteed_remarks = $row['proposal_guaranteed_remarks'];            
            $this->proposal_signatory_paragraph = ""; //$row['proposal_signatory_paragraph'];            
            $this->proposal_event_menu = $row['proposal_event_menu'];            
            $this->proposal_regular_lunch = $row['proposal_regular_lunch'];            
            $this->proposal_lunch_remarks = $row['proposal_lunch_remarks'];            
            $this->proposal_sunday_lunch = $row['proposal_sunday_lunch'];
            $this->proposal_refreshment = $row['proposal_refreshment'];            
            $this->proposal_beverages = $row['proposal_beverages'];            
            $this->proposal_venue_fee = $row['proposal_venue_fee'];            
            $this->proposal_venue_arrangement = $row['proposal_venue_arrangement'];            
            $this->proposal_package_breakdown = $row['proposal_package_breakdown'];            
            $this->proposal_file_path = $row['proposal_file_path'];            
            $this->proposal_menu_attachments = $row['proposal_menu_attachments'];            
            $this->proposal_price = $row['proposal_price'];            
            $this->proposal_discount_amount = $row['proposal_discount_amount'];            
            $this->staff_id = $row['staff_id'];            
            $this->dateadded = $row['date_added'];
            $this->date_updated = $row['date_updated'];            
            $count++;
        } 
        $this->numrows= $count;

        /* Reset modified PHP configuration to default */
        ini_set('odbc.defaultlrl',$DefaultLongFieldLimit);
    }   

}

Class BanquetEventOrderInfo {
    public $id;
    public $reservation_id;
    public $beo_room_rent;
    public $beo_min_gtd;
    public $beo_max_gtd;
    public $beo_announcement_board;
    public $beo_announcement_message;
    public $beo_supervisor_staff_id;
    public $beo_supervisor_staff_name;
    public $beo_menu;
    public $beo_beverages;
    public $beo_accounting;
    public $beo_room_configuration;
    public $beo_housekeeping;
    public $beo_information_technology;
    public $beo_engineering;
    public $beo_fnb;
    public $beo_venue_arrangement;
    public $beo_guest_will_bring;
    public $beo_maintenance;
    public $beo_food;
    public $beo_deposit_balances;
    public $staff_id;        
    public $dateadded;    
    public $numrows;

    function fetchdatabyreservationid($reservationid) {
        /* Long Field Handeler manipulation */      
        $LongFieldLimit=10240;
        $DefaultLongFieldLimit=ini_get('odbc.defaultlrl');      
        ini_set('odbc.defaultlrl',$LongFieldLimit);

        $count=0;

        $sqlquery = "SELECT B.*, U.staff_name FROM BanquetEventOrder B LEFT JOIN vw_UserProfile U ON U.staff_id=B.beo_supervisor_staff_id WHERE reservation_id=$reservationid";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['beo_id'];
            $this->reservation_id = $row['reservation_id'];
            $this->beo_room_rent = $row['beo_room_rent'];
            $this->beo_min_gtd = $row['beo_min_gtd'];            
            $this->beo_max_gtd = $row['beo_max_gtd'];            
            $this->beo_announcement_board = $row['beo_announcement_board'];            
            $this->beo_announcement_message = $row['beo_announcement_message'];            
            $this->beo_supervisor_staff_id = $row['beo_supervisor_staff_id'];            
            $this->beo_supervisor_staff_name = $row['staff_name'];            
            $this->beo_menu = $row['beo_menu'];
            $this->beo_beverages = $row['beo_beverages'];            
            $this->beo_accounting = $row['beo_accounting'];            
            $this->beo_room_configuration = $row['beo_room_configuration'];            
            $this->beo_housekeeping = $row['beo_housekeeping'];            
            $this->beo_information_technology = $row['beo_information_technology'];            
            $this->beo_engineering = $row['beo_engineering'];            
            $this->beo_fnb = $row['beo_fnb'];            
            $this->beo_venue_arrangement = $row['beo_venue_arrangement'];            
            $this->beo_guest_will_bring = $row['beo_guest_will_bring'];            
            $this->beo_maintenance = $row['beo_maintenance'];            
            $this->beo_food = $row['beo_food'];            
            $this->beo_deposit_balances = $row['beo_deposit_balances'];            
            $this->staff_id = $row['staff_id'];            
            $this->dateadded = $row['date_added'];
            $count++;
        } 
        $this->numrows= $count;

        /* Reset modified PHP configuration to default */
        ini_set('odbc.defaultlrl',$DefaultLongFieldLimit);
    }   

    function fetchdatabybookingid($bookingid) {
        /* Long Field Handeler manipulation */      
        $LongFieldLimit=10240;
        $DefaultLongFieldLimit=ini_get('odbc.defaultlrl');      
        ini_set('odbc.defaultlrl',$LongFieldLimit);

        $count=0;

        $sqlquery = "SELECT TOP 1 B.*, U.staff_name 
                    FROM BanquetEventOrder B 
                    LEFT JOIN vw_UserProfile U ON U.staff_id=B.beo_supervisor_staff_id 
                    WHERE reservation_id IN(SELECT reservation_id FROM Reservations WHERE booking_id=$bookingid) 
                    ORDER BY ISNULL(B.date_updated,B.date_added) DESC";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['beo_id'];
            $this->reservation_id = $row['reservation_id'];
            $this->beo_room_rent = $row['beo_room_rent'];
            $this->beo_min_gtd = $row['beo_min_gtd'];            
            $this->beo_max_gtd = $row['beo_max_gtd'];            
            $this->beo_announcement_board = $row['beo_announcement_board'];            
            $this->beo_announcement_message = $row['beo_announcement_message'];            
            $this->beo_supervisor_staff_id = $row['beo_supervisor_staff_id'];            
            $this->beo_supervisor_staff_name = $row['staff_name'];            
            $this->beo_menu = $row['beo_menu'];
            $this->beo_beverages = $row['beo_beverages'];            
            $this->beo_accounting = $row['beo_accounting'];            
            $this->beo_room_configuration = $row['beo_room_configuration'];            
            $this->beo_housekeeping = $row['beo_housekeeping'];            
            $this->beo_information_technology = $row['beo_information_technology'];            
            $this->beo_engineering = $row['beo_engineering'];            
            $this->beo_fnb = $row['beo_fnb'];            
            $this->beo_venue_arrangement = $row['beo_venue_arrangement'];            
            $this->beo_guest_will_bring = $row['beo_guest_will_bring'];            
            $this->beo_maintenance = $row['beo_maintenance'];            
            $this->beo_food = $row['beo_food'];            
            $this->beo_deposit_balances = $row['beo_deposit_balances'];            
            $this->staff_id = $row['staff_id'];            
            $this->dateadded = $row['date_added'];
            $count++;
        } 
        $this->numrows= $count;

        /* Reset modified PHP configuration to default */
        ini_set('odbc.defaultlrl',$DefaultLongFieldLimit);
    }   

}

Class StatusInfo {
    public $id;
    public $statusname;
    public $statuscomment;    
    public $notifycustomer;    
    public $statusfor;
    public $statuscolor;    
    public $statuses;
    public $numrows;

    function fetchstatuses() {
        $count=0;
        
        $sqlquery = "SELECT * FROM Statuses WHERE status_for='reserve' ORDER BY status_name";

        $this->statuses = mssql_resultset($sqlquery);
        $count = count($this->statuses);
        $this->numrows= $count;
    }

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Statuses WHERE status_for='reserve' AND status_id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['status_id'];
            $this->statusname = $row['status_name'];            
            $this->statuscomment = $row['status_comment'];            
            $this->notifycustomer = $row['notify_customer'];
            $this->statusfor = $row['status_for'];            
            $this->statuscolor = $row['status_color'];            
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyname($name) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Statuses WHERE status_for='reserve' AND status_name='$name'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['status_id'];
            $this->statusname = $row['status_name'];            
            $this->statuscomment = $row['status_comment'];            
            $this->notifycustomer = $row['notify_customer'];
            $this->statusfor = $row['status_for'];            
            $this->statuscolor = $row['status_color'];            
            $count++;
        }        
        $this->numrows= $count;
    }

}

Class MealtimesInfo {
    public $id;
    public $mealtimename;
    public $mealtimefor;    
    public $mealtimes;
    public $numrows;

    function fetchmealtimes() {
        $count=0;
        
        $sqlquery = "SELECT * FROM Mealtimes ORDER BY mealtime_for, mealtime_name";

        $this->mealtimes = mssql_resultset($sqlquery);
        $count = count($this->mealtimes);
        $this->numrows = $count;
    }

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Mealtimes WHERE mealtime_id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['mealtime_id'];
            $this->mealtimename = $row['mealtime_name'];
            $this->mealtimefor = $row['mealtime_for'];
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyname($name) {
        $count=0;
        
        $sqlquery = "SELECT * FROM Mealtimes WHERE mealtime_name='$name'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['mealtime_id'];
            $this->mealtimename = $row['mealtime_name'];
            $this->mealtimefor = $row['mealtime_for'];
            $count++;
        }        
        $this->numrows= $count;
    }

}


class PackageItem {
    private $id;
    private $description;
    private $metrics;
    private $cost;
    private $category;
    private $userid;

    function __construct() {
        $this->id = isset($_POST['i_id']) ? $_POST['i_id'] : null;
        $this->description = isset($_POST['i_name']) ? $_POST['i_name'] : null;
        $this->metrics = isset($_POST['i_metrics']) ? $_POST['i_metrics'] : null;
        $this->cost = isset($_POST['i_cost']) ? $_POST['i_cost'] : null;        
        $this->category = isset($_POST['i_category']) ? $_POST['i_category'] : null;        
        $this->userid = isset($_POST['userid']) ? $_POST['userid'] : "0";
    }

    function append() {
        if (empty($this->description) || empty($this->metrics) || empty($this->cost)) {
            // throw new Exception("Empty Post not allowed");
            echo "Empty Post not allowed";
        } else {
            /*
            ================
            Append to Items
            ================
            */

            try {
                $sqlquery = "INSERT INTO PackageItems(package_item_name, package_item_metrics, package_item_unit_cost,package_item_category)
                            SELECT '".ms_escape_string($this->description)."'
                                    ,'".ms_escape_string($this->metrics)."'
                                    ,".$this->cost."
                                    ,'".ms_escape_string($this->category)."'";

                $inserted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }

            /*
            ===========================
            Fetch New Item Information
            ===========================            
            */
            $iteminfo = new PackageItemInfo();
            $iteminfo->fetchdatabyname($this->description);
            
            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>added</b> chargeable item <a href="'.ROOT_URL.'admin/item-edit.php?id='.$iteminfo->id.'"><b>'.$iteminfo->description.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'chargeable-item', 'staff', ".$userinfo->staffid.", 'added', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Chargeable Item <strong>".$iteminfo->description."</strong> 
                    was added successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }

    function update() {
        if (empty($this->id) || empty($this->description) || empty($this->metrics) || empty($this->cost) || empty($this->category)) {
            // throw new Exception("Empty Post not allowed");
            echo "Empty Post not allowed";
        } else {

            try {
                $sqlquery = "UPDATE PackageItems SET 
                            package_item_name='".ms_escape_string($this->description)."' 
                            ,package_item_metrics='".ms_escape_string($this->metrics)."' 
                            ,package_item_unit_cost=".$this->cost." 
                            ,package_item_category='".ms_escape_string($this->category)."' 
                            WHERE package_item_id=".$this->id;
                // die($sqlquery);
                $updated = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }

            /*
            ===========================
            Fetch Item Information
            ===========================            
            */
            $iteminfo = new PackageItemInfo();
            $iteminfo->fetchdatabyid($this->id);
            
            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>updated</b> chargeable item <a href="'.ROOT_URL.'admin/item-edit.php?id='.$iteminfo->id.'"><b>'.$iteminfo->description.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'chargeable-item', 'staff', ".$userinfo->staffid.", 'updated', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);


            echo "<div class='alert alert-success alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    Chargeable Item <strong>".$iteminfo->description."</strong> 
                    was updated successfully.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }

    function delete() {
        if (empty($this->id)) {
            // throw new Exception("Empty Post not allowed");
            echo "<div class='alert alert-danger alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>Empty Post not allowed</strong> 
                </div>";
        } else {           

            /*
            ===========================
            Fetch Item Information
            ===========================            
            */
            $iteminfo = new PackageItemInfo();
            $iteminfo->fetchdatabyid($this->id);
            
            /* 
            ============
            Activity log 
            ============
            */

            /*
            Fetch Logged User Information            
            */
            $userinfo = new UserInfo();
            $userinfo->fetchdatabyuserid($this->userid);

            $ActivityMessage = $userinfo->staffname.' <b>deleted</b> chargeable item <a href="'.ROOT_URL.'admin/item-edit.php?id='.$iteminfo->id.'"><b>'.$iteminfo->description.'.</b></a>';
            $sqlquery="EXEC sp_s_Activities 'admin', 'chargeable-item', 'staff', ".$userinfo->staffid.", 'deleted', '$ActivityMessage'";
            // die($sqlquery);
            $rows = mssql_resultset($sqlquery);            
            foreach ($rows as $row) {        
                $ResultStatus = (int) $row['Result'];
                $ResultMessage = $row['ResultMessage'];        
            }

            if ($ResultStatus==0)
                die($ResultMessage);

            /*
            =========
            MAIN CODE
            =========
            */
            try {
                $sqlquery = "DELETE FROM PackageItems WHERE package_item_id=".$this->id;

                $deleted = mssql_executequery($sqlquery);
            } 
            catch(Exception $e) {   
                echo "<pre>";            
                print_r($e);            
                print_r($_POST);
                echo "</pre>";          
                echo "<a href='#' class='alert-link' onclick='location.reload(); return false;'>Reload</a>" ;
                die();
            }


            echo "<div class='alert alert-warning alert-dismissible fade in text-center'>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                    <strong>".$iteminfo->description."</strong> 
                    was deleted.
                </div>";
            // echo "<pre>";
            // print_r($_POST);
            // // print_r($_SERVER);
            // echo "</pre>";  
        }
    }
}

Class PackageItemInfo {
    public $id;
    public $items;
    public $description;
    public $metrics;    
    public $cost;
    public $category;    
    public $dateadded;
    public $datemodified;
    public $numrows;

    function fetchpackageitems() {
        $count=0;
        
        $sqlquery = "SELECT * FROM PackageItems ORDER BY package_item_name";

        $this->items = mssql_resultset($sqlquery);
        $count = count($this->items);
        $this->numrows = $count;
    }

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM PackageItems WHERE package_item_id=$id";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['package_item_id'];
            $this->description = $row['package_item_name'];
            $this->metrics = $row['package_item_metrics'];
            $this->cost = $row['package_item_unit_cost'];
            $this->category = $row['package_item_category'];
            $this->dateadded = $row['date_added'];
            $this->datemodified = $row['date_updated'];
            $count++;
        }        
        $this->numrows= $count;
    }

    function fetchdatabyname($name) {
        $count=0;
        
        $sqlquery = "SELECT * FROM PackageItems WHERE package_item_name='$name'";

        $rows = mssql_resultset($sqlquery);
        foreach ($rows as $row) {               
            $this->id = (int) $row['package_item_id'];
            $this->description = $row['package_item_name'];
            $this->metrics = $row['package_item_metrics'];
            $this->cost = $row['package_item_unit_cost'];
            $this->category = $row['package_item_category'];
            $this->dateadded = $row['date_added'];
            $this->datemodified = $row['date_updated'];
            $count++;
        }        
        $this->numrows= $count;
    }

}


Class TourAgentInfo {
    public $id;
    public $agents;
    public $agentname;
    public $email;    
    public $phone;
    public $sapcode;
    public $isactive;    
    public $dateadded;    
    public $numrows;

    function fetchtouragents() {
        $count=0;
        
        $sqlquery = "SELECT * FROM TourAgent ORDER BY agent_name";

        $this->agents = mssql_resultset($sqlquery);
        $this->numrows = count($this->agents);
    }

    function fetchdatabyid($id) {
        $count=0;
        
        $sqlquery = "SELECT * FROM TourAgent WHERE agent_id=$id";

        $rows = mssql_resultset($sqlquery);
        initialize($rows);
    }

    function fetchdatabyname($name) {
        $count=0;
        
        $sqlquery = "SELECT * FROM TourAgent WHERE agent_name='$name'";

        $rows = mssql_resultset($sqlquery);
        initialize($rows);
    }

    function initialize($data=array()) {
        foreach ($data as $row) {
            $this->id = (int) $row['agent_id'];
            $this->agentname = $row['agent_name'];
            $this->email = $row['agent_email'];
            $this->phone = $row['agent_phone'];
            $this->sapcode = $row['sap_code'];
            $this->isactive = $row['is_active'];
            $this->dateadded = $row['date_added'];
            $this->datemodified = $row['date_updated'];
        }
        $this->numrows= count($data);
    }

}

class SendSMS {    
    public $status;
    public $statusmessage;

    function sendToITE($phonenumber,$message) {
        include_once("functions.php");
        global $ApplicationName;
        global $HelpdeskEmailAddress;

        try {
            $dummyMailRecipient = array();

            /* Get client's information */   
            // $ClientsHostName = gethostname();
            // $SenderEmail = "noreply@spnfreesms.com"; // This is the default value
            $SenderEmail = "noreply.$HelpdeskEmailAddress"; // This is the default value
            // $SenderName = "SPN Free SMS"; // This is the default value
            $SenderName = $ApplicationName; // This is the default value
            // $SendTo = str_replace(" ", "", str_replace("+", "", str_replace("-", "", str_replace(")", "", str_replace("670","",str_replace("(", "", $phonenumber)))))) . "@sms.ite.net"; // This should be the selected group's email e.g. sysdev@itdservicerequest.com
            // $sanitizednumber = filter_var($phonenumber, FILTER_SANITIZE_NUMBER_INT);
            $sanitizednumber = substr(preg_replace("/[^0-9]/","",$phonenumber), -7,7); // strip off characters, country code and area code and extract only the local number
            $SendTo = "1670$sanitizednumber@sms.ite.net"; 
            // $SendTo = "1670".$SendTo;

            $SendToName = "Receipient"; // This should be the selected group's name e.g. System Development Team        
            $Subject =  "";        
            
            $emailMessage = $message;

            $this->status = sendSMTPMail($SenderEmail,$SenderName,$SendTo,$SendToName,$dummyMailRecipient,$dummyMailRecipient,$Subject,$emailMessage); 
            $this->statusmessage = "Message sent to $sanitizednumber";                 

        } 
        catch(Exception $e) {   
            $this->status=false;
            $this->statusmessage = $e;                 
            // $this->statusmessage = 'Something went wrong while sending SMS using IT&E. Please contact your administrator.';                 
        }        
    }
}

?>