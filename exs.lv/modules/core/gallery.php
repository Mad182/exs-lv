<?php

if (!empty($_GET['i'])) {
	redirect('/gallery/' . intval($_GET['g']) . '/' . intval($_GET['i']), true);
} else {
	redirect('/gallery/' . intval($_GET['g']), true);
}
