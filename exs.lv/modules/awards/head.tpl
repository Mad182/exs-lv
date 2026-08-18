<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script>
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

		$('body').on('mouseenter', 'a.clue', function() {
			var $el = $(this);
			if ($el.data('loaded')) return;
			var url = $el.attr('data-url');
			if (!url) return;
			$.ajax({ url: url }).done(function(content) {
				$el.attr('title', content).data('loaded', true);
			});
		});
	});
</script>
