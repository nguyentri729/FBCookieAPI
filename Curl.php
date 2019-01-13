<?php

/**
* Curl Class
* Code by nguyentri729
*/

class Curl

{

	public function curl_url($url){

	    $ch = @curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);

	    curl_setopt($ch, CURLOPT_ENCODING, '');

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(

	        'Expect:'

	    ));

	    $page = curl_exec($ch);

	    curl_close($ch);

	    return $page;
	}

	

	

	public function post_data_cookie($site,$data,$cookie, $browser = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36'){

	    $datapost = curl_init();

	    $headers = array("Expect:");

	    curl_setopt($datapost, CURLOPT_URL, $site);

	    curl_setopt($datapost, CURLOPT_TIMEOUT, 40000);

	    curl_setopt($datapost, CURLOPT_HEADER, TRUE);

	    curl_setopt($datapost, CURLOPT_HTTPHEADER, $headers);

	    curl_setopt($datapost, CURLOPT_USERAGENT, $browser);

	    curl_setopt($datapost, CURLOPT_POST, TRUE);

	    curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);

	    curl_setopt($datapost, CURLOPT_COOKIE,$cookie);

	    ob_start();

	    return curl_exec ($datapost);

	    ob_end_clean();

	    curl_close ($datapost);

	    unset($datapost); 
	}

	

	public function curl_post($url, $method, $postinfo, $cookie_file_path, $proxy = ''){



	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_HEADER, true);

	    curl_setopt($ch, CURLOPT_NOBODY, false);

	    curl_setopt($ch, CURLOPT_URL, $url);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	    curl_setopt($ch, CURLOPT_COOKIE, $cookie_file_path);

	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36");

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    

	    if($proxy!=''){

	    	$pr = explode(':', $proxy);

			curl_setopt($ch, CURLOPT_PROXY, $pr[0]);

			curl_setopt($ch, CURLOPT_PROXYPORT, $pr[1]);

		/*

	    	curl_setopt($ch, CURLOPT_PROXY, $proxy);

			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);*/

	    }

	    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	    

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

	    if ($method == 'POST') {

	        curl_setopt($ch, CURLOPT_POST, 1);

	        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);

	    }

	    $html = curl_exec($ch);

	  	$code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    curl_close($ch);

	    return $code;

	}

	

	

	public function curl_get($url,$cookie, $browser = 'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14'){

	    $ch = @curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);

	    $head[] = "Connection: keep-alive";

	    $head[] = "Keep-Alive: 300";

	    $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

	    $head[] = "Accept-Language: en-us,en;q=0.5";

	    curl_setopt($ch, CURLOPT_USERAGENT, $browser);

	   

	    curl_setopt($ch, CURLOPT_ENCODING, '');

	    curl_setopt($ch, CURLOPT_COOKIE, $cookie);

	    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(

	        'Expect:'

	    ));

	    $page = curl_exec($ch);

	    curl_close($ch);

	    return $page;

	}

}

