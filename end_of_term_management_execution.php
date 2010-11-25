<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

function set_enabledness($b){
  $bb = ($b ? '1' : '0');
  CONFIG::query('
    UPDATE scalars
    SET value=?
    WHERE item=?;
  ', array($bb, 'end_of_term'));
}

$action = $_REQUEST['action'];
$previous_page = $_REQUEST['previous_page'];

if($action == 'Enable Evaluations'){
  set_enabledness(true);
  CONFIG::redirect("$previous_page?evaluations_enabled");
}
if($action == 'Disable Evaluations'){
  set_enabledness(false);
  CONFIG::redirect("$previous_page?evaluations_disabled");
}

