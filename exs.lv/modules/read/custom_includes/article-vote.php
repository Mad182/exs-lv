<h2>Konkursam iesniegtie raksti</h2>

<ul class="blockhref mb-col" style="list-style:none;padding:20px">
<?php

$c_pages = $db->get_results("SELECT `id`, `strid`, `title`, `text`, `sm_avatar`, `category` FROM `pages` WHERE `custom_param` = 'vote-2015' ORDER BY `title` ASC");
foreach($c_pages as $c_pg) {
	$c_cat = get_cat($c_pg->category);
	echo '<li style="padding: 4px 20px;margin:4px 20px">';
	echo '<a href="/read/' . $c_pg->strid . '"><img class="av" style="width:75px;height:75px;" src="//img.exs.lv/'.$c_pg->sm_avatar.'"><h3 style="font-size:110%;margin-top:0;padding-top:2px">' . $c_pg->title . ' <span style="color:#ccc">('.str_replace('Spēļu portāls', 'Jaunumi', $c_cat->title).')</span></h3><p>' . textlimit(trim(strip_tags($c_pg->text)), 220) . '</p></a><div class="c"></div>';
	echo '</li>';
}
?>
</ul>

