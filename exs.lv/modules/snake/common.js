/*
 *
 * Filename:	common.js
 * Developer:	Richard Willis
 *
 */

$(function(){
	// FF2/Mac Opacity Bug
	($.browser.mozilla && parseFloat($.browser.version) < 1.9 && 
	navigator.appVersion.indexOf('Mac') !== -1) && 
	$('body').css('-moz-opacity',.999);
	
	// IE6 background css flickering bug
	(!$.browser.msie) && (function(){
		try{document.execCommand('BackgroundImageCache', false, true);}
		catch(e){};
	})();
	
	// setup the game
	Snake.setup();
			
	// start the game
	$("a#start-game").click(function(e){
		e.preventDefault();
		Snake.newGame(true);
	});

	$("a.accordian:not('a[href=#high-scores])").each(function(){
		$(this).click(function(e){
			e.preventDefault();
			$("#"+this.href.replace(/[^#]+#/, '')).animate({height:"toggle",opacity:"toggle"}, 560, "jswing");
		});
	});

	var timer = 0;
			
	// get the high scores
	$("a[href='#high-scores']").click(function(e){

		// until i've completed scoring, i'll just display a 'coming soon' message
		e.preventDefault();
		var
			anchor = this,
			$msg =	!
				$(this).next().length 
				? 
					$('<small><em>&nbsp;(coming soon)</em></small>')
					.css({display:"none",color:"#000"})
					.attr("id", "comingsoon") 
				: 
				$("#comingsoon");

		!$(this).next().length && (function(){
			$(anchor).after($msg);
		})();
		if ($msg[0].style.display == 'none') {
			timer = setTimeout(function(){
				$msg.fadeOut();
			}, 3000);
			$msg.fadeIn();
		} else {
			clearTimeout(timer);
			$msg.show().fadeOut();
		}
		return;
		$.ajax({
			type : "GET",
			url : "snakey-scores.php",
			data : "view=10",
			dataType : "json",
			success : function(data){
				eval(data);
				$("#high-scores").html("<span class=\"head\">\
							<span class=\"col1\">Pos.</span>\
							<span class=\"col2\">Score</span>\
							<span class=\"col3\">Name</span>&nbsp;</span>");
				var $scores = $("<span class=\"scroller\"></span>");
				for(var i in data) {
                      	        	 $scores.append("<span class=\"row\">\
								<span class=\"col1\">"+(parseInt(i)+1)+"</span>\
								<span class=\"col2\">"+data[i][0]+"</span>\
								<span class=\"col3\">"+data[i][1]+"</span>&nbsp;</span>");
				}
	                	$("#high-scores").append($scores).slideToggle();
				$("#high-scores .row").hover(
				function(){
					this.style.background="#eee";
				},
				function(){
					this.style.background="#fff";
				});
       	                }
		});
	});
});

/* end of file */
