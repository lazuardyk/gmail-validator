<?php 

echo "Input txt gmail: (misal: gmail.txt)\n";
@$file = trim(fgets(STDIN));
$lines = file($file);
foreach($lines as $line){
	$email = trim($line);
	check($email);
}

function check($email){
	$header = ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'];
	$url = 'https://mailboxlayer.com/';

	$req = request($url, null, $header, 'GET');
	preg_match('/name="scl_request_secret" value="(.*?)"/', $req[0], $preg);
	$kode = $preg[1];

	$gabung = $email.$kode;
	$hash = md5($gabung);

	$url2 = 'https://mailboxlayer.com/php_helper_scripts/email_api_n.php?secret_key='.$hash.'&email_address='.urlencode($email);
	$header2 = ['Accept: application/json, text/javascript, */*; q=0.01', 'X-Requested-With: XMLHttpRequest'];
	$req2 = request($url2, null, $header2, 'GET');

	$jason = json_decode($req2[0], true);
	if ($jason['error']){
		$msg = $jason['error'];
		echo "$email | $msg\n";
		return;
	}
	$score = $jason['score'];
	if ($score < 0.7){
		echo "$email | tidak valid\n";
	} else {
		$open = fopen('resultgmail.txt', 'a');
		$str = "$email | valid\n";
		fwrite($open, $str);
		fclose($open);
		echo $str;
	}
}
function request($url, $param, $headers, $request)
{
 $ch = curl_init();
 $data = array(
         CURLOPT_URL                => $url,
         CURLOPT_POSTFIELDS        => $param,
         CURLOPT_HTTPHEADER         => $headers,
         CURLOPT_CUSTOMREQUEST     => $request,
         CURLOPT_HEADER             => true,
         CURLOPT_RETURNTRANSFER    => true,
         CURLOPT_FOLLOWLOCATION     => true,
         CURLOPT_SSL_VERIFYPEER    => false
     );
 curl_setopt_array($ch, $data);
 $execute = curl_exec($ch);
 $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
 $header = substr($execute, 0, $header_size);
 $body = substr($execute, $header_size);
 curl_close($ch);
 return [$body, $header];
}


 ?>