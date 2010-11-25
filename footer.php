<?php
require_once 'config.php';
function footer(){
  #TODO
  return '';
  $t = new vlibTemplate('footer.tmpl');
  $items = array();
  for($i = 0; $i < 3; $i++){
    array_push($items, array('url' => '#', 'text' => "Footer item $i"));
  }
  $t->setloop('footer_items', $items);
  return $t->grab();
}
?>
