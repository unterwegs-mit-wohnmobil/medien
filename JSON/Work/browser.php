<?php
// Source in https://jsonjfi.de.cool/jfi.php
function getPHPversion() 
{
	echo '"' . phpversion() . '"';
}
function getBrowserInfo() 
{
  $browser = get_browser(null, true);
  echo '"' . implode("<br>", $browser) . '"';
}
function getBrowserInfoDetails() 
{
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Trident/i',$u_agent)) 
    { // this condition is for IE11
        $bname = 'Internet Explorer'; 
        $ub = "rv"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    // Added "|:"
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
     ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    echo '"' . 'UserAgent: ' . $u_agent . '<br>Browser: ' . $bname . '<br>Version: ' . $version . '<br>platform: ' . $platform . '<br>pattern: ' . $pattern . '"';
}
?>
<!DOCTYPE html>
<html>
<body>
<title>Show Browser Info</title>
<h3>Browser Info</h3>
<script>
	var PHPversion = <?=getPHPversion()?>;
	var BrowserInfo = <?=getBrowserInfo()?>;
	var getBrowserInfoDetails = <?=getBrowserInfoDetails()?>;
	document.write('<h3>' + getBrowserInfoDetails + '</h3>');
	document.write('<h3>' + "appName: " + navigator.appName + '</h3>');
	document.write('<h3>' + "appVersion: " + navigator.appVersion + '</h3>');
	document.write('<h3>' + "cookieEnabled: " + navigator.cookieEnabled + '</h3>');
	document.write('<h3>' + "geolocation: " + navigator.geolocation + '</h3>');
	document.write('<h3>' + "language: " + navigator.language + '</h3>');
	document.write('<h3>' + "onLine: " + navigator.onLine + '</h3>');
	document.write('<h3>' + "platform: " + navigator.platform + '</h3>');
	document.write('<h3>' + "product: " + navigator.product + '</h3>');
	document.write('<h3>' + "userAgent: " + navigator.userAgent + '</h3>');
	document.write('<h3>' + BrowserInfo + '</h3>');
	document.write('<h3>' + "PHPversion: " + PHPversion + '</h3>');
</script>
<noscript>
	Der Browser unterst&uuml;tzt kein JavaScript
</noscript>
</body>
</html>
