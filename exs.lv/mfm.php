<?php

require('configdb.php');

session_start();

/* ielādē kopīgos failus */
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

$auth = new Auth();

$isblog = get_blog_by_user($auth->id);

if (!$auth->ok || ($auth->level == 0 && !$isblog && $auth->id != 25709)) {
	echo 'Tev nav tiesību augšupielādēt failus!';
	exit;
}

//echo $auth->level;
//config (if your TinyMCE location is different from example, you should also check paths at line ~360)
$file_root = 'upload/user' . $auth->id;   //where to store files, must be created and writable
$root_path = '';   //path from webroot, without trailing slash. If your page is located in http://www.example.com/john/, this should be '/john'
$thmb_size = 100;  //max size of preview thumbnail
$no_script = true; //true/false - turns scripts into text files
$lang = 'lv';   //language (look in /mfm/lang/ for available)
$current_month = $file_root . '/' . date('Y-m');

rmkdir($file_root);
rmkdir($current_month);

//array of known file types (used for icons)
$file_class = array(
	'swf',
	'txt',
	'htm',
	'html',
	'zip',
	'gz',
	'rar',
	'cab',
	'tar',
	'7z',
	'deb',
	'rpm',
	'php',
	'mp3',
	'ogg',
	'mid',
	'avi',
	'mpg',
	'flv',
	'mpeg',
	'pdf',
	'ttf',
	'exe'
);

//upload class (see file for credits)
require('includes/class.upload.php');

//lang
$lng = array();
require('mfm/lang/lang_' . strtolower($lang) . '.php');
header("Content-type: text/html; charset=utf-8");

//stand alone or tynimce?
$mode = 'mce';
if (isset($_GET['mode'])) {
	$mode = $_GET['mode'];
}

