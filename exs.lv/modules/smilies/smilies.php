<?php

$smilies_arr = insert_smilies('', true);

$out = '<table class="table">';
$i = 1;
foreach ($smilies_arr as $symbol => $icon) {
	$i++;
	if($i % 2 == 0) {
		$out .= '<tr>';
	}
	$out .= '<td style="width:99px">' . $symbol . '</td>';
	$out .= '<td>' . add_smile($symbol) . '</td>';

	if($i % 2 == 1) {
		$out .= '</tr>';
	}
}

$out .= '</table>';

$tpl->assignGlobal('smilies-out', $out);

