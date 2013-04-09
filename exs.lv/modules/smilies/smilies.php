<?php

$str = "			':D' => 'smiley-grin.png',
			':)' => 'smiley.png',
			':(' => 'smiley-sad.png',
			';)' => 'smiley-wink.png',
			';(' => 'smiley-cry.png',
			'8=)' => 'smiley-cool.png',
			':cool:' => 'smiley-cool.png',
			':sweat:' => 'smiley-sweat.png',
			':P' => 'smiley-razz.png',
			':o:' => 'smiley-surprise.png',
			':|' => 'smiley-neutral.png',
			':lol:' => 'smiley-lol.png',
			':mrgreen:' => 'mrgreen.gif',
			':eek:' => 'smiley-eek.png',
			':roll:' => 'smiley-roll.png',
			':cat:' => 'smiley-kitty.png',
			':kitty:' => 'smiley-kitty.png',
			':minka:' => 'smiley-kitty.png',
			':confused:' => 'smiley-confuse.png',
			':nerd:' => 'smiley-nerd.png',
			':sleep:' => 'smiley-sleep.png',
			':fat:' => 'smiley-fat.png',
			':evil:' => 'smiley-evil.png',
			':twist:' => 'smiley-twist.png',
			':red:' => 'smiley-red.png',
			':blush:' => 'smiley-red.png',
			':yell:' => 'smiley-yell.png',
			':slim:' => 'smiley-slim.png',
			':money:' => 'smiley-money.png',
			':cry:' => 'smiley-cry.png',
			':kiss:' => 'smiley-kiss.png',
			':sad:' => 'smiley-sad.png',
			':angel:' => 'smiley-angel.png',
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
			':heart:' => 'heart.png',
			':sirds:' => 'heart.png',
			':game:' => 'game.png',
			':apple:' => 'fruit.png',
			':candle:' => 'candle.png',
			':candle-white:' => 'candle-white.png',
			':beer:' => 'beer.gif',
			':alus:' => 'beer.gif',
			':rofl:' => 'rofl.gif',
			':latvija:' => 'latvija.gif',
			':audi:' => 'kissmyrings.gif',
			':shura:' => 'shura.gif',
			':rock:' => 'rock.gif',
			':geek:' => 'icon_geek.gif',
			':mjau:' => 'mjau.gif',
			':hihi:' => 'hihi.gif',
			':thumb:' => 'icon_thumleft.gif',
			':crazy:' => 'crazy.gif',
			':facepalm:' => 'facepalm.gif',
			':ile:' => 'loveexs.gif',
			':pohas:' => 'pohas.gif',
			':cepure:' => 'cepure.gif',
			':ban:' => 'ban.gif',
			':tease:' => 'tease.gif',
			':agresivs:' => 'agresivs.gif',
			':slims:' => 'ill.gif',
			':zzz:' => 'lazy.gif',
			':yahoo:' => 'yahoo.gif',
			':shock:' => 'shok.gif',
			':fool:' => 'fool.gif'";

$aa = explode("\n",$str);

$out = '<table class="main-table">';
foreach($aa as $a) {
	$out .= '<tr>';
	$bb = explode(' => ', $a);
	$bb[0] = trim(str_replace("'", '', $bb[0]));

	$out .= '<td>'.$bb[0].'</td>';
	$out .= '<td>'.add_smile($bb[0]).'</td>';

	
	$out .= '</tr>';

}

$out .= '</table>';

$tpl->assignGlobal('smilies-out', $out);
