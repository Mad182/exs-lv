<?php

$str = "			':sweat:' => 'smiley-sweat.png',
			':o:' => 'smiley-surprise.png',
			':eek:' => 'smiley-eek.png',
			':roll:' => 'smiley-roll.png',
			':confused:' => 'smiley-confuse.png',
			':nerd:' => 'smiley-nerd.png',
			':sleep:' => 'smiley-sleep.png',
			':fat:' => 'smiley-fat.png',
			':twist:' => 'smiley-twist.png',
			':slim:' => 'smiley-slim.png',
			':money:' => 'smiley-money.png',
			':android:' => 'android.png',
			':dog:' => 'animal-dog.png',
			':monkey:' => 'animal-monkey.png',
			':pingvins:' => 'animal-penguin.png',
			':linux:' => 'animal-penguin.png',
			':windows:' => 'windows.png',
			':mac:' => 'mac-os.png',
			':applefag:' => 'mac-os.png',
			':bug:' => 'bug.png',
			':star:' => 'star.png',
			':zvaigzne:' => 'star.png',
			':cookie:' => 'cookie.png',
			':cookies:' => 'cookies.png',
			':burger:' => 'hamburger.png',
			':burgers:' => 'hamburger.png',
			':game:' => 'game.png',
			':apple:' => 'fruit.png',
			':candle:' => 'candle.png',
			':candle-white:' => 'candle-white.png',
			':latvija:' => 'latvija.gif',
			':audi:' => 'kissmyrings.gif',
			':shura:' => 'shura.gif',
			':geek:' => 'icon_geek.gif',
			':tease:' => 'tease.gif',
			':slims:' => 'ill.gif',
			':zzz:' => 'lazy.gif',
			':shock:' => 'shok.gif',
			':beer:' => 'beer.gif',
			':alus:' => 'beer.gif',
			':pohas:' => 'pohas.gif',
			':cepure:' => 'cepure.gif',
			':crazy:' => 'crazy.gif',
			':rokas:' => 'rokas.gif',
			':facepalm:' => 'facepalm.gif',
			':hihi:' => 'hihi.gif',
			':ile:' => 'loveexs.gif',
			':ban:' => 'ban.gif',
			':mjau:' => 'mjau.gif',
			':rock:' => 'rock.gif',
			':drink:' => 'drink_mini.gif',
			':lol:' => 'lol_mini.gif',
			':happy:' => 'happy_mini.gif',
			':greeting:' => 'greeting_mini.gif',
			':cry:' => 'cray_mini.gif',
			':dance:' => 'dance_mini.gif',
			';(' => 'cray_mini2.gif',
			':acute:' => 'acute_mini.gif',
			':thumb:' => 'good_mini.gif',
			':aggressive:' => 'aggressive_mini.gif',
			':agresivs:' => 'aggressive_mini.gif',
			':beee:' => 'beee_mini.gif',
			':bomb:' => 'bomb_mini.gif',
			':puke:' => 'bo_mini.gif',
			':mrgreen:' => 'biggrin_mini.gif',
			':D' => 'biggrin_mini2.gif',
			':P' => 'blum_mini.gif',
			':blush:' => 'blush_mini.gif',
			':kiss:' => 'air_kiss_mini.gif',
			':angel:' => 'angel_mini.gif',
			':bored:' => 'boredom_mini.gif',
			':bye:' => 'bye_mini.gif',
			':chok:' => 'chok_mini.gif',
			':clap:' => 'clapping_mini.gif',
			':headbang:' => 'dash_mini.gif',
			':evil:' => 'diablo_mini.gif',
			'8=)' => 'dirol_mini.gif',
			':cool:' => 'dirol_mini.gif',
			':fool:' => 'fool_mini.gif',
			':heart:' => 'heart_mini.gif',
			':sirds:' => 'heart_mini.gif',
			':help:' => 'help_mini.gif',
			':laugh:' => 'laugh_mini.gif',
			':mad:' => 'mad_mini.gif',
			':mail:' => 'mail1_mini.gif',
			':mamba:' => 'mamba_mini.gif',
			':inlove:' => 'man_in_love_mini.gif',
			':mocking:' => 'mocking_mini.gif',
			':music:' => 'music_mini.gif',
			':nea:' => 'nea_mini.gif',
			':fingers:' => 'new_russian_mini.gif',
			':ok:' => 'ok_mini.gif',
			':pardon:' => 'pardon_mini.gif',
			':rofl:' => 'rofl_mini.gif',
			':rolleyes' => 'rolleyes_mini.gif',
			':rose:' => 'rose_mini.gif',
			':(' => 'sad_mini.gif',
			':sad:' => 'sad_mini2.gif',
			':think:' => 'scratch_one-s_head_mini.gif',
			':secret:' => 'secret_mini.gif',
			':shout:' => 'shout_mini.gif',
			':)' => 'smile_mini.gif',
			':sorry:' => 'sorry_mini.gif',
			':|' => 'connie_mini_huh.gif',
			':stop:' => 'stop_mini.gif',
			':dunno:' => 'unknw_mini.gif',
			':unsure:' => 'unsure_mini.gif',
			':vava:' => 'vava_mini.gif',
			':wacko:' => 'wacko_mini.gif',
			';)' => 'wink_mini.gif',
			':yahoo:' => 'yahoo_mini.gif',
			':yes:' => 'yes_mini.gif',
			':yell:' => 'shout_mini.gif',
			':cat:' => 'connie_mini_kitty.gif',
			':minka:' => 'connie_mini_kitty.gif',
			':buck:' => 'connie_mini_buck.gif',
			':bump:' => 'connie_mini_bump.gif',
			':shifty:' => 'shifty.gif',
			':bulduris:' => 'bulduris.png',
			':agility:' => 'agility.png',
			':11:' => '11.png',
			':zagis:' => 'chainsaw.gif',
			':dickbutt:' => 'dickbutt.gif'";

$aa = explode("\n", $str);

$i = 1;

$out = '<table class="table">';
foreach ($aa as $a) {

	$i++;

	if($i % 2 == 0) {
		$out .= '<tr>';
	}

	$bb = explode(' => ', $a);
	$bb[0] = trim(str_replace("'", '', $bb[0]));

	$out .= '<td style="width:99px">' . $bb[0] . '</td>';
	$out .= '<td>' . add_smile($bb[0]) . '</td>';

	if($i % 2 == 1) {
		$out .= '</tr>';
	}

}

$out .= '</table>';

$tpl->assignGlobal('smilies-out', $out);
