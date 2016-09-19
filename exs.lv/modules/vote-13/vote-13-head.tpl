<style>
	input[type=radio], input[type=checkbox] {
		margin-left:25px
	}
	p strong {
		margin:10px auto
	}
	.vote-question {
		margin-top:20px;
		margin-bottom:10px;
		clear:both
	}
	.vote-question input[type=text] {
		margin-left:20px
	}
	.vote-question p {
		margin:5px;
		padding:5px 5px 5px 15px;
		background:lavender
	}
	.button {
		margin-top:15px
	}
	form .vote-question label {
		font-weight:normal
	}
	.vote-question label:hover {
		cursor:pointer
	}
	.input-error {
		font-weight:bold;
		color:red;
		margin-left:20px
	}
	.vote-table {
		margin:5px 30px
	}
	.vote-table tr td.left-td {
		padding:5px 12px;
		text-align:center
	}
	.vote-table tr.header td {
		font-weight:bold
	}
	.vote-bar-outer, .vote-bar {
		min-height:8px;
		background:lavender;
		overflow:auto
	}
	.vote-bar-outer {
		display:inline-block;
		min-width:150px
	}
	.vote-bar {
		background:#6ba6ee
	}
</style>
<script>
	$(document).ready(function() {
		$('.form').on('submit', function() {
			$un = $('#user-name').val();
			$ua = $('#user-age').val();
			if ($un.length < 3) {
				$('#user-name-error').html('Kļūda! Nav ievadīts korekts vārds!');
				return false;
			} else if ($ua === "" || !$.isNumeric($ua)) {
				$('#user-age-error').html('Kļūda! Nav ievadīts korekts vecums!');
				return false;
			}
		});
	});
</script>
