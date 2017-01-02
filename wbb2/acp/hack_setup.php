<?php
/***************************************************************************
 *
 *   copyright            : © 2017 @ kill0rz
 *   web                  : kill0rz.com
 *   Boardversion         : Burning Board wBB 2.3
 ***************************************************************************/

if (file_exists("./lib/HACK_install.lock")) {
	die("Bitte l&ouml;schen Sie die Datei acp/lib/HACK_install.lock, um die Installation erneut ausf&uuml;hren zu k&ouml;nnen!");
}

$filename = "HACK_setup.php";

$version = "v1";

@error_reporting(7);
@set_time_limit(0);
if (get_magic_quotes_runtime()) {
	// Deactivate
	set_magic_quotes_runtime(0);
}
$phpversion = phpversion();

/* page output function */
function informationPage($content, $title = '') {
	echo '<?xml version="1.0" encoding="windows-1252"?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
			<head>
				<title>' . $title . '</title>
				<link rel="stylesheet" href="css/other.css" />
			</head>

			<body>
				<table align="center" width="500">
					<tr>
						<td align="center"><img src="./images/acp-logo.gif" border="0" alt="" /></td>
					</tr>
					<tr>
						<td><br /><br />' . $content . '</td>
					</tr>
				</table>
			</body>
		</html>';
}

// WBB-Funktionen includen und Datenbank &ouml;ffnen
require "./lib/config.inc.php";
require "./lib/options.inc.php";
require "./lib/class_db_mysql.php";
require "./lib/functions.php";
require "./lib/admin_functions.php";
$db = new db($sqlhost, $sqluser, $sqlpassword, $sqldb, $phpversion);

list($wbbversion) = $db->query_first("SELECT value FROM bb" . $n . "_options WHERE varname = 'boardversion'");

if (isset($_REQUEST['step'])) {
	$step = $_REQUEST['step'];
} else {
	$step = "";
}

// Header ausgeben
print("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=iso-8859-1\">\n");
print "<link rel=\"stylesheet\" href=\"css/other.css\"></head><body align=\"center\">\n";
print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"600\" align=\"center\"><tr><td width=\"100%\" align=\"left\">\n";
print "<h2><center>HACK $version Installation</center></h2>\n<br />\n";

