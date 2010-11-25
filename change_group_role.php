<?php
require_once 'config.php';
require_once 'footer.php';
require_once 'menu_student.php';
CONFIG::continue_session();
CONFIG::connect();

function display(){
  $t = new vlibTemplate('change_group_role.tmpl');

  if(isset($_REQUEST['success'])){
    $t->setvar('success', $_REQUEST['success']);
  }
  $current_role_row = mysql_fetch_assoc(CONFIG::query('
    SELECT role_name
    FROM student_information
    WHERE username = ?;
  ', array(CONFIG::session_get('username'))));
  $current_role = $current_role_row['role_name'];

  $t->setvar('role', $current_role);
  $t->setdbloop('roles', CONFIG::query('
    SELECT id, text
    FROM group_roles
    WHERE text != ?;
  ', array($current_role)));
  $t->setvar('previous_page', $_SERVER['PHP_SELF']);

  $layout = new vlibTemplate('layout.tmpl');
  $layout->setvar('main', $t->grab());
  $layout->setvar('footer', footer());
  $layout->setvar('navigation', menu_student());
  $layout->setvar('title', 'Change Group Role');

  $layout->pparse();
  exit(0);
}

display();
