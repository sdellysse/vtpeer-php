<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$t = new vlibTemplate('user_management.tmpl');
$t->setdbloop('users', CONFIG::query('
  SELECT username, first_name, last_name, group_id
  FROM student_information INNER JOIN active_groups
  ON student_information.group_id = active_groups.id
  ORDER BY group_id, username;
'));
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
if(isset($_REQUEST['no_user_selected'])){
  $t->setvar('no_user_selected', $_REQUEST['no_user_selected']);
}

if(isset($_REQUEST['users_removed'])){
  $t->setvar('users_removed', $_REQUEST['users_removed']);
}

if(isset($_REQUEST['passwords_reset'])){
  $t->setvar('passwords_reset', $_REQUEST['passwords_reset']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'User Management');
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();
