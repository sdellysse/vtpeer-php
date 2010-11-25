<?php
require_once 'config.php';
require_once 'footer.php';
require_once 'menu_login.php';
CONFIG::connect();

function registration_is_enabled(){
  $registration_enabled_row = mysql_fetch_assoc(CONFIG::query('
    SELECT value
    FROM scalars
    WHERE item = ?;
  ', array('registration_enabled')));
  return $registration_enabled_row['value'] == '1';
}

function registration_code_is_valid($code){
  return (1 == mysql_num_rows(CONFIG::query('
    SELECT value
    FROM scalars
    WHERE item = ?
    AND value = ?;
  ', array('registration_code', $code))));
}

function do_passwords_match($p1, $p2){
  return (strcmp($p1, $p2) == 0) && (strcmp($p1, '') != 0);
}

function username_is_available($username){
  return (0 == mysql_num_rows(CONFIG::query('
    SELECT username
    FROM users
    WHERE username = ?;
  ', array(
    $username
  ))));
}

function register_student($username, $password, $email_address, $first_name, $last_name,
 $group_id, $group_role_id){
  $r1 = CONFIG::query('
    INSERT INTO users(username, password, email_address,
      first_name, last_name,
      access_role, title, is_student)
    VALUES(?, md5(?), ?, ?, ?, ?,  ?, ?);
  ', array(
    $username, $password, $email_address, $first_name, $last_name, '1', '1', '1'
  ));
  $r2 = CONFIG::query('
    INSERT INTO students(username, current_role, group_id)
    VALUES(?, ?, ?);
  ', array(
    $username, $group_role_id, $group_id
  ));

  return ($r1 && $r2);
}

function display($username_taken = false, $password_mismatch = false, $code_invalid = false){
  $layout = new vlibTemplate('layout.tmpl');
  $t = new vlibTemplate('register.tmpl');
  $t->setvar('username_taken', $username_taken);
  $t->setvar('password_mismatch', $password_mismatch);
  $t->setvar('registration_code_invalid', $code_invalid);
  $t->setvar('registration_enabled', registration_is_enabled());
  $t->setvar('action', $_SERVER['PHP_SELF']);
  $t->setdbloop('group', CONFIG::query('
    SELECT id
    FROM active_groups;
  '));
  $t->setdbloop('role', CONFIG::query('
    SELECT id, text
    FROM group_roles;
  '));

  $layout->setvar('main', $t->grab());
  $layout->setvar('footer', footer());
  $layout->setvar('navigation', menu_login());
  $layout->setvar('not_logged_in', '1');
  $layout->setvar('title', 'Register');

  $layout->pparse();
  exit(0);
}

if(isset($_POST['process'])){
  if(!registration_code_is_valid($_POST['registration_code'])){
    display('', '', 'invalid');
  }
  if(!username_is_available($_POST['username'])){
    display('taken', '');
  }
  if(!do_passwords_match($_POST['password'], $_POST['password_repeat'])){
    display('', 'mismatch');
  }
  register_student(
    $_POST['username'], $_POST['password'], $_POST['email_address'], $_POST['first_name'],
    $_POST['last_name'], $_POST['group'], $_POST['role']
  );
  CONFIG::redirect('login.php?new_user=' . $_POST['username']);
}

display();
