<!-- START BLOCK : user-profile-->
<div class="tabMain">
{edit-link}
<h4>{user-nick}</h4>                           
<!-- START BLOCK : user-profile-last_action-->
<p>Šobrīd skatās {user-last_action}</p>
<!-- END BLOCK : user-profile-last_action-->
<p>Lapā jau: {user-days} {user-days-text}<br />
Manīts pirms: {user-lastseen}<br />
Mājaslapa: <a href="{user-web}" title="{user-nick} mājas lapa" rel="nofollow">{user-web}</a><br />
Karma: {user-karma}<br />
Komentēja {user-posts} reizes (vidēji {user-postsday} komentāri dienā)<br />
Izveidotās tēmas: <a href="/?r={user-id}">{user-pages}</a><br />
Ieraksti miniblogos: {user-miniblog}<br />
<!-- START BLOCK : user-profile-skype-->
Skype niks: <a href="callto:{user-skype}">{user-skype}</a><br />
<!-- END BLOCK : user-profile-skype-->
<!-- START BLOCK : user-profile-rs_nick-->
Runescape niks: <a class="rs-stats" href="http://hiscore.runescape.com/hiscorepersonal.ws?user1={user-rs_nick}">{user-rs_nick}</a><br />
<!-- END BLOCK : user-profile-rs_nick-->
<!-- START BLOCK : user-profile-city-->
Pilsēta: {user-city}<br />
<!-- END BLOCK : user-profile-city-->
<!-- START BLOCK : user-profile-lastip-->
Pēdējā IP adrese: {user-lastip}<br />
<!-- END BLOCK : user-profile-lastip-->   
<!-- START BLOCK : user-profile-ban-->
<a href="/?u={user-id}&amp;mode=block">Bloķēt pieeju lapai</a><br />
<!-- END BLOCK : user-profile-ban-->
<!-- START BLOCK : user-profile-pm-->
<a href="/?c=104&amp;act=compose&amp;to={user-id}" title="Nosūtīt vēstuli">Nosūtīt vēstuli</a><br />
<!-- END BLOCK : user-profile-pm-->
{friend-link}
</p>
</div>

<h2>Pēdējie komentāri rakstos</h2>
<div class="box">
<!-- START BLOCK : user-profile-lastcom-->
<ul class="bloglist">
<!-- START BLOCK : user-profile-lastcom-node-->
<li><a href="{url}">{comments-text}</a></li>
<!-- END BLOCK : user-profile-lastcom-node-->
</ul>
<!-- END BLOCK : user-profile-lastcom-->
</div>

<h2>Jaunākās {user-nick} tēmas</h2>
<div class="box">
<!-- START BLOCK : user-profile-lastpage-->
<ul class="bloglist">
<!-- START BLOCK : user-profile-lastpage-node-->
<li><a href="{node-url}">{lastpage-title}</a></li>
<!-- END BLOCK : user-profile-lastpage-node-->
</ul>
<!-- END BLOCK : user-profile-lastpage-->
</div>


<!-- END BLOCK : user-profile-->

<!-- START BLOCK : user-profile-changenick-->
<div class="tabMain">
<form id="edit-profile" class="form" action="{page-url}" method="post">
<script type="text/javascript">
function UserExists() {
  nick = document.getElementById('new-nick').value;
  load("/?c=250&user="+nick, 'userexists');
}
</script>
<fieldset>
<legend>Exs.lv nika maiņa</legend>
<p>
	<label for="new-nick">Jaunais niks:</label><br />
	<input type="text" class="text" name="new-nick" id="new-nick" value="" maxlength="14" onblur="UserExists();" onkeyup="UserExists();" /> <span id="userexists"></span>
</p>
<p>
	<input type="submit" name="submit" id="submit" value="Saglabāt" />
</p>
<p>Nika maiņa ir maksas pakalpojums. Katra nika mainīšanas reize maksā <strong>5</strong> exs.lv kredīta punktus. Tev šobrīd ir <strong>{user-credit}</strong> kredīta punkti. Apdomā labi, un raksti uzmainīgi, jo par 5 punktiem niku varēsi mainīt tikai vienu reizi. Pēc nika maiņas būs jāielogojas atkārtoti. Ja rodas jautājumi vai problēmas, vispirms sazinies ar lietotāju <a href="/user/1-Minka"><span class="admins">Minka</span></a>.</p>


<h4>Kā iegādāties 5 kredīta punktus?</h4>



