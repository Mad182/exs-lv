<script>

var position = {player-now-position};
var duration = {player-now-duration};
var time_wait = {player-now-duration}-{player-now-position};

function reload_video() {

	$.getJSON('/player/getnext/?_=1', function(response) {
		console.log(response);
		if (response.state == 'success') {
			position = response.position;
			duration = response.duration;
			time_wait = duration-position;
			$("#player-embed").html(response.html);
			reload_playlist();
			setTimeout(function(){ reload_video() },time_wait*1000);
		} else {
			alert('Neizdevās ielādēt nākamo dziesmu :(');
			location.reload();
		}
	});

}

function youtube_parser(url){
	var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
	var match = url.match(regExp);
	if (match&&match[7].length==11){
		return match[7];
	}else{
		return false;
	}
}

function reload_playlist() {

	$.getJSON('/player/list/?_=1', function(response) {
		if (response.state == 'success') {
			$("#player-playlist").html("");    
			$.each(response.songs, function(key, val) {

				like = '<a class="player-resubmit" href="/player/add/'+val.id+'?_=1">+1 balss</a>';

				$("#player-playlist").append('<div><span class="player-likes">+'+val.likes+'</span><img class="player-avatar" src="https://i4.ytimg.com/vi/'+val.id+'/2.jpg" alt="" /><h3>'+val.title+'</h3>'+like+'<br />'+val.likers+'<div class="clear" style="height:0;min-height:0;"></div></div>');
			});
		} else {
			alert('Neizdevās pievienot dziesmu :(');
		}
	});
}

function reload_mylist() {

	$.getJSON('/player/mylist/?_=1', function(response) {
		if (response.state == 'success') {
			$("#player-mylist").html("");    
			$.each(response.songs, function(key, val) {

				var like = "";
				if(val.archived == 1) {
					like = '<a class="player-resubmit" href="/player/add/'+val.id+'?_=1">&laquo; ievietot sarakstā</a>';
				}

				$("#player-mylist").append('<div><img class="player-avatar" src="https://i4.ytimg.com/vi/'+val.id+'/2.jpg" alt="" /><h3>'+val.title+'</h3>'+like+'</div>')

			});
		} else {
			alert('Neizdevās pievienot dziesmu :(');
		}
	});
}

$(document).ready(function () {

	//pievieno dziesmu no url formas
	$('#player-form').live('submit', function(e) {
		var id = youtube_parser($('#video-url').val());
		if(id != false) {

		$('#mbresponse-submit').hide();
		$('#mbresponse-waiting').show();

		$.getJSON('/player/add/'+id+'?_=1', function(response) {
			if (response.state == 'success') {        
				$('#video-url').val("");
				reload_playlist();
				reload_mylist();
				$('#mbresponse-waiting').hide();
				$('#mbresponse-submit').show();
			} else {
				alert('Neizdevās pievienot dziesmu :(');
			}
		});

		} else {
			alert("Nesanāca atpazīt linku :(");
		}
		e.preventDefault();
	});

	//pievieno dziesmu no lietotāja saraksta
	$('a.player-resubmit').live('click', function(e) {

		$.getJSON($(this).attr("href"), function(response) {
			console.log(response);
			if (response.state == 'success') {        
				reload_playlist();
				reload_mylist();
				if(response.like == 'error') {
					$(this).html("Jau nobalsots!");
				}
			} else {
				alert('Neizdevās pievienot dziesmu :(');
			}
		});

		e.preventDefault();
	});

	reload_playlist();
	reload_mylist();

	setTimeout(function(){ reload_video() },time_wait*1000);

});
</script>

<div id="player-wrapper">

	<div class="player-column" id="player-column-1">
		<div id="player-embed">
			<iframe width="560" height="315" src="//www.youtube.com/embed/{player-now-id}?start={player-now-position}&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>
			<a style="float:right;" class="player-resubmit" href="/player/add/{player-now-id}?_=1">+1 pievienot sarakstam</a>
			<div class="clear"></div>
		</div>

		<h2>Nākamais sarakstā</h2>

		<div id="player-playlist"></div>

	</div>

	<div class="player-column" id="player-column-2">

		<form class="form" action="" method="post" id="player-form">
			<fieldset>
				<legend>Pievienot video</legend>
				<p>
					<label for="video-url">Youtube URL:</label><br />
					<input class="text" type="text" name="video-url" id="video-url" />
				</p>
				<p>
					<input id="mbresponse-submit" tabindex="2" class="button primary" type="submit" name="submit" value="Pievienot" />
					<input id="mbresponse-waiting" class="button disabled" type="submit" style="display:none" value="Pievienot" disabled="disabled" />
				</p>
			</fieldset>
		</form>

		<h2>Manas dziesmas</h2>

		<div id="player-mylist"></div>

	</div>

</div>