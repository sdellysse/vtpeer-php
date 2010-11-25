<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$t = new vlibTemplate('create_admin.tmpl');
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
if(isset($_REQUEST['password_mismatch'])){
  $t->setvar('password_mismatch', 1);
}
if(isset($_REQUEST['username_taken'])){
  $t->setvar('username_taken', 1);
}
if(isset($_REQUEST['admin_created'])){
  $t->setvar('admin_created', 1);
  $t->setvar('first_name', $_REQUEST['first_name']);
  $t->setvar('last_name', $_REQUEST['last_name']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'Create an Administrator');
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();
