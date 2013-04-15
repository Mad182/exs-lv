<?php

function array_average_nonzero($arr) { 
   return array_sum($arr) / count(array_filter($arr)); 
} 

if(isset($_GET['var1'])) {
	$usr = (int) $_GET['var1'];
	$inprofile = get_user($usr);
} elseif($auth->ok) {
	$inprofile = get_user($auth->id);
} else {
	set_fash('Lietotājs nav atrasts!', 'error');
	redirect();
}

$date = date('Y-m-d',strtotime("-1 year last Monday"));

$images = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `images` WHERE `uid` = '" . $inprofile->id . "' AND `date` > date('$date') GROUP BY DATE(`images`.`date`) ORDER BY `date` DESC");
$pages = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `date` > date('$date') GROUP BY DATE(`pages`.`date`) ORDER BY `date` DESC LIMIT 365");
$mbs = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `miniblog` WHERE `author` = '" . $inprofile->id . "' AND `miniblog`.`removed` = '0' AND `date` > date('$date') GROUP BY DATE(`miniblog`.`date`) ORDER BY `date` DESC");
$comments = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `comments` WHERE `author` = '" . $inprofile->id . "' AND `comments`.`removed` = '0' AND `date` > date('$date') GROUP BY DATE(`comments`.`date`) ORDER BY `date` DESC");

$values = array();
foreach ($mbs as $mb) {
	$values[$mb->date] = $mb->count;
}
foreach ($comments as $comments) {
	if(!empty($values[$comments->date])) {
		$values[$comments->date] += $comments->count;
	} else {
		$values[$comments->date] = $comments->count;
	}
}
foreach ($pages as $page) {
	if(!empty($values[$page->date])) {
		$values[$page->date] += $page->count;
	} else {
		$values[$page->date] = $page->count;
	}
}
foreach ($images as $image) {
	if(!empty($values[$image->date])) {
		$values[$image->date] += $image->count;
	} else {
		$values[$image->date] = $image->count;
	}
}

$data = array();
//$max = 0;
for ($i = 0; $i <= 374; $i++) {
	$key = date('Y-m-d', strtotime('-' . $i . ' days'));
	if (!empty($values[$key])) {
		$data[$key] = $values[$key];
		/*if($values[$key] > $max) {
			$max = $values[$key];
		}*/
	} else {
		$data[$key] = 0;
	}
}

$avg = array_average_nonzero($data);
$max = $avg*2.3;

$values = array_reverse($data);

$i = 1;
foreach($values as $key => $val) {
	
	if($key > $date) {
	
		if($i == 1) {
			$tpl->newBlock('week');
		}
	
		if($i == 7) {
			$i = 1;
		} else {
			$i++;
		}
	
		$tpl->newBlock('day');
		
		$percent = (int)(100/$max*$val);
		if($percent > 100) {
			$percent = 100;
		}
	
		$tpl->assign(array(
			'date' => date('Y.m.d', strtotime($key)),
			'count' => $val,
			'percent' => $percent,
			'decimal' => round((100/$max*$val/100),2),
		));
	
	}
}
