<?php

if($auth->id != 1) {
  die('nothing...');
}

$jquery = true;

$tpl->assignInclude('module-head','modules/' . $category->module . '/head.tpl');
$tpl->prepare();



$out = '';


$positions = $db->get_results("SELECT * FROM exl_map ORDER BY id ASC");

foreach ($positions as $pos) {
  $out .= '<div class="map map-'.$pos->type.'" style="top:'.($pos->posx*25).'px;left:'.($pos->posy*25).'px;">'.$pos->id.'</div>';
}

/*for($i=0;$i<48;$i++) {
 
  for($i2=0;$i2<36;$i2++) {
    $out .= '<div class="map" style="top:'.($i*25).'px;left:'.($i2*25).'px;">lala</div>';
    //$db->query("INSERT INTO exl_map (posx,posy) VALUES ('$i','$i2')");
  } 
 
  
}*/

$tpl->assignGlobal('out',$out);



?>