<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$t = new vlibTemplate('registration_management.tmpl');
$t->setvar('previous_page', $_SERVER['PHP_SELF']);

$reg_enabled_row = mysql_fetch_assoc(CONFIG::query('
  SELECT value
  FROM scalars
  WHERE item = ?;
', array('registration_enabled')));
$reg_enabled = $reg_enabled_row['value'];

$t->setvar('registration_enabled', $reg_enabled);

$reg_code_row = mysql_fetch_assoc(CONFIG::query('
  SELECT value
  FROM scalars
  WHERE item = ?;
', array('registration_code')));
$reg_code = $reg_code_row['value'];

$t->setvar('registration_code', $reg_code);

if(isset($_REQUEST['registration_updated'])){
  $t->setvar('registration_updated', 1);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('footer', footer());
$layout->setvar('navigation', menu_admin());
$layout->setvar('title', 'Registration Management');

$layout->pparse();
