<?php

/**
 * Callback klase regexpam, kas atpazīst #hashtag miniblogos
 */
class Hashtag {

	private $mbid = null;

	function __construct($mbid) {
		$this->mbid = $mbid;
	}

	public function hashtag($matches) {
		global $db;

		$tag = $matches[1];

		if ($mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$this->mbid' AND `parent` = '0' AND `removed` = '0'")) {
			include_once(CORE_PATH . '/includes/class.tags.php');
			$tags = new tags;

			if (strlen($tag) < 30 && strlen($tag) > 2) {
				$newtag = sanitize(mb_ucfirst(strtolower(trim($tag))));
				$nslug = mkslug($tag);
				if (!empty($newtag)) {
					$tagid = $db->get_var("SELECT `id` FROM `tags` WHERE `slug` = '$nslug'");
					if ($tagid) {
						$tags->add_tag($mb->id, $tagid, 2);
					} else {
						$db->query("INSERT INTO `tags` (`name`, `slug`) VALUES ('$newtag', '$nslug')");
						$tagid = $db->get_var("SELECT `id` FROM `tags` WHERE `slug` = '$nslug'");
						$tags->add_tag($mb->id, $tagid, 2);
					}
				}
				return '<a class="post-tag" href="/tag/' . $nslug . '" title="' . $newtag . '"><span class="hash-sign">#</span>' . $tag . '</a>';
			}
		}
		return '#' . $tag;
	}

}
