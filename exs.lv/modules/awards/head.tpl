<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script src="{static-server}/js/jquery.qtip.min.js"></script>
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

		$('a.clue').qtip({
            content: {
                text: function(event, api) {
                    $.ajax({
                        url: $(this).attr('data-url')
                    })
                    .then(function(content) {
                        api.set('content.text', content);
                    }, function(xhr, status, error) {
                        api.set('content.text', 'Ielādes kļūda.');
                    });
                    return /*'Ielādē...'*/'';
                }
            }
        });
	});
</script>
<link rel="stylesheet" href="{static-server}/css/jquery.qtip.min.css">
