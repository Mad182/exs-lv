<script type="text/javascript">

$(document).ready(function($) {

	$('#checkform').on('click', '.get-user-info', function(e) {
		e.preventDefault();
		var userid = $(this).attr('data-id');
		if ($('#data-' + userid).parent().hasClass('hide-userdata')) {
			$('#data-' + userid).parent().removeClass('hide-userdata');
			$.ajax({
				type: "GET",
				url: '/{category-url}?display=' + userid,
				data: $(this).serialize(),
				success: function(data) {
					$('#data-' + userid).html(data);
				}
			});
		} else {
			$('#data-' + userid).parent().addClass('hide-userdata');
		}
	});

	$('.show-rows').on('click', function() {
		$(this).parent().parent().siblings('.hide-rows').toggle('slow');
		if ($(this).text() == 'rādīt vairāk') {
			$(this).text('rādīt mazāk');
		} else
			$(this).text('rādīt vairāk');
	});

	$('#show_more_all').on('click', function() {
		$('#all_ips .hidden-row').toggle('slow');
		if ($(this).children('.toggle-text').text() == 'vairāk') {
			$(this).children('.toggle-text').text('mazāk');
		} else
			$(this).children('.toggle-text').text('vairāk');
	});

	$('#show_more_unique').on('click', function() {
		$('#unique_ips .hidden-row').toggle('slow');
		if ($(this).children('.toggle-text').text() == 'vairāk') {
			$(this).children('.toggle-text').text('mazāk');
		} else
			$(this).children('.toggle-text').text('vairāk');
	});
	
});

</script>
