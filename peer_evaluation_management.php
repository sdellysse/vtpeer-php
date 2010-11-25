<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$t = new vlibTemplate('peer_evaluation_management.tmpl');
$t->setdbloop('roles', CONFIG::query('
  SELECT id, text
  FROM group_roles;
'));
$t->setdbloop('senior_questions', CONFIG::query('
  SELECT id, text
  FROM peer_evaluation_questions
  WHERE reviewee_role = ?;
', array('2')));
$t->setdbloop('junior_questions', CONFIG::query('
  SELECT id, text
  FROM peer_evaluation_questions
  WHERE reviewee_role = ?;
', array('1')));
$t->setdbloop('staff_questions', CONFIG::query('
  SELECT id, text
  FROM peer_evaluation_questions
  WHERE reviewee_role = ?;
', array('3')));
if(isset($_REQUEST['empty_question_text'])){
  $t->setvar('empty_question_text', 1);
}
if(isset($_REQUEST['no_selection'])){
  $t->setvar('no_selection', 1);
}
if(isset($_REQUEST['question_added'])){
  $t->setvar('question_added', 1);
}
if(isset($_REQUEST['questions_edited'])){
  $t->setvar('questions_edited', 1);
}
$t->setvar('previous_page', $_SERVER['PHP_SELF']);

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());
$layout->setvar('title', 'Peer Evaluation Management');

$layout->pparse();
