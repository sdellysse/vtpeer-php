<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

$query = '
  UPDATE scalars
  SET value = ?
  WHERE item = ?;
';

$_REQUEST['enable'] = 1;
if($_REQUEST['action'] == 'Disable Registration'){
  $_REQUEST['enable'] = 0;
}

CONFIG::query($query, array(
  $_REQUEST['enable'], 'registration_enabled'
)) or die(mysql_error());

if($_REQUEST['enable']){
  CONFIG::query($query, array(
    $_REQUEST['code'], 'registration_code'
  )) or die(mysql_error());
}

CONFIG::redirect($_REQUEST['previous_page'] . '?registration_updated');
?>
