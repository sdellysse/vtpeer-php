<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function startswith($haystack, $needle){
  return strpos($haystack, $needle) === 0;
}

function reset_password($username){
  $new_password = mt_rand();
  $update_password = CONFIG::query('
    UPDATE users
    SET password = md5(?)
    WHERE username = ?;
  ', array("$new_password", $username));
  return $new_password;
}

function email_password($username, $new_password){
  $web_address = 'http://php.radford.edu/~softeng17/db';
  $email_address_row = mysql_fetch_assoc(CONFIG::query('
    SELECT email_address
    FROM users
    WHERE username = ?;
  ', array($username)));
  $email_address = $email_address_row['email_address'];

  $subject = "Password reset request for Peer Review System";

  $message = "
    Your password for Peer Evaluation System has been reset.

    Your new password is: $new_password.
    Please login to the Peer Review System at $web_address and change your
    password soon.
  ";

  mail($email_address, $subject, $message);
}


$_REQUEST['selection'] = array();
$_REQUEST['selectioncount'] = 0;

foreach($_REQUEST as $key=>$value){
  if(!startswith($key, 'selection_')){
    continue;
  }
  $new_key = substr($key, strlen('selection_'));
  $_REQUEST['selection'][$new_key] = $value;
  $_REQUEST['selectioncount']++;
}

$previous = $_REQUEST['previous_page'];
if($_REQUEST['selectioncount'] === 0){
  CONFIG::redirect("$previous?no_user_selected=1");
}

$action = $_REQUEST['action'];
foreach($_REQUEST['selection'] as $selection){
  if($action === 'Reset Password'){
    $new_password = reset_password($selection);
    email_password($selection, $new_password);
    CONFIG::redirect("$previous?passwords_reset=1");
  }
}
