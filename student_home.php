<?php
require_once 'config.php';
require_once 'menu_student.php';
require_once 'footer.php';
CONFIG::connect();
CONFIG::continue_session();

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

function get_current_role(){
  $current_role_row = mysql_fetch_assoc(CONFIG::query('
    SELECT role_name
    FROM student_information
    WHERE username = ?;
  ', array(CONFIG::session_get('username'))));
  return $current_role_row['role_name'];
}

function get_group_id(){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT group_id
    FROM students
    WHERE username = ?;
  ', array(CONFIG::session_get('username'))));
  return $row['group_id'];
}

$reviewees = CONFIG::query('
  SELECT reviewee, first_name, last_name, role_name
  FROM reviewer_reviewee_matrix INNER JOIN student_information
  ON reviewer_reviewee_matrix.reviewee = student_information.username
  WHERE reviewer = ?
  AND group_id = ?;
', array(CONFIG::session_get('username'), get_group_id()));
$reviewees_status = array();
while($row = mysql_fetch_assoc($reviewees)){
  $row['is_eval_finished'] = (
    mysql_num_rows(CONFIG::query('
      SELECT id
      FROM peer_evaluation
      WHERE phase = ?
      AND reviewer = ?
      AND reviewee = ?;
    ', array(
      get_current_phase(),
      CONFIG::session_get('username'),
      $row['reviewee']
  ))));
  array_push($reviewees_status, $row);
}

$t = new vlibTemplate('student_home.tmpl');
$t->setvar('current_phase', number_to_text(get_current_phase()));
$t->setvar('your_role', get_current_role());
$t->setloop('reviewees', $reviewees_status);
if(get_current_role() !== 'Staff'){
  $group_id = CONFIG::index(
    mysql_fetch_assoc(CONFIG::query('
      SELECT group_id
      FROM student_information
      WHERE username = ?;
    ', array(CONFIG::session_get('username'))
    )), 'group_id'
  );

  if(1 != mysql_num_rows(CONFIG::query('
    SELECT username
    FROM student_information
    WHERE role_name = ?
    AND group_id = ?;
  ', array(get_current_role(), $group_id)))
  ){
    $t->setvar('role_too_crowded', 1);
  }
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('navigation', menu_student());
$layout->setvar('footer', footer());
$layout->setvar('main', $t->grab());
$layout->setvar('title', 'Home - Student Status');

$layout->pparse();
?>
