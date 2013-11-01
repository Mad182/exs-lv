<link rel="stylesheet" type="text/css" href="/modules/checkform/{skinid}.css">

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://static.exs.lv/js/jquery.cluetip.js"></script>

<script type="text/javascript">
	$('.get-user-info').live('click',function() {
		var userid = $(this).attr('data-id');
		if ($('#data-'+userid).parent().hasClass('hide-userdata')) {
			$('#data-'+userid).parent().removeClass('hide-userdata');
			$.ajax({
				type: "GET",
				url: window.location.href + '?display=' + userid,
				data: $(this).serialize(),
				success: function(data) {
					$('#data-'+userid).html(data);
				}
			});
		} else {
			$('#data-'+userid).parent().addClass('hide-userdata');
		}		
		return false;
	});
	$('.show-rows').live('click',function(){
		$(this).parent().parent().siblings('.hide-rows').toggle('slow');
		if ($(this).text() == 'rādīt vairāk') {
			$(this).text('rādīt mazāk');
		} else $(this).text('rādīt vairāk');
	});
	$(document).ready(function() {
		$('a.clue').cluetip({showTitle: false});
	});
</script>

<link rel="stylesheet" href="http://static.exs.lv/css/jquery.cluetip.css" type="text/css" />