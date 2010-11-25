<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session();
CONFIG::connect();

function display(){
  $p = new vlibTemplate('phase_management.tmpl');
  $current_phase_row = mysql_fetch_assoc(CONFIG::query('
    SELECT value
    FROM scalars
    WHERE item = ?;
  ', array('current_phase')));
  $current_phase = $current_phase_row['value'];

  $p->setvar('phase', $current_phase);
  $p->setvar('previous_page', $_SERVER['PHP_SELF']);
  if(isset($_REQUEST['phase_changed'])){
    $p->setvar('phase_changed', 1);
  }
  $p->setdbloop('phases', CONFIG::query('
    SELECT id
    FROM phases;
  '));

  $layout = new vlibTemplate('layout.tmpl');
  $layout->setvar('main', $p->grab());
  $layout->setvar('footer', footer());
  $layout->setvar('navigation', menu_admin());
  $layout->setvar('title', 'Phase Management');

  $layout->pparse();
  exit(0);
}

display();
