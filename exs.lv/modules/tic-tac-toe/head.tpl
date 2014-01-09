<script type="text/javascript">
	$(document).ready(function() {

		$('#desas a, #desas-drop').live('click', function() {
			load_desas($(this).attr('href'));
			return false;
		});

	});
</script>