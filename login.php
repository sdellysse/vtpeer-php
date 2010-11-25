<?php
require_once 'config.php';
require_once 'footer.php';
require_once 'menu_login.php';
CONFIG::connect();

define('STUDENT_HOME', 'student_home.php');
define('ADMIN_HOME', 'admin_home.php');

function login($user, $pass){
  $result = CONFIG::query('
    SELECT username
    FROM users
    WHERE username = ? AND password = md5(?);
  ', array($user, $pass));
  if(mysql_num_rows($result) == 1){         //1 row for value user/pass
    $user_row = mysql_fetch_assoc($result);
    $role_row = mysql_fetch_assoc(CONFIG::query('
      SELECT access_role
      FROM user_access_roles
      WHERE username = ?
    ', array($user_row['username'])));

    CONFIG::start_session($user_row['username'], $role_row['access_role']);

    //see if user is admin
    if(CONFIG::session_get('access_role') == 'admin'){
      CONFIG::redirect(ADMIN_HOME);
    }else{
      CONFIG::redirect(STUDENT_HOME);
    }
    return 'success';
  }
  return 'fail';
}

function display($login_valid){
  $login_failed = ('fail' === $login_valid);
  $layout = new vlibTemplate('layout.tmpl');
  $t = new vlibTemplate('login.tmpl');
  $t->setvar('login_failed', $login_failed);

  if(isset($_REQUEST['new_user'])){
    $t->setvar('new_user', $_REQUEST['new_user']);
  }

  $layout->setvar('title', 'Login');
  $layout->setvar('main', $t->grab());
  $layout->setvar('footer', footer());
  $layout->setvar('not_logged_in', '1');
  $layout->setvar('navigation', menu_login());
  $layout->pparse();
}

$valid = null;
if(isset($_POST['process'])){
  $valid = login($_POST['username'], $_POST['password']);
}
display($valid);
?>
