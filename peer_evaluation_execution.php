<?php
require_once 'config.php';
CONFIG::continue_session();
CONFIG::connect();

$reviewer = CONFIG::session_get('username');
$reviewee = $_REQUEST['reviewee'];
$previous_page = $_REQUEST['previous_page'];
$comment = $_REQUEST['comment'];

$questions = array();
foreach($_REQUEST as $key => $value){
  if(strpos($key, 'question_') === 0){
    $questions[substr_replace($key, '', 0, strlen('question_'))] = $value;
  }
}

$reviewer_role_row = mysql_fetch_assoc(CONFIG::query('
  SELECT current_role
  FROM students
  WHERE username = ?;
', array($reviewer)));
$reviewer_role = $reviewer_role_row['current_role'];

$reviewee_role_row = mysql_fetch_assoc(CONFIG::query('
  SELECT current_role
  FROM students
  WHERE username = ?;
', array($reviewee)));
$reviewee_role = $reviewee_role_row['current_role'];

$current_phase_row = mysql_fetch_assoc(CONFIG::query('
  SELECT value
  FROM scalars
  WHERE item = ?;
', array('current_phase')));
$current_phase = $current_phase_row['value'];

CONFIG::query('
  INSERT INTO peer_evaluation(
    reviewer, reviewee, reviewer_role, reviewee_role, phase, comment
  )VALUES(
    ?, ?, ?, ?, ?, ?
  );
', array(
  $reviewer, $reviewee, $reviewer_role, $reviewee_role, $current_phase, $comment
));
$peer_evaluation_id = mysql_insert_id();

foreach($questions as $number => $response){
  CONFIG::query('
    INSERT INTO peer_evaluation_responses(
      question_id, evaluation_id, response
    )VALUES(
      ?, ?, ?
    );
  ', array($number, $peer_evaluation_id, $response));
}

$name_info = mysql_fetch_assoc(CONFIG::query('
  SELECT first_name, last_name
  FROM users
  WHERE username = ?;
', array($reviewee)));
CONFIG::redirect($previous_page . "?evaluation_completed&first_name={$name_info['first_name']}&last_name={$name_info['last_name']}");
?>
