<?php
require_once 'config.php';
function menu_admin(){
  $t = new vlibTemplate('menu.tmpl');

  $sub_menus = array();
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Home', 'url'=> 'admin_home.php'),
      array('text'=> 'Change Password', 'url'=> 'admin_change_password.php')
    )
  ));
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Phase Management', 'url'=> 'phase_management.php'),
      array('text'=> 'Registration Management', 'url'=> 'registration_management.php'),
      array('text'=> 'Group Management', 'url'=> 'group_management.php')
    )
  ));
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Peer Evaluation Management', 'url'=> 'peer_evaluation_management.php')#,
      #array('text'=> 'End of Term Management', 'url'=> 'peer_evaluation_management.php'),
      #array('text'=> 'Group Presentation Management', 'url'=> 'peer_evaluation_management.php')
    )
  ));
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Create an Administrator', 'url'=> 'create_admin.php'),
      array('text'=> 'User Management', 'url'=> 'user_management.php'),
      array('text'=> 'View Reports', 'url'=> 'view_reports.php')
    )
  ));

  $t->setloop('sub_menus', $sub_menus);
  return $t->grab();
}
?>
