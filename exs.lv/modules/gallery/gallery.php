<?php

if ($inprofile = get_user(intval($_GET['var1']))) {

	include(CORE_PATH . '/includes/class.tags.php');

	if ($auth->ok) {
		set_action($inprofile->nick . ' galeriju');
	}

	//write comment
	if (isset($_POST['comment-pid']) && !empty($_POST['commenttext']) && $auth->ok) {
		$page_id = (int) $_POST['comment-pid'];
		if (!isset($_POST['checksrc']) || $_POST['checksrc'] != md5($page_id . $remote_salt . $auth->id . $inprofile->nick)) {
			set_flash('Kļūdains pieprasījums! Hacking around?', 'error');
			redirect();
		}
		require(CORE_PATH . '/includes/class.comment.php');
		$addcom = new Comment();
		$addcom->add_comment($page_id, $auth->id, $_POST['commenttext'], 1);
		$img = $db->get_row("SELECT thb,text FROM images WHERE id = '$page_id'");
		$url = '/gallery/' . $inprofile->id . '/' . $page_id;
		push('Pievienoja komentāru <a href="' . $url . '">' . $inprofile->nick . ' attēlam ' . textlimit($img->text, 32, '...') . '</a>', 'http://img.exs.lv/' . $img->thb, 'img' . $page_id);
		notify($inprofile->id, 1, $page_id, $url);
		redirect($url);
	}

	//edit image properaties
	if ($auth->ok && $auth->id == $inprofile->id || im_mod()) {
		if (isset($_POST['edit-image-id'])) {
			$closed = (bool) $_POST['edit-image-disablecomments'];
			$editim = (int) $_POST['edit-image-id'];
			//interešu kategorijas id. Ja mēģina nofeikot, tad 0
			$interest_id = (int) $_POST['image-interest'];
			if (!$db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest_id'")) {
				$interest_id = 0;
			}
			$auth->log('Laboja attēlu (slēgt: ' . intval($closed) . ') (kategorija: ' . $interest_id . ')', 'images', $editim);
			$db->query("UPDATE `images` SET `closed` = '$closed', `interest_id` = '$interest_id' WHERE id = '$editim' AND uid = '$inprofile->id'");
			die('ok');
		}
	}

	//add new image
	if ($auth->ok && $auth->id == $inprofile->id && isset($_FILES['new-image'])) {

		if (!isset($_POST['unique-form-check']) or $_POST['unique-form-check'] != md5($remote_salt . $auth->id)) {
			set_flash('Aizdomas par spamu. Attēla pievienošana pārtraukta. Ja uzskati, ka tā ir kļūda, ziņo administrācijai!', 'error');
			redirect('/gallery/' . $auth->id);
		}

		require(CORE_PATH . '/includes/class.upload.php');

		ini_set('memory_limit', '160M');

		$description = post2db($_POST['new-image-description']);

		//interešu kategorijas id. Ja mēģina nofeikot, tad 0
		$interest_id = (int) $_POST['new-image-interest'];
		if (!$db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest_id'")) {
			$interest_id = 0;
		}

		$text = date('dHis') . '_' . $auth->id;
		$folder = 'dati/bildes/g' . date('Y') . '_' . date('m') . '/';
		rmkdir($folder);
		$foo = new Upload($_FILES['new-image']);
		$foo->file_new_name_body = 'large_' . $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 560;
		if ($lang == 7) {
			$foo->image_x = 540;
		}
		$foo->image_y = 700;
		$foo->allowed = array('image/*');
		$foo->image_ratio_no_zoom_in = true;
		$foo->jpeg_quality = 97;
		$foo->process($folder);

		if ($foo->processed) {
			$foo->file_new_name_body = 'thb_' . $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 56;
			$foo->image_y = 56;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = true;
			$foo->jpeg_quality = 97;
			$foo->process($folder);
			$foo->clean();

			$file = $folder . 'large_' . $text . '.jpg';
			$thb = $folder . 'thb_' . $text . '.jpg';

			if (file_exists($file)) {
				$db->query("INSERT INTO images (`id`,`uid`,`url`,`thb`,`text`,`date`,`bump`, `ip`, `interest_id`, `lang`) VALUES (NULL,'$auth->id','$file','$thb','$description',NOW(),NOW(),'$auth->ip','$interest_id', '$lang')");
				push('Pievienoja <a href="/gallery/' . $auth->id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit($description, 32, '...') . '</a>', 'http://img.exs.lv/' . $thb);
				update_karma($auth->id, true);
			}
		} else {
			set_flash('Attēlu neizdevās pievienot: ' . $foo->error, 'error');
		}
		redirect('/gallery/' . $auth->id);
	}


	//edit comment
	if ($auth->ok && isset($_POST['edit-comment-id'])) {
		$edit_comment_id = (int) $_POST['edit-comment-id'];
		$edit_comment_text = htmlpost2db($_POST['edit-comment-text']);

		$edit_comment_author = $db->get_var("SELECT author FROM galcom WHERE id = '$edit_comment_id' LIMIT 1");
		if ($auth->level == 3 && $edit_comment_author != $auth->id) {
			set_flash('Tu vari labot tikai savus komentārus!', 'error');
			redirect();
		}
		if ($auth->level == 0) {
			if ($auth->karma < 100 or $edit_comment_author != $auth->id) {
				set_flash('Tev jābūt vismaz 100 karmai un tu vari labot tikai savus komentārus!', 'error');
				redirect();
			}
		}
		$db->query("UPDATE galcom SET text = ('$edit_comment_text'), edit_time = '" . time() . "', edit_user = '$auth->id', edit_times = edit_times+1 WHERE id = '$edit_comment_id' LIMIT 1");

		$auth->log('Laboja attēla komentāru', 'galcom', $edit_comment_id);

		redirect('/gallery/' . $inprofile->id . '/' . intval($_GET['var2']));
	}

	//delete comment
	if ($auth->ok && ($auth->level == 1 or $auth->level == 2) && isset($_GET['delcom'])) {
		$del = (int) $_GET['delcom'];
		$comment = $db->get_row("SELECT * FROM galcom WHERE id = '$del'");
		if ($comment && $comment->bid == intval($_GET['var2'])) {
			$db->query("UPDATE `galcom` SET `removed` = 1 WHERE `id` = '$del' LIMIT 1");
			$db->query("UPDATE `images` SET `posts` = `posts`-1 WHERE `id` = '" . intval($_GET['var2']) . "'");
			$db->query("UPDATE `users` SET `posts` = `posts`-1 WHERE `id` = '$comment->author'");
			$auth->log('Izdzēsa attēla komentāru', 'galcom', $del);
			redirect('/gallery/' . $inprofile->id . '/' . intval($_GET['var2']));
		}
	}

	$tpl->newBlock('profile-menu');
	$tpl->assign('user-menu-add', ' galerija');

	$tpl->newBlock('user-gallery');
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-gallery' => 'active'
	));

	$page_title = $inprofile->nick . ' galerija';

	//add image form
	if ($auth->ok && $auth->id == $inprofile->id) {
		if ($inprofile->maximg <= $db->get_var("SELECT count(*) FROM images WHERE uid = '" . $inprofile->id . "'")) {
			$tpl->newBlock('add-image-max');
			$tpl->assign('max', $inprofile->maximg);
		} else {
			$tpl->newBlock('add-image-form');
			$tpl->assign('gallery-check', md5($remote_salt . $auth->id));

			if ($lang == 1) {
				$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
				foreach ($interests as $interest) {
					$tpl->newBlock('select-new-interest');
					$tpl->assignAll($interest);
				}
			}
		}
	}

	$image_id = 0;
	if (isset($_GET['var2'])) {
		$image_id = (int) $_GET['var2'];
	}

	//image list
	$images = $db->get_results("SELECT id,thb,url,posts FROM images WHERE `uid` = '" . $inprofile->id . "' AND `lang` = '$lang' ORDER BY id DESC");
	if ($images) {
		$tpl->newBlock('image-list');
		$linkid = 1;
		$total = count($images);
		$i = 0;
		$img_page = 0;
		foreach ($images as $image) {
			remake_thb($image->url, $image->thb);
			if (!$image_id) {
				$image_id = $image->id;
			}
			$block = '';
			if ($i == 18) {
				$block = '</div><div>';
				$i = 0;
				$img_page++;
			}
			if ($image_id == $image->id or (!$image_id && $total == $linkid)) {
				$sel = 'sel';
				$tpl->assignGlobal('current-img-page', $img_page);
			} else {
				$sel = '';
			}
			$tpl->newBlock('image-list-node');
			$tpl->assign(array(
				'image-list-id' => $image->id,
				'image-list-thb' => $image->thb,
				'image-list-posts' => $image->posts,
				'image-list-sel' => $sel,
				'image-list-linkid' => $linkid,
				'imgblock-seperator' => $block,
			));
			$linkid++;
			$i++;
		}
	}

	unset($image);

	$image = $db->get_row("SELECT * FROM `images` WHERE `id` = '" . $image_id . "' AND `uid` = '$inprofile->id' AND `lang` = '$lang'");
	if ($image) {


		$rating_users = unserialize($image->rating_users);

		if (isset($_POST['vote']) && !isset($_POST['questions'])) {
			if (isset($_POST['vote']) && $auth->ok) {
				$vote = 1 * $_POST['vote'];

				//parbauda vai deriga vote vertiba
				if ($vote < 0 || $vote > 5) {
					die('Kļūdaina vērtība');
				}

				//parbauda vai lietotajs jau nav balsojis
				if (in_array($auth->id, $rating_users)) {
					die('Tu jau nobalsoji');
				}

				//pievieno balsojumu
				$rating_users[] = $auth->id;

				$rating = ($image->rating + $vote);

				$db->query("UPDATE `images` SET
		                  `rating` = $rating,
		                  `rating_count` = `rating_count`+1,
		                  `rating_users` = '" . serialize($rating_users) . "'
		                WHERE `id` = '$image->id'");
				die('Lasītāju vērtējums: ' . round($rating / ($image->rating_count + 1), 2) . ' (' . ($image->rating_count + 1) . ' balsis)');
			}
			die('Jāielogojas lai balsotu');
		}


		//edit comment
		if ($auth->ok && isset($_GET['editcom'])) {
			$editcom = (int) $_GET['editcom'];
			$comment = $db->get_row("SELECT text,id,author FROM galcom WHERE id = '$editcom' AND bid = '$image->id' AND `removed` = 0 LIMIT 1");

			if (!im_mod() && ($auth->karma < 100 || $comment->author != $auth->id)) {
				set_flash('Tev jābūt vismaz 100 karmai un tu vari labot tikai savus komentārus!', 'error');
				redirect('/gallery/' . $inprofile->id);
			}

			$tpl->newBlock('adm-edit-comment');
			$tpl->assign(array(
				'comment-text' => htmlspecialchars($comment->text),
				'comment-id' => $comment->id
			));

			$tpl->newBlock('tinymce-enabled');
			$page_title = 'Komentāra labošana | ' . $page_title;
		} else {

			//remove image, comments and rating, unlink image file
			if ((isset($_GET['mode']) && $_GET['mode'] == 'delete') && ($auth->ok && $auth->id == $inprofile->id or $auth->ok && ($auth->level == 1 or $auth->level == 2))) {
				$db->query("DELETE FROM images WHERE id = '$image->id' LIMIT 1");
				$db->query("DELETE FROM galcom WHERE bid = '$image->id'");
				if ($inprofile->id != '18696') { //gamevision bildes nedzēšam
					@unlink($image->url);
					@unlink($image->thb);
				}
				redirect('/gallery/' . $inprofile->id);
			}

			//pieliek tagus
			if ($auth->ok && in_array($auth->level, array(1, 2, 3)) && isset($_POST['newtags'])) {
				$newtags = explode(',', $_POST['newtags']);
				$tags = new tags;
				foreach ($newtags as $newtag) {
					if (strlen(trim($newtag)) > 1) {
						$newtag = htmlspecialchars(ucfirst(strip_tags(trim($newtag))));
						$nslug = mkslug($newtag);
						if (!empty($newtag)) {
							$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
							if (!$tagid) {
								$db->query("INSERT INTO tags (name,slug) VALUES ('" . sanitize($newtag) . "','$nslug')");
								$tagid = $db->insert_id;
							}
							if ($tags->add_tag($image->id, $tagid, 1)) {
								$auth->log('Pievienoja tagu (' . $nslug . ')', 'images', $image->id);
								echo '<li><a href="/tag/' . $nslug . '" rel="tag">' . $newtag . '</a></li>';
							}
						}
					}
				}
				exit;
			}


			$db->query("UPDATE `images` SET `views` = `views`+1 WHERE `id` = '$image->id'");

			if ($auth->ok) {
				if (empty($image->readby)) {
					$db->query("UPDATE `images` SET `readby` = '" . serialize(array($auth->id)) . "' WHERE `id` = '$image->id'");
				} else {
					$readby = unserialize($image->readby);
					if (!in_array($auth->id, $readby)) {
						$readby[] = $auth->id;
						$db->query("UPDATE `images` SET `readby` = '" . serialize($readby) . "' WHERE `id` = '$image->id'");
					}
				}
			}

			$image_title = ucfirst(htmlspecialchars(substr(strip_tags($image->text), 0, 128)));

			$date = display_time(strtotime($image->date));

			$newerstr = '';
			$olderstr = '';

			$newer = $db->get_row("SELECT id FROM images WHERE id > '" . $image->id . "' AND uid = '" . $image->uid . "' AND groupid = 0 ORDER BY id ASC LIMIT 1");
			//$db->debug();
			if ($newer) {
				$newerstr = '<a href="/gallery/' . $inprofile->id . '/' . $newer->id . '#images" class="img-newer ajax-gallery" title="Jaunāka bilde">&laquo; Jaunāka</a>';
			}
			$older = $db->get_row("SELECT id FROM images WHERE id < '" . $image->id . "' AND uid = '" . $image->uid . "' AND groupid = 0 ORDER BY id DESC LIMIT 1");
			if ($older) {
				$olderstr = '<a href="/gallery/' . $inprofile->id . '/' . $older->id . '#images" class="img-older ajax-gallery" title="Vecāka bilde">Vecāka &raquo;</a>';
			}

			if ($image->rating_count > 0) {
				$rating = round($image->rating / $image->rating_count, 2);
			} else {
				$rating = 0;
			}

			$im_size = getimagesize(CORE_PATH . '/' . $image->url);
			if (!empty($image->youtube_video)) {
				$im_size[1] = 280;
				$im_size[0] = 560;
			}
			$tpl->newBlock('image-view');
			$tpl->assign(array(
				'width' => $im_size[0],
				'height' => $im_size[1],
				'image-url' => $image->url,
				'image-text' => add_smile($image->text),
				'image-title' => $image_title,
				'image-views' => $image->views + 1,
				'image-date' => $date,
				'image-posts' => $image->posts,
				'rating' => $rating,
				'rating_count' => $image->rating_count,
				'newer' => $newerstr,
				'older' => $olderstr
			));

			if (empty($image->youtube_video)) {
				$tpl->newBlock('image-view-img');
				$tpl->assign(array(
					'width' => $im_size[0],
					'height' => $im_size[1],
					'image-url' => $image->url,
					'image-title' => $image_title,
					'newer' => $newerstr,
					'older' => $olderstr
				));
			} else {
				$tpl->newBlock('image-view-video');
				$str = add_smile('<p><a href="http://www.youtube.com/watch?v=' . $image->youtube_video . '">http://www.youtube.com/watch?v=' . $image->youtube_video . '</a></p>', 0);
				$tpl->assign('video', $str);
			}

			$tpl->assignGlobal('rate-url', '/gallery/' . $inprofile->id . '/' . $image->id);

			if (empty($rating_users)) {
				$rating_users = array();
			}

			$article_tags = $db->get_results("
  			SELECT
  				`taged`.`id` AS `id`,
  				`taged`.`tag_id` AS `tag_id`,
  				`tags`.`name` AS `name`,
  				`tags`.`slug` AS `slug`
  			FROM
  				`taged`,
  				`tags`
  			WHERE
  				`taged`.`page_id` = '$image->id' AND
  				`taged`.`type` = '1' AND
  				`tags`.`id` = `taged`.`tag_id`
  			");

			$tpl->newBlock('post-tags-ul');
			$tagcount = 0;
			if ($article_tags) {
				foreach ($article_tags as $article_tag) {
					$tagcount++;
					$tpl->newBlock('post-tags-node');
					$tpl->assign(array(
						'tag-title' => $article_tag->name,
						'tag-id' => $article_tag->tag_id,
						'slug' => $article_tag->slug,
					));
				}
			}

			if ($auth->ok && ($auth->level == 1 or $auth->level == 2)) {
				$tpl->newBlock('post-newtags');
			}

			//edit image form
			if ($auth->ok && $auth->id == $inprofile->id || im_mod()) {
				$tpl->newBlock('edit-image-form');
				if ($image->closed) {
					$closemark = ' checked="checked"';
				} else {
					$closemark = '';
				}
				$tpl->assign(array(
					'edit-image-id' => $image->id,
					'edit-image-closed' => $closemark,
					'edit-image-text' => $image->text
				));

				if ($lang == 1) {
					$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
					foreach ($interests as $interest) {
						$tpl->newBlock('select-interest');
						$tpl->assignAll($interest);
						if ($interest->id == $image->interest_id) {
							$tpl->assign('sel', ' selected="selected"');
						}
					}
				}
			}
			if (empty($image_title)) {
				$image_title = 'Bez nosaukuma #' . $image->id;
			}
			$page_title = $image_title . ' | ' . $page_title;


			$comments = $db->get_results("
	SELECT
		`galcom`.`id` AS `id`,
		`galcom`.`date` AS `date`,
		`galcom`.`text` AS `text`,
		`galcom`.`author` AS `author`,
		`galcom`.`edit_times` AS `edit_times`,
		`galcom`.`edit_user` AS `edit_user`,
		`galcom`.`vote_value` AS `vote_value`,
		`galcom`.`vote_users` AS `vote_users`,
		`users`.`nick` AS `author_nick`,
		`users`.`level` AS `author_level`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`karma` AS `author_karma`
	FROM
		`galcom`,
		`users`
	WHERE
		`galcom`.`bid` = '" . $image->id . "' AND
		`galcom`.`removed` = 0 AND
		`users`.`id` = `galcom`.`author`
	ORDER BY
		`galcom`.`id` ASC
			");

			if ($comments) {
				$userclass = array('Lietotāji', 'Admini', 'Moderatori', 'RA', 'Lietotāji');
				$tpl->newBlock('comments-block');
				$comment_number = 1;
				foreach ($comments as $comment) {
					$tpl->newBlock('comments-node');
					$tpl->newBlock('comments-node-user');

					$editedby = '';
					if ($comment->edit_times > 0 && $auth->ok) {
						if ($comment->edit_user == $comment->author) {
							$edit_usr = $comment->author_nick;
						} else {
							$edit_usrinfo = get_user($comment->edit_user);
							$edit_usr = $edit_usrinfo->nick;
						}
						$editedby = '<p class="comment-edited-by">Laboja ' . $edit_usr . ', ' . $comment->edit_times . 'x</p>';
					}

					$comment->date = display_time(strtotime($comment->date));

					//assign comment variables
					$tpl->assign(array(
						'comment-id' => $comment->id,
						'comment-number' => $comment_number,
						'comment-text' => add_smile($comment->text),
						'comment-date' => $comment->date,
						'comment-author' => usercolor($comment->author_nick, $comment->author_level, false, $comment->author),
						'comment-author-id' => $comment->author,
						'aurl' => mkurl('user', $comment->author, $comment->author_nick),
						'avatar' => get_avatar($comment),
						'comment-author-karma' => $comment->author_karma,
						'comment-author-class' => $userclass[$comment->author_level],
						'comment-author-title' => htmlspecialchars($comment->author_nick),
						'comment-editedby' => $editedby,
					));

					$pluslnk = '<span class="voted1"></span>';
					$minuslnk = '<span class="voted2"></span>';

					if ($auth->ok && !$auth->mobile) {
						$check = substr(md5($comment->id . $remote_salt . $auth->id), 0, 5);

						if (!empty($comment->vote_users)) {
							$voters = unserialize($comment->vote_users);
						} else {
							$voters = array();
						}
						$voted = in_array($auth->id, $voters);

						if (!$voted && $auth->id != $comment->author) {
							if (isset($_GET['com_page'])) {
								$skips = (int) $_GET['com_page'];
								$pluslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;type=gallery&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
								$minuslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;type=gallery&&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
							} else {
								$pluslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;type=gallery&&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
								$minuslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;type=gallery&&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
							}
						}
					}

					if ($comment->vote_value > 0) {
						$comment->vote_value = '+' . $comment->vote_value;
						$vclass = 'positive';
					} elseif ($comment->vote_value < 0) {
						$vclass = 'negative';
					} else {
						$vclass = 'zero';
					}

					$tpl->newBlock('comments-vote');
					$tpl->assign(array(
						'comment-id' => $comment->id,
						'comment-vote_value' => $comment->vote_value,
						'comment-plus' => $pluslnk,
						'comment-minus' => $minuslnk,
						'comment-vclass' => $vclass
					));

					$comment_number++;

					if ($auth->ok && ($auth->level == 1 or $auth->level == 2)) {
						$tpl->newBlock('comments-adm');
						$tpl->assign(array(
							'delete' => '/gallery/' . $inprofile->id . '/' . $image->id . '/?delcom=' . $comment->id,
							'edit' => '/gallery/' . $inprofile->id . '/' . $image->id . '/?editcom=' . $comment->id,
						));
					} elseif ($auth->ok && ($auth->level == 3 || $comment->author_karma >= 100) && $auth->id == $comment->author) {
						$tpl->newBlock('comments-own');
						$tpl->assign(array(
							'edit' => '/gallery/' . $inprofile->id . '/' . $image->id . '/?editcom=' . $comment->id,
						));
					}

					if ($auth->ok) {
						$tpl->newBlock('comments-pm');
						$tpl->assign('comment-author-id', $comment->author);
					}
				}
			}

			//sorry, comments closed
			if ($auth->ok && $image->closed) {
				$tpl->newBlock('article-closed');

				//comment form
			} elseif ($auth->ok && $image) {
				$tpl->newBlock('add-comment');
				$tpl->assign(array(
					'comment-pid' => $image->id,
					'comment-pid-check' => md5($image->id . $remote_salt . $auth->id . $inprofile->nick),
				));

				$tpl->newBlock('tinymce-simple');
			} elseif (!$auth->ok) {
				$tpl->newBlock('login-to-comment');
			}
		}
	}

	if ($auth->ok && $auth->id == $inprofile->id) {
		$tpl->assignGlobal('gal-sel', ' class="selected"');
	}

	$tpl->assignGlobal('jquery-tools', ',jquery.tools.min.js');
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

$pagepath = '';

