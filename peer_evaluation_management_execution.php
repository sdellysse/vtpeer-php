<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function add_question($role, $question_text){
  CONFIG::query('
    INSERT INTO
    peer_evaluation_questions(reviewee_role, text)
    VALUES(?, ?);
  ', array($role, $question_text));
}

$action = $_REQUEST['action'];
$role = $_REQUEST['role'];
$question_text = $_REQUEST['question_text'];
$previous_page = $_REQUEST['previous_page'];

if($action === 'Add Question'){
  if(strlen($question_text) == 0){
    CONFIG::redirect("$previous_page?empty_question_text");
  }
  add_question($role, $question_text);
  CONFIG::redirect($previous_page . '?question_added');
}

