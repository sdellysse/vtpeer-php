<?php
require_once 'config.php';
require_once 'menu_student.php';
require_once 'footer.php';
CONFIG::continue_session();
CONFIG::connect();

function get_current_role(){
  $current_role_row = mysql_fetch_assoc(CONFIG::query('
    SELECT role_name
    FROM student_information
    WHERE username = ?;
  ', array(CONFIG::session_get('username'))));
  return $current_role_row['role_name'];
}

function role_too_crowded($group_id){
  if (get_current_role() == 'Staff'){
    return 0;
  }
  return 1 != mysql_num_rows(CONFIG:: query('
    SELECT username
    FROM student_information
    WHERE role_name = ?
    AND group_id = ?;
    ', array(get_current_role(), $group_id))
  );
}

function staff(){
 return get_current_role() == 'Staff';
}

function no_user_selected(){
  return isset($_REQUEST['no_user_selected']);
}


$current_phase = CONFIG::query_one_value('SELECT value FROM scalars WHERE item=?;', array('current_phase'));
$group_id = CONFIG::query_one_value('SELECT group_id FROM students WHERE username = ?;', array(CONFIG::session_get('username')));


$members = array();
$members_sql = CONFIG::query('
  SELECT reviewee AS username, first_name, last_name, role_name AS role
  FROM reviewer_reviewee_matrix INNER JOIN student_information
  ON reviewer_reviewee_matrix.reviewee = student_information.username
  WHERE reviewer = ?
  AND group_id = ?;
', array(CONFIG::session_get('username'), $group_id));
while($row = mysql_fetch_assoc($members_sql)){
  $row['completed'] = mysql_num_rows(CONFIG::query('
    SELECT id
    FROM peer_evaluation
    WHERE reviewer = ?
    AND reviewee = ?
    AND phase = ?;
  ', array(CONFIG::session_get('username'), $row['username'], $current_phase)));
  array_push($members, $row);
}
$t = new vlibTemplate('peer_evaluation_selection.tmpl');
$t->setloop('members', $members);
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
$t->setvar('role_too_crowded', role_too_crowded($group_id));
$t->setvar('your_role', get_current_role());
$t->setvar('staff', staff());
$t->setvar('no_user_selected', no_user_selected());

if(isset($_REQUEST['evaluation_completed'])){
  $t->setvar('evaluation_completed', 1);
  $t->setvar('first_name', $_REQUEST['first_name']);
  $t->setvar('last_name', $_REQUEST['last_name']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_student());
$layout->setvar('footer', footer());
$layout->setvar('title', 'Select a Peer Evaluation');

$layout->pparse();

