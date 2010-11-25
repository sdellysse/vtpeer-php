<?php
require_once 'config.php';
require_once 'footer.php';
require_once 'menu_admin.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$group_id = $_REQUEST['group_id'];
$group_usernames = get_group_usernames($group_id);

$t = new vlibTemplate('view_peer_evaluation_report.tmpl');
$t->setvar('group_id', $group_id);
$t->setloop('group_members', group_members($group_usernames));
$t->setloop('rows', rows($group_usernames));
$t->setloop('average_grade_given', average_grade_given($group_usernames));
$t->setloop('each_member', each_member($group_usernames));

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('title', 'Peer Evaluation Report for Group '. $group_id);
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_admin());
$layout->setvar('footer', footer());

$layout->pparse();

function group_members($usernames){
  $group_members = array();
  foreach($usernames as $username){
    $row = mysql_fetch_assoc(CONFIG::query('
      SELECT first_name, last_name
      FROM users
      WHERE username = ?;
    ', array($username)));
    array_push($group_members, $row);
  }
  return $group_members;
}

function rows($usernames){
  $rows = array();
  foreach($usernames as $reviewee){
    $row = array();
    $row['grades'] = array();
    $row['first_name'] = rows__first_name($reviewee);
    $row['last_name'] = rows__last_name($reviewee);
    foreach($usernames as $reviewer){
      array_push($row['grades'], rows__grade($reviewer, $reviewee));
    }
    $row['average_grade_received'] = rows__average_grade_received($reviewee);

    array_push($rows, $row);
  }

  return $rows;
}

function rows__first_name($reviewee){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT first_name
    FROM users
    WHERE username = ?;
  ', array($reviewee)));
  return $row['first_name'];
}

function rows__last_name($reviewee){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT last_name
    FROM users
    WHERE username = ?;
  ', array($reviewee)));
  return $row['last_name'];
}

function rows__grade($reviewer, $reviewee){
  $grade = array();

  if($reviewer == $reviewee){
    $grade['self_square'] = 1;
    return $grade;
  }

  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT reviewer, reviewee, avg(average_response) AS avg_response
    FROM peer_evaluation_response_summary_information
    WHERE reviewer=? AND reviewee=?
    GROUP BY reviewer, reviewee;
  ', array($reviewer, $reviewee)));
  $grade['grade'] = format_grade($row['avg_response']);

  return $grade;
}

function rows__average_grade_received($reviewee){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT reviewer, reviewee, avg( average_response ) AS average_received
    FROM peer_evaluation_response_summary_information
    WHERE reviewee = ?;
  ', array($reviewee)));
  return format_grade($row['average_received']);
}

function average_grade_given($reviewers){
  $grades = array();
  foreach($reviewers as $reviewer){
    array_push($grades, average_grade_given__grade($reviewer));
  }
  return $grades;
}

function average_grade_given__grade($reviewer){
  $grade = array();

  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT reviewer, reviewee, avg( average_response ) AS average_received
    FROM peer_evaluation_response_summary_information
    WHERE reviewer = ?;
  ', array($reviewer)));
  $grade['grade'] = format_grade($row['average_received']);
  return $grade;
}

function each_member($usernames){
  $each_member = array();
  foreach($usernames as $username){
    if(each_member__member_has_comments($username)){
      array_push($each_member, each_member__each_member($username));
    }
  }

  return $each_member;
}

function each_member__member_has_comments($username){
  return 0 < mysql_num_rows(CONFIG::query('
    SELECT phase
    FROM peer_eval_comments_by_reviewer
    WHERE reviewer = ?;
  ', array($username)));
}

function each_member__each_member($username){
  $member = array();
  $member['first_name'] = each_member__each_member__first_name($username);
  $member['last_name'] = each_member__each_member__last_name($username);
  $member['phases'] = each_member__each_member__phases();
  $member['each_comment_given'] = each_member__each_member__each_comment_given($username);
  $member['each_comment_received'] = each_member__each_member__each_comment_received($username);
  $member['rows_given'] = each_member__each_member__rows_given($username);
  $member['rows_received'] = each_member__each_member__rows_received($username);
  $member['average_grade_given'] = each_member__each_member__average_grade_given($username);
  $member['average_grade_received'] = each_member__each_member__average_grade_received($username);
  return $member;
}

function each_member__each_member__first_name($username){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT first_name
    FROM users
    WHERE username = ?;
  ', array($username)));
  return $row['first_name'];
}

function each_member__each_member__last_name($username){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT last_name
    FROM users
    WHERE username = ?;
  ', array($username)));
  return $row['last_name'];
}

function each_member__each_member__phases(){
  $phases = array();
  $sql = CONFIG::query('
    SELECT id
    FROM phases;
  ');
  while($row = mysql_fetch_assoc($sql)){
    array_push($phases, $row);
  }
  return $phases;
}

function each_member__each_member__each_comment_given($username){
  $each_comment_given = array();
  $comments = each_member__each_member__each_comment_given__comments($username);
  foreach($comments as $comment){
    if(isset($comment)){
      array_push($each_comment_given, $comment);
    }
  }
  return $each_comment_given;
}

function each_member__each_member__each_comment_given__comments($username){
  $comments = array();

  $sql = CONFIG::query('
    SELECT reviewee_first_name AS first_name, reviewee_last_name AS last_name, comment, phase
    FROM peer_eval_comments_by_reviewer
    WHERE reviewer = ?
    ORDER BY phase;
  ', array($username));
  while($row = mysql_fetch_assoc($sql)){
    array_push($comments, $row);
  }

  return $comments;
}

function each_member__each_member__each_comment_received($username){
  $each_comment_received = array();
  $comments = each_member__each_member__each_comment_received__comments($username);
  foreach($comments as $comment){
    if(isset($comment)){
      array_push($each_comment_received, $comment);
    }
  }
  return $each_comment_received;
}

function each_member__each_member__each_comment_received__comments($username){
  $comments = array();

  $sql = CONFIG::query('
    SELECT reviewer_first_name AS first_name, reviewer_last_name AS last_name, comment, phase
    FROM peer_eval_comments_by_reviewee
    WHERE reviewee = ?
    ORDER BY phase;
  ', array($username));
  while($row = mysql_fetch_assoc($sql)){
    array_push($comments, $row);
  }

  return $comments;
}

function each_member__each_member__rows_given($username){
  $rows_given = array();
  $group_id = get_group_id($username);
  $reviewees = each_member__each_member__rows_given__reviewees($group_id, $username);
  foreach($reviewees as $reviewee){
    $row = each_member__each_member__rows_given__row($username, $reviewee);
    array_push($rows_given, $row);
  }
  return $rows_given;
}

function each_member__each_member__rows_given__reviewees($group_id, $reviewer){
  $reviewees = array();
  $reviewees_sql = CONFIG::query('
    SELECT username
    FROM students
    WHERE username != ?
    AND group_id = ?;
  ', array($reviewer, $group_id));
  while($row = mysql_fetch_assoc($reviewees_sql)){
    array_push($reviewees, $row['username']);
  }
  return $reviewees;
}

function each_member__each_member__rows_given__row($reviewer, $reviewee){
  $row = array();
  $row['grades_given_by_phase'] = array();
  $row['first_name'] = each_member__each_member__rows_given__row__first_name($reviewee);
  $row['last_name'] = each_member__each_member__rows_given__row__last_name($reviewee);
  $row['average_grade_given'] = each_member__each_member__rows_given__row__average_grade_given($reviewer, $reviewee);

  $phase_sql = CONFIG::query('SELECT id FROM phases;');
  while($phase_row = mysql_fetch_assoc($phase_sql)){
    $phase = $phase_row['id'];
    $grade = array();
    $grade['grade'] = format_grade(CONFIG::query_one_value('
      SELECT average_response
      FROM peer_evaluation_response_summary_information
      WHERE reviewer = ?
      AND reviewee = ?
      AND phase = ?;
    ', array($reviewer, $reviewee, $phase)));
    array_push($row['grades_given_by_phase'], $grade);
  }
  return $row;
}

function each_member__each_member__rows_given__row__first_name($username){
  return CONFIG::query_one_value('
    SELECT first_name
    FROM users
    WHERE username = ?;
  ', array($username));
}

function each_member__each_member__rows_given__row__last_name($username){
  return CONFIG::query_one_value('
    SELECT last_name
    FROM users
    WHERE username = ?;
  ', array($username));
}

function each_member__each_member__rows_given__row__average_grade_given($reviewer, $reviewee){
  return format_grade(CONFIG::query_one_value('
    SELECT AVG(average_response)
    FROM peer_evaluation_response_summary_information
    WHERE reviewer = ?
    AND reviewee = ?
    GROUP BY reviewer, reviewee;
  ', array($reviewer, $reviewee)));
}

function each_member__each_member__average_grade_given($username){
  $givens = array();
  $phase_sql = CONFIG::query('SELECT id FROM phases;');
  while($row = mysql_fetch_assoc($phase_sql)){
    array_push($givens, each_member__each_member__average_grade_given__phase($row['id'], $username));
  }
  return $givens;
}

function each_member__each_member__average_grade_given__phase($phase, $reviewer){
  $average_grade_given = array();
  $average_grade_given['grade'] = format_grade(CONFIG::query_one_value('
    SELECT AVG(average_response)
    FROM peer_evaluation_response_summary_information
    WHERE reviewer = ?
    AND phase = ?
    GROUP BY reviewer, phase;
  ', array($reviewer, $phase)));
  return $average_grade_given;
}

function each_member__each_member__rows_received($reviewee){
  $rows = array();
  $reviewers = each_member__each_member__rows_received__reviewers($reviewee);
  foreach($reviewers as $reviewer){
    $row = array();
    $row['first_name'] = each_member__each_member__rows_received__row__first_name($reviewer);
    $row['last_name'] = each_member__each_member__rows_received__row__last_name($reviewer);
    $row['grades_received_by_phase'] = each_member__each_member__rows_received__received_by_phase($reviewer, $reviewee);
    $row['average_grade_received'] = each_member__each_member__rows_received__row__average_grade_received($reviewer, $reviewee);
    array_push($rows, $row);
  }
  return $rows;
}

function each_member__each_member__rows_received__reviewers($reviewee){
  $reviewers = array();
  $reviewers_sql = CONFIG::query('
    SELECT username
    FROM students
    WHERE username != ?
    AND group_id = ?;
  ', array($reviewee, get_group_id($reviewee)));
  while($row = mysql_fetch_assoc($reviewers_sql)){
    array_push($reviewers, $row['username']);
  }
  return $reviewers;
}

function each_member__each_member__rows_received__received_by_phase($reviewer, $reviewee){
  $row = array();
  $phase_sql = CONFIG::query('SELECT id FROM phases;');
  while($phase_row = mysql_fetch_assoc($phase_sql)){
    $phase = $phase_row['id'];
    $grade = array();
    $grade['grade'] = format_grade(CONFIG::query_one_value('
      SELECT average_response
      FROM peer_evaluation_response_summary_information
      WHERE reviewer = ?
      AND reviewee = ?
      AND phase = ?;
    ', array($reviewer, $reviewee, $phase)));
    array_push($row, $grade);
  }
  return $row;
}

function each_member__each_member__average_grade_received($reviewee){
  $grades = array();
  $phase_sql = CONFIG::query('SELECT id FROM phases;');
  while($phase_row = mysql_fetch_assoc($phase_sql)){
    $phase = $phase_row['id'];
    $grade = array();

    $grade['grade'] = format_grade(CONFIG::query_one_value('
      SELECT AVG(average_response)
      FROM peer_evaluation_response_summary_information
      WHERE reviewee = ?
      AND phase = ?
      GROUP BY reviewee, phase;
    ', array($reviewee, $phase)));

    array_push($grades, $grade);
  }
  return $grades;
}


function each_member__each_member__rows_received__row__first_name($reviewee){
  return CONFIG::query_one_value('
    SELECT first_name
    FROM users
    WHERE username = ?;
  ', array($reviewee));
}

function each_member__each_member__rows_received__row__last_name($reviewee){
  return CONFIG::query_one_value('
    SELECT last_name
    FROM users
    WHERE username = ?;
  ', array($reviewee));
}

function each_member__each_member__rows_received__row__average_grade_received($reviewer, $reviewee){
  return each_member__each_member__rows_given__row__average_grade_given($reviewer, $reviewee);
}


function get_group_usernames($group_id){
  $usernames = array();
  $result = CONFIG::query('
    SELECT username
    FROM students
    WHERE group_id = ?
    ORDER BY username;
  ', array($group_id));
  while($row = mysql_fetch_assoc($result)){
    $usernames []= $row['username'];
  }
  return $usernames;
}

function get_group_id($username){
  return CONFIG::query_one_value('
    SELECT group_id
    FROM students
    WHERE username=?;
  ', array($username));
}

function format_grade($grade){
  if(isset($grade)){
    return sprintf('%01.2f', $grade);
  }else{
    return false;
  }
}

