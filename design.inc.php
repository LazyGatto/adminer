<?php
function page_header($title) {
	global $LANG;
	header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $LANG; ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<title><?php echo lang('phpMinAdmin') . " - $title"; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<style type="text/css">
BODY { color: Black; background-color: White; }
A { color: Blue; }
A:visited { color: Navy; }
H1 { font-size: 150%; margin: 0; }
H2 { font-size: 150%; margin: 0; }
H3 { margin: 0; }
FIELDSET { float: left; padding: .5em; margin: 0; }
PRE { margin: .12em 0; }
TABLE { margin-top: 1em; }
.error { color: Red; }
.message { color: Green; }
#menu { position: absolute; top: 8px; left: 8px; width: 15em; overflow: auto; white-space: nowrap; }
#content { margin-left: 16em; }
</style>
<?php if ($_SESSION["highlight"] == "jush") { ?>
<style type="text/css">@import url(http://jush.info/jush.css);</style>
<script type="text/javascript" src="http://jush.info/jush.js" defer="defer"></script>
<script type="text/javascript">body.onload = function () { jush.highlight_tag('pre'); }</script>
<?php } ?>
</head>

<body>

<div id="content">
<?php
	echo "<h2>$title</h2>\n";
	if ($_SESSION["message"]) {
		echo "<p class='message'>$_SESSION[message]</p>\n";
		$_SESSION["message"] = "";
	}
	session_write_close();
}

function page_footer($missing = false) {
	global $SELF, $mysql;
?>
</div>

<div id="menu">
<h1><a href="<?php echo (strlen($SELF) > 1 ? htmlspecialchars(substr($SELF, 0, -1)) : "."); ?>"><?php echo lang('phpMinAdmin'); ?></a></h1>
<?php switch_lang(); ?>
<?php if ($missing != "auth") { ?>
<p>
<a href="<?php echo htmlspecialchars($SELF); ?>sql="><?php echo lang('SQL command'); ?></a>
<a href="<?php echo htmlspecialchars($SELF); ?>dump="><?php echo lang('Dump'); ?></a>
<a href="<?php echo htmlspecialchars(preg_replace('~db=[^&]*&~', '', $SELF)); ?>logout="><?php echo lang('Logout'); ?></a>
</p>
<form action="" method="get">
<p><?php if (strlen($_GET["server"])) { ?><input type="hidden" name="server" value="<?php echo htmlspecialchars($_GET["server"]); ?>" /><?php } ?>
<select name="db" onchange="this.form.submit();"><option value="">(<?php echo lang('database'); ?>)</option>
<?php
		flush();
		$result = $mysql->query("SHOW DATABASES");
		while ($row = $result->fetch_row()) {
			echo "<option" . ($row[0] == $_GET["db"] ? " selected='selected'" : "") . ">" . htmlspecialchars($row[0]) . "</option>\n";
		}
		$result->free();
		?>
</select><?php if (isset($_GET["sql"])) { ?><input type="hidden" name="sql" value="" /><?php } ?></p>
<noscript><p><input type="submit" value="<?php echo lang('Use'); ?>" /></p></noscript>
</form>
<?php
		if ($missing != "db" && strlen($_GET["db"])) {
			$result = $mysql->query("SHOW TABLE STATUS");
			if (!$result->num_rows) {
				echo "<p class='message'>" . lang('No tables.') . "</p>\n";
			} else {
				echo "<p>\n";
				while ($row = $result->fetch_assoc()) {
					echo '<a href="' . htmlspecialchars($SELF) . 'select=' . urlencode($row["Name"]) . '">' . lang('select') . '</a> ';
					echo '<a href="' . htmlspecialchars($SELF) . (isset($row["Engine"]) ? 'table' : 'view') . '=' . urlencode($row["Name"]) . '">' . htmlspecialchars($row["Name"]) . "</a><br />\n";
				}
				echo "</p>\n";
			}
			echo '<p><a href="' . htmlspecialchars($SELF) . 'create=">' . lang('Create new table') . "</a></p>\n";
			$result->free();
		}
	}
	?>
</div>

</body>
</html>
<?php
}
