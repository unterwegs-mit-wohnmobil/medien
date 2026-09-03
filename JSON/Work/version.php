<?php
// Source in https://jsonjfi.de.cool/jfi.php
function getPHPversion() 
{
	echo '"' . phpversion() . '"';
}
?>
<!DOCTYPE html>
<html>
<body>
<title>ShowPHPversion</title>
<h3>ShowPHPversion des Webservers</h3>
<script>
	var PHPversion = <?=getPHPversion()?>;
	document.write('<h3>' + PHPversion + '</h3>');
</script>
<noscript>
	Der Browser unterst&uuml;tzt kein JavaScript, daher Weiterleitung in das Verzeichnis der Bilder
	<meta http-equiv="Refresh" content="3; url=images" />
</noscript>
</body>
</html>

