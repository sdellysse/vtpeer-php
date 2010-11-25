<?php
require_once 'config.php';
function menu_student(){
  $t = new vlibTemplate('menu.tmpl');

  $sub_menus = array();
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Home', 'url'=> 'student_home.php'),
      array('text'=> 'Change Password', 'url'=>'student_change_password.php')
    )
  ));
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Change Group Role', 'url'=> 'change_group_role.php')
    )
  ));
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Select Peer Evaluation', 'url'=> 'peer_evaluation_selection.php')#,
      #array('text'=> 'Perform Group Evaluation', 'url'=> 'perform_group_evaluation.php'),
      #array('text'=> 'Select End of Term Evaluation', 'url'=> 'end_of_term_evaluation_selection.php')
    )
  ));

  $t->setloop('sub_menus', $sub_menus);
  return $t->grab();
}
?>
