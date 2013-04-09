<link rel="stylesheet" type="text/css" href="/css/utstats.css" />
<script language="JavaScript" type="text/JavaScript">
function changePage(newLoc) {
	nextPage = "index.php?stats=players&amp;type=" + newLoc.options[newLoc.selectedIndex].value
	if (nextPage != "")
		document.location.href = nextPage
}
</script>