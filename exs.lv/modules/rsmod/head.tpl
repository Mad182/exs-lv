<link rel="stylesheet" type="text/css" href="/modules/rsmod/{skinid}.css" />
<script type="text/javascript">
$('.show-ph-links').live('click',function() {			
	$(this).parent().parent().parent().siblings('.ph-links').toggle(300);
	$(this).parent().parent().siblings('.ph-title').css('color','red');
	return false;
});
</script>