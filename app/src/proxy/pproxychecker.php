<?
# coded by stik.name
# web: http://stik.name

set_time_limit(0);
ini_set("display_errors","0");

function get($url, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
    curl_setopt($ch, CURLOPT_PROXY, "$proxy");
    #curl_setopt($ch, CURLOPT_PROXYUSERPWD, "login:pass");
    $ss=curl_exec($ch);
    curl_close($ch);
    return $ss;
} 

$gdata = array();$gproxy_a = array();$gproxy = "";$c=$g=$b=$t='0';
$proxy_a = file("proxylist.txt");$t=count($proxy_a);
foreach ($proxy_a as $key => $value) {
	$c++;
	$value = str_replace( array( "\n", "\r", " " ), '', $value);
	$buf = get('http://yoip.ru/', $value);
	preg_match("#<span class='ip'>([a-z:0-9.]+)</span>#i", $buf, $gdata["ip"]);
	if (isset($gdata["ip"][1])) {
		$ip=$gdata["ip"][1];
		if (!isset($gproxy_a[$ip])) {
			$g++;
			$gproxy_a[$ip]=$value;
			$gproxy.=$value."
";
		}
		echo "[c:$c/t:$t][g:$g/b:$b] ".$value." (".$ip.") - ok;\r\n";
	} else {
		$b++;
		echo "[c:$c/t:$t][g:$g/b:$b] ".$value." - error;\r\n";

	}
}

if ($fp = fopen ("goodproxy.txt", "w")) {
	fwrite ($fp, $gproxy);
	fclose ($fp);
}

?>