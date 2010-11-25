<?php
require_once 'config.php';
require_once 'menu_admin.php';
require_once 'footer.php';

CONFIG::continue_session('admin');
CONFIG::connect();

function startswith($haystack, $needle){
  return strpos($haystack, $needle) === 0;
}

$previous_page = $_REQUEST['previous_page'];
$selections = array();

foreach($_REQUEST as $key=>$value){
  if(startswith($key, 'selection_')){
    array_push($selections, substr($key, strlen('selection_')));
  }
}

$t = new vlibTemplate("peer_evaluation_question_editor.tmpl");
$t->setloop('questions', questions($selections));
$t->setvar('previous_page', $previous_page);

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'Question Editor');
$layout->setvar('footer', footer());
$layout->setvar('navigation', menu_admin());
$layout->pparse();

function questions($ids){
  $questions = array();
  foreach($ids as $id){
    $q_sql = CONFIG::query('
      SELECT peer_evaluation_questions.id AS id
      , group_roles.text AS role
      , peer_evaluation_questions.text AS question_text
      FROM peer_evaluation_questions INNER JOIN group_roles
      ON peer_evaluation_questions.reviewee_role = group_roles.id
      WHERE peer_evaluation_questions.id = ?;
    ', array($id));
    $q_row = mysql_fetch_assoc($q_sql);
    array_push($questions, $q_row);
  }

  return $questions;
}

