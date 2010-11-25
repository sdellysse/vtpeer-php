<?php
require_once 'config.php';
CONFIG::continue_session('admin');
CONFIG::connect();

CONFIG::query('
  UPDATE scalars
  SET value = ?
  WHERE item = ?;
', array(
  $_REQUEST['phase_box'], 'current_phase'
)) or die(mysql_error());

CONFIG::redirect($_REQUEST['previous_page'] . '?phase_changed');
?>