if (!$step) {

	informationPage('<b>Herzlich Willkommen bei der Einrichtung des HACKs f&uuml;r das WBB 2.3.x</b><br /><br />
  <table>
  <tr>
   <td colspan="3">Systemvoraussetzungen:</td>
  </tr>
  <tr>
   <td><u>Eigenschaft</u></td>
   <td><u>erforderlich</u></td>
   <td><u>vorhanden</u></td>
  </tr>
  <tr>
   <td>WoltLab Burning Board</td>
   <td>2.3.x</td>
   <td><span style="color: ' . ((version_compare($wbbversion, "2.3.6") == -1) ? ('red') : ('lime')) . '">' . $wbbversion . '</span></td>
  </tr>
  <tr>
   <td>PHP Version</td>
   <td>5.6</td>
   <td><span style="color: ' . ((version_compare($phpversion, "5.6") == -1) ? ('red') : ('lime')) . '">' . $phpversion . '</span></td>
  </tr>
  <tr>
   <td>Sprachdatei "/acp/HACK_de.lng"</td>
   <td>Ja</td>
   <td><span style="color: ' . ((!is_file("../acp/HACK_de.lng")) ? ('red') : ('lime')) . '">' . ((is_file("../acp/HACK_de.lng")) ? ('Ja') : ('Nein')) . '</span></td>
  </tr>
  <tr>
   <td>Variablendatei "/acp/HACK.wbb"</td>
   <td>Ja</td>
   <td><span style="color: ' . ((!is_file("../acp/HACK.wbb")) ? ('red') : ('lime')) . '">' . ((is_file("../acp/HACK.wbb")) ? ('Ja') : ('Nein')) . '</span></td>
  </tr>
  <tr>
   <td>SQL-Datei "/acp/HACK.sql"</td>
   <td>Ja</td>
   <td><span style="color: ' . ((!is_file("../acp/HACK.sql")) ? ('red') : ('lime')) . '">' . ((is_file("../acp/HACK.sql")) ? ('Ja') : ('Nein')) . '</span></td>
  </tr>
  <tr>
   <td>Haupt-Datei "/HACK.php"</td>
   <td>Ja</td>
   <td><span style="color: ' . ((!is_file("../HACK.php")) ? ('red') : ('lime')) . '">' . ((is_file("../HACK.php")) ? ('Ja') : ('Nein')) . '</span></td>
  </tr>
  <tr>
   <td>ACP-Datei "/acp/HACK_admin.php"</td>
   <td>Ja</td>
   <td><span style="color: ' . ((!is_file("../acp/HACK_admin.php")) ? ('red') : ('lime')) . '">' . ((is_file("../acp/HACK_admin.php")) ? ('Ja') : ('Nein')) . '</span></td>
  </tr>
</table>
 <p><i>Sollten eine oder mehrere Voraussetzungen nicht erf&uuml;llt sein, kann ein einwandfreier Betrieb des HACKs nicht gew&auml;hrleistet werden.</i></p>
 <br />
 ');

	// WBB-Version checken
	if (version_compare($wbbversion, "2.3.0") < 0) {
		print "Das HACK $version braucht ein Woltlab Burning Board in der Version 2.3.x oder h&ouml;her!<p />";
		print "Ihre WBB-Version ist " . $wbbversion . ".<p />";
		print "<b>Installation wird abgebrochen.</b>";
		print "</td></tr></table></body></html>";
		exit();
	}

	print "<b>Wichtige Hinweise:</b>";
	print "<br />Voraussetzung f&uuml;r diese Version ist die WBB-Version 2.3.x oder h&ouml;her - wenn Sie eine andere WBB-Version ";
	print "einsetzen, brechen Sie die Installation hier ab!<p/>";

	// Files soweit kopiert? Teste als Stichprobe ein File in jedem Verzeichnis
	// und die Installationsfiles
	$filefehlt = 0;
	$filefehlta = '';
	if (!is_file("./HACK_de.lng")) {
		$filefehlt = 1;
		$filefehlta .= "<li>Sprachdatei \"/acp/HACK_de.lng\" nicht gefunden</li>";
	}
	if (!is_file("./HACK.wbb")) {
		$filefehlt = 2;
		$filefehlta .= "<li>Rechtedatei \"/acp/HACK.wbb\" nicht gefunden</li>";
	}
	if (!is_file("./HACK.sql")) {
		$filefehlt = 3;
		$filefehlta .= "<li>SQL-Datei \"/acp/HACK.sql\" nicht gefunden</li>";
	}
	if (!is_file("../HACK.php")) {
		$filefehlt = 4;
		$filefehlta .= "<li>Haupt-Datei \"/HACK.php\" nicht gefunden</li>";
	}
	if (!is_file("./HACK_admin.php")) {
		$filefehlt = 5;
		$filefehlta .= "<li>ACP-Datei \"acp/HACK_admin.php\" nicht gefunden</li>";
	}

	if ($filefehlt > 0) {
		print "<br />\n";
		print "ABBRUCH.<br />\n";
		print "Die ben&ouml;tigten Dateien wurden nicht gefunden.<br />\n";
		print $filefehlta . "<br />\n";
		print "Bitte laden Sie erst alle Dateien des HACKs $version in die entsprechenden Verzeichnisse \n";
		print "und starten Sie dieses Installationsskript neu!<br /><br />\n\n";
		print "Lesen Sie dazu ggf. auch die Installationsanleitung.<br />\n";
		print "<br />\n";
		print "</td></tr></table></body></html>\n";
		exit();
	}

	print "Die ben&ouml;tigten Dateien wurden gefunden (Stichproben).<br>\n";
	print "<a href=\"./$filename?step=acptemplates\">Installation fortsetzen</a> (Installation der Board-Templates)";
	print "</td></tr></table></body></html>";
	exit();
}

// ##################################################################################################
// ACP-Templates importieren und cachen
// ##################################################################################################

if ($step == "acptemplates") {

// # L&ouml;schen der alten Eintr&auml;ge

	@$db->query("DELETE FROM bb" . $n . "_groupvariablegroups WHERE title='USER_HACK'");
	@$db->query("DELETE FROM bb" . $n . "_groupvariablegroups WHERE title='ADMIN_HACK'");

	@$db->query("DELETE FROM bb" . $n . "_groupvariables WHERE variablename='can_HACK_see'");
	@$db->query("DELETE FROM bb" . $n . "_groupvariables WHERE variablename='can_HACK_use'");
	@$db->query("DELETE FROM bb" . $n . "_groupvariables WHERE variablename='a_can_HACK_edit'");

	@$db->query("DELETE FROM bb" . $n . "_acpmenuitemgroups WHERE title='HACK'");

	@$db->query("DELETE FROM bb" . $n . "_acpmenuitems WHERE languageitem='HACK_INDEX'");
	@$db->query("DELETE FROM bb" . $n . "_acpmenuitems WHERE languageitem='HACK_OPTIONS'");
	@$db->query("DELETE FROM bb" . $n . "_acpmenuitems WHERE languageitem='HACK_PUNKTE'");
	@$db->query("DELETE FROM bb" . $n . "_acpmenuitems WHERE languageitem='HACK_RESULTS'");

	@$db->query("DELETE FROM bb" . $n . "_languages WHERE itemname like '%_HACK_%'");

	@$db->query("DELETE FROM bb" . $n . "_languagecats WHERE catname = 'HACK'");
	@$db->query("DELETE FROM bb" . $n . "_languagecats WHERE catname = 'acp_HACK'");
	@$db->query("DELETE FROM bb" . $n . "_languagecats WHERE catname = 'HACK_de'");
	@$db->query("DELETE FROM bb" . $n . "_languagecats WHERE catname = 'HACK_en'");

	informationPage('<b>Die ACP-Templates des HACKs werden nun vorbereitet.</b>');

	require "./lib/class_templateparser.php";

	//
	// this is more or less equal to acp_tpl.php from Woltlab
	//

	/** search for new templates that must be compiled (by filemtime) */
	clearstatcache();
	$templates = array();
	$handle = @opendir("./templates");
	while ($file = readdir($handle)) {
		if ($file == ".." || $file == "." || substr($file, -3) != "htm") {
			continue;
		}

		$templatename = substr($file, 0, -1 * strlen(strrchr($file, ".")));
		if (!file_exists("../cache/templates/acp/" . $templatename . ".php")) {
			$templates[] = $templatename;
		} elseif (filemtime("../acp/templates/" . $file) != filemtime("../cache/templates/acp/" . $templatename . ".php")) {
			$templates[] = $templatename;
		}

		unset($templatename);
	}
	@closedir($handle);
	unset($handle);

	/** compile these templates */
	foreach ($templates as $templatename) {
		$tplparser = new TemplateParser();
		$template = implode("", file("./templates/" . $templatename . ".htm"));
		$template = preg_replace("!(\r\n)|(\r)!", "\n", $template);
		$template = $tplparser->parse($template);
		$fp = fopen("../cache/templates/acp/" . $templatename . ".php", "w+");
		fwrite($fp, "<?php
/*
templatepackid: acp template
templatename: " . $templatename . "
*/

\$this->templates['acp_" . $templatename . "']=\"" . addcslashes($template, "$\"\\") . "\";
?>");
		fclose($fp);
		chmod("../cache/templates/acp/" . $templatename . ".php", 0666);
		touch("../cache/templates/acp/" . $templatename . ".php", filemtime("./templates/" . $templatename . ".htm"));
	}

	print "<br>Wenn auf dieser Seite keine Fehlermeldungen erschienen sind, wurden die ACP-Templates ";
	print "erfolgreich installiert.<br>\n";
	print "<br><b>WICHTIG: Falls Fehlermeldungen erschienen sind, gab es Probleme</b> - in diesem Fall sollten Sie die ACP-Templates ";
	print "nach dem Fertigstellen dieser Installation noch einmal mit dem Woltlab-Tool acp_tpl.php cachen, das Sie im ";
	print "WBB2.3-Downloadarchiv im Ordner 'extra' finden. Vorher sollten Sie *alle* Dateien in dem Ordner ";
	print "cache/templates/acp l&ouml;schen, damit diese von dem Woltlab-Skript neu angelegt werden k&ouml;nnen.<br />";
	print "<br><a href=\"./$filename?step=langask\">Installation fortsetzen</a> (Ausw&auml;hlen der Sprachpakete)";
	print "</td></tr></table></body></html>";
	exit();

}

// ##################################################################################################
// Sprachvariablen importieren - deutsches Sprachpaket
// ##################################################################################################

if ($step == "langask") {

	informationPage('<b>Das Sprachpaket des HACKs installieren.</b>');

	$langplist_de = "";

	$result = $db->query("SELECT * FROM bb" . $n . "_languagepacks ORDER BY languagepackid");
	while ($row = $db->fetch_array($result)) {

		$langplist_de .= "<option value=\"" . ($row['languagepackid'] + 1) . "\" ";
		$langplist_de .= ($row['languagecode'] == 'de') ? ' selected="selected" ' : '';
		$langplist_de .= ">" . getlangvar($row['languagepackname'], $lang) . " (" . $row['languagecode'] . ")</option>\n";

	}

	print "Das HACK $version kann auf deutsch dargestellt werden.<p />";
	print "Wenn Sie in Ihrem Forum mehrere Sprachpakete installiert haben, k&ouml;nnen Sie ";
	print "hier bestimmen, in welchen Sprachpaketen das HACK $version dargestellt werden soll.<p />";

	print "Normalerweise erkennt das Programm das selbst und Sie brauchen hier nichts zu &auml;ndern.<p />";

	print "In welchen Ihrer Sprachpakete soll das HACK auf  <b>deutsch</b> erscheinen?<br />";
	print "(Mit der [Strg]-Taste k&ouml;nnen mehrere Sprachpakete ausgew&auml;hlt werden)<br />";

	print "<form name=\"languagepacks\" method=\"post\" action=\"$filename?step=langinst\">\n";
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=\"languagepackids_de[]\" size=\"5\" multiple=\"multiple\">\n" . $langplist_de;
	print "\n</select><br /><br />";

	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"forward\" type=\"submit\" value=\"Weiter\" /> (Installation der Sprachvariablen)</form>";
	print "</td></tr></table></body></html>";

	exit();

}

if ($step == "langinst") {

	if (isset($_REQUEST['languagepackids_de'])) {
		$languagepackids_de = $_REQUEST['languagepackids_de'];
	} else {
		$languagepackids_de = "";
	}

	if (empty($languagepackids_de)) {
		print "<br /><b>Sie haben kein Sprachpaket f&uuml;r die Installation der Sprachvariablen ausgew&auml;hlt!</b><p />\n";
		print "Ohne Sprachvariablen kann das HACK nicht benutzt werden. Sie k&ouml;nnen die<br />\n";
		print "Sprachvariablen des HACKs entweder sp&auml;ter von Hand im ACP importieren, oder<br />\n";
		print "jetzt zur&uuml;ck zu den Auswahlseiten gehen:<p />\n";
		print "<a href=\"./$filename?step=langask\">Zur&uuml;ck zur Auswahl der Sprachpakete (empfohlen)</a><p />\n";
		print "<a href=\"./$filename?step=groups\">Weiter, keine Sprachvariablen installieren</a><p />\n";
	}

	//
	// Deutsche Variablen
	//

	informationPage('<b>Installation der Sprachvariablen</b>');

	if (!empty($languagepackids_de)) {
		print "Deutsche Sprachvariablen werden importiert.<br/>";

		//
		// Using Code from Troublegum / Woltlab
		//
		$lngfile = "HACK_de.lng";

		// read language file
		$lngdata = readlngfile($lngfile);

		if (!$lngdata) {
			print "Konnte die Language-Datei {$lngfile} nicht laden.<p />";
			print "ABBRUCH.";
			print "</td></tr></table></body></html>";
			exit();
		}

		$installed = 0; // installation counter
		while (list($key, $languagepackid) = each($languagepackids_de)) {

			$languagepackid = $languagepackid - 1;

			if (count($lngdata['cats'])) {
				$where = "";
				foreach ($lngdata['cats'] as $cat) {
					$db->unbuffered_query("INSERT IGNORE INTO bb" . $n . "_languagecats (catname) VALUES ('" . addslashes($cat) . "')");
					$where .= ",'" . addslashes($cat) . "'";
				}
				$cats = array();
				$result = $db->query("SELECT catid,catname FROM bb" . $n . "_languagecats WHERE catname IN(" . substr($where, 1) . ")");
				while ($row = $db->fetch_array($result)) {
					$cats[$row['catname']] = $row['catid'];
				}

			}

			if (count($lngdata['items'])) {
				$insert_str = "";
				foreach ($lngdata['items'] as $cat => $itemarray) {
					$showorder = 1;
					foreach ($itemarray as $itemname => $item) {
						$insert_str .= ",('$languagepackid','" . $cats[$cat] . "', '" . addslashes($itemname) . "', '" . addslashes($item) . "', '" . $showorder . "')";
						$showorder++;
					}
				}
				if ($insert_str) {
					$db->unbuffered_query("REPLACE INTO bb" . $n . "_languages (languagepackid,catid,itemname,item,showorder) VALUES " . substr($insert_str, 1), 1);
				}

				foreach ($cats as $catname => $catid) {
					updateCache($languagepackid, $catid);
				}

			}
			$installed++;
		}

		print "Die deutschen Sprachvariablen wurden erfolgreich installiert.<p />\n";
	}

	print "<br><a href=\"./$filename?step=groups\">Installation fortsetzen</a> (Gruppenvariablen und ACP-Men&uuml;punkte)";
	print "</td></tr></table></body></html>";
	exit();

}

// ##################################################################################################
// Gruppenvariablen und ACP-Menuepunkte installieren
// ##################################################################################################

if ($step == "groups") {

	informationPage('<b>Die Gruppenvariablen und ACP-Menupunkte werden angelegt.</b>');

	require "./lib/class_variableimport.php";

	$variableimport = new variableimport("./HACK.wbb");
	if ($variableimport->errors()) {
		informationPage('Es sind Fehler beim lesen der Variablendatei aufgetreten: <br />' . $variableimport->getErrors() . '<br />');
	} else {
		$variableimport->import();
		if ($variableimport->errors()) {
			informationPage("Ung&uuml;ltige Variablendatei: " . $variableimport->getErrors());
		}

	}

	print "ACP-Men&uuml;punkte neu eingef&uuml;gt.<br />\n";

	print "<br>Die Gruppenvariablen und ACP-Men&uuml;punkte wurden erfolgreich angelegt.<br>\n";
	print "<br><a href=\"./$filename?step=database\">Installation fortsetzen</a> (Datenbank-Tabellen)";
	print "</td></tr></table></body></html>";
	exit();

}

// ##################################################################################################
// DB-Tabellen map_* installieren
// dazu SQL-Datei ausfuehren
// ##################################################################################################

if ($step == "database") {

	informationPage('<b>Die Datenbanktabellen werden angelegt.</b>');

	$sqlfname = "HACK.sql";
	$query = implode(" ", file($sqlfname));

	if ($query != "") {

		// Richtige Boardnummer einsetzen
		$query = str_replace("bb1_", "bb" . $n . "_", $query);

		require "./lib/class_query.php";
		$sql_query = new query($query);
		$sql_query->doquery();

		print "Datenbank-Datei '$sqlfname' installiert.<br />\n";

	} else {
		print "<br />\n";
		print "FEHLER:<br />\n";
		print "Die Datenbank-Datei '{$sqlfname}' konnte nicht geladen werden.<br />\n";
		print "Diese mu&szlig; im acp-Verzeichnis vorhanden sein. Bitte laden Sie die diese Datei ";
		print "neu hoch und f&uuml;hren Sie die Installation noch einmal aus.\n";
		print "</pre></td></tr></table></body></html>";
		exit();
	}

	// ####################################################################
	// FERTIG !!!
	// ####################################################################
	$fp = @fopen("./lib/HACK_install.lock", "w+");
	fclose($fp);

	print "<b><h3>Installation erfolgreich!</h3></b>";
	print " ";
	print "<b>Fertig!</b><p>";
	print "Die Installation wurde erfolgreich abgeschlossen.<br />";
	print "Sie k&ouml;nnen das HACK {$version} jetzt benutzen.<p>";
	print "Bitte richten sie es noch im <a href=\"./index.php\">ACP</a> ein, und l&ouml;schen Sie diese<br />";
	print "Installationsdateien aus dem ACP-Verzeichnis:<br />";
	print "<pre>HACK_setup.php, HACK_de.lng, HACK.wbb, HACK.sql</pre>";
	print "<b>Viel Spa&szlig;! :)</b></td></tr></table></body></html>";
	exit();
}

print "ABBRUCH: Falscher Link.";
print "</td></tr></table></body></html>";
exit();
?>