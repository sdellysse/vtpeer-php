<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function startswith($haystack, $needle){
  return strpos($haystack, $needle) === 0;
}

$previous_page = $_REQUEST['previous_page'];
$questions = array();

foreach($_REQUEST as $key=>$value){
  if(startswith($key, 'question_')){
    $new_key = substr($key, strlen('question_'));
    $questions[$new_key] = $value;
  }
}

foreach($questions as $key=>$value){
  echo "<!-- k: $key; v: $value -->\n";
  CONFIG::query('
    UPDATE peer_evaluation_questions
    SET text=?
    WHERE id=?;
  ', array($value, $key)) or die(mysql_error());
}

CONFIG::redirect("$previous_page?questions_edited");
