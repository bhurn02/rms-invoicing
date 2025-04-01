<?php
$IPAddress = $_SERVER['REMOTE_ADDR'];
$UserAgent = $_SERVER['HTTP_USER_AGENT'];

$user_id = Login::isLoggedIn();  
// $user_pk = Login::isLoggedIn();  
// $is_allowed = (isset($page) && $page!="request-details")?"NO":"YES";
// die($is_allowed);
if (!$user_id) {
    $currentURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];  
    if ($currentURL !== PARENT_URL."main/login.php") { // this prevents too many redirect error
      header('Location: '.PARENT_URL."main/login.php");
    }
} else {
  $userloggedinfo = new Users();
  // $userloggedinfo->fetchdatabyuserpk($user_id);
  $userloggedinfo->fetchdatabyid($user_id);

  $profilepicurl=PARENT_URL.'main/assets/images/users/'.$userloggedinfo->profileimage.'.jpg';
  // $profilepicurl=(file_exists($profilepicurl))?$profilepicurl:PARENT_URL.'etlar/assets/images/users/no_photo.jpg';
  // if (file_exists($profilepicurl))  
  // { 
  //     echo "<br><br><br>The file $profilepicurl exists"; 
  // } 
  // else 
  // { 
  //     echo "<br><br><br>The file $profilepicurl does 
  //                             not exists"; 
  // } 
  // die();
  // $profilepicurl=(file_exists($profilepicurl))?$profilepicurl:PARENT_URL.'etlar/assets/images/users/no_photo.jpg';

  $cardno = ($userloggedinfo->cardno)?$userloggedinfo->cardno:$cardno;
  $user_pk = ($userloggedinfo->userpk)?$userloggedinfo->userpk:$user_pk;

  if (isset($_GET['logout']) || isset($_POST['logout'])) {
    $ActivityMessage = '<a href="'.ROOT_URL.'modules/profile/index.php?id='.$userloggedinfo->id.'">'.$userloggedinfo->fullname.'</a> <b>logged out</b>.';
    $sqlquery="EXEC sp_s_Activities @Domain='main', @Context='users', @User='user', @UserID=".$userloggedinfo->id.", @Action='logged out', @Message='$ActivityMessage', @IPAddress='$IPAddress', @UserAgent='$UserAgent'";
    $rows = mssql_resultset($sqlquery);
    setcookie('SNID',"",1,"/");
    setcookie('SNID_',"",1,"/");
    header('Location: '.PARENT_URL);
  }
}

?>