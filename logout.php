<?php
require_once 'config.php';
CONFIG::continue_session();
CONFIG::end_session();
CONFIG::redirect('login.php');
?>
