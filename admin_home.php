<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function number_to_text($num){
  $l = array(
    1=> 'First',
    2=> 'Second',
    3=> 'Third',
    4=> 'Fourth',
    5=> 'Fifth',
    6=> 'Sixth',
    7=> 'Seventh',
    8=> 'Eighth',
    9=> 'Ninth',
    10=> 'Tenth'
  );
  return $l[$num];
}

function get_current_phase(){
  $current_phase_row = mysql_fetch_assoc(CONFIG::query('
    SELECT value
    FROM scalars
    WHERE item = ?;
  ', array('current_phase')));
  return $current_phase_row['value'];
}

$reg_enabled_row = mysql_fetch_assoc(CONFIG::query('
  SELECT value
  FROM scalars
  WHERE item = ?;
', array('registration_enabled')));
$reg_enabled = $reg_enabled_row['value'];

$reg_code_row = mysql_fetch_assoc(CONFIG::query('
  SELECT value
  FROM scalars
  WHERE item = ?;
', array('registration_code')));
$reg_code = $reg_code_row['value'];

$groups = CONFIG::query('
  SELECT id AS number, active AS group_is_active
  FROM groups;
');

$t = new vlibTemplate('admin_home.tmpl');
$t->setvar('current_phase', number_to_text(get_current_phase()));
$t->setvar('registration_enabled', $reg_enabled);
$t->setvar('registration_code', $reg_code);
$t->setdbloop('groups', $groups);


$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'Home');
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();
?>
