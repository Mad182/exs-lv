<script type="text/javascript">

$(document).ready(function($) {

	$('#findby').on('click', '.get-user-info', function(e) {
		e.preventDefault();
		var userid = $(this).attr('data-id');
		if ($('#data-' + userid).parent().hasClass('is-hidden')) {
			$('#data-' + userid).parent().removeClass('is-hidden');
			$.ajax({
				type: 'get',
				url: '/{category-url}?display=' + userid,
				data: $(this).serialize(),
				success: function(data) {
					$('#data-' + userid).html(data);
				}
			});
		} else {
			$('#data-' + userid).parent().addClass('is-hidden');
		}
	});

	$('#findby').on('click', '.show-rows', function() {
		$(this).parent().parent().siblings('.is-hidden').toggle('slow');
		if ($(this).text() == 'rādīt vairāk') {
			$(this).text('rādīt mazāk');
		} else {
			$(this).text('rādīt vairāk');
        }
	});

	$('#findby').on('click', '#show_more_all', function() {
		$('#all_ips .is-hidden').toggle('slow');
		if ($(this).children('.toggle-text').text() == 'vairāk') {
			$(this).children('.toggle-text').text('mazāk');
		} else {
			$(this).children('.toggle-text').text('vairāk');
        }
	});

	$('#findby').on('click', '#show_more_unique', function() {
		$('#unique_ips .is-hidden').toggle('slow');
		if ($(this).children('.toggle-text').text() == 'vairāk') {
			$(this).children('.toggle-text').text('mazāk');
		} else {
			$(this).children('.toggle-text').text('vairāk');
        }
	});	
    
    $('#findby').on('click', '.ajax-search-page', function(e) {
        e.preventDefault();
        $elem = $(this);
        $('#user-results').fadeTo(150, 0.7);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: $elem.attr('href'),
            data: $('#search-vip').serialize(),
            success: function(data) {
                if (typeof data.content !== 'undefined') {
                    $('#user-results').html(data.content).fadeTo(50, 1);
                }
            }
        });
    });
});

</script>
