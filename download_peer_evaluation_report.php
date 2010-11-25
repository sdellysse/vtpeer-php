<?php
require_once 'config.php';
require_once 'footer.php';
require_once 'menu_admin.php';
CONFIG::connect('admin');

function get_grade($array){
  return rand(0,100);
}

function get_avg_received($user){
  return rand(0,100);
}

function get_avg_given($user){
  return rand(0,100);
}

$rows = array();
$each_member = array();

//shows each comment each group member gave for each phase
$each_member_sql = CONFIG::query('
  SELECT DISTINCT username, first_name, last_name
  FROM student_information A INNER JOIN peer_evaluation B
  ON A.username = B.reviewer
  WHERE group_id = ?
  ORDER BY username;
', array($_REQUEST['group_id']));

while($row = mysql_fetch_assoc($each_member_sql)){
  $each_comment = array();
  $each_comment_sql = CONFIG::query('
    SELECT reviewee_first_name, reviewee_last_name, comment, phase
    FROM peer_eval_comments_by_reviewer
    WHERE reviewer = ?;
  ', array($row['username']));
  while($comment_row = mysql_fetch_assoc($each_comment_sql)){
    array_push($each_comment, $comment_row);
  }
  $row['each_comment'] = $each_comment;
  array_push($each_member, $row);
}
//end each comment

$group_members = array();
$group_members_sql = CONFIG::query('
  SELECT username, first_name, last_name
  FROM student_information
  WHERE group_id = ?
  ORDER BY username;
', array($_REQUEST['group_id']));
while($row = mysql_fetch_assoc($group_members_sql)){
  array_push($group_members, $row);
}

$group_members_rows = array();
$average_grade_given_columns = array();
foreach($group_members as $reviewer){
  $row = array();
  $row['first_name'] = $reviewer['first_name'];
  $row['last_name'] = $reviewer['last_name'];

  $grades = array();
  foreach($group_members as $reviewee){
    $grade = array();
    if($reviewee['username'] == $reviewer['username']){
      $grade['grade'] = false;
    }else{
      $grade['grade'] = get_grade(array(
        'reviewer' => $reviewer['username'],
        'reviewee' => $reviewee['username']
      ));
    }
    array_push($grades, $grade);
  }

  $row['grades'] = $grades;
  $row['avg_grade_received'] = get_avg_received($reviewee['username']);
  array_push($group_members_rows, $row);

  $avg_grades_given_column = array(
    'grade' => get_avg_given($reviewer['username'])
  );
  array_push($average_grade_given_columns, $avg_grades_given_column);
}

$t = new vlibTemplate('download_peer_evaluation_report.tmpl');
$t->setvar('group_id', $_REQUEST['group_id']);
$t->setloop('group_members', $group_members);
$t->setloop('rows', $group_members_rows);
$t->setloop('each_member', $each_member);
$t->setloop('average_grade_given', $average_grade_given_columns);

///////////////////////////////////////////////////////////////////////////
//Above was copied from view_peer_evaluation.php . please update accordingly.
//TODO fix this dependency.
//TODO send correct headers.
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: inline; filename=download.xml");
header("Pragma: no-cache");
header("Expires: 0");
echo '<?xml version="1.0"?>' . "\n";
echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
$t->pparse();