//replaces special characters for latvian and russian lang., and removes all other
function format_filename($filename) {
	$bads = array(' ', 'ā', 'č', 'ē', 'ģ', 'ī', 'ķ', 'ļ', 'ņ', 'ŗ', 'š', 'ū', 'ž', 'Ā', 'Č', 'Ē', 'Ģ', 'Ī', 'Ķ', 'Ļ', 'Ņ', 'Ŗ', 'Š', 'Ū', 'Ž', '$', '&', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'ЫЬ', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'шщ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
	$good = array('-', 'a', 'c', 'e', 'g', 'i', 'k', 'l', 'n', 'r', 's', 'u', 'z', 'A', 'C', 'E', 'G', 'I', 'K', 'L', 'N', 'R', 'S', 'U', 'Z', 's', 'and', 'A', 'B', 'V', 'G', 'D', 'E', 'J', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'C', 'S', 'S', 'T', 'T', 'E', 'Ju', 'Ja', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'c', 's', 't', 't', 'y', 'z', 'e', 'ju', 'ja');
	$filename = str_replace($bads, $good, trim($filename));
	$allowed = "/[^a-z0-9\\.\\-\\_\\\\]/i";
	$filename = preg_replace($allowed, '', $filename);
	return $filename;
}

//convert file size to human readable format
function byte_convert($bytes) {
	$symbol = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
	$exp = 0;
	$converted_value = 0;
	if ($bytes > 0) {
		$exp = floor(log($bytes) / log(1024));
		$converted_value = ( $bytes / pow(1024, floor($exp)) );
	}
	return sprintf('%.2f ' . $symbol[$exp], $converted_value);
}

//show recursive directory tree
function print_tree($dir = '.') {
	global $root_path;
	echo '<ul class="dirlist">';
	$d = opendir($dir);
	while ($f = readdir($d)) {
		if (strpos($f, '.') === 0)
			continue;
		$ff = $dir . '/' . $f;
		if (is_dir($ff)) {
			echo '<li><a href="' . $root_path . '/' . $ff . '/" onclick="load(\'mfm.php?viewdir=' . $ff . '\',\'view-files\'); return false;">' . $f . '</a>';
			print_tree($ff);
			echo '</li>';
		}
	}
	echo '</ul>';
}

//show file list of given directory
function print_files($c = '.') {
	global $root_path, $mode, $thmb_size, $file_class, $lng;
	echo('<table id="file-list">');
	$d = opendir($c);
	$i = 0;
	while ($f = readdir($d)) {
		if (strpos($f, '.') === 0)
			continue;
		$ff = $c . '/' . $f;
		$ext = strtolower(substr(strrchr($f, '.'), 1));
		if (!is_dir($ff)) {
			echo '<tr' . ($i % 2 ? ' class="light"' : ' class="dark"') . '>';
			//show preview and different icon, if file is image
			$imageinfo = @getimagesize($ff);
			if ($imageinfo && $imageinfo[2] > 0 && $imageinfo[2] < 4) {
				$resize = '';
				if ($imageinfo[0] > $thmb_size or $imageinfo[1] > $thmb_size) {
					if ($imageinfo[0] > $imageinfo[1]) {
						$resize = ' style="width: ' . $thmb_size . 'px;"';
					} else {
						$resize = ' style="height: ' . $thmb_size . 'px;"';
					}
				}
				if ($imageinfo[2] == 1) {
					$imagetype = "image_gif";
				} elseif ($imageinfo[2] == 2) {
					$imagetype = "image_jpg";
				} elseif ($imageinfo[2] == 3) {
					$imagetype = "image_jpg";
				} else {
					$imagetype = "image";
				}
				echo '<td><a class="file thumbnail ' . $imagetype . '" href="#" onclick="submit_url(\'' . $root_path . '/' . $ff . '\');">' . $f . '<span><img' . $resize . ' src="' . $root_path . '/' . $ff . '" /></span></a>';
				echo '</td>';
				//known file types
			} elseif (in_array($ext, $file_class)) {
				echo '<td><a class="file file_' . $ext . '" href="#" onclick="submit_url(\'' . $root_path . '/' . $ff . '\');">' . $f . '</a>';
				echo '</td>';
				//all other files
			} else {
				echo '<td><a class="file unknown" href="#" onclick="submit_url(\'' . $root_path . '/' . $ff . '\');">' . $f . '</a>';
				echo '</td>';
			}
			echo '<td>' . byte_convert(filesize($ff)) . '</td>';
			echo '<td class="delete"><a href="#" title="' . $lng['delete_title'] . '" onclick="delete_file(\'' . $c . '\',\'' . $f . '\');">' . $lng['delete'] . '</a></td>';
			echo '</tr>';
			$i++;
		}
	}
	echo('</table>');
}

$uploadstatus = 0;
if (isset($_GET['status'])) {
	$uploadstatus = $_GET['status'];
}

//handles file uploads
if (isset($_FILES['new_file']) && isset($_POST['return'])) {
	if (is_dir($_POST['return'])) {
		$handle = new upload($_FILES['new_file']);
		if ($handle->uploaded) {
			$handle->file_new_name_body = format_filename(substr($_FILES['new_file']['name'], 0, -4));
			//resize image. more options coming soon.
			if (isset($_POST['new_resize']) && $_POST['new_resize'] > 0) {
				$handle->image_resize = true;
				$handle->image_x = (int) $_POST['new_resize'];
				$handle->image_ratio_y = true;
			}
			if (isset($_POST['new_greyscale']) && $_POST['new_greyscale']) {
				$handle->image_greyscale = true;
			}
			if (isset($_POST['new_rotate']) && $_POST['new_rotate'] == 90 or $_POST['new_rotate'] == 180 or $_POST['new_rotate'] == 270) {
				$handle->image_rotate = $_POST['new_rotate'];
			}
			if (trim($handle->file_src_mime) == 'image/bmp') {
				$handle->image_convert = 'png';
			}
			$handle->mime_check = $no_script;
			$handle->no_script = $no_script;
			$handle->process($_POST['return'] . '/');
			if ($handle->processed) {
				$handle->clean();
				$uploadstatus = 1;
			} else {
				//uncomment for upload debugging
				echo 'error : ' . $handle->error;
				$uploadstatus = 2;
			}
		}
	} else {
		$uploadstatus = 3;
	}
}

//display only directory tree for dynamic AHAH requests
if (isset($_GET['viewtree'])) {
	?>
	<ul class="dirlist">
		<li><a href="<?php echo $root_path . '/' . $file_root; ?>/" onclick="load('mfm.php?viewdir=<?php echo $file_root; ?>','view-files'); return false;"><?php echo $file_root; ?></a> <a href="#" onclick="load('mfm.php?viewtree=true','view-tree'); return false;" id="refresh-tree"><?php echo $lng['refresh']; ?></a>
			<?php print_tree($file_root); ?>
		</li>
	</ul>
	<?php
	exit;
}

//display file list for dynamic requests
if (isset($_GET['viewdir'])) {
	?>
	<ul id="browser-toolbar">
		<li class="file-new"><a href="#" title="<?php echo $lng['new_file_title']; ?>" onclick="toggle_visibility('load-file'); return false;"><?php echo $lng['new_file']; ?></a></li>
		<li class="file-refresh"><a href="#" title="<?php echo $lng['refresh_files_title']; ?>" onclick="load('mfm.php?viewdir=<?php echo $_GET['viewdir']; ?>','view-files'); return false;"><?php echo $lng['refresh']; ?></a></li>
	</ul>

	<div id="current-loction">
		<?php echo htmlspecialchars($root_path . '/' . $_GET['viewdir'] . '/'); ?>
	</div>

	<form style="display: none;" id="load-file" action="" class="load-file" method="post" enctype="multipart/form-data">

		<fieldset>
			<legend><?php echo $lng['new_file_title']; ?></legend>
			<input type="hidden" value="<?php echo $_GET['viewdir']; ?>" name="return" />
			<label><?php echo $lng['form_file']; ?><input type="file" name="new_file" /></label>
		</fieldset>

		<fieldset>
			<legend><?php echo $lng['new_file_manipulations']; ?></legend>
			<table>
				<tr>
					<td><label for="new_resize"><?php echo $lng['form_width']; ?></label></td>
					<td><input type="text" class="number" maxlength="4" id="new_resize" name="new_resize" value="" /> px</td>
				</tr>
				<tr>
					<td><label for="new_rotate"><?php echo $lng['form_rotate']; ?></label></td>
					<td>
						<select id="new_rotate" name="new_rotate">
							<option value="0"></option>
							<option value="90">90</option>
							<option value="180">180</option>
							<option value="270">270</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" class="checkbox" id="new_greyscale" name="new_greyscale" /><label for="new_greyscale"><?php echo $lng['form_greyscale']; ?></label></td>
				</tr>
			</table>
		</fieldset>

		<input type="submit" id="insert" value="<?php echo $lng['form_submit']; ?>" />

	</form>
	<?php
	//create directory and show results
	if (isset($_GET['newdir'])) {
		$new_title = format_filename($_GET['newdir']);
		if (!is_dir($_GET['viewdir'] . '/' . $new_title)) {
			if (mkdir($_GET['viewdir'] . '/' . $new_title, 0777)) {
				echo '<p class="successful">&quot;' . $new_title . '&quot;' . $lng['message_created_folder'] . '</p>';
			} else {
				echo '<p class="failed">' . $lng['message_cannot_create'] . '&quot;' . $new_title . '&quot;!<br />' . $lng['message_cannot_write'] . '</p>';
			}
		} else {
			echo '<p class="failed">' . $lng['message_cannot_create'] . '&quot;' . $new_title . '&quot;!<br />' . $lng['message_exists'] . '</p>';
		}
	}

	//remove unnecessary files
	if (isset($_GET['deletefile'])) {
		if (!file_exists($_GET['viewdir'] . '/' . $_GET['deletefile'])) {
			echo '<p class="failed">' . $lng['message_cannot_delete_nonexist'] . '</p>';
		} else {
			if (unlink($_GET['viewdir'] . '/' . $_GET['deletefile'])) {
				echo '<p class="successful">' . $lng['message_deleted'] . '</p>';
			} else {
				echo '<p class="failed">' . $lng['message_cannot_delete'] . '</p>';
			}
		}
	}

	//show status messages by code
	if (isset($_GET['status'])) {
		//upload file
		if ($_GET['status'] == 1) {
			echo '<p class="successful">' . $lng['message_uploaded'] . '</p>';
		} elseif ($_GET['status'] == 2) {
			echo '<p class="failed">' . $lng['message_upload_failed'] . '</p>';
		} elseif ($_GET['status'] == 3) {
			echo '<p class="failed">' . $lng['message_wrong_dir'] . '</p>';
			//remove directory
		} elseif ($_GET['status'] == 4) {
			echo '<p class="successful">' . $lng['message_folder_deleted'] . '</p>';
		} elseif ($_GET['status'] == 5) {
			echo '<p class="failed">' . $lng['message_cant_delete_folder'] . '</p>';
		} elseif ($_GET['status'] == 6) {
			echo '<p class="failed">' . $lng['message_folder_not_exist'] . '</p>';
		}
	}

	//finally show file list
	print_files($_GET['viewdir']);
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-language" content="en" />
		<title><?php echo $lng['window_title']; ?></title>
		<link rel="stylesheet" href="mfm/style.css?widths" type="text/css" />
		<link rel="stylesheet" href="<?php echo $root_path; ?>/tiny_mce/themes/advanced/skins/default/dialog.css" type="text/css" />
		<script type="text/javascript" src="<?php echo $root_path; ?>/tiny_mce/tiny_mce_popup.js"></script>
		<script type="text/javascript">
			//<![CDATA[

			//load content using AHAH (asynchronous HTML and HTTP)
			function ahah(url, target) {
				document.getElementById(target).innerHTML = '<img src="mfm/loading.gif" alt="" /> <?php echo $lng['loading']; ?>';
				if (window.XMLHttpRequest) {
					req = new XMLHttpRequest();
				} else if (window.ActiveXObject) {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				}
				if (req != undefined) {
					req.onreadystatechange = function() {ahahDone(url, target);};
					var url = url + ((url.indexOf("?") == -1) ? "?" : "&") + escape(new Date().toString()); //permet de ne pas utiliser le cache
					req.open("GET", url, true);
					req.send("");
				}
			}

			function ahahDone(url, target) {
				if (req.readyState == 4) {
					if (req.status == 200) {
						document.getElementById(target).innerHTML = req.responseText;
					} else {
						document.getElementById(target).innerHTML=" AHAH Error:\n"+ req.status + "\n" +req.statusText;
					}
				}
			}

			function load(name, div) {
				ahah(name,div);
				return false;
			}

			//ask for folder title and request it's creation
			function create_folder(viewdir) {
				var name=prompt("<?php echo $lng['ask_folder_title']; ?>","<?php echo $lng['default_folder']; ?>");
				if (name!=null && name!=""){
					load('mfm.php?viewdir=' + viewdir + '&newdir=' + name + '','view-files');
				}
			}

<?php
//first one for inserting file name into given field, second for working as tinyMCE plugin
if ($mode == 'standalone' && isset($_GET['field'])) {
	?>
			function submit_url(URL) {
				window.opener.document.getElementById('<?php echo $_GET['field']; ?>').value = URL;
				self.close();
			}
	<?php
} else {
	?>
			function submit_url(URL) {
				var win = tinyMCEPopup.getWindowArg("window");
				win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
				if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
				if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
				tinyMCEPopup.close();
			}
<?php } ?>

	//confirm and delete file
	function delete_file(dir,file) {
		var answer = confirm("<?php echo $lng['ask_really_delete']; ?>");
		if (answer){
			load('mfm.php?viewdir=' + dir + '&deletefile=' + file,'view-files');
		}
	}


	//show/hide element (for file upload form)
	function toggle_visibility(id) {
		var e = document.getElementById(id);
		if(e != null) {
			if(e.style.display == 'none') {
				e.style.display = 'block';
			} else {
				e.style.display = 'none';
			}
		}
	}
	//]]>
		</script>
	</head>

	<?php
	$return = $current_month;
	if (isset($_REQUEST['return'])) {
		$return = $_REQUEST['return'];
	}
	?>

	<body onload="load('mfm.php?status=<?php echo $uploadstatus; ?>&amp;viewdir=<?php echo $return; ?>','view-files');">
		<div id="browser-wrapper">
			<div id="view-tree">
				<ul class="dirlist">
					<li><a href="<?php echo $root_path . '/' . $file_root; ?>/" onclick="load('mfm.php?viewdir=<?php echo $file_root; ?>','view-files'); return false;"><?php echo $file_root; ?></a>
						<?php print_tree($file_root); ?>
					</li>
				</ul>
			</div>
			<div id="view-files"></div>
		</div>

	</body>

</html>
