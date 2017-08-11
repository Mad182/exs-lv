<?php

$module_content = curl_get($tab->module_data);
$module_content = htmlpost2db($module_content, false);
$module_content = add_smile($module_content);

