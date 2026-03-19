<?php

/**
 * Avatara attēla maiņa
 */
//write changes
if (isset($_POST['submit']) && check_token('avatar', $_POST['xsrf_token'])) {

    /* load libraries */
    require_once(LIB_PATH . '/verot/src/class.upload.php');

    //new avatar image
    if (isset($_FILES['edit-avatar'])) {

        $rand = md5(microtime() . $auth->id);
        $avatar_path = substr($rand, 0, 1) . '/' . substr($rand, 1, 1) . '/';

        $text = time() . '_' . $auth->id;
        $foo = new Upload($_FILES['edit-avatar']);
        $foo->file_new_name_body = $text;
        $foo->image_max_pixels = 200000000;
        $foo->image_resize = true;
        $foo->image_convert = 'jpg';
        $foo->image_x = 90;
        $foo->image_y = 90;
        $foo->allowed = ['image/*'];
        $foo->image_ratio_crop = true;
        $foo->jpeg_quality = 99;
        $foo->file_auto_rename = false;
        $foo->file_overwrite = true;
        $foo->process(CORE_PATH . '/dati/bildes/useravatar/' . $avatar_path);
        if ($foo->processed) {

            $foo->file_new_name_body = $text;
            $foo->image_resize = true;
            $foo->image_convert = 'jpg';
            $foo->image_x = 45;
            $foo->image_y = 45;
            $foo->allowed = ['image/*'];
            $foo->image_ratio_crop = true;
            $foo->jpeg_quality = 99;
            $foo->file_auto_rename = false;
            $foo->file_overwrite = true;
            $foo->process(CORE_PATH . '/dati/bildes/u_small/' . $avatar_path);

            $foo->file_new_name_body = $text;
            $foo->image_resize = true;
            $foo->image_convert = 'jpg';
            $foo->image_x = 280;
            $foo->image_y = 280;
            $foo->allowed = ['image/*'];
            $foo->image_ratio_crop = false;
            $foo->image_ratio_no_zoom_in = true;
            $foo->jpeg_quality = 99;
            $foo->file_auto_rename = false;
            $foo->file_overwrite = true;
            $foo->process(CORE_PATH . '/dati/bildes/u_large/' . $avatar_path);

            if (file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $avatar_path . $text . '.jpg')) {
                $inprofile->avatar = $avatar_path . $text . '.jpg';
                $inprofile->av_alt = 1;
            }
            $foo->clean();
        } else {
            set_flash('Kļūda: ' . $foo->error, 'error');
            redirect('/user/avatar');
        }
    }

    $db->update('users', $auth->id, [
        'avatar' => $inprofile->avatar,
        'av_alt' => $inprofile->av_alt
    ]);

    $auth->reset();
    update_karma($auth->id, true);

    set_flash('Izmaiņas saglabātas!', 'success');
    redirect('/user/avatar');
}

$tpl->newBlock('user-profile-avatar');
$tpl->assign('xsrf', make_token('avatar'));

$page_title = 'Avatara attēla maiņa';