<div class="box">
<ul id="paytabs" class="shadetabs">
	<li><a href="/?c=313" rel="pay"><img src="/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></span></li>
	<li><a href="/?c=313&lang=uk" rel="pay"><img src="/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
	<li><a href="/?c=313&lang=ie" rel="pay"><img src="/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
</ul>

<div id="pay" class="ajaxbox"><noscript><p>Sūti īsziņu ar tekstu: <strong>TXT EXS {user-id}</strong> uz numuru 1897</p>
<p>
<p><small>Maksa (0,98 LVL) ir pievienota telefona rēķinam vai atrēķināta no priekšapmaksas kartes.<br />
Atbalsts: +37128690182 | info@openidea.lv<br />
Piedāvā fortumo.lv</small></p></noscript></div>
<script type="text/javascript">
var pay=new ddajaxtabs("paytabs", "pay")
pay.setpersist(true)
pay.setselectedClassTarget("link")
pay.init(9976000)
</script>
</div>



</fieldset>
</form>

</div>
<!-- END BLOCK : user-profile-changenick-->     


<!-- START BLOCK : user-profile-edit-->
<div class="tabMain">
<p><a href="/?u={user-id}&changenick=true">Mainīt niku</a></p>
<form id="edit-profile" class="form" action="{page-url}" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Tava profila iestatījumi</legend>
<!-- START BLOCK : invalid-mail-->
<p class="error">Nekorekti norādīta e-pasta adrese!</p>
<!-- END BLOCK : invalid-mail-->

<!-- START BLOCK : invalid-web-->
<p class="error">Nekorekti norādīta mājas lapas adrese!</p>
<!-- END BLOCK : invalid-web-->

<!-- START BLOCK : invalid-pwd-->
<p class="error">Parole nav pareiza!</p>
<!-- END BLOCK : invalid-pwd-->

<!-- START BLOCK : invalid-pwdlen-->
<p class="error">Jaunajai parolei jābūt vismaz 6 simbolus garai!</p>
<!-- END BLOCK : invalid-pwdlen-->

<!-- START BLOCK : save-suc-->
<p class="success">Izmaiņas tika saglabātas!</p>
<!-- END BLOCK : save-suc-->

<!-- START BLOCK : save-pwd-->
<p class="success">Parole tika nomainīta!</p>
<!-- END BLOCK : save-pwd-->

<!-- START BLOCK : custom_title-->
<p>
	<label for="edit-custom_title">Lietotāja nosaukums:</label><br />
	<input type="text" class="text" name="edit-custom_title" id="edit-custom_title" value="{user-custom_title}" maxlength="18" />
</p>
<!-- START BLOCK : custom_title-->

<p>
	<label for="edit-mail">E-pasta adrese:</label><br />
	<input type="text" class="text" name="edit-mail" id="edit-mail" value="{user-mail}" maxlength="64" />
</p>
<p>
	<label for="edit-web">Mājaslapa:</label><br />
	<input type="text" class="text" name="edit-web" id="edit-web" value="{user-web}" maxlength="128" />
</p>
<h4>Paroles maiņa:</h4>
<p>
	<label for="password-old">Vecā parole:</label><br />
	<input type="password" class="text" name="password-old" id="password-old" value="" />
</p>
<p>
	<label for="password-1">Jaunā parole:</label><br />
	<input type="password" class="text" name="password-1" id="password-1" value="" />
</p>
<p>
	<label for="password-2">Atkārto jauno paroli:</label><br />
	<input type="password" class="text" name="password-2" id="password-2" value="" />
</p>
<p>
	<input type="submit" name="submit" id="submit" value="Saglabāt" />
</p>
</form>

</div>
<!-- END BLOCK : user-profile-edit-->

<!-- START BLOCK : user-profile-block-->
<div class="tabMain">
<form id="edit-profile" class="form" action="/?u={user-id}&amp;mode=block" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Bloķēt pieeju lapai</legend>
<p>
<label for="block-reason">Iemesls:</label><br />
<input type="text" class="text" name="block-reason" id="block-reason" value="" maxlength="256" /></p>
<p>
<select name="block-length">
<option value="21600">6 stundas</option> 
<option value="86400" selected="selected">1 diena</option>
<option value="259200">3 dienas</option>
<option value="604800">1 nedēļa</option>
<option value="1209600">2 nedēļas</option>
<option value="2629743">1 mēnesis</option>
<option value="7889231">3 mēneši</option>  
<option value="31556926">1 gads</option>
</select>
</p>
<p><input type="submit" name="submit" id="submit" value="OK" /></p>
</form>
</div>
<!-- END BLOCK : user-profile-block-->