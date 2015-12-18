<?php

/**
 * Rakstu iesniegšana un iesniegto rakstu apstiprināšana
 */
$robotstag[] = 'noindex';
 
// raksta pievienošana datubāzē
if (isset($_POST['new-topic-body'])) {

	$body = trim($_POST['new-topic-body']);
	$title = trim($_POST['new-topic-title']);
    
	if (empty($title)) {
		$title = 'Nosaukums nav norādīts';
	}
    
	$newcat = (int) $_POST['new-topic-category'];
    
	if ($body && $title) {
    
        // runescape apakšprojektā var pievienot platus rakstus bez kreisās kolonnas
        $is_wide = (isset($_GET['wide']) && $lang == 9) ? 1 : 0;

		$title = title2db($title);
		$body = htmlpost2db($body);

		$textid = date('YmdHis');
		$strid = mkslug_newpage($title);

		if ($auth->ok && ($auth->level == 3 or $auth->level == 2 or $auth->level == 1)) {
        
			$insert = $db->query("INSERT INTO pages (strid,textid,category,text,title,author,date,updated,bump,ip,lang,is_wide)
									VALUES ('$strid','$textid','$newcat','$body','$title','$auth->id',NOW(),NOW(),NOW(),'$auth->ip','$lang',$is_wide)");

			$topicid = $db->insert_id;

			update_stats($newcat);

			if (isset($_FILES['edit-avatar']) && !empty($_FILES['edit-avatar'])) {
				require_once('includes/class.upload.php');
				$foo = new Upload($_FILES['edit-avatar']);
				$foo->image_max_pixels = 200000000;
				$foo->file_new_name_body = $topicid;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->allowed = array('image/*');
				$foo->image_ratio = true;
				$foo->image_ratio_pixels = 17800;
				$foo->jpeg_quality = 96;
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
					$foo->jpeg_quality = 96;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process('dati/bildes/av_sm/');
					$foo->clean();
					$avatar = 'dati/bildes/avatari/' . $topicid . '.jpg';
					$sm_avatar = 'dati/bildes/av_sm/' . $topicid . '.jpg';
					$db->query("UPDATE pages SET avatar = ('$avatar'), sm_avatar = ('$sm_avatar') WHERE id = '$topicid'");
				}
			}

			update_karma($auth->id);
			redirect('/read/' . $strid);
            
		} else {

			$insert = $db->query("INSERT INTO approve (category,text,title,author,date,ip,lang,is_wide)
									VALUES ('$newcat','$body','$title','$auth->id',NOW(),'$auth->ip','$lang',$is_wide)");
			$topicid = $db->insert_id;

			if (isset($_FILES['edit-avatar']) && !empty($_FILES['edit-avatar'])) {
				require_once('includes/class.upload.php');
				$foo = new Upload($_FILES['edit-avatar']);
				$foo->image_max_pixels = 200000000;
				$foo->file_new_name_body = $topicid;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->allowed = array('image/*');
				$foo->image_ratio = true;
				$foo->image_ratio_pixels = 17800;
				$foo->jpeg_quality = 96;
				$foo->image_ratio_no_zoom_in = true;
				$foo->file_auto_rename = false;
				$foo->file_overwrite = true;
				$foo->process('modules/approve/av_l/');
				if ($foo->processed) {
					$foo->file_new_name_body = $topicid;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 75;
					$foo->image_y = 75;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 96;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process('modules/approve/av_sm/');
					$foo->clean();
				}
			}

			redirect('/write/list');
		}
	}
}

if ($auth->ok) {

    // raksta dzēšana
	if (im_mod() && isset($_GET['var1']) && $_GET['var1'] == 'delete') {
		$delete = (int) $_GET['var2'];
		$db->query("UPDATE `approve` SET `removed` = 1 WHERE `id` = '$delete'");
		@unlink('modules/approve/av_l/' . $delete . '.jpg');
		@unlink('modules/approve/av_sm/' . $delete . '.jpg');
		redirect('/write/list');
	}

    // raksta apstiprināšanas forma
	$tpl->newBlock('approve-body');
	if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && ($auth->level == 1 or $auth->level == 2)) {

		$tpl->assign('edit-active', 'active');

		$edit = (int) $_GET['var2'];

		if (isset($_POST['ap-topic-title']) && isset($_POST['ap-topic-body'])) {
			$body = trim($_POST['ap-topic-body']);
			$title = trim($_POST['ap-topic-title']);
			if (!empty($body) && !empty($title)) {

				$title = title2db($title);
				$body = htmlpost2db($body);
				$author = (int) $_POST['ap-topic-author'];
				$added = sanitize($_POST['ap-topic-date']);
				$ip = sanitize($_POST['ap-topic-ip']);
				$category = (int) $_POST['ap-topic-category'];
				$textid = date('YmdHis');
				$strid = mkslug_newpage($title);
                $make_wide = (isset($_POST['ap-topic-wide']) && (int)$_POST['ap-topic-wide'] == 1) ? 1 : 0;
				$db->query("INSERT INTO pages (strid,textid,category,text,title,author,date,bump,updated,ip,lang,is_wide) VALUES ('$strid','$textid','$category','$body','$title','$author','$added',NOW(),NOW(),'$ip','$lang',$make_wide)");
				$topicid = $db->insert_id;
				update_stats($category);

				if (file_exists('modules/approve/av_l/' . $edit . '.jpg')) {
					rename('modules/approve/av_l/' . $edit . '.jpg', 'dati/bildes/avatari/' . $topicid . '.jpg');
					rename('modules/approve/av_sm/' . $edit . '.jpg', 'dati/bildes/av_sm/' . $topicid . '.jpg');
					$avatar = 'dati/bildes/avatari/' . $topicid . '.jpg';
					$sm_avatar = 'dati/bildes/av_sm/' . $topicid . '.jpg';
					$db->query("UPDATE pages SET avatar = ('$avatar'), sm_avatar = ('$sm_avatar') WHERE id = '$topicid'");
				}

				$db->query("UPDATE `approve` SET `removed` = 1 WHERE `id` = '$edit'");

				update_karma($author, true);
				redirect('/read/' . $strid);
			}
		}

		$article = $db->get_row("SELECT * FROM approve WHERE id = '$edit' AND `removed` = 0");
		$author = get_user($article->author);
        
        // runescape apakšprojektā eksistē raksti ar platām tabulām,
        // tāpēc tādiem vienu kolonnu aizvācam
        if ($article->is_wide && $lang == 9) {
            $tpl_options = 'no-left';
        }

		$tpl->newBlock('approve-edit');

		$tpl->assign(array(
			'article-showtitle' => $article->title,
			'article-title' => $article->title,
			'article-text' => h($article->text),
			'article-id' => $article->id,
			'article-ip' => $article->ip,
			'article-author' => $article->author,
			'article-author-nick' => usercolor($author->nick, $author->level, false, $article->author),
			'aurl' => '/user/' . $article->author,
			'article-date' => $article->date,
            'article-wide' => $article->is_wide
		));

		if (file_exists('modules/approve/av_l/' . $article->id . '.jpg')) {
			$tpl->assign(array(
				'article-avatar' => '<strong>Avatars:</strong><br/ ><img src="/modules/approve/av_l/' . $article->id . '.jpg" alt="" />',
			));
		}

        // runescape apakšprojekta kategoriju sadalījums
        if ($lang == 9) {
        
            $rscats = get_rs_page_categories();
            $tpl->newBlock('rs-cat-app-selection');
            
            foreach ($rscats as $ctitle => $catgroup) {
            
                $tpl->newBlock('rs-app-catgroup');
                $tpl->assign('title', $ctitle);
                
                foreach ($catgroup as $key => $val) {
                
                    $category_sel = ($key == $article->category) ? ' selected="selected"' : '';
                
                    $tpl->newBlock('rs-app-category');  
                    $tpl->assign(array(
                        'category-title' => $val,
                        'category-id'    => $key,
                        'category-sel'   => $category_sel
                    ));
                }
            }
        }
        // citu apakšprojektu kategoriju sadalījums
        else {
            $categorys = $db->get_results("SELECT id,title FROM `cat` WHERE (module = 'list' OR module = 'movies' OR module = 'wall' OR module = 'rshelp') AND isblog = '0' AND mods_only = '0' AND (`lang` = '$lang' OR `lang` = '0')");
            if ($categorys) {
                
                $tpl->newBlock('cat-app-selection');
            
                foreach ($categorys as $category_l) {
                    $tpl->newBlock('select-app-category');
                    $sel = '';
                    if ($category_l->id == $article->category) {
                        $sel = ' selected="selected"';
                    }
                    $tpl->assign(array(
                        'category-title' => $category_l->title,
                        'category-id' => $category_l->id,
                        'category-sel' => $sel
                    ));
                }
            }
        }

		$tpl->newBlock('tinymce-enabled');
        
    // iesniegto rakstu saraksts
	} elseif (isset($_GET['var1']) && $_GET['var1'] == 'list') {

		$tpl->assign('edit-active', 'active');
		$tpl->newBlock('approve-view');

		$articles = $db->get_results("SELECT id,title FROM `approve` WHERE `author` = '" . $auth->id . "' AND `lang` = '$lang' AND `removed` = 0");
		if ($articles) {
			$tpl->newBlock('approve-list');
			foreach ($articles as $article) {
				$tpl->newBlock('approve-list-node');
				$tpl->assign(array(
					'approve-list-title' => $article->title,
					'approve-list-id' => $article->id,
				));
			}
		}

		if (im_mod()) {
			$tpl->newBlock('approveadm-view');
			$articles = $db->get_results("SELECT id,title FROM `approve` WHERE `lang` = '$lang' AND `removed` = 0 ORDER BY `date` ASC");
			if ($articles) {
				$tpl->newBlock('approveadm-list');
				foreach ($articles as $article) {
					$tpl->newBlock('approveadm-list-node');
					$tpl->assign(array(
						'approve-list-title' => $article->title,
						'approve-list-id' => $article->id,
					));
				}
			}
		}
        
    // raksta pievienošanas forma
	} else {       
    
		$tpl->assign('new-active', 'active');
		$tpl->newBlock('approve-new');
        
        // runescape apakšprojektā eksistē platie raksti,
        // kuriem nav kreisās kolonnas
        if (isset($_GET['wide']) && $lang == 9) {
            $tpl_options = 'no-left';
        }
        // izdrukās lapā adresi, caur kuru iespējams atvērt kādu no skatiem
        if (!isset($_GET['wide']) && $lang == 9) {
            $tpl->newBlock('goto-wide-page');
        } else if ($lang == 9) {
            $tpl->newBlock('goto-narrow-page');
        }
        
        // runescape apakšprojekta kategoriju sadalījums
        if ($lang == 9) {
        
            $rscats = get_rs_page_categories();

            $tpl->newBlock('rs-cat-selection');
            
            foreach ($rscats as $ctitle => $catgroup) {
            
                $tpl->newBlock('rs-catgroup');
                $tpl->assign('title', $ctitle);
                
                foreach ($catgroup as $key => $val) {
                
                    $tpl->newBlock('rs-category');  
                    $tpl->assign(array(
                        'category-title' => $val,
                        'category-id'    => $key
                    ));
                }
            }
        }
        else {
            $categorys = $db->get_results("SELECT id,title FROM `cat` WHERE `isforum` = '0' AND (module = 'list' OR module = 'movies' OR module = 'wall' OR module = 'rshelp') AND isblog = '0' AND mods_only = '0' AND (`lang` = '$lang' OR `lang` = '0')");
            if ($categorys) {
                $tpl->newBlock('cat-selection');
                foreach ($categorys as $category_l) {
                    $tpl->newBlock('select-category');
                    $tpl->assign(array(
                        'category-title' => $category_l->title,
                        'category-id' => $category_l->id,
                    ));
                }
            }
        }

		$tpl->newBlock('tinymce-enabled');
	}
} else {
	$tpl->newBlock('error-nologin');
}

