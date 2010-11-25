<?php
require_once 'config.php';
function menu_login(){
  $t = new vlibTemplate('menu.tmpl');

  $sub_menus = array();
  array_push($sub_menus, array(
    'menu_items'=> array(
      array('text'=> 'Login', 'url'=> 'login.php'),
      array('text'=> 'Register', 'url'=> 'register.php')
    )
  ));

  $t->setloop('sub_menus', $sub_menus);
  return $t->grab();
}
?>
