<?php
require_once 'config.php';
require_once 'menu_student.php';
require_once 'footer.php';
CONFIG::continue_session();
CONFIG::connect();

function members($group_id){
  $members = array();
  $usernames = get_group_usernames(get_group_id());
  foreach($usernames as $username){
    $row = array();
    $row['completed'] = members__completed(CONFIG::session_get('username'), $username);
    $row['username'] = $username;
    $row['first_name'] = members__first_name($username);
    $row['last_name'] = members__last_name($username);
    array_push($members, $row);
  }
  return $members;
}

function members__completed($reviewer, $reviewee){
  return 1 == mysql_num_rows(CONFIG::query('
    SELECT reviewer, reviewee
    FROM eot_evaluation
    WHERE reviewer = ?
    AND reviewee = ?;
  ', array($reviewer, $reviewee)));
}

function members__first_name($username){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT first_name
    FROM users
    WHERE username=?;
  ', array($username)));
  return $row['first_name'];
}

function members__last_name($username){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT last_name
    FROM users
    WHERE username=?;
  ', array($username)));
  return $row['last_name'];
}

function get_group_id(){
  $row = mysql_fetch_assoc(CONFIG::query('
    SELECT group_id
    FROM students
    WHERE username = ?;
  ', array(CONFIG::session_get('username'))));
  return $row['group_id'];
}

function get_group_usernames($group_id){
  $usernames = array();
  $result = CONFIG::query('
    SELECT username
    FROM students
    WHERE group_id = ?
    AND username != ?
    ORDER BY username;
  ', array($group_id, CONFIG::session_get('username')));
  while($row = mysql_fetch_assoc($result)){
    $usernames []= $row['username'];
  }
  return $usernames;
}

$t = new vlibTemplate('end_of_term_evaluation_selection.tmpl');
$t->setloop('members', members(get_group_id()));
$t->setvar('previous_page', $_SERVER['PHP_SELF']);
if(isset($_REQUEST['evaluation_completed'])){
  $t->setvar('evaluation_completed', 1);
  $t->setvar('first_name', $_REQUEST['first_name']);
  $t->setvar('last_name', $_REQUEST['last_name']);
}

$layout = new vlibTemplate('layout.tmpl');
$layout->setvar('main', $t->grab());
$layout->setvar('navigation', menu_student());
$layout->setvar('footer', footer());
$layout->setvar('title', 'Select an End of Term Evaluation');

$layout->pparse();

