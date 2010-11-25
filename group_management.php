<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();


$t = new vlibTemplate('group_management.tmpl');
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
$t->setdbloop('groups', CONFIG::query('
  SELECT id, active
  FROM groups
  ORDER BY id;
'));

if(isset($_REQUEST['no_group_selected'])){
  $t->setvar('no_group_selected', 1);
}

if(isset($_REQUEST['num_groups_added'])){
  $t->setvar('num_groups_added', $_REQUEST['num_groups_added']);
  $t->setvar('groups_added_active', $_REQUEST['active']);
}

if(isset($_REQUEST['groups_made_active'])){
  $t->setvar('groups_activeness_change', 1);
  $t->setvar('groups_made_active', $_REQUEST['groups_made_active']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());
$layout->setvar('title', 'Group Management');
$layout->setloop('scripts', array(array('src'=>'group_management.js')));

$layout->pparse();
