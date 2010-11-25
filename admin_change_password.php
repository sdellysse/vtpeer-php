<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');

$t = new vlibTemplate('change_password.tmpl');
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
if(isset($_REQUEST['success'])){
  $t->setvar('success', $_REQUEST['success']);
}
if(isset($_REQUEST['password_mismatch'])){
  $t->setvar('password_mismatch', $_REQUEST['password_mismatch']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());
$layout->setvar('title', 'Change Password');

$layout->pparse();
