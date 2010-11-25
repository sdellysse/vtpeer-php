<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$t = new vlibTemplate('view_reports.tmpl');
$t->setdbloop('peer_evaluation_groups', CONFIG::query('
  SELECT id AS group_id
  FROM active_groups
  ORDER BY id;
'));
$t->setdbloop('group_eval_groups', CONFIG::query('
  SELECT id AS group_id
  FROM active_groups
  ORDER BY id;
'));

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'View Reports');
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();
