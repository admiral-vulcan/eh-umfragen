<?php

	function String2Hex($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++){
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}

	function Hex2String($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}

	function encodeString($plain) {
		$salt = 18473163166326;
		$p_rand = rand(0,9);
		$num_char = 6; //best: 6
		$i_max = intval(strlen($plain) / $num_char) + 1;
		$final = "";
		
		for ($i = 0; $i < $i_max; $i++) {
			if ($i == ($i_max - 1)) $p_end = strlen($plain)%$num_char;
			else $p_end = $num_char;
			if ($p_end == 0) break;
			$p_sub = substr($plain, $i*$num_char, $p_end);
			$final = $final.chr(97+$num_char).$p_rand.chr(97+$i_max).strrev(dechex(hexdec(String2Hex($p_sub)) + $salt));
		}
		return $final;
	}
	function decodeString($code) {
		//if (($code == "") || ($code == 0) || ($code == NULL)) return "";
		$salt = 18473163166326;
		$c_len = ord(substr($code, 0, 1))-97;
		$c_pts = ord(substr($code, 2, 1))-97;
		$c_cut = substr($code, 0, 3);
		$c_sub = "";
		$final = "";

		for ($i = 0; $i < $c_pts; $i++) {
			$code = substr($code, strpos($code, $c_cut)+3, strlen($code)-3);
			if (strlen($code) == $c_len*2) return $final.Hex2String(dechex(hexdec(strrev($code)) - $salt));
			if ($i == ($c_pts-1)) return $final.Hex2String(dechex(hexdec(strrev($code)) - $salt));
			$c_sub = substr($code, 0, strpos($code, $c_cut));
			$final = $final.Hex2String(dechex(hexdec(strrev($c_sub)) - $salt));
		}
		return $final;
	}


	function encodeInt($plain) {
		$salt = 19275368650485;
		return strrev(dechex(($plain + $salt)));
	}

	function decodeInt($code) {
		//if (($code == "") || ($code == 0) || ($code == NULL)) return "";
		$salt = 19275368650485;
		return hexdec(strrev($code)) - $salt;
	}

	function encodeIP($plain) {
		//Check for IPv4
		if(filter_var($plain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {    
			return encodeString(strrev(encodeInt(ip2long($plain))));
		}
		//Check for IPv6
		else if(filter_var($plain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return encodeString($plain);
		}
		else {
			return strrev(encodeInt(9999999999999));
		}
	}
	function decodeIP($code) {
		//if (($code == "") || ($code == 0) || ($code == NULL)) return "";
		$ip4 = long2ip(decodeInt(strrev(decodeString($code))));
		$ip6 = decodeString($code);
		//Check for IPv4
		if(filter_var($ip4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return $ip4;
		}
		//Check for IPv6
		else if(filter_var($ip6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return $ip6;
		}
		else {
			return "0";
		}
	}
	/*
	$test = "Hallo mein bester!";
	echo $test;
	echo "<br>";
	echo encodeString($test);
	echo "<br>";
	echo decodeString(encodeString($test));
	echo "<br>";
	*/
	
	/*
	$test = "192.168.240.35";
	$test = "4bd4:a8fc:5833:3a32:4b35:2e65:8bc8:a258";
	echo $test;
	echo "<br>";
	echo encodeIP($test);
	echo "<br>";
	echo decodeIP(encodeIP($test));
	echo "<br>";
	
	
	$test = "1";
	$test = "1559860482";
	echo $test;
	echo "<br>";
	echo encodeInt($test);
	echo "<br>";
	echo decodeInt(encodeInt($test));
	echo "<br>";
	
	*/

?>