<?php
//echo get_top_awards(1);
if ($auth->id != 1) {
	die('err');
}
?>

<form action="" method="post">
	<input type="text" name="keyword" />
	<input type="submit" value="tag it" />
</form>


<?php
if (isset($_POST['keyword'])) {
	$tagz = sanitize(trim($_POST['keyword']));

	include_once('includes/class.tags.php');
	$tags = new Tags;

	$groups = $db->get_results("SELECT id,title
  			FROM pages WHERE `title` LIKE '%" . $tagz . "%' ORDER BY id ASC");


	foreach ($groups as $group) {

		$newtag = $tagz;
		$nslug = mkslug($newtag);
		if (!empty($newtag)) {
			$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
			if (!$tagid) {
				$db->query("INSERT INTO tags (name,slug) VALUES ('" . sanitize($newtag) . "','$nslug')");
				$tagid = $db->insert_id;
			}
			if ($tags->add_tag($group->id, $tagid, 0)) {
				echo '<p><a href="/?p=' . $group->id . '">' . textlimit($group->title, 50) . '</a></p>';
			}
		}
	}
}
exit;
?>
