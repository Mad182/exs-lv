<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type="text/javascript" src="{static-server}/js/jquery.cluetip.js"></script>
<script type="text/javascript">

	function savePosition(arr) {
		$.post("{page-url}", {position: arr});
	}

	$(document).ready(function() {

		$("#user-awards-current").disableSelection();
		$("#user-awards-current").sortable({
			update: function(event, ui) {
				savePosition($('#user-awards-current').sortable('toArray'));
			}
		});


		$('a.clue').cluetip({showTitle: false});
	});

</script>

<link rel="stylesheet" href="{static-server}/css/jquery.cluetip.css" type="text/css" />
