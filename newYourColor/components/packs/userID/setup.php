<?php 

function UserID(){

  $user_cookie_name = "userID";
  $user_cookie_ID = uniqid();
  if(isset($_COOKIE['userID']) &&  $_COOKIE['userID'] !==''){
    $usrID = $_COOKIE['userID'];
  }else{
    $usrID = $user_cookie_ID;
    setcookie($user_cookie_name, $user_cookie_ID, time() + (86400 * 360), "/"); // 86400 = 1 day
  }

  return $usrID;
}