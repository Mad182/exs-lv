<?php

$strid = sanitize($_GET['var1']);
$article = $db->get_row("SELECT * FROM `pages` WHERE `strid` = '" . $strid . "' LIMIT 1");

if ($article && ($auth->ok === true || !$article->private)) {

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
	if (!$article->closed && isset($_POST['rpl-comment']) && !empty($_POST['rpl-txt']) && $auth->ok && $_POST['rpl-page'] == $article->id && check_token('reply', $_POST['xsrf_token'])) {
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

		//@mention
		$edit_comment_text = mention($edit_comment_text, '/read/' . $article->strid, 'page', $article->id);

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
		redirect($article->redirect, true);
	}

	if (!$rating_users = unserialize($article->rating_users)) {
		$rating_users = [];
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
			$db->query("UPDATE pages SET readby = ('" . serialize([$auth->id]) . "') WHERE id = '$article->id'");
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

			if (isset($_POST['attach-do']) && ($category->isforum || $category->id == 1)) {
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
			$tpl->assign([
				'article-id' => $article->id,
				'token' => make_token('delpage' .  $article->id)
			]);
		} elseif($auth->id == $article->author) { 
			$tpl->newBlock('page-delete');
			$tpl->assign([
				'article-id' => $article->id,
				'token' => make_token('delpage' .  $article->id)
			]);
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
			$tpl->assign([
				'comment-text' => h($comment->text),
				'comment-id' => $comment->id
			]);

			$tpl->newBlock('tinymce-enabled');
			$page_title = 'Komentāra labošana rakstam: &quot;' . $article->title . '&quot; | ' . $category->title;
		}

		// raksta dzēšana
		elseif ($auth->ok && isset($_GET['mode']) && $_GET['mode'] == 'delete' && (can_edit_page($article) || $auth->id == $article->author) && check_token('delpage' .  $article->id, $_GET['token'])) {

			$db->query("DELETE FROM `pages` WHERE `id` = " . $article->id);
			$db->query("DELETE FROM `bookmarks` WHERE `pageid` = " . $article->id . " AND `foreign_table` = 'pages'");
			$db->query("DELETE FROM `taged` WHERE `page_id` = " . $article->id . " AND `type` = 0");
			$db->query("UPDATE `comments` SET `removed` = 1 WHERE `pid` = ".$article->id);
			$db->query("DELETE FROM `userlogs` WHERE `action` LIKE '%/read/".$article->strid."#%'");
			$db->query("DELETE FROM `userlogs` WHERE `action` LIKE '%/read/".$article->strid."\"%'");
			$db->query("DELETE FROM `notify` WHERE `url` LIKE '/read/".$article->strid."'");

			if($auth->id != 1) {
				$auth->log('Izdzēsa rakstu ('.$article->title.')', 'pages', $article->id);
			}

			redirect('/' . $category->textid);

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
						require_once(LIB_PATH . '/verot/src/class.upload.php');
						$foo = new Upload($_FILES['edit-avatar']);
						$foo->image_max_pixels = 200000000;
						$foo->file_new_name_body = $topicid;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->allowed = ['image/*'];
						$foo->image_ratio = true;
						$foo->image_ratio_pixels = 17800;
						$foo->jpeg_quality = 98;
						$foo->image_ratio_no_zoom_in = true;
						$foo->file_auto_rename = false;
						$foo->file_overwrite = true;
						$foo->process('dati/bildes/avatari/');
						if ($foo->processed) {
						
						
						
							//new article images
							$dir1 = substr($article->id, -1);
							if (!$dir1) {
								$dir1 = 0;
							}
							$dir2 = substr($article->id, -2, 1);
							if (!$dir2) {
								$dir2 = 0;
							}
							$path = $dir1 . '/' . $dir2;
							rmkdir(IMG_PATH . '/topics/large/' . $path . '/');
							rmkdir(IMG_PATH . '/topics/thb/' . $path . '/');

							$file_title = mkslug($article->title . '-image');
							$foo->allowed = ['image/*'];
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
							$foo->process(IMG_PATH . '/topics/thb/' . $path . '/');
							$foo->file_new_name_body = $file_title;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 800;
							$foo->image_y = 800;
							$foo->allowed = ['image/*'];
							$foo->image_ratio_crop = false;
							$foo->image_ratio_no_zoom_in = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process(IMG_PATH . '/topics/large/' . $path . '/');
							$foo->file_new_name_body = $file_title;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 600;
							$foo->image_y = 240;
							$foo->allowed = ['image/*'];
							$foo->image_ratio_crop = true;
							$foo->image_ratio_no_zoom_in = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process(IMG_PATH . '/topics/frontpage/' . $path . '/');
							$article->image = $path . '/' . $foo->file_dst_name;
							
							
							

							$foo->file_new_name_body = $topicid;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 75;
							$foo->image_y = 75;
							$foo->allowed = ['image/*'];
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

					$db->query("INSERT INTO pages_ver (pid,time,title,text,nextmod,category,is_wide,ip) VALUES (
                        '$article->id',
                        '" . time() . "',
                        '" . sanitize($article->title) . "',
                        '" . sanitize($article->text) . "',
                        '" . $lastmodu . "',
                        '" . $article->category . "',
                        '" . (int) $article->is_wide . "',
						'" . $auth->ip . "'
                    )");

					$db->query("UPDATE `pages` SET
						`text` = ('$body'),
						`intro` = (''),
						`title` = ('$title'),
						`avatar` = ('$article->avatar'),
						`sm_avatar` = ('$article->sm_avatar'),
						`image` = ('$article->image'),
						`category` = ('$topiccat'),
						`edit_time` = ('" . time() . "'),
						`edit_user` = ('$auth->id'),
						`edit_times` = edit_times+1,
						`updated` = NOW(),
                        `is_wide` = $topicwide
					WHERE `id` = '$topicid'");

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
			$tpl->assign([
				'article-showtitle' => $article->title,
				'article-title' => $article->title,
				'article-text' => h($article->text),
				'article-id' => $article->id
			]);

			// izdrukās lapā adresi, caur kuru iespējams atvērt kādu no skatiem
			if ($lang == 9 && (!$article->is_wide && !isset($_GET['wide']) || isset($_GET['narrow']))) {
				$tpl->newBlock('goto-wide-page');
				$tpl->assign('wide-page-url', str_replace(['wide=1', 'narrow=1', '\&', '&amp;'], '', h($_SERVER['REQUEST_URI'])));
			} else if ($lang == 9) {
				$tpl->newBlock('goto-narrow-page');
				$tpl->assign('wide-page-url', str_replace(['wide=1', 'narrow=1', '\&', '&amp;'], '', h($_SERVER['REQUEST_URI'])));
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
						$tpl->assign([
							'title' => $val,
							'id' => $key,
							'sel' => $sel,
						]);
					}
				}
			}

			if ($article->avatar && $category->textid != 'filmas') {
				$tpl->newBlock('edit-article-av');
				$tpl->assign([
					'img' => $article->avatar
				]);
			}

			if ($category->textid == 'filmas' && im_mod()) {

				$images = [];

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

						require_once(LIB_PATH . '/verot/src/class.upload.php');
						$foo = new Upload($tmpname);
						$foo->image_max_pixels = 200000000;
						$foo->allowed = ['image/*'];
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
							$foo->allowed = ['image/*'];
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
							$foo->allowed = ['image/*'];
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
								$foo->allowed = ['image/*'];
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

								$db->query("UPDATE `pages` SET
									`avatar` = ('$article->avatar'),
									`sm_avatar` = ('$article->sm_avatar'),
									`updated` = NOW()
								WHERE `id` = '$article->id'");
							}

							$foo->clean();
							set_flash('Attēls pievienots', 'success');
						}
					}
				} elseif (isset($_POST['search-avatar'])) {

					$q = urlencode($article->title . ' movie poster');
					$jsonurl = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&tbs=iar:t&q=' . $q;
					$result = json_decode(curl_get($jsonurl), true);
					$images = [
						$result['responseData']['results'][0]['url'],
						$result['responseData']['results'][1]['url'],
						$result['responseData']['results'][2]['url'],
						$result['responseData']['results'][3]['url']
					];
				}

				$movie_data = $db->get_row("SELECT * FROM `movie_data` WHERE `page_id` = '$article->id'");
				$tpl->newBlock('edit-movie-data');
				if (!empty($movie_data)) {
					$tpl->assignAll($movie_data);
					$tpl->assign('sel-' . $movie_data->type, ' selected="selected"');
				}


				$tpl->newBlock('edit-movie');
				$tpl->assign([
					'article-id' => $article->id,
				]);
				if (!empty($images)) {
					foreach ($images as $img) {
						$tpl->newBlock('edit-movie-image');
						$tpl->assign([
							'url' => $img,
						]);
					}
				}

				$avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1");
				if ($avatar) {
					$tpl->newBlock('edit-movie-avatar');
					$tpl->assign([
						'url' => $avatar->thb,
					]);
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
					$tpl->assign([
						'title' => $record->title,
						'time' => date('Y-m-d H:i', $record->time),
						'id' => $record->id,
						'user' => $db->get_var("SELECT nick FROM users WHERE id = '$record->nextmod' LIMIT 1"),
						'symbols' => strlen($record->text)
					]);
				}
			}

			//add bookmark
		} elseif ($auth->ok && isset($_GET['mode']) && $_GET['mode'] == 'bookmark') {

			if (!$bm = get_bookmarked_id($article->id, $auth->id)) {
				add_bookmark($article->id, $auth->id);
				if (!empty($article->avatar)) {
					push('Pievienoja savai izlasei rakstu &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;', '/dati/bildes/topic-av/' . $article->id . '.jpg');
				} else {
					push('Pievienoja savai izlasei rakstu &quot;<a href="/read/' . $article->strid . '">' . $article->title . '</a>&quot;');
				}
			} else {
				remove_bookmark($bm);
			}

			redirect('/read/' . $article->strid);

			//show page contents
		} else {

			$date = display_time(strtotime($article->date));
			$updated = display_time(strtotime($article->updated));
			$post_bump = strtotime($article->bump);

			if ($article->edit_times > 0 && empty($article->custom_include)) {
				$edit_usrinfo = get_user($article->edit_user);
				$edit_usr = $edit_usrinfo->nick;
				$article->text .= '<p class="comment-edited-by">Laboja ' . $edit_usr . ', labots ' . $article->edit_times . 'x</p>';
			}

			$rat = 0;
			if ($article->rating_count > 0) {
				$rat = round($article->rating / $article->rating_count, 2);
			}

			$article_text = add_smile($article->text, 1, $article->disable_emotions);

			if (!$author->deleted) {
				$author_link = '<span class="author vcard"><a class="url fn n" href="/user/' . $article->author . '" rel="author">' . usercolor($author->nick, $author->level, false, $article->author) . '</a></span>';
			} else {
				$author_link = '<em>dzēsts</em>';
			}

			$custom_content = '';
			if(!empty($article->custom_include) && file_exists(CORE_PATH . '/modules/read/custom_includes/' . mkslug($article->custom_include) . '.php')) {
				ob_start();
				include(CORE_PATH . '/modules/read/custom_includes/' . mkslug($article->custom_include) . '.php');
				$custom_content = ob_get_clean();
			}

			$tpl->newBlock('read-article');

			$permalink = get_protocol($lang) . get_domain($lang) . '/read/' . $article->strid;

			$tpl->assign([
				'title' => $article->title,
				'text' => $article_text,
				'custom_content' => $custom_content,
				'id' => $article->id,
				'bookmark' => $permalink,
				'views' => $article->views + 1,
				'date' => $date,
				'updated' => $updated,
				'date_atom' => date(DATE_ATOM, strtotime($article->date)),
				'updated_atom' => date(DATE_ATOM, strtotime($article->updated)),
				'author' => $author_link,
				'level' => $author->level,
				'posts' => $article->posts,
				'rating' => $rat,
				'rating_count' => $article->rating_count,
				'avatar' => get_avatar($author, 's'),
			]);

			$page_title = $article->title . ' - ' . $category->title;

			// filmu rakstiem specifiska informācija
			if ($category->textid == 'filmas') {
				$avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1");
				if (!empty($avatar)) {

					$tpl->newBlock('movie-avatar');
					$tpl->assignAll($avatar);

					if (!$auth->mobile) {
						$opengraph_meta['title'] = 'Filma ' . h($article->title);
						$opengraph_meta['type'] = 'article';
						$canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/read/' . $article->strid;
						$opengraph_meta['image'] = 'https://img.exs.lv' . $avatar->image;
						$opengraph_meta['description'] = h(textlimit($article->text, 200));
						$twitter_meta['card'] = 'summary';
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

						$types = [
							'movie' => 'Filma',
							'documentary' => 'Dokumentāls raidījums',
							'animation' => 'Animācijas filma',
							'series' => 'Seriāls'
						];

						$tpl->newBlock('movie-info-type');
						$tpl->assign('type', $types[$movie_data->type]);
						$tpl->assign('title', $article->title);
					}


					if ($genres = $db->get_col("SELECT `genre` FROM `movie_genres` WHERE `page_id` = '$article->id'")) {
						$gen = [];
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
							$tpl->assign([
								'rest' => ' un ' . $rest . ' citi...'
							]);
						}

						foreach ($likes as $user_like) {
							$tpl->newBlock('movie-likes-user');
							$tpl->assign([
								'avatar' => get_avatar($user_like, 's'),
								'nick' => $user_like->nick
							]);
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
			} else {
			//NOT FILMAS

				//opengraph tagi
				if (!$auth->mobile) {
					$opengraph_meta['title'] = $article->title;
					$opengraph_meta['type'] = 'article';
					$canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/read/' . $article->strid;
					$opengraph_meta['description'] = h(textlimit($article->text, 200));

					if (!empty($article->image)) {
						$opengraph_meta['image'] = 'https://img.exs.lv/topics/large/' . $article->image;
						$twitter_meta['card'] = 'summary_large_image';
					} else {
						$twitter_meta['card'] = 'summary';
					}
				}
			}

			if ($auth->ok) {
				$tpl->newBlock('add-bookmark');

				$added = get_bookmarked_id($article->id, $auth->id);
				$icon = 'heart-empty.png';
				if($added) {
					$icon = 'heart.png';
				}

				$tpl->assign([
					'icon' => $icon
				]);
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
							$tpl->assign([
								'title' => $tag_page->title,
								'url' => '/read/' . $tag_page->strid
							]);
						}
					}
				}
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
					if ($category->isforum || $category->id == 1) {
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

				$childs = [];
				if (!empty($childs_q)) {
					foreach ($childs_q as $comment) {
						$childs[$comment->parent][$comment->id] = $comment;
					}
				}

				$author = [];
				foreach ($parents as $comment) {


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
					
					if (!$author[$comment->author]->deleted) {
						$author_box = '<a class="username" id="c' . $comment->id . '" href="/user/' . $comment->author . '">';
						$author_box .= usercolor($author[$comment->author]->nick, $author[$comment->author]->level, false, $comment->author) . '</a>';
						$author_box .= '<a href="/user/' . $comment->author . '"><img class="comments-avatar" src="' . get_avatar($author[$comment->author]) . '" alt="" /></a>';
						$author_box .= '<span class="custom-title">' . custom_user_title($author[$comment->author]) . '</span>';
						$author_box .= '<span class="author-info">Karma: ' . $author[$comment->author]->karma . '</span>';
					} else {
						$author_box = '<em class="username" id="c' . $comment->id . '">dzēsts lietotājs</em>';
						$author_box .= '<img class="comments-avatar" src="' . get_avatar($author[$comment->author]) . '" alt="{title}" />';
					}
					

					$tpl->assign([
						'comment-id' => $comment->id,
						'comment-number' => $comment_number,
						'comment-text' => add_smile($comment->text),
						'comment-date' => $comment->date,
						'author' => $author_box,
						'comment-editedby' => $editedby
					]);

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
							$voters = [];
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
						$tpl->assign([
							'comment-id' => $comment->id,
							'comment-vote_value' => $comment->vote_value,
							'comment-plus' => $pluslnk,
							'comment-minus' => $minuslnk,
							'comment-vclass' => $vclass
						]);
					}

					if (im_mod()) {
						$tpl->newBlock('comments-adm');
						$tpl->assign([
							'delete' => '?delcom=' . $comment->id . '&token=' . make_token('delcom'),
							'edit' => '?editcom=' . $comment->id,
						]);
					} elseif ($auth->ok && $auth->karma >= $min_post_edit && $auth->id == $comment->author) {
						$tpl->newBlock('comments-own');
						$tpl->assign([
							'edit' => '?editcom=' . $comment->id,
						]);
					}

					if ($auth->ok && !$article->closed && empty($auth->mobile)) {
						$tpl->newBlock('comments-reply');
						$tpl->assign([
							'comment-id' => $comment->id,
							'page-id' => $article->id
						]);
					}

					//	pārkāpuma ziņošanas podziņa komentāra labajā pusē
					if ($auth->ok && !$auth->mobile && in_array($lang, [1, 7, 9])) {
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

								if (!$author[$reply->author]->deleted) {
									$author_link = '<a href="/user/' . $reply->author . '">' . usercolor($author[$reply->author]->nick, $author[$reply->author]->level, false, $reply->author) . '</a>';
								} else {
									$author_link = '<em>dzēsts</em>';
								}

								$tpl->assign([
									'rpl-id' => $reply->id,
									'rpl-text' => add_smile($reply->text),
									'rpl-date' => $reply->date,
									'rpl-author' => $author_link,
									'rpl-author-id' => $reply->author,
									'rpl-avatar' => $avatar
								]);


								$pluslnk = '<span class="voted1"></span>';
								$minuslnk = '<span class="voted2"></span>';

								if ($auth->ok && !$auth->mobile) {
									$check = substr(md5($reply->id . $remote_salt . $auth->id), 0, 5);

									if (!empty($reply->vote_users)) {
										$voters = unserialize($reply->vote_users);
									} else {
										$voters = [];
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
									$tpl->assign([
										'comment-id' => $reply->id,
										'comment-vote_value' => $reply->vote_value,
										'comment-plus' => $pluslnk,
										'comment-minus' => $minuslnk,
										'comment-vclass' => $vclass
									]);
								}

								if (!$auth->mobile && (im_mod() || ($auth->ok && $auth->karma >= $min_post_edit && $auth->id == $reply->author))) {
									$tpl->newBlock('reply-adm');
									$tpl->assign([
										'edit' => '?editcom=' . $reply->id,
										'delete' => '?delcom=' . $reply->id . '&token=' . make_token('delcom')
									]);
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
					$tpl->assignGlobal([
						'pager-next' => $pager['next'],
						'pager-prev' => $pager['prev'],
						'pager-numeric' => $pager['pages']
					]);
				}
			}

			unset($author);

			//sorry, comments closed
			if ($article->closed) {
				$tpl->newBlock('article-closed');

				//comment form
			} elseif ($auth->ok) {
				$tpl->newBlock('add-comment');
				$tpl->assign([
					'comment-pid' => $article->id,
					'comment-pid-check' => substr(md5($article->id . $remote_salt . $auth->id), 0, 8),
				]);

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

		$tpl->assignGlobal([
			'page-sel-' . $article->id => ' class="selected"',
			'cur-url' => '/read/' . $article->strid
		]);
        
        // poga ritināšanai līdz pašai augšai mobilajā versijā
        if ($auth->mobile) {
            $tpl->newBlock('scroll-up-mobile');
        }

	} else {
		set_flash('Tev nav atļauts apskatīt šo sadaļu!', 'error');
		redirect();
	}
} else {
	error_404();
}

