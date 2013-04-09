var search_trigerred = false;
var ssearch = false;

$(document).ready(function() {
	// small search form
	$('#ssearch-form').live('submit',function() {
		$.ajax({
			async: false,
			data: $(this).serialize(),
			type: "get",
			dataType: 'json',
			url: $(this).attr('action'),
			success: function(data) {
				if (data.state == 'success') {
					$('#ssearch-results').html(data.content);
					$('#nextpage').html(data.next);
					$('#prevpage').html(data.prev);
					if (ssearch == false) {
						$('#ssearch-results').fadeIn('slow');
						$('.close-results').fadeIn('slow');
						ssearch = true;						
					}
				} else if (data.state == 'null') {
					$('#nextpage').html('');
					$('#prevpage').html('');
					$('#ssearch-results').fadeOut(600);
					ssearch = false;
				}
			}
		});	
		return false;
	});
	// small search - click on page
	$('.form-page').live('click',function() {
		$.ajax({
			async: false,
			dataType: 'json',
			url: $(this).attr('href'),
			success: function(data) {
				if (data.state == 'success' || data.state == 'null') {
					$('#ssearch-results').html(data.content);
					$('#nextpage').html(data.next);
					$('#prevpage').html(data.prev);
					if (ssearch == false) {
						$('#ssearch-results').fadeIn('slow');
						$('.close-results').fadeIn('slow');
						ssearch = true;						
					}
				} else if (data.state == 'null') {
					$('#ssearch-results').fadeOut(600);
					ssearch = false;
				}
			}
		});	
		return false;
	});
	// close small input
	$('.close-form').live('click',function() {
		$('#nextpage').html('');
		$('#prevpage').html('');
		$('.close-results').hide('slow');
		$('#ssearch-results').fadeOut('slow');
		ssearch = false;	
	});
	// checkbox toggle
	$('#search_ch').live('click',function(){
		$.ajax({
		  url: "/db/checkbox",
		  async: 'false'
		})
		if (search_trigerred) {
			$('.jqsearch').submit();
		}
	});
	// mainpage short lists
	$('.jqnew a, .jqupd a, .jqapp a').live('click',function(){
		var data = $(this).attr('href');
		$(this).parent().parent().fadeTo(250, 0.5).load(data).fadeTo(250, 1);
		return false;
	});
	// search results
	$('.jqsearch').live('submit',function(){
		search_trigerred = true;		
		$('#idb-homepage').hide();
		//$('#searchload').show();
		$('#searchpage').fadeTo(1000,0.2);
		$.ajax({
			type: "get",
			async: false,
			dataType: 'json',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function(data) {
				if (data.state == 'failure2') {
					search_trigerred = false;
					//$('#searchload').hide();
					$('#searchpage').fadeOut().delay(1500);
					$('#idb-homepage').fadeTo(250,1.0);					
				} else {
					//$('#searchload').hide();
					$('#searchpage').html(data.content).fadeTo(350,1);
				}
			}
		});		
		return false;
	});
	// search result pages
	$('.bottom-page').live('click',function(){
		$('#searchload').show();
		$('#searchpage').fadeTo(1000,0.2);
		$.ajax({
			async: false,
			dataType: 'json',
			url: $(this).attr('href'),
			success: function(data) {
				if (data.state == 'success') {
					$('#searchload').hide();
					$('#searchpage').html(data.content).fadeTo(350,1);
				} else if (data.state == 'failure1') {
					$('#searchload').hide();
					$('#searchpage').html(data.content).fadeTo(350,1);
				} else {					
					$('#searchload').hide();
					$('#searchpage').fadeOut().delay(1500);
					$('#idb-homepage').fadeTo(250,1.0);					
				}
			}
		});	
		return false;
	});
	// upper list of pages
	$('.list-page').live('click',function(){
		$('#load-list').fadeTo(1000,0.2);
		$.ajax({
			async: false,
			url: $(this).attr('href'),
			success: function(data) {
				$('#load-list').html(data);
				$('#load-list').fadeTo(200,1);
			}
		});	
		return false;
	});
	// stacks,equips utt
	$('.idb_button0, .idb_button1').live('click',function(){
		var val = $(this).attr('data-id');
		if ($('#'+val).attr('value') == 1) {
			$('#'+val).attr('value',0);
			$(this).removeClass('idb_button1');
			$(this).addClass('idb_button0');
		} else {
			$('#'+val).attr('value',1);
			$(this).removeClass('idb_button0');
			$(this).addClass('idb_button1');
		}
	});
});