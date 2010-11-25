<?php
require_once 'config.php';
CONFIG::connect('admin');

$previous_page = $_REQUEST['previous_page'];
$action = $_REQUEST['action'];

function startswith($haystack, $needle){
  return strpos($haystack, $needle) === 0;
}

function add_group($count, $active){
  for($i=0; $i<$count; $i++){
    CONFIG::query('
    INSERT INTO groups(active)
    VALUES(?);
  ', array($active)) or die(mysql_error());
  }
}

function set_group_activeness($group_id, $active){
  CONFIG::query('
    UPDATE groups
    SET active = ?
    WHERE id = ?;
  ', array($active, $group_id)) or die(mysql_error());
}

if($action === 'Add Active Groups'){
  add_group($_REQUEST['group_count'], 1);
  CONFIG::redirect("$previous_page?num_groups_added={$_REQUEST['group_count']}&active=1");
}
if($action === 'Add Inactive Groups'){
  add_group($_REQUEST['group_count'], 0);
  CONFIG::redirect("$previous_page?num_groups_added={$_REQUEST['group_count']}&active=0");
}
if($action === 'Make Active'){
  foreach($_REQUEST as $key => $value){
    if(startswith($key, 'selection_')){
      $new_key = substr($key, strlen('selection_'));
      set_group_activeness($new_key, 1);
    }
  }
  CONFIG::redirect("$previous_page?groups_made_active=1");
}
if($action === 'Make Inactive'){
  foreach($_REQUEST as $key => $value){
    if(startswith($key, 'selection_')){
      $new_key = substr($key, strlen('selection_'));
      set_group_activeness($new_key, 0);
    }
  }
  CONFIG::redirect("$previous_page?groups_made_active=0");
}
