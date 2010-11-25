<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$previous_page = $_REQUEST['previous_page'];
$user_name = $_REQUEST['username'];
if(1 == mysql_num_rows(CONFIG::query('
  SELECT username
  FROM users
  WHERE username = ?;
', array($user_name))))
{
  CONFIG::redirect("$previous_page?username_taken");
}
$password = $_REQUEST['password'];
$password_repeat = $_REQUEST['password_repeat'];

if($password != $password_repeat){
  CONFIG::redirect("$previous_page?password_mismatch");
}
if(strlen($password) == 0){
  CONFIG::redirect("$previous_page?password_mismatch");
}

$first_name = $_REQUEST['first_name'];
$last_name = $_REQUEST['last_name'];

CONFIG::query('
  INSERT INTO
  users(username, password, email_address, first_name, last_name, access_role, is_student, title)
  VALUES(?, MD5(?), ?, ?, ?, ?, ?, ?);
', array(
  $user_name,
  $password,
  'N/A',
  $first_name,
  $last_name,
  '2',
  '0',
  '1'
));

CONFIG::redirect("$previous_page?admin_created&first_name=$first_name&last_name=$last_name");
