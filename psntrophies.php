<?php

/* Script que convierte una pagina html en formato CSV
 * del sitio web https://psntrophyleaders.com/
 * Funciona sólo para la sección de revisión de trofeos
 */

require("simple_html_dom/simple_html_dom.php");
//$file_name = "PSN Trophy Leaders _ HUGO8862003 - God of War.html";
$url_game_trophies = "https://psntrophyleaders.com/user/view/SlySinatra/dragon-ball-fighterz-ps4";
$response_html = curl_scrapping($url_game_trophies);
$html = str_get_html($response_html);

$ret = $html->getElementById("game_details_table")->children(1)->childNodes();
//var_dump($ret);
$id_trophy = 0;

$arrTrophies = array();
foreach($ret as $val_dom){
		foreach($val_dom->children as $child_dom){
			if($child_dom->attr["class"] == "sort XMB"){
				$id_trophy = trim($child_dom->plaintext);
			}
			if($child_dom->attr["class"] == "trophy_title"){
				//echo $id_trophy.";";
				//echo $child_dom->children(3)->children(0)->plaintext;//Description trophy
				$trophy_name = $child_dom->children(1)->plaintext;
			}
			if($child_dom->attr["class"] == "date_earned"){
				$date_trophy_earned =  $child_dom->children(0)->plaintext;
				$trophy_earned = date("M d, Y H:i:s", $date_trophy_earned);
				$arrTrophies[] = array( "time" => $date_trophy_earned,
												"ID" => $id_trophy,
												"Trophy" => $trophy_name,
												"Date" => $trophy_earned);				
			}
		}
}
rsort($arrTrophies);
if($arrTrophies[0]["ID"] !== "0"){
	array_unshift($arrTrophies, $arrTrophies[1]);
	unset($arrTrophies[2]);
}
//print_r($arrTrophies);
echo "ID;Trophy;Date\n";
foreach($arrTrophies as $trophy){
	echo $trophy["ID"].";".$trophy["Trophy"].";".$trophy["Date"]."\n";
}


function curl_scrapping($url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
//	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	//curl_setopt($curl, CURLOPT_POSTFIELDS, $data_post);
	curl_setopt($curl, CURLOPT_PORT, '443');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
//	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);
	curl_setopt($curl, CURLOPT_TIMEOUT, 300);
	curl_setopt($curl, CURLOPT_HEADER, 'Content-Type: application/html');
	curl_setopt($curl, CURLOPT_HTTPHEADER,  array(
		"Cache-Control: no-cache",
		"Content-Type: application/x-www-form-urlencoded"
		));
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($curl, CURLOPT_COOKIESESSION, true );
	curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie_psn.txt' );
	curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie_psn.txt' );
	//curl_setopt($curl, CURLOPT_COOKIE, 'cookiename=cookievalue');
	curl_setopt($curl, CURLOPT_MAXREDIRS, 100);
	curl_setopt($curl, CURLOPT_REFERER, 'https://www.psntrophyleaders.com/');
	try{
		$response_html   = curl_exec($curl);
	}catch (Error $e) {
		echo "Error en curl_exec";
	}
	curl_close($curl);
  
	
	
	return $response_html;
}

?>
