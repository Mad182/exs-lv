<?php

// nerādīs apakšējo dev joslu, kas sadaļā neiederas :)
$debug = false;

$msgs = array(
	'<p>Cilvēki ar aizspriedumiem piektdienu uzskata par sevišķi neveiksmīgu dienu, ja tā iekrīt 13. datumā. Bailes no piektdienas psihologi sauc par paraskevidekatriafobiju.</p>',
	'<p>Senajā Romā piektdiena bija veltīta dievietei Venērai. Vēl mūsdienās itāļu valodā piektdienu sauc venerdì, tāpat spāņu un franču nosaukumi atvasināti no Venēras. Senie ģermāņi piektdienu dēvēja auglības dievietes Freijas vārdā (angļu: Friday). Arī vācu nosaukums Freitag cēlies no Freijas.</p>',
	'<p>Piektdiena ir nedēļas piektā diena. Piektdiena ir pēdējā darba nedēļas diena valstīs ar piecu dienu darba nedēļu.</p>',
	'<p>Īso/garo gadu sistēma izraisa statistisku anomāliju - iespēja, ka piektdiena būs 13. datumā ir par 0.3% lielāka, kā jebkurā citā datumā.</p>',
	'<p>Musulmaņu likumi aizliedz sodīt vergus piektdienā.</p>',
	'<p>Saudi Arābijā un Irānā nedēļas sākas ar sestdienu un beidzas ar piektdienu.</p>',
	'<p>Kristietībā Lielā piektdiena ir piektdiena pirms Lieldienām.</p>',
	'<p>Pēc Romas Katoļu tradīcijām, piektdienās nedrīkst ēst gaļu :(</p>',
	'<p>Melnā piektdiena attiecas uz visām katastrofām, kas vēsturē notikušas piektdienā, 13. To attiecina arī uz visām piektdienām, kuras ir 13. datumā.</p>',
);
shuffle($msgs);

if (date('l') == 'Friday') {
	$out = '<h1 class="a win">PIEGDIENAAAAAAAAH!!!</h1><object width="560" height="349"><param name="movie" value="http://www.youtube.com/v/6GggY4TEYbk?fs=1&autoplay=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/6GggY4TEYbk?fs=1&autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="349"></embed></object>';
} else {
	$out = '<h1 class="a fail">Nope. Turpini refrešot... :(</h1>' . $msgs[0];
}

$tpl->assignGlobal('out', $out);
