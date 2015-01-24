<?php

$strid = sanitize($_GET['var1']);
$article = $db->get_row("SELECT * FROM `pages` WHERE `strid` = '" . $strid . "' LIMIT 1");

if ($article) {

	//redirektē uz pareizo adresi, ja kaut kādā veidā atvērts derīgs strid, bet nepareizā domēnā
	if ($article->lang != $lang) {
		redirect(get_protocol($article->lang) . $config_domains[$article->lang]['domain'] . '/read/' . $article->strid, true);
	}

	// runescape apakšprojektā eksistē raksti ar platām tabulām,
	// tāpēc tādiem vienu kolonnu aizvācam
	if ($lang == 9 && ($article->is_wide && !isset($_GET['narrow']) || isset($_GET['wide']))) {
		$tpl_options = 'no-left';
	}

	$category = get_cat($article->category);

	include(CORE_PATH . '/includes/class.tags.php');

	$end = $comments_per_page;

	if (isset($_GET['var2']) && $_GET['var2'] == 'com_page') {
		$skip = (int) $_GET['var3'];
	} else {
		$skip = 0;
	}
	$skip = $skip * $end;

	// pārvietošanās pa komentāru lapām
	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
		redirect('/read/' . $article->strid . '/com_page/' . $skip / $comments_per_page);
	}

	// raksta "pielīmēšana"
	if ((im_mod() || im_cat_mod()) && isset($_GET['attach'])) {
		$db->query("UPDATE pages SET attach = '1' WHERE id = '$article->id'");
		redirect('/read/' . $article->strid);
	}

	if ((im_mod() || im_cat_mod()) && isset($_GET['detach'])) {
		$db->query("UPDATE pages SET attach = '0' WHERE id = '$article->id'");
		redirect('/read/' . $article->strid);
	}

	// komentāra pievienošana
	if (!$article->closed && isset($_POST['comment-pid']) && !empty($_POST['commenttext']) && $auth->ok && $_POST['comment-pid'] == $article->id) {
		if (!isset($_POST['checksrc']) or $_POST['checksrc'] != substr(md5($article->id . $remote_salt . $auth->id), 0, 8)) {
			redirect();
		}
		require(CORE_PATH . '/includes/class.comment.php');
		$addcom = new Comment();
		$addcom->add_comment($article->id, $auth->id, $_POST['commenttext'], 0);
		$total = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = '" . $article->id . "' AND parent = '0'");

		if ($total > $end) {
			$skip = '/com_page/' . floor($total / $end);
		} else {
			$skip = '';
		}

		update_stats($article->category);
		if (!empty($category->parent)) {
			update_stats($category->parent);
		}

		redirect('/read/' . $article->strid . $skip);
	}


	// atbildes komentāram pievienošana
	if (!$article->closed && isset($_POST['rpl-comment']) && !empty($_POST['rpl-txt']) && $auth->ok && $_POST['rpl-page'] == $article->id) {
		$comment = (int) $_POST['rpl-comment'];
		$comment = $db->get_row("SELECT * FROM `comments` WHERE `id` = '$comment' AND `pid` = '$article->id' AND `parent` = 0");
		if (!$comment) {
			set_flash('Kļūdains lapas vai komentāra ID');
		} else {
			require(CORE_PATH . '/includes/class.comment.php');
			$addcom = new Comment();
			$addcom->add_comment($article->id, $auth->id, $_POST['rpl-txt'], 0, $comment->id);
			$total = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = '" . $article->id . "' AND parent = '0' AND `removed` = 0");
			if ($total > $end) {
				$skip = '/com_page/' . floor($total / $end);
			} else {
				$skip = '';
			}
			$url = '/read/' . $article->strid . $skip;
			if ($comment->author != $article->author) {
				notify($comment->author, 0, $comment->id, $url, textlimit(hide_spoilers($article->title), 64));
			}

			update_stats($article->category);
			if (!empty($category->parent)) {
				update_stats($category->parent);
			}

			redirect($url);
		}
	}

	// komentāra rediģēšana
	if ($auth->ok && isset($_POST['edit-comment-id'])) {
		$edit_comment_id = (int) $_POST['edit-comment-id'];
		$edit_comment_text = htmlpost2db($_POST['edit-comment-text']);

		$edit_comment_author = $db->get_var("SELECT author FROM comments WHERE id = '$edit_comment_id' LIMIT 1");
		if ((($auth->level == 3 || $article->lang == 5) && $edit_comment_author != $auth->id) && !im_cat_mod() && !im_mod()) {
			set_flash('Tu vari labot tikai savus komentārus!', 'error');
			redirect('/read/' . $article->strid);
		}
		if ($auth->level == 0) {
			if (($auth->karma < $min_post_edit or $edit_comment_author != $auth->id) && !im_cat_mod()) {
				set_flash('Tev jābūt vismaz ' . $min_post_edit . ' karmai un tu vari labot tikai savus komentārus!', 'error');
				redirect('/read/' . $article->strid);
			}
		}
		$db->query("UPDATE comments SET text = ('$edit_comment_text'), edit_time = '" . time() . "', edit_user = '$auth->id', edit_times = edit_times+1 WHERE id = '$edit_comment_id' AND pid = '$article->id' LIMIT 1");
		$auth->log('Laboja komentāru', 'comments', $edit_comment_id);
		redirect('/read/' . $article->strid);
	}

	// komentāra dzēšana
	if (im_mod() && isset($_GET['delcom']) && check_token('delcom', $_GET['token'])) {
		$del = (int) $_GET['delcom'];
		$comment = $db->get_row("SELECT * FROM comments WHERE id = '$del' AND `removed` = 0");
		if ($del > 0 && $comment->pid == $article->id) {
			$db->query("UPDATE `comments` SET `removed` = 1 WHERE `id` = '$del' AND `pid` = '$article->id'");

			//delete comment replies
			if ($comment->replies && !$comment->parent) {
				$db->query("UPDATE `comments` SET `removed` = 1 WHERE `parent` = '$del' AND `pid` = '$article->id'");
			} elseif ($comment->parent) {
				$db->query("UPDATE `comments` SET `posts` = `posts`-1 WHERE `id` = '$comment->parent'");
			}

			//recount posts and update counter cache
			$posts = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = '$article->id' AND `removed` = 0");
			$db->query("UPDATE `pages` SET `posts` = '$posts' WHERE id = '$article->id'");

			//update user posts, if not anonymous
			if ($comment->author != 0) {
				$db->query("UPDATE `users` SET `posts` = `posts`-1 WHERE `id` = '$comment->author'");
			}

			$auth->log('Izdzēsa komentāru', 'comments', $comment->id);
			redirect('/read/' . $article->strid);
		}
	}

	//pieliek tagus
	if ($auth->ok && in_array($auth->level, array(1, 2, 3)) && isset($_POST['newtags'])) {
		$newtags = explode(',', $_POST['newtags']);
		$tags = new tags;
		foreach ($newtags as $newtag) {
			if (strlen(trim($newtag)) > 1) {
				$newtag = h(ucfirst(strip_tags(trim($newtag))));
				$nslug = mkslug($newtag);
				if (!empty($newtag)) {
					$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
					if (!$tagid) {
						$db->query("INSERT INTO tags (name,slug) VALUES ('" . sanitize($newtag) . "','$nslug')");
						$tagid = $db->insert_id;
					}
					if ($tags->add_tag($article->id, $tagid)) {
						$auth->log('Pievienoja tagu (' . $nslug . ')', 'pages', $article->id);
						echo '<li><a href="/tag/' . $nslug . '" rel="tag">' . $newtag . '</a></li>';
					}
				}
			}
		}
		exit;
	}

	// lietotāja paraksta dzēšana
	if (im_mod()) {
		if (isset($_GET['remove_signature'])) {
			$remove_signature = (int) $_GET['remove_signature'];
			$message = sanitize(h(strip_tags($_GET['message'])));
			$db->query("UPDATE `users` SET `signature` = ('<small>Noņēma: " . $auth->nick . ". Iemesls: " . $message . "</small>') WHERE `id` = '" . $remove_signature . "' LIMIT 1");
			get_user($remove_signature, true);
			$auth->log('Nodzēsa parakstu (' . $message . ')', 'users', $remove_signature);
			redirect('/read/' . $article->strid);
		}
	}

	// redirekts
	if (!empty($article->redirect)) {
		header("HTTP/1.1 301 Moved Permanently");
		redirect($article->redirect);
	}

	if (!$rating_users = unserialize($article->rating_users)) {
		$rating_users = array();
	}

	// raksta vērtēšana
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

			$rating = ($article->rating + $vote);

			$db->query("UPDATE pages SET
                  rating = $rating,
                  rating_count = rating_count+1,
                  rating_users = '" . serialize($rating_users) . "'
                WHERE id = '$article->id'
                ");
			die('Lasītāju vērtējums: ' . round($rating / ($article->rating_count + 1), 2) . ' (' . ($article->rating_count + 1) . ' ' . lv_dsk(($article->rating_count + 1), 'balss', 'balsis') . ')');
		}
		die('Jāielogojas lai balsotu');
	}

	// atsvaidzina raksta datus
	if ($auth->ok) {
		set_action('rakstu &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;');
	}

	$db->query("UPDATE `pages` SET `views` = `views`+1 WHERE `id` = '$article->id'");

	if ($auth->ok) {
		if (empty($article->readby)) {
			$db->query("UPDATE pages SET readby = ('" . serialize(array($auth->id)) . "') WHERE id = '$article->id'");
		} else {
			$readby = unserialize($article->readby);
			if (!in_array($auth->id, $readby)) {
				$readby[] = $auth->id;
				$db->query("UPDATE pages SET readby = ('" . serialize($readby) . "') WHERE id = '$article->id'");
			}
		}
	}

	// priviliģēto līmeņu iespējas
	if (!$category->mods_only || im_mod() || im_rs_mod() || $auth->level == 5) {

		// komentāru slēgšana/atslēgšana
		if ($auth->ok && (($auth->id == $article->author && !$article->disable_close) || im_mod() || im_cat_mod())) {
			if (isset($_POST['close-do'])) {
				$closed = (bool) $_POST['close'];
				$db->query("UPDATE `pages` SET `closed` = '$closed' WHERE `id` = '$article->id'");
				if ($closed) {
					$auth->log('Aizslēdza komentārus rakstam', 'pages', $article->id);
				} else {
					$auth->log('Atvēra komentārus rakstam', 'pages', $article->id);
				}
				die('ok');
			}
		}

		// autora komentāru atvēršanas bloķēšana
		if ($auth->ok && (im_mod() || im_cat_mod())) {
			if (isset($_POST['disable-close-do'])) {
				$closed = (bool) $_POST['disable-close'];
				$db->query("UPDATE `pages` SET `disable_close` = '$closed' WHERE `id` = '$article->id'");
				if ($closed) {
					$auth->log('Bloķēja autora komentāru atvēršanu', 'pages', $article->id);
				} else {
					$auth->log('Atbloķēja autora komentāru atvēršanu', 'pages', $article->id);
				}
				die('ok');
			}

			if (isset($_POST['attach-do']) && $category->isforum) {
				$attach = (bool) $_POST['attach'];
				$db->query("UPDATE `pages` SET `attach` = '$attach' WHERE `id` = '$article->id'");
				if ($attach) {
					$auth->log('Piesprauda rakstu', 'pages', $article->id);
				} else {
					$auth->log('Atsprauda rakstu', 'pages', $article->id);
				}
				die('ok');
			}
		}

		$author = get_user($article->author);

		if (can_edit_page($article)) {
			$tpl->newBlock('page-options');
			$tpl->assign(array(
				'article-id' => $article->id
			));
		}

		// komentāra rediģēšanas forma
		if ($auth->ok && isset($_GET['editcom'])) {
			$editcom = (int) $_GET['editcom'];
			$comment = $db->get_row("SELECT text,id,author FROM comments WHERE id = '$editcom' AND pid = '$article->id' AND removed = 0 LIMIT 1");
			if (($auth->level == 3 && $comment->author != $auth->id) && !im_cat_mod()) {
				echo 'Tu vari labot tikai savus komentārus!';
				exit;
			}
			if ($auth->level == 0) {
				if (($auth->karma < $min_post_edit or $comment->author != $auth->id) && !im_cat_mod()) {
					echo 'Tev jābūt vismaz ' . $min_post_edit . ' karmai un tu vari labot tikai savus komentārus!';
					exit;
				}
			}
			$tpl->newBlock('adm-edit-comment');
			$tpl->assign(array(
				'comment-text' => h($comment->text),
				'comment-id' => $comment->id
			));

			$tpl->newBlock('tinymce-enabled');
			$page_title = 'Komentāra labošana rakstam: &quot;' . $article->title . '&quot; | ' . $category->title;
		}

		// raksta rediģēšanas forma
		elseif (isset($_GET['mode']) && $_GET['mode'] == 'edit' && can_edit_page($article)) {

			// iesniegti $_POST dati
			if (isset($_POST['edit-topic-title']) && isset($_POST['edit-topic-body']) && isset($_POST['edit-topic-id'])) {
				$body = trim($_POST['edit-topic-body']);
				$title = trim($_POST['edit-topic-title']);
				$topicid = (int) $_POST['edit-topic-id'];

				// rs apakšprojektā mainīt sadaļu iespējams tikai moderatoriem
				if (im_mod() || $lang != 9) {
					$topiccat = (int) $_POST['edit-category'];
				} else {
					$topiccat = $article->category;
				}


				$topicwide = (isset($_GET['wide']) || $article->is_wide && !isset($_GET['narrow'])) ? 1 : 0;

				if ($body && $title && $topicid) {

					$title = title2db($title);
					$body = htmlpost2db($body);

					if (isset($_FILES['edit-avatar']) && !empty($_FILES['edit-avatar'])) {
						require(CORE_PATH . '/includes/class.upload.php');
						$foo = new Upload($_FILES['edit-avatar']);
						$foo->file_new_name_body = $topicid;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->allowed = array('image/*');
						$foo->image_ratio = true;
						$foo->image_ratio_pixels = 17800;
						$foo->jpeg_quality = 98;
						$foo->image_ratio_no_zoom_in = true;
						$foo->file_auto_rename = false;
						$foo->file_overwrite = true;
						$foo->process('dati/bildes/avatari/');
						if ($foo->processed) {
							$foo->file_new_name_body = $topicid;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 75;
							$foo->image_y = 75;
							$foo->allowed = array('image/*');
							$foo->image_ratio_crop = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/av_sm/');
							unlink('dati/bildes/topic-av/' . $topicid . '.jpg');
							$foo->clean();
							$article->avatar = 'dati/bildes/avatari/' . $topicid . '.jpg';
							$article->sm_avatar = 'dati/bildes/av_sm/' . $topicid . '.jpg';
						}
					}

					if ($article->edit_user) {
						$lastmodu = $article->edit_user;
					} else {
						$lastmodu = $article->author;
					}

					$db->query("INSERT INTO pages_ver (pid,time,title,text,nextmod,category,is_wide) VALUES (
                        '$article->id',
                        '" . time() . "',
                        '" . sanitize($article->title) . "',
                        '" . sanitize($article->text) . "',
                        '" . $lastmodu . "',
                        '" . $article->category . "',
                        '" . (int) $article->is_wide . "'
                    )");

					$db->query("UPDATE pages SET
						text = ('$body'),
						intro = (''),
						title = ('$title'),
						avatar = ('$article->avatar'),
						sm_avatar = ('$article->sm_avatar'),
						category = ('$topiccat'),
						edit_time = ('" . time() . "'),
						edit_user = ('$auth->id'),
						edit_times = edit_times+1,
                        is_wide = $topicwide
					WHERE id = '$topicid'");

					update_stats($topiccat);

					if ($category->textid == 'filmas' && im_mod()) {
						$title_lv = title2db($_POST['movie-titlelv']);
						if ($title_lv == 'Bez nosaukuma') {
							$title_lv = '';
						}
						$year = intval($_POST['movie-year']);
						if (empty($year)) {
							$year = '';
						}
						$imdb_id = sanitize($_POST['movie-imdb']);
						$movie_type = sanitize($_POST['movie-type']);

						$movie_data = $db->get_row("SELECT * FROM `movie_data` WHERE `page_id` = '$article->id'");
						if (!empty($movie_data)) {
							$db->query("UPDATE `movie_data` SET `year` = '$year', `title_lv` = '$title_lv', `imdb_id` = '$imdb_id', `type` = '$movie_type' WHERE `page_id` = '$article->id'");
						} else {
							$db->query("INSERT INTO `movie_data` (`page_id`, `title_lv`, `imdb_id`, `year`, `type`) VALUES ('$article->id', '$title_lv', '$imdb_id', '$year', '$movie_type')");
						}


						//iegūst datus no IMDB pēc nosaukuma
						if ($_POST['imdb-getdata']) {

							include(LIB_PATH . '/imdb-grabber/imdb.class.php');
							$oIMDB = new IMDB($title);
							if ($oIMDB->isReady) {

								if ($year = $oIMDB->getYear()) {
									$db->query("UPDATE `movie_data` SET `year` = '$year' WHERE `page_id` = '$article->id'");
								}

								if ($runtime = $oIMDB->getRuntime()) {
									$db->query("UPDATE `movie_data` SET `runtime` = '$runtime' WHERE `page_id` = '$article->id'");
								}

								if ($rating = $oIMDB->getRating()) {
									$db->query("UPDATE `movie_data` SET `rating` = '$rating' WHERE `page_id` = '$article->id'");
								}

								//pievieno žanrus
								if ($genres = $oIMDB->getGenre()) {
									$genres = explode('/', $genres);
									foreach ($genres as $genre) {
										$genre = sanitize(trim($genre));
										if (!empty($genre) && !$db->get_var("SELECT count(*) FROM `movie_genres` WHERE `page_id` = '$article->id' AND `genre` = '$genre'")) {
											$db->query("INSERT INTO `movie_genres` (`page_id`, `genre`) VALUES ('$article->id', '$genre')");
										}
									}
								}

								set_flash("IMDB dati veiksmīgi iegūti!", "success");

								$auth->log('Atjaunoja IMDB datus', 'pages', $topicid);
							} else {
								set_flash("Neizdevās iegūt IMDB datus, lūdzu ievadi derīgu linku vai nosaukumu!", "error");
							}
						}
					}

					$auth->log('Laboja rakstu', 'pages', $topicid);
					redirect('/read/' . $article->strid);
				}
			}

			// raksta rediģēšanas forma ar datiem
			$tpl->newBlock('edit-article');
			$tpl->assign(array(
				'article-showtitle' => $article->title,
				'article-title' => $article->title,
				'article-text' => h($article->text),
				'article-id' => $article->id
			));

			// izdrukās lapā adresi, caur kuru iespējams atvērt kādu no skatiem
			if ($lang == 9 && (!$article->is_wide && !isset($_GET['wide']) || isset($_GET['narrow']))) {
				$tpl->newBlock('goto-wide-page');
				$tpl->assign('wide-page-url', str_replace(array('wide=1', 'narrow=1', '\&', '&amp;'), '', h($_SERVER['REQUEST_URI'])));
			} else if ($lang == 9) {
				$tpl->newBlock('goto-narrow-page');
				$tpl->assign('wide-page-url', str_replace(array('wide=1', 'narrow=1', '\&', '&amp;'), '', h($_SERVER['REQUEST_URI'])));
			}


			// runescape projektā pie rediģēšanas sadaļu saraksts redzams tikai modiem
			if (im_rs_mod() || $lang != 9) {

				// atgriež sarakstu ar formā izvadāmajām kategorijām
				if ($lang == 9) {
					$cats = get_rs_page_categories($article->category);
				} else {
					$cats = get_page_categories($article->category);
				}

				$tpl->newBlock('edit-article-category');

				foreach ($cats as $ctitle => $catgroup) {

					$tpl->newBlock('catgroup');
					$tpl->assign('title', $ctitle);

					foreach ($catgroup as $key => $val) {
						$tpl->newBlock('catitem');
						$sel = '';
						if ($key == $article->category) {
							$sel = ' selected="selected"';
						}
						$tpl->assign(array(
							'title' => $val,
							'id' => $key,
							'sel' => $sel,
						));
					}
				}
			}

			if ($article->avatar && $category->textid != 'filmas') {
				$tpl->newBlock('edit-article-av');
				$tpl->assign(array(
					'img' => $article->avatar
				));
			}

			if ($category->textid == 'filmas' && im_mod()) {

				$images = array();

				if (isset($_POST['avatar-url']) && !empty($_POST['avatar-url']) && isset($_POST['submit'])) {

					$data = curl_get($_POST['avatar-url']);
					$ext = substr($_POST['avatar-url'], -4);
					if ($data) {
						//directory
						$dir1 = substr($article->id, -1);
						if (!$dir1) {
							$dir1 = 0;
						}
						$dir2 = substr($article->id, -2, 1);
						if (!$dir2) {
							$dir2 = 0;
						}
						$path = $dir1 . '/' . $dir2;
						rmkdir(IMG_PATH . '/movies/large/' . $path . '/');
						rmkdir(IMG_PATH . '/movies/thb/' . $path . '/');

						$tmpname = '/tmp/' . uniqid() . '.' . $ext;
						file_put_contents($tmpname, $data);

						$file_title = mkslug($article->title . '-poster');

						require_once(CORE_PATH . '/includes/class.upload.php');
						$foo = new Upload($tmpname);
						$foo->allowed = array('image/*');
						$foo->image_resize = true;
						$foo->image_ratio_crop = true;
						$foo->image_y = 215;
						$foo->image_x = 145;
						$foo->file_new_name_body = $file_title;
						$foo->image_ratio_no_zoom_in = false;
						$foo->image_ratio_crop = true;
						$foo->jpeg_quality = 98;
						$foo->file_overwrite = true;
						$foo->image_convert = 'jpg';
						$foo->process(IMG_PATH . '/movies/thb/' . $path . '/');
						if ($foo->processed) {

							$foo->file_new_name_body = $file_title;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 620;
							$foo->image_y = 620;
							$foo->allowed = array('image/*');
							$foo->image_ratio_crop = false;
							$foo->image_ratio_no_zoom_in = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process(IMG_PATH . '/movies/large/' . $path . '/');

							$db->query("DELETE FROM `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id'");
							$db->query("INSERT INTO `movie_images` (`page_id`, `main`, `image`, `thb`, `title`, `created`, `created_by`)
									VALUES ('$article->id', 1, '" . sanitize('/movies/large/' . $path . '/' . $foo->file_dst_name) . "', '" . sanitize('/movies/thb/' . $path . '/' . $foo->file_dst_name) . "', '" . sanitize($article->title . ' poster') . "', NOW(), '$auth->id')");

							//update page avatars
							$foo->file_new_name_body = $article->id;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->allowed = array('image/*');
							$foo->image_ratio = true;
							$foo->image_ratio_pixels = 17800;
							$foo->jpeg_quality = 98;
							$foo->image_ratio_no_zoom_in = true;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/avatari/');
							if ($foo->processed) {
								$foo->file_new_name_body = $article->id;
								$foo->image_resize = true;
								$foo->image_convert = 'jpg';
								$foo->image_x = 75;
								$foo->image_y = 75;
								$foo->allowed = array('image/*');
								$foo->image_ratio_crop = true;
								$foo->jpeg_quality = 98;
								$foo->file_auto_rename = false;
								$foo->file_overwrite = true;
								$foo->process('dati/bildes/av_sm/');

								if (file_exists('dati/bildes/topic-av/' . $article->id . '.jpg')) {
									unlink('dati/bildes/topic-av/' . $article->id . '.jpg');
								}

								$foo->clean();
								$article->avatar = 'dati/bildes/avatari/' . $article->id . '.jpg';
								$article->sm_avatar = 'dati/bildes/av_sm/' . $article->id . '.jpg';

								$db->query("UPDATE pages SET
									avatar = ('$article->avatar'),
									sm_avatar = ('$article->sm_avatar')
								WHERE id = '$article->id'");
							}

							$foo->clean();
							set_flash('Attēls pievienots', 'success');
						}
					}
				} elseif (isset($_POST['search-avatar'])) {

					$q = urlencode($article->title . ' movie poster');
					$jsonurl = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&tbs=iar:t&q=' . $q;
					$result = json_decode(curl_get($jsonurl), true);
					$images = array(
						$result['responseData']['results'][0]['url'],
						$result['responseData']['results'][1]['url'],
						$result['responseData']['results'][2]['url'],
						$result['responseData']['results'][3]['url']
					);
				}

				$movie_data = $db->get_row("SELECT * FROM `movie_data` WHERE `page_id` = '$article->id'");
				$tpl->newBlock('edit-movie-data');
				if (!empty($movie_data)) {
					$tpl->assignAll($movie_data);
					$tpl->assign('sel-' . $movie_data->type, ' selected="selected"');
				}


				$tpl->newBlock('edit-movie');
				$tpl->assign(array(
					'article-id' => $article->id,
				));
				if (!empty($images)) {
					foreach ($images as $img) {
						$tpl->newBlock('edit-movie-image');
						$tpl->assign(array(
							'url' => $img,
						));
					}
				}

				$avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1");
				if ($avatar) {
					$tpl->newBlock('edit-movie-avatar');
					$tpl->assign(array(
						'url' => $avatar->thb,
					));
				}
			}


			$tpl->newBlock('tinymce-enabled');

			// raksta iepriekšējo versiju saraksts
		} elseif (isset($_GET['mode']) && $_GET['mode'] == 'history' && can_edit_page($article)) {

			$tpl->newBlock('page-history');

			$page_title = 'Saglabātās versijas &quot;' . $article->title . '&quot; | ' . $category->title;

			$records = $db->get_results("SELECT * FROM pages_ver WHERE pid = '$article->id' ORDER BY time DESC");

			if ($records) {
				$tpl->newBlock('page-history-list');
				foreach ($records as $record) {
					$tpl->newBlock('page-history-node');
					$tpl->assign(array(
						'title' => $record->title,
						'time' => date('Y-m-d H:i', $record->time),
						'id' => $record->id,
						'user' => $db->get_var("SELECT nick FROM users WHERE id = '$record->nextmod' LIMIT 1"),
						'symbols' => strlen($record->text)
					));
				}
			}

			//add bookmark
		} elseif ($auth->ok && isset($_GET['mode']) && $_GET['mode'] == 'bookmark') {
			if (!$db->get_var("SELECT id FROM bookmarks WHERE userid = '$auth->id' AND pageid = '$article->id'")) {
				$db->query("INSERT INTO bookmarks (userid,pageid) VALUES ('$auth->id','$article->id')");
				if (!empty($article->avatar)) {
					push('Pievienoja savai izlasei rakstu &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;', '/dati/bildes/topic-av/' . $article->id . '.jpg');
				} else {
					push('Pievienoja savai izlasei rakstu &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;');
				}
				redirect('/read/' . $article->strid . '/?status=added');
			} else {
				redirect('/read/' . $article->strid . '/?status=inbookmarks');
			}

			//show page contents
		} else {

			$date = display_time(strtotime($article->date));

			if ($article->edit_times > 0) {
				$edit_usrinfo = get_user($article->edit_user);
				$edit_usr = $edit_usrinfo->nick;
				$article->text .= '<p class="comment-edited-by">Laboja ' . $edit_usr . ', labots ' . $article->edit_times . 'x</p>';
			}

			$rat = 0;
			if ($article->rating_count > 0) {
				$rat = round($article->rating / $article->rating_count, 2);
			}

			$article_text = add_smile($article->text, 1, $article->disable_emotions);
			if ($article->strid == 'exs-lv-infografiks') {
				$article_text .= '<iframe src="//infogr.am/exs_lv-9431592631" width="499" height="12542" scrolling="no" frameborder="0" style="border:none;margin: 0 auto;width:499px;display:block;"></iframe><div style="width:499px;border-top:1px solid #acacac;padding-top:3px;font-family:Arial;font-size:10px;text-align:center;"><a target="_blank" href="http://infogr.am/exs_lv-9431592631" style="color:#acacac;text-decoration:none;">VĒSTURESTATISTIKA</a> | <a style="color:#acacac;text-decoration:none;" href="http://infogr.am" target="_blank">Create infographics</a></div>';
			}

			if (!$author->deleted) {
				$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($author->nick, $author->level, false, $article->author) . '</a>';
			} else {
				$author_link = '<em>dzēsts</em>';
			}

			$tpl->newBlock('read-article');
			$tpl->assign(array(
				'article-title' => $article->title,
				'article-text' => $article_text,
				'article-id' => $article->id,
				'article-views' => $article->views + 1,
				'article-date' => $date,
				'author' => $author_link,
				'level' => $author->level,
				'gender' => $author->gender,
				'article-posts' => $article->posts,
				'rating' => $rat,
				'rating_count' => $article->rating_count
			));



			$page_title = $article->title . ' - ' . $category->title;


			// filmu rakstiem specifiska informācija
			if ($category->textid == 'filmas') {
				$avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1");
				if (!empty($avatar)) {

					$tpl->newBlock('movie-avatar');
					$tpl->assignAll($avatar);

					if (!$auth->mobile) {
						$tpl->newBlock('opengraph');
						$tpl->assign(array(
							'title' => h($article->title),
							'type' => 'video.movie',
							'url' => 'http://' . $_SERVER['SERVER_NAME'] . '/read/' . $article->strid,
							'image' => $img_server . $avatar->thb
						));
					}
				}
				$movie_data = $db->get_row("SELECT * FROM `movie_data` WHERE `page_id` = '$article->id'");
				if (!empty($movie_data)) {
					if (!empty($movie_data->title_lv)) {
						$tpl->newBlock('title-lv');
						$tpl->assign('title', $movie_data->title_lv);
					}

					$tpl->newBlock('movie-info');

					$page_title = $article->title;

					if (!empty($movie_data->title_lv)) {
						$page_title .= ' / ' . $movie_data->title_lv;
					}

					if (!empty($movie_data->year)) {
						$page_title .= ' (' . $movie_data->year . ')';
						$tpl->newBlock('movie-info-year');
						$tpl->assign('year', $movie_data->year);
					}

					$page_title .= ' - ' . $category->title;

					if (!empty($movie_data->runtime)) {
						$tpl->newBlock('movie-info-runtime');
						$tpl->assign('runtime', $movie_data->runtime);
					}

					if (!empty($movie_data->rating)) {
						$tpl->newBlock('movie-info-rating');
						$tpl->assign('rating', $movie_data->rating);
					}

					if (!empty($movie_data->type)) {

						$types = array(
							'movie' => 'Filma',
							'documentary' => 'Dokumentāls raidījums',
							'animation' => 'Animācijas filma',
							'series' => 'Seriāls'
						);

						$tpl->newBlock('movie-info-type');
						$tpl->assign('type', $types[$movie_data->type]);
						$tpl->assign('title', $article->title);
					}


					if ($genres = $db->get_col("SELECT `genre` FROM `movie_genres` WHERE `page_id` = '$article->id'")) {
						$gen = array();
						foreach ($genres as $genre) {
							$gen[] = '<a href="/filmas/search?genre=' . $genre . '">' . translate_genres($genre) . '</a>';
						}
						$tpl->newBlock('movie-info-genres');
						$tpl->assign('genres', implode(' / ', $gen));
					}

					if ($like_count = $db->get_var("SELECT count(*) FROM `movie_ratings` WHERE `page_id` = $article->id AND `rating` = 1")) {

						$likes = $db->get_results("
							SELECT
								`users`.`id`,
								`users`.`nick`,
								`users`.`avatar`,
								`users`.`av_alt`
							FROM
								`movie_ratings`,
								`users`
							WHERE
								`movie_ratings`.`page_id` = $article->id AND
								`movie_ratings`.`rating` = 1 AND
								`users`.`id` = `movie_ratings`.`user_id`
							ORDER BY
								`users`.`karma` DESC
							LIMIT 9
						");

						$tpl->newBlock('movie-likes');

						$rest = $like_count - count($likes);
						if ($rest > 0) {
							$tpl->assign(array(
								'rest' => ' un ' . $rest . ' citi...'
							));
						}

						foreach ($likes as $user_like) {
							$tpl->newBlock('movie-likes-user');
							$tpl->assign(array(
								'avatar' => get_avatar($user_like, 's'),
								'nick' => $user_like->nick
							));
						}
					}

					/* vertejuma pievienošana */
					$like = '';
					if ($auth->ok && $auth->karma >= 100 && !$db->get_var("SELECT count(*) FROM `movie_ratings` WHERE `page_id` = '$article->id' AND `user_id` = '$auth->id'")) {

						$token = md5($auth->id . '-' . $article->id . '-' . $remote_salt);

						if (isset($_GET['movie']) && isset($_GET['check']) && $_GET['check'] == $token) {
							$rating = 1;
							if (isset($_GET['dislike'])) {
								$rating = -1;
							}
							$db->query("INSERT INTO `movie_ratings` (`page_id`, `user_id`, `rating`, `created`, `ip`) VALUES ('$article->id', '$auth->id', '$rating', NOW(), '$auth->ip')");
							$db->query("UPDATE
									`movie_data`
								SET
									`exs_likes` = (SELECT count(*) FROM `movie_ratings` WHERE `page_id` = $article->id AND `rating` = 1),
									`exs_dislikes` = (SELECT count(*) FROM `movie_ratings` WHERE `page_id` = $article->id AND `rating` = '-1')
								WHERE
									`page_id` = $article->id");

							if ($rating == 1 && !empty($types[$movie_data->type])) {
								push('Patīk ' . strtolower($types[$movie_data->type]) . ' &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;', '/dati/bildes/topic-av/' . $article->id . '.jpg');
							}

							//ajax
							if (isset($_GET['_'])) {
								die('Balsojums pieņemts!');

								//nav ajax, redirektejam
							} else {
								set_flash('Balsojums pieņemts!', 'success');
								redirect('/read/' . $article->strid);
							}
						}

						$like .= '<strong>Tavs vērtējums:</strong> <span style="padding: 20px 0 0" class="movie-liker"><a href="?movie=' . $article->id . '&amp;check=' . $token . '&amp;like" class="button success small">Man patīk</a> ';
						$like .= '<a href="?movie=' . $article->id . '&amp;check=' . $token . '&amp;dislike" class="button danger small">Man nepatīk</a></span>';
					}
					if (!empty($like)) {
						$tpl->newBlock('movie-like');
						$tpl->assign('like', $like);
					}
				}
			} elseif ($article->avatar) {

				if (in_array($article->category, array(81, 1))) {
					$tpl->newBlock('article-avatar-box');
					$tpl->assign(array(
						'article-avatar-image' => trim($article->avatar),
						'article-avatar-alt' => trim(h($article->title))
					));
				}
			}

			if ($auth->ok) {
				$tpl->newBlock('add-bookmark');
				$bkm_status = '';
				if (isset($_GET['status']) && $_GET['status'] == 'added') {
					$bkm_status = ' <span class="thanks">Pievienots!</span>';
				} elseif (isset($_GET['status']) && $_GET['status'] == 'inbookmarks') {
					$bkm_status = ' <span class="fail">Jau ir izlasē!</span>';
				}
				$tpl->assign(array(
					'article-id' => $article->id,
					'article-status' => $bkm_status,
				));
			}

			$stag = $db->get_var("SELECT `tags`.`id` FROM `taged`,`tags` WHERE `taged`.`page_id` = '$article->id' AND `tags`.`special` = '1' AND `tags`.`id` = `taged`.`tag_id` AND `taged`.`lang` = '$lang' LIMIT 1");
			if ($stag) {
				$article_tags = $db->get_results("SELECT * FROM `taged` WHERE `tag_id` = '$stag'");

				$tpl->newBlock('post-stags');
				if ($article_tags) {
					foreach ($article_tags as $article_tag) {
						$tag_page = $db->get_row("SELECT `title`,`strid` FROM `pages` WHERE `id` = '$article_tag->page_id'");
						if (!empty($tag_page) && $article->id != $article_tag->page_id) {
							$tpl->newBlock('post-stags-node');
							$tpl->assign(array(
								'title' => $tag_page->title,
								'url' => '/read/' . $tag_page->strid
							));
						}
					}
				}
			}

			$article_tags = $db->get_results("SELECT
			`taged`.`id` AS `id`,
			`tags`.`name` AS `name`,
			`tags`.`slug` AS `slug`
		FROM
			`taged`,
			`tags`
		WHERE
			`taged`.`page_id` = '$article->id' AND
			`taged`.`type` = '0' AND
			`taged`.`lang` = '$lang' AND
			`tags`.`id` = `taged`.`tag_id`
		LIMIT 16");
			$tpl->newBlock('post-tags');
			$tagcount = 0;

			if ($article_tags) {
				$tpl->newBlock('post-tags-ul');
				foreach ($article_tags as $article_tag) {
					$tagcount++;
					$tpl->newBlock('post-tags-node');
					$tpl->assign(array(
						'tag-title' => $article_tag->name,
						'slug' => $article_tag->slug,
					));
				}
			}

			if ($auth->ok && in_array($auth->level, array(1, 2, 3))) {
				$tpl->newBlock('post-newtags');
			}

			if ($auth->ok && (($auth->id == $article->author) || im_mod() || im_cat_mod())) {
				$tpl->newBlock('post-tools');
				if ($article->closed) {
					$closemark = ' checked="checked"';
				} else {
					$closemark = '';
				}
				$tpl->assign('edit-page-closed', $closemark);
				if ($auth->id == $article->author && !im_mod() && !im_cat_mod()) {
					$disablemark = '';
					if ($article->disable_close) {
						$disablemark = ' disabled="disabled"';
					}
					$tpl->assign('edit-page-disable-closing', $disablemark);
				}
				if (im_mod() || im_cat_mod()) {
					$tpl->newBlock('post-disableclose');
					$closemark = '';
					if ($article->disable_close) {
						$closemark = ' checked="checked"';
					}
					$tpl->assign('edit-page-disabled', $closemark);

					//attach page in forum view
					if ($category->isforum) {
						$tpl->newBlock('post-attach');
						$atachmark = '';
						if ($article->attach) {
							$atachmark = ' checked="checked"';
						}
						$tpl->assign('edit-page-attached', $atachmark);
					}
				}
			}

			$parents = $db->get_results("SELECT * FROM comments WHERE pid = '" . $article->id . "' AND parent = 0 AND removed = 0 ORDER BY id ASC LIMIT $skip,$end");

			if ($parents) {

				$tpl->newBlock('comments-block');
				$comment_number = $skip + 1;
				$childs_q = $db->get_results("SELECT `id`,`date`,`parent`,`author`,`text`,`vote_value`,`vote_users` FROM `comments` WHERE pid = '" . $article->id . "' AND parent != 0 AND removed = 0 ORDER BY id ASC");

				$childs = array();
				if (!empty($childs_q)) {
					foreach ($childs_q as $comment) {
						$childs[$comment->parent][$comment->id] = $comment;
					}
				}

				$author = array();
				foreach ($parents as $comment) {

					//REGISTERED USERS
					if ($comment->author != 0) {
						if (empty($author[$comment->author])) {
							$author[$comment->author] = get_user($comment->author);
						}

						$tpl->newBlock('comments-node');
						$tpl->newBlock('comments-node-user');

						$editedby = '';
						if ($comment->edit_times > 0 && $auth->ok) {
							if (empty($author[$comment->edit_user])) {
								$author[$comment->edit_user] = get_user($comment->edit_user);
							}
							$editedby = '<p class="comment-edited-by">Laboja ' . $author[$comment->edit_user]->nick . ', ' . $comment->edit_times . 'x</p>';
						}

						$comment->date = display_time(strtotime($comment->date));

						$tpl->assign(array(
							'comment-id' => $comment->id,
							'comment-number' => $comment_number,
							'comment-text' => add_smile($comment->text),
							'comment-date' => $comment->date,
							'comment-author' => usercolor($author[$comment->author]->nick, $author[$comment->author]->level, false, $comment->author),
							'comment-author-id' => $comment->author,
							'aurl' => '/user/' . $comment->author,
							'avatar' => get_avatar($author[$comment->author]),
							'karma' => $author[$comment->author]->karma,
							'title' => h($author[$comment->author]->nick),
							'custom_title' => custom_user_title($author[$comment->author]),
							'comment-editedby' => $editedby,
						));

						if ($auth->ok && $auth->showsig && $author[$comment->author]->signature && !$auth->mobile) {
							$signature = '<div class="comment-signature">' . $author[$comment->author]->signature . '</div>';
							if (im_mod() && $author[$comment->author]->level != 1) {
								$signature .= '[<a onclick="prompt_why_delete(\'?remove_signature=' . $comment->author . '\');" href="#"><span class="red">dzēst parakstu</span></a>]';
							}
							$tpl->assign('signature', add_smile($signature));
						}

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
									$pluslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
									$minuslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
								} else {
									$pluslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
									$minuslnk = '<a href="/rate-comment/?vc=' . $comment->id . '&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
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

						if (!$auth->mobile) {
							$tpl->newBlock('comments-vote');
							$tpl->assign(array(
								'comment-id' => $comment->id,
								'comment-vote_value' => $comment->vote_value,
								'comment-plus' => $pluslnk,
								'comment-minus' => $minuslnk,
								'comment-vclass' => $vclass
							));
						}

						if (im_mod()) {
							$tpl->newBlock('comments-adm');
							$tpl->assign(array(
								'delete' => '?delcom=' . $comment->id . '&token=' . make_token('delcom'),
								'edit' => '?editcom=' . $comment->id,
							));
						} elseif ($auth->ok && $auth->karma >= $min_post_edit && $auth->id == $comment->author) {
							$tpl->newBlock('comments-own');
							$tpl->assign(array(
								'edit' => '?editcom=' . $comment->id,
							));
						}

						if ($auth->ok && !$article->closed && empty($auth->mobile)) {
							$tpl->newBlock('comments-reply');
							$tpl->assign(array(
								'comment-id' => $comment->id,
								'page-id' => $article->id
							));
						}

						//	pārkāpuma ziņošanas podziņa komentāra labajā pusē
						if ($auth->ok && !$auth->mobile && in_array($lang, array(1, 7, 9))) {
							$tpl->newBlock('report-comment');
							$tpl->assign('comment-id', $comment->id);
						}

						if ($comment->replies > 0) {

							if (!empty($childs[$comment->id])) {
								$tpl->newBlock('com-replies');
								foreach ($childs[$comment->id] as $reply) {
									$tpl->newBlock('com-reply');
									if (empty($author[$reply->author])) {
										$author[$reply->author] = get_user($reply->author);
									}

									$reply->date = strtolower(display_time(strtotime($reply->date)));

									$avatar = get_avatar($author[$reply->author], 's');

									$tpl->assign(array(
										'rpl-id' => $reply->id,
										'rpl-text' => add_smile($reply->text),
										'rpl-date' => $reply->date,
										'rpl-author' => usercolor($author[$reply->author]->nick, $author[$reply->author]->level, false, $reply->author),
										'rpl-author-id' => $reply->author,
										'rpl-aurl' => '/user/' . $reply->author,
										'rpl-avatar' => $avatar
									));


									$pluslnk = '<span class="voted1"></span>';
									$minuslnk = '<span class="voted2"></span>';

									if ($auth->ok && !$auth->mobile) {
										$check = substr(md5($reply->id . $remote_salt . $auth->id), 0, 5);

										if (!empty($reply->vote_users)) {
											$voters = unserialize($reply->vote_users);
										} else {
											$voters = array();
										}
										$voted = in_array($auth->id, $voters);

										if (!$voted && $auth->id != $reply->author) {
											if (isset($_GET['com_page'])) {
												$skips = (int) $_GET['com_page'];
												$pluslnk = '<a href="/rate-comment/?vc=' . $reply->id . '&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
												$minuslnk = '<a href="/rate-comment/?vc=' . $reply->id . '&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
											} else {
												$pluslnk = '<a href="/rate-comment/?vc=' . $reply->id . '&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
												$minuslnk = '<a href="/rate-comment/?vc=' . $reply->id . '&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
											}
										}
									}

									if ($reply->vote_value > 0) {
										$reply->vote_value = '+' . $reply->vote_value;
										$vclass = 'positive';
									} elseif ($reply->vote_value < 0) {
										$vclass = 'negative';
									} else {
										$vclass = 'zero';
									}

									if (!$auth->mobile) {
										$tpl->newBlock('reply-vote');
										$tpl->assign(array(
											'comment-id' => $reply->id,
											'comment-vote_value' => $reply->vote_value,
											'comment-plus' => $pluslnk,
											'comment-minus' => $minuslnk,
											'comment-vclass' => $vclass
										));
									}

									if (!$auth->mobile && (im_mod() || ($auth->ok && $auth->karma >= $min_post_edit && $auth->id == $reply->author))) {
										$tpl->newBlock('reply-adm');
										$tpl->assign(array(
											'edit' => '?editcom=' . $reply->id,
											'delete' => '?delcom=' . $reply->id . '&token=' . make_token('delcom')
										));
									}

									// komentāra atbildes ziņošanas podziņa
									if (!$auth->mobile && $auth->ok && $lang == 1) {
										$tpl->newBlock('report-reply');
										$tpl->assign('comment-id', $reply->id);
									}
								}
							}
						}

						if (!$auth->mobile && $lang == 1) {
							$tpl->newBlock('comment-tools');
							$tpl->assign('id', $comment->author);
							if ($auth->ok) {
								$tpl->newBlock('comments-pm');
								$tpl->assign('id', $comment->author);
							}
						}

						//ANONIMOUS COMMENTS
					} else {
						$tpl->newBlock('comments-node');
						$tpl->newBlock('comments-node-anon');

						if (empty($comment->anon_nick)) {
							$comment->anon_nick = 'Viesis';
						}

						$comment->date = display_time(strtotime($comment->date));

						$tpl->assign(array(
							'comment-id' => $comment->id,
							'comment-number' => $comment_number,
							'comment-text' => add_smile($comment->text),
							'comment-date' => $comment->date,
							'comment-anon_nick' => $comment->anon_nick,
							'comment-avatar' => '/dati/bildes/useravatar/none.png'
						));
						if (im_mod()) {
							$tpl->newBlock('comments-anon-adm');
							$tpl->assign(array(
								'comment-id' => $comment->id,
								'page-id' => $article->id,
								'comment-ip' => $comment->ip,
							));
						}
					}

					$comment_number++;
				}

				unset($childs);
				unset($parents);

				//pager
				$total = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = '" . $article->id . "' AND `parent` = '0' AND `removed` = 0");
				if ($total > $end) {
					$total = $total / $end;
					$skip = $skip / $end;

					$pager = pager($total, $skip, 1, '/read/' . $article->strid . '/com_page/');
					$tpl->assignGlobal(array(
						'pager-next' => $pager['next'],
						'pager-prev' => $pager['prev'],
						'pager-numeric' => $pager['pages']
					));
				}
			}

			unset($author);

			//sorry, comments closed
			if ($article->closed) {
				$tpl->newBlock('article-closed');

				//comment form
			} elseif ($auth->ok) {
				$tpl->newBlock('add-comment');
				$tpl->assign(array(
					'comment-pid' => $article->id,
					'comment-pid-check' => substr(md5($article->id . $remote_salt . $auth->id), 0, 8),
				));

				if ($auth->id == 1) {
					$tpl->newBlock('resp-tools');
				}
				$tpl->newBlock('tinymce-simple');

				//login to comment
			} else {
				$tpl->newBlock('login-to-comment');
			}
		}

		//page path
		$pagepath = '<a href="/' . $category->textid . '">' . $category->title . '</a> / ' . $article->title;
		if ($category->parent) {
			$category2 = get_cat($category->parent);
			$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
		}

		$tpl->assignGlobal(array(
			'page-sel-' . $article->id => ' class="selected"',
			'cur-url' => '/read/' . $article->strid
		));

		if (!empty($article->custom_ad) && $article->custom_ad == 'dateks') {
			$tpl->newBlock('page-ad-dateks');
		}

	} else {
		set_flash('Tev nav atļauts apskatīt šo sadaļu!', 'error');
		redirect();
	}
} else {
	set_flash('Raksts netika atrasts! Kļūdains links?', 'error');
	redirect();
}

