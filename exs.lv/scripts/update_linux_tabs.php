<?php

/**
 * update_linux_tabs.php
 *
 * Updates Linux group tabs on exs.lv:
 * - Updates Ubuntu tab with modern LTS info, download links, and rich description
 * - Creates / updates tabs for Debian, Omarchy, Arch, and Mint
 *
 * Usage:
 *   php exs.lv/scripts/update_linux_tabs.php
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

chdir(__DIR__ . '/..');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'exs.lv';

require_once('configdb.php');
require_once('includes/class.mdb.php');

$db = new mdb($username, $password, $database, $hostname);

// Find Linux group (clan_id)
$group = $db->get_row("SELECT id, strid, title FROM clans WHERE strid = 'linux' OR id = 122");
if (!$group) {
    die("Error: Linux group not found!\n");
}

echo "Found group: {$group->title} (ID: {$group->id})\n";

$now = time();

$tabs = [
    [
        'slug' => 'Ubuntu',
        'title' => 'Ubuntu',
        'text' => '<div class="distro-card" style="margin-bottom: 20px;">
	<h2 style="margin-top: 0; margin-bottom: 6px;">Ubuntu</h2>
	<p style="font-size: 15px; color: #555; margin-bottom: 15px;">Pasaulē populārākā un plašāk atbalstītā uz Linux balstītā operētājsistēma personālajiem datoriem, serveriem un mākoņskaitļošanai.</p>

	<table class="table table-bordered table-striped" style="margin-bottom: 18px; max-width: 680px;">
		<tbody>
			<tr>
				<td style="width: 35%; font-weight: bold;">Pašreizējā LTS versija</td>
				<td><strong>Ubuntu 24.04 LTS</strong> („Noble Numbat”) — atbalsts līdz 2029. gadam (ar Ubuntu Pro līdz 2034)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Izlaidumu modelis</td>
				<td>Fiksēts: jauna versija ik pēc 6 mēnešiem, ilgtermiņa LTS laidiens ik pēc 2 gadiem (aprīlī)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Noklusējuma darbvirsma</td>
				<td>GNOME (pielāgota ar ērto Ubuntu dock sānjoslu un modernu izskatu)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Pakotņu pārvaldība</td>
				<td>APT (<code>.deb</code> pakotnes) un Canonical uzturētā Snap sistēma</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Bāze</td>
				<td>Debian</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Piemērots</td>
				<td>Iesācējiem, biroja darbam, programmētājiem, datorspēlēm un serveru infrastruktūrai</td>
			</tr>
		</tbody>
	</table>

	<h3>Par Ubuntu</h3>
	<p>Ubuntu ir Canonical un globālās atvērtā koda kopienas uzturēta operētājsistēma, kuras mērķis kopš pirmsākumiem 2004. gadā ir bijis padarīt Linux pieejamu un ērti lietojamu ikvienam. Ubuntu tiek uzskatīts par zelta standartu programmatūras saderībā — lielākā daļa trešo pušu programmu (Steam, Discord, Spotify, VS Code, Chrome u.c.) piedāvā gatavas instalācijas pakotnes tieši Ubuntu videi.</p>

	<h4>Galvenās iezīmes un priekšrocības:</h4>
	<ul>
		<li><strong>Gatavs lietošanai uzreiz pēc uzstādīšanas:</strong> iekļauts tīmekļa pārlūks, pilns LibreOffice biroja programmu komplekts, multivides atskaņotāji un patentēto draiveru pārvaldnieks (NVIDIA, AMD, Wi-Fi).</li>
		<li><strong>Lietotņu centrs (App Center):</strong> ērts grafiskais katalogs ar tūkstošiem brīvi pieejamu programmu un spēļu.</li>
		<li><strong>Lieliska spēļu saderība:</strong> pateicoties Valve Steam un Proton tehnoloģijām, Ubuntu vidē nevainojami darbojas tūkstošiem Windows spēļu.</li>
		<li><strong>Daudzveidīgas oficiālās versijas (Flavours):</strong> līdzās galvenajai GNOME versijai pieejami varianti Kubuntu (KDE Plasma), Xubuntu (viegls Xfce), Lubuntu (LXQt) un Ubuntu Budgie.</li>
	</ul>

	<h4>Ieteicamās sistēmas prasības:</h4>
	<ul>
		<li>2 GHz divkodolu procesors vai jaunāks (64-bit)</li>
		<li>4 GB operatīvā atmiņa (RAM)</li>
		<li>25 GB brīvas vietas diskā</li>
		<li>USB zibatmiņa (vismaz 8 GB) uzstādīšanai</li>
	</ul>

	<div class="box" style="margin-top: 20px; padding: 14px 16px; border-radius: 4px;">
		<h4 style="margin-top: 0; margin-bottom: 10px;">Lejupielādes un noderīgas saites:</h4>
		<p style="margin-bottom: 12px;">
			<a class="button primary" href="https://ubuntu.com/download/desktop" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-weight: bold; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px;">Lejupielādēt Ubuntu Desktop (LTS)</a>
			<a class="button" href="https://ubuntu.com/download/server" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Ubuntu Server</a>
			<a class="button" href="https://ubuntu.com/desktop/flavours" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Citi varianti (Kubuntu, Xubuntu...)</a>
		</p>
		<p style="font-size: 13px; color: #666; margin-bottom: 0;">
			Oficiālā vietne: <a href="https://ubuntu.com/" target="_blank" rel="noopener noreferrer">ubuntu.com</a> | Dokumentācija: <a href="https://help.ubuntu.com/" target="_blank" rel="noopener noreferrer">help.ubuntu.com</a>
		</p>
	</div>
</div>'
    ],
    [
        'slug' => 'Debian',
        'title' => 'Debian',
        'text' => '<div class="distro-card" style="margin-bottom: 20px;">
	<h2 style="margin-top: 0; margin-bottom: 6px;">Debian</h2>
	<p style="font-size: 15px; color: #555; margin-bottom: 15px;">„Universālā operētājsistēma” — viens no vissenākajiem, stabilākajiem un ietekmīgākajiem atvērtā pirmkoda projektiem pasaulē.</p>

	<table class="table table-bordered table-striped" style="margin-bottom: 18px; max-width: 680px;">
		<tbody>
			<tr>
				<td style="width: 35%; font-weight: bold;">Pašreizējā stabilā versija</td>
				<td><strong>Debian 12</strong> („Bookworm”)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Nākamā versija (Testing)</td>
				<td>Debian 13 („Trixie”)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Izlaidumu modelis</td>
				<td>Fiksēts / Stabils (jauns laidiens aptuveni ik pēc 2 gadiem pēc rūpīgas un pamatīgas testēšanas)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Pieejamās darbvirsmas</td>
				<td>GNOME, KDE Plasma, Xfce, LXDE, LXQt, MATE, Cinnamon (izvēle uzstādīšanas laikā)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Pakotņu pārvaldība</td>
				<td>APT (<code>apt</code>, <code>dpkg</code>) — milzīga krātuve ar vairāk nekā 64 000 pārbaudītu pakotņu</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Piemērots</td>
				<td>Serveriem, stabilām darba stacijām, programmētājiem un lietotājiem, kuri augstāk par visu vērtē uzticamību</td>
			</tr>
		</tbody>
	</table>

	<h3>Par Debian</h3>
	<p>Debian projektu 1993. gadā dibināja Ians Mērdoks (Ian Murdock). Tā ir pilnībā neatkarīga, bezpeļņas un atvērtā koda kopienas vadīta distribūcija, kas balstās uz stingriem brīvās programmatūras principiem (Debian Free Software Guidelines). Uz Debian pamata ir veidotas simtiem citu populāru distribūciju, tostarp Ubuntu, Linux Mint, Kali Linux un Raspberry Pi OS.</p>

	<h4>Kāpēc izvēlēties Debian?</h4>
	<ul>
		<li><strong>Akmenscieta stabilitāte:</strong> Debian Stable laidienos iekļautās programmatūras versijas tiek ilgstoši pārbaudītas, nodrošinot maksimālu drošību un darbības nepārtrauktību bez negaidītiem pārsteigumiem.</li>
		<li><strong>Milzīgs arhitektūru atbalsts:</strong> darbojas praktiski uz jebkuras skaitļošanas platformas — no moderniem 64-bit x86 un ARM datoriem līdz pat Raspberry Pi un specializētiem serveru procesoriem.</li>
		<li><strong>Pilnīga kontrole un brīvība:</strong> instalējot iespējams izvēlēties minimālu bāzes sistēmu bez jebkādas liekas programmatūras (bloatware) un pievienot tikai to, kas patiešām nepieciešams.</li>
		<li><strong>Komplektā ar nebrīvajiem draiveriem (kopš Debian 12):</strong> mūsdienu Debian oficiālajos instalācijas attēlos pēc noklusējuma ir iekļauta nepieciešamā patentētā aparātprogrammatūra (non-free-firmware), kas būtiski atvieglo Wi-Fi un grafisko karšu atpazīšanu instalācijas laikā.</li>
	</ul>

	<div class="box" style="margin-top: 20px; padding: 14px 16px; border-radius: 4px;">
		<h4 style="margin-top: 0; margin-bottom: 10px;">Lejupielādes un noderīgas saites:</h4>
		<p style="margin-bottom: 12px;">
			<a class="button primary" href="https://www.debian.org/download" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-weight: bold; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px;">Lejupielādēt Debian</a>
			<a class="button" href="https://cdimage.debian.org/debian-cd/current-live/amd64/iso-hybrid/" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Debian Live ISO (izmēģināt bez instalēšanas)</a>
			<a class="button" href="https://www.debian.org/distrib/netinst" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Netinst (tīkla instalators)</a>
		</p>
		<p style="font-size: 13px; color: #666; margin-bottom: 0;">
			Oficiālā vietne: <a href="https://www.debian.org/" target="_blank" rel="noopener noreferrer">debian.org</a> | Dokumentācija: <a href="https://www.debian.org/doc/" target="_blank" rel="noopener noreferrer">debian.org/doc</a> | Debian Wiki: <a href="https://wiki.debian.org/" target="_blank" rel="noopener noreferrer">wiki.debian.org</a>
		</p>
	</div>
</div>'
    ],
    [
        'slug' => 'Omarchy',
        'title' => 'Omarchy',
        'text' => '<div class="distro-card" style="margin-bottom: 20px;">
	<h2 style="margin-top: 0; margin-bottom: 6px;">Omarchy</h2>
	<p style="font-size: 15px; color: #555; margin-bottom: 15px;">DHH (David Heinemeier Hansson) radīta, uz Arch Linux bāzēta „agentic” darba vide un operētājsistēma moderniem programmatūras izstrādātājiem.</p>

	<table class="table table-bordered table-striped" style="margin-bottom: 18px; max-width: 680px;">
		<tbody>
			<tr>
				<td style="width: 35%; font-weight: bold;">Autors un izcelsme</td>
				<td>Deivids Heinemeiers Hansons (DHH / 37signals / Ruby on Rails un Basecamp radītājs)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Bāze</td>
				<td>Arch Linux (ar nepārtraukto atjaunināšanu un pilnu AUR krātuves atbalstu)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Logu pārvaldnieks / Saskarne</td>
				<td>Hyprland (dinamisks Wayland flīzēšanas logu pārvaldnieks) + Quickshell darbvirsmas čaula</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Koncepts</td>
				<td>„Agentic OS” — vide ar integrētiem MI programmēšanas aģentiem un pilnu tastatūras kontroli</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Noklusējuma rīki</td>
				<td>Neovim, Chromium, Obsidian, Ghostty terminālis, LibreOffice, 1Password, Signal</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Mērķauditorija</td>
				<td>Web un lietotņu izstrādātāji, kuri vēlas maksimālu ātrumu un produktivitāti bez liekiem klikšķiem</td>
			</tr>
		</tbody>
	</table>

	<h3>Kas ir Omarchy?</h3>
	<p>Omarchy ir viedoklī balstīta (opinionated) operētājsistēma, ko DHH izveidoja savai un 37signals komandas ikdienas darba videi, nomainot macOS pret pilnībā atvērtu, zibenīgu un kontrolējamu Linux darbstaciju. Tā apvieno Arch Linux minimālismu ar gatavu, estētiski izstrādātu un konfigurētu programmētāja darba vidi.</p>

	<h4>Galvenās īpašības:</h4>
	<ul>
		<li><strong>Integrēti MI aģenti („Agentic workflow”):</strong> operētājsistēmā jau sākotnēji iebūvēti palaišanas rīki vadošajiem AI kodēšanas asistentiem (Claude Code, Copilot, Grok u.c.), ļaujot tos acumirklī iesaistīt programmēšanas un atkļūdošanas uzdevumos tieši no termināļa.</li>
		<li><strong>Hyprland flīzēšanas saskarne (Tiling Window Manager):</strong> logi automātiski organizējas ekrānā bez nepieciešamības tos bīdīt ar peli. Visa navigācija un logu pārslēgšana notiek ar ātriem tastatūras īsceļiem.</li>
		<li><strong>Pārdomātas noklusējuma konfigurācijas:</strong> nav jātērē dienas, lai konfigurētu Wayland displeja serveri, fontus, audio, statusa joslu vai krāsu tēmas — viss ir pārdomāts un saskaņots jau no pirmās palaišanas sekundes.</li>
		<li><strong>Drošība un veiktspēja:</strong> instalators piedāvā pilnu diska šifrēšanu (LUKS) un optimizētus iestatījumus moderniem daudzkodolu procesoriem un NVMe diskiem.</li>
	</ul>

	<div class="box" style="margin-top: 20px; padding: 14px 16px; border-radius: 4px;">
		<h4 style="margin-top: 0; margin-bottom: 10px;">Lejupielādes un noderīgas saites:</h4>
		<p style="margin-bottom: 12px;">
			<a class="button primary" href="https://omarchy.org/" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-weight: bold; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px;">Oficiālā vietne (omarchy.org)</a>
			<a class="button" href="https://github.com/basecamp/omarchy" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Omarchy GitHub krātuve</a>
			<a class="button" href="https://omarchy.org/manual/" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Lietotāja rokasgrāmata (Manual)</a>
		</p>
		<p style="font-size: 13px; color: #666; margin-bottom: 0;">
			Piezīme: Omarchy uzstādīšanas ISO ir paredzēts instalēšanai uz atsevišķa/vesela diska. Pirms uzstādīšanas ieteicams iepazīties ar rokasgrāmatu.
		</p>
	</div>
</div>'
    ],
    [
        'slug' => 'Arch',
        'title' => 'Arch',
        'text' => '<div class="distro-card" style="margin-bottom: 20px;">
	<h2 style="margin-top: 0; margin-bottom: 6px;">Arch Linux</h2>
	<p style="font-size: 15px; color: #555; margin-bottom: 15px;">Minimālistiska, viegla un vienmēr aktuāla („rolling release”) neatkarīga Linux distribūcija zinošiem lietotājiem.</p>

	<table class="table table-bordered table-striped" style="margin-bottom: 18px; max-width: 680px;">
		<tbody>
			<tr>
				<td style="width: 35%; font-weight: bold;">Izlaidumu modelis</td>
				<td><strong>Rolling release</strong> — nepārtraukti atjauninājumi bez nepieciešamības pārinstalēt sistēmu</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Pakotņu pārvaldība</td>
				<td><code>pacman</code> + <strong>AUR</strong> (Arch User Repository)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Bāze</td>
				<td>Neatkarīga distribūcija (radīta pilnībā no nulles)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Darbvirsma</td>
				<td>Nav uzspiesta — lietotājs pats izvēlas (KDE Plasma, GNOME, Hyprland, i3, Sway u.c.)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Filozofija</td>
				<td>„The Arch Way” — vienkāršība, modernums, pragmatisms un pilnīga lietotāja kontrole</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Piemērots</td>
				<td>Entuziastiem, programmētājiem un ikvienam, kurš vēlas dziļāk izprast Linux uzbūvi un konfigurāciju</td>
			</tr>
		</tbody>
	</table>

	<h3>Par Arch Linux</h3>
	<p>Arch Linux ir neatkarīga GNU/Linux distribūcija, ko 2002. gadā izveidoja Džads Vins (Judd Vinet). Tās pamatā ir princips „Keep It Simple, Stupid” (KISS). Vienkāršība Arch izpratnē nozīmē tīru sistēmas arhitektūru bez liekiem starpslāņiem un grafiskiem iestatījumu aizsegiem — programmatūra tiek piegādāta pēc iespējas tuvāka tās oriģinālajam autoru kodam (upstream).</p>

	<h4>Kāpēc Arch ir tik populārs?</h4>
	<ul>
		<li><strong>Vienmēr jaunākā programmatūra:</strong> pateicoties nepārtraukto izlaidumu modelim, lietotāji saņem jaunākos Linux kodolus, draiverus, grafiskās vides un programmatūras versijas dažu dienu vai stundu laikā pēc to oficiālās iznākšanas.</li>
		<li><strong>AUR (Arch User Repository):</strong> pasaulē lielākā un bagātākā lietotāju uzturētā programmu krātuve. Ja programma eksistē operētājsistēmai Linux, tā gandrīz droši ir atrodama AUR un uzstādāma ar vienu komandu.</li>
		<li><strong>ArchWiki:</strong> viena no detalizētākajām un kvalitatīvākajām tehniskās dokumentācijas krātuvēm visā IT nozarē, kas kalpo par uzticamu rokasgrāmatu arī citu Linux distribūciju lietotājiem.</li>
		<li><strong>Uzstādīšana ar <code>archinstall</code>:</strong> lai gan Arch tradicionāli instalē no komandrindas, oficiālajā ISO attēlā ir iekļauts ērts palīgrīks <code>archinstall</code>, kas ļauj uzstādīt pilnvērtīgu sistēmu ar izvēlēto grafisko vidi dažu minūšu laikā.</li>
	</ul>

	<div class="box" style="margin-top: 20px; padding: 14px 16px; border-radius: 4px;">
		<h4 style="margin-top: 0; margin-bottom: 10px;">Lejupielādes un noderīgas saites:</h4>
		<p style="margin-bottom: 12px;">
			<a class="button primary" href="https://archlinux.org/download/" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-weight: bold; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px;">Lejupielādēt Arch Linux ISO</a>
			<a class="button" href="https://wiki.archlinux.org/title/Installation_guide" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Uzstādīšanas pamācība</a>
			<a class="button" href="https://aur.archlinux.org/" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">AUR pakotņu krātuve</a>
		</p>
		<p style="font-size: 13px; color: #666; margin-bottom: 0;">
			Oficiālā vietne: <a href="https://archlinux.org/" target="_blank" rel="noopener noreferrer">archlinux.org</a> | Dokumentācija: <a href="https://wiki.archlinux.org/" target="_blank" rel="noopener noreferrer">wiki.archlinux.org</a>
		</p>
	</div>
</div>'
    ],
    [
        'slug' => 'Mint',
        'title' => 'Mint',
        'text' => '<div class="distro-card" style="margin-bottom: 20px;">
	<h2 style="margin-top: 0; margin-bottom: 6px;">Linux Mint</h2>
	<p style="font-size: 15px; color: #555; margin-bottom: 15px;">Draudzīgākā un ērtākā darbvirsmas distribūcija, īpaši ieteicama lietotājiem, kuri pārnāk no Windows vides.</p>

	<table class="table table-bordered table-striped" style="margin-bottom: 18px; max-width: 680px;">
		<tbody>
			<tr>
				<td style="width: 35%; font-weight: bold;">Pašreizējā versija</td>
				<td><strong>Linux Mint 22</strong> („Wilma”) — ilgtermiņa atbalsts līdz 2029. gadam</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Bāze</td>
				<td>Ubuntu LTS (pieejama arī LMDE — Debian bāzēta alternatīva)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Galvenā darbvirsma</td>
				<td><strong>Cinnamon</strong> (pieejami arī vieglāki MATE un Xfce varianti)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Pakotņu pārvaldība</td>
				<td>APT (<code>.deb</code>) un Flatpak (iebūvēts programmatūras pārvaldniekā)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Filozofija</td>
				<td>Stabilitāte, vienkāršība, elegance un funkcionēšana bez liekas konfigurēšanas</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Piemērots</td>
				<td>Mājas datoriem, biroja darbam, iesācējiem, kā arī vecāku datoru atdzīvināšanai</td>
			</tr>
		</tbody>
	</table>

	<h3>Par Linux Mint</h3>
	<p>Linux Mint kopš 2006. gada ir viena no iecienītākajām un stabilākajām darbvirsmas Linux sistēmām pasaulē. Tās pamatā ir mērķis nodrošināt modernu, elegantu un ērtu vidi, kas „vienkārši strādā”. Mint jau standartā ietver visus nepieciešamos multivides kodekus, atbalsta plašu aparatūras klāstu un piedāvā pārskatāmu un saprotamu vadības paneli.</p>

	<h4>Kāpēc izvēlēties Linux Mint?</h4>
	<ul>
		<li><strong>Pazīstama darbvirsmas saskarne (Cinnamon):</strong> intuitīvs izkārtojums ar tradicionālu uzdevumjoslu ekrāna apakšā, ērtu izvēlni un sistēmas ikonām, kas ļauj bez grūtībām iejusties lietotājiem, kuri pieraduši pie Windows.</li>
		<li><strong>Izcili un pārdomāti sistēmas rīki:</strong>
			<ul>
				<li><strong>Update Manager:</strong> droša un skaidra atjauninājumu pārvaldība, kas neuzbāžas ar paziņojumiem un ļauj izvēlēties stabilitātes līmeņus.</li>
				<li><strong>Timeshift:</strong> iebūvēts sistēmas momentuzņēmumu (snapshot) rīks, kas ļauj jebkuru kļūmju gadījumā atjaunot sistēmas iepriekšējo stāvokli pāris klikšķos.</li>
				<li><strong>Driver Manager:</strong> vienkārša patentēto draiveru (Wi-Fi, video kartes) uzstādīšana ar vienu peles klikšķi bez komandrindas lietošanas.</li>
			</ul>
		</li>
		<li><strong>Lietotņu pārvaldnieks ar Flatpak:</strong> milzīgs programmu katalogs ar vērtējumiem, atsauksmēm un drošu smilškastes (sandbox) atbalstu.</li>
		<li><strong>Privātums un kontrole:</strong> Mint neapkopo lietotāju telemetrijas datus un neiekļauj uzspiestas pakotņu formas (piemēram, Snap ir pēc noklusējuma atspējots un aizstāts ar atvērto Flatpak standartu).</li>
	</ul>

	<div class="box" style="margin-top: 20px; padding: 14px 16px; border-radius: 4px;">
		<h4 style="margin-top: 0; margin-bottom: 10px;">Lejupielādes un noderīgas saites:</h4>
		<p style="margin-bottom: 12px;">
			<a class="button primary" href="https://linuxmint.com/download.php" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-weight: bold; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px;">Lejupielādēt Linux Mint (Cinnamon)</a>
			<a class="button" href="https://linuxmint.com/edition.php?id=317" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 8px; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">MATE versija</a>
			<a class="button" href="https://linuxmint.com/edition.php?id=318" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: 1px solid #ccc;">Xfce versija (vieglākai tehnikai)</a>
		</p>
		<p style="font-size: 13px; color: #666; margin-bottom: 0;">
			Oficiālā vietne: <a href="https://linuxmint.com/" target="_blank" rel="noopener noreferrer">linuxmint.com</a> | Forumi: <a href="https://forums.linuxmint.com/" target="_blank" rel="noopener noreferrer">forums.linuxmint.com</a> | Dokumentācija: <a href="https://linuxmint-user-guide.readthedocs.io/" target="_blank" rel="noopener noreferrer">linuxmint-user-guide</a>
		</p>
	</div>
</div>'
    ]
];

foreach ($tabs as $t) {
    $slug = $db->real_escape_string($t['slug']);
    $title = $db->real_escape_string($t['title']);
    $text = $db->real_escape_string($t['text']);

    $existing = $db->get_row("SELECT id FROM clans_tabs WHERE clan_id = '{$group->id}' AND slug = '$slug'");
    if ($existing) {
        $db->query("UPDATE clans_tabs SET 
            `title` = '$title',
            `text` = '$text',
            `date_modified` = '$now',
            `public` = 1,
            `modified_by` = 1
            WHERE `id` = '{$existing->id}'");
        echo "Updated tab '{$t['title']}' (ID: {$existing->id})\n";
    } else {
        $db->query("INSERT INTO clans_tabs 
            (`clan_id`, `slug`, `title`, `text`, `date_modified`, `created_by`, `modified_by`, `public`, `module`, `module_data`, `pic_heavy`)
            VALUES 
            ('{$group->id}', '$slug', '$title', '$text', '$now', 1, 1, 1, '', '', 0)");
        $new_id = $db->insert_id;
        echo "Created tab '{$t['title']}' (ID: {$new_id})\n";
    }
}

// Clear memcached if available
if (!empty($mc_host) && class_exists('Memcached')) {
    try {
        $mc = new Memcached();
        $mc->addServer($mc_host, $mc_port);
        $mc->flush();
        echo "Flushed Memcached.\n";
    } catch (Throwable $e) {
        echo "Memcached flush notice: " . $e->getMessage() . "\n";
    }
}

echo "All tabs updated successfully!\n";
