<?php

function mta_hash($pass) {
	return strtoupper(md5('vgrpkeyscotland'.$pass));
}

