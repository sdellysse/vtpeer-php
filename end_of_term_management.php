<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function evaluations_enabled(){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT value
    FROM scalars
    WHERE item=?;
  ', array('end_of_term')));
  return '1' == $row['value'];
}
$t = new vlibTemplate('end_of_term_management.tmpl');
$t->setvar('evaluations_enabled', evaluations_enabled());
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
if(isset($_REQUEST['evaluations_enabled'])){
  $t->setvar('evaluations_enabled', 1);
}
if(isset($_REQUEST['evaluations_disabled'])){
  $t->setvar('evaluations_disabled', 1);
}


$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'End of Term Management');
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();
