<link rel="stylesheet" type="text/css" href="/modules/checkform/{skinid}.css" />
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
</script>