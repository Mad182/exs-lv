<?php
if(!im_mod() && $auth->level != 3) {
	redirect();
}