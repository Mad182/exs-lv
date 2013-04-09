<link rel="stylesheet" type="text/css" href="/modules/rshelp/{skinid}.css" />
<script type="text/javascript">
$('a.skill-pager').live('click', function() {
	var elem = $(this).parent().parent();
	elem.fadeTo(250, 0.5);
	elem.load($(this).attr('href'), function() {
		elem.fadeTo(250, 1);
	});
	return false;
});	
$('#show-facts').live('click',function() {			
	$(this).siblings('#hidden-facts').toggle(800);
	$(this).replaceWith('');
	return false;
});
$('#show-rows').live('click',function() {			
	$('.hidden-row').toggle(1000);
	$(this).replaceWith('');
	return false;
});
$('.showph').live('click',function() {			
	$(this).parent().siblings('.ph-hidden').toggle(200);
	$(this).parent().css('display','none');
	return false;
});
</script>
