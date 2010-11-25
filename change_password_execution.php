<?php
require_once 'config.php';
CONFIG::continue_session();
CONFIG::connect();

function do_passwords_match($p1, $p2){
  return (strcmp($p1, $p2) == 0) && (strcmp($p1, '') != 0);
}

$previous_page = $_REQUEST['previous_page'];

$password = $_REQUEST['password'];
$password_repeat = $_REQUEST['password_repeat'];

if(!do_passwords_match($password, $password_repeat)){
  CONFIG::redirect($previous_page . "?password_mismatch=1");
}

CONFIG::query('
  UPDATE users
  SET password = md5(?)
  WHERE username = ?;
', array($password, CONFIG::session_get('username'))
) or die(mysql_error());

CONFIG::redirect($previous_page . "?success=1");

