<?php
require_once 'config.php';
CONFIG::continue_session();
CONFIG::connect();

CONFIG::query('
  UPDATE students
  SET current_role = ?
  WHERE username = ?;
', array(
  $_REQUEST['new_group_role'], CONFIG::session_get('username')
)) or die(mysql_error());

CONFIG::redirect($_REQUEST['previous_page'] . '?success=1');
?>
