$tagz = 'RSPS';

include_once('includes/class.tags.php');
$tags = new tags;

$groups = $db->get_results("SELECT id,parent,text
			FROM miniblog WHERE removed = '0' AND groupid = '0'
			AND `text` LIKE '%soulspli%'");


foreach($groups as $group) {
	echo '<p>'.textlimit($group->text,50) . '</p>';
	if($group->parent != 0) {
		$group->id = $group->parent;
	}
	$newtag = $tagz;
	$nslug = mkslug($newtag);
	if(!empty($newtag)) {
		$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
		if(!$tagid) {
			$db->query("INSERT INTO tags (name,slug) VALUES ('". sanitize($newtag)."','$nslug')");
			$tagid = $db->insert_id;
		}
		if($tags->add_tag($group->id,$tagid,2)) {
		}
	}

}
