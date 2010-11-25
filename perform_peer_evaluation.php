<?php
require_once 'config.php';
require_once 'menu_student.php';
require_once 'footer.php';

CONFIG::continue_session();
CONFIG::connect();



$previous_page = $_REQUEST['previous_page'];
if(!isset($_REQUEST['member'])){
  CONFIG::redirect("$previous_page?no_user_selected");
}

$reviewee = $_REQUEST['member'];

$reviewee_row = mysql_fetch_assoc(CONFIG::query('
  SELECT first_name, last_name, role_name
  FROM student_information
  WHERE username = ?;
', array($reviewee)));
$reviewee_role = $reviewee_row['role_name'];
$reviewee_first_name = $reviewee_row['first_name'];
$reviewee_last_name = $reviewee_row['last_name'];


$t = new vlibTemplate('perform_peer_evaluation.tmpl');
$t->setvar('reviewee_first_name', $reviewee_first_name);
$t->setvar('reviewee_last_name', $reviewee_last_name);
$t->setvar('reviewee_role', $reviewee_role);
$t->setvar('previous_page', $previous_page);
$t->setvar('reviewee', $reviewee);

$reviewee_role_id_row = mysql_fetch_assoc(CONFIG::query('
  SELECT id
  FROM group_roles
  WHERE text = ?;
', array($reviewee_role)));
$reviewee_role_id = $reviewee_role_id_row['id'];

$t->setdbloop('questions', CONFIG::query('
  SELECT id, text
  FROM peer_evaluation_questions
  WHERE reviewee_role = ?;
', array($reviewee_role_id)));

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'Peer Evaluation');
$layout->setvar('footer', footer());
$layout->setvar('navigation', menu_student());
$layout->pparse();
