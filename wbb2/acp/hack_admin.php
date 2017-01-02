<?php

/* HACK v1.0 by kill0rz (C) 2017 */
/* for WBB 2.3.6 */
/* kill0rz.com */

require './global.php';
require './lib/class_parse.php';
require './lib/class_parsecode.php';

if (!checkAdminPermissions("a_can_HACK_edit")) {
	access_error(1);
}

$lang->load("ACP_HACK,MISC,POSTINGS");

if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} else {
	$action = "info";
}

switch ($action) {
	case 'test':
		eval("\$tpl->output(\"" . $tpl->get('HACK_test', 1) . "\");");
		break;

	default:
		eval("\$tpl->output(\"" . $tpl->get('HACK_info', 1) . "\");");
		break;
}