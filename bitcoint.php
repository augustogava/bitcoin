<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);

chdir(dirname(__FILE__));

require "api/DataAPI.php";
$dao 	= new DataAPI();
print "<pre>";
/**

  	Compra
  		Comprei BTC por $100.
  	
  	 Venda BTC (Exemplos)
  	 	- Verificar VALORCOMPRA ($100)
  	 	- Verificar valor medio atual (AVG ULTIMAS TRANSACOES)
  	 	- Se valor (AVG ULTIMAS TRANSACOES)-$percLostSell < VALORCOMPRA
  	 		Vende por VALORCOMPRA + $percAddRiskProfit
  	 		se Maior
  	 			Vende por (AVG ULTIMAS TRANSACOES) + $percAddProfit
 
 
 */
$identificator = "c31455aefa68f71a8d04b5b310fa2a93";
$secret = "37409978c98a9ac4e7ad6d7406283eca6733a4e325d922d9105d04c844fd86ab";

$REQUEST_HOST = 'https://www.mercadobitcoin.net/tapi/v3/';
$REQUEST_PATH = '/tapi/v3/';

function curl_post($url, array $post = array(), array $headers = array())
{
	
	print http_build_query($post);
	print_r($headers);

	$defaults = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
		
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => http_build_query($post)	,
			CURLOPT_HEADER => $headers,
			
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER=> 1,
			
			CURLOPT_VERBOSE => 1
			
	);


	$ch = curl_init();
	
	curl_setopt_array($ch, ($defaults));

		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//exit();
		// cURL SSL options for increased security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);*/
	
	if( ! $result = curl_exec($ch))
	{
// 		$error_message = curl_error($ch);
// 		echo "cURL error ({$errno}):\n {$error_message}";
	}
	
	$info = curl_getinfo($ch);
// 	print_r($info);
	
	curl_close($ch);
	
	fclose($out); 
	$debug = ob_get_clean();
	print $debug;
	return $result;
} 

function curl_get($url, array $options = array())
{
	$defaults = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER=> 0,
			CURLOPT_TIMEOUT => 4
	);
	
	$ch = curl_init();
	curl_setopt_array($ch, ($options + $defaults));
	if( ! $result = curl_exec($ch))
	{
		trigger_error(curl_error($ch));
	}
	curl_close($ch);
	return $result;
} 



/**
*	GetBit Value
*/

//json_encode json_decode

function getTrades($timestamp){
	$ret = curl_get("https://www.mercadobitcoin.net/api/trades/$timestamp/");
	return json_decode($ret);
}

$tradesFromJson= getTrades(strtotime('1 minutes ago'));


if( count($tradesFromJson) > 1 ){
	foreach( $tradesFromJson as $valueTradesFromJson){
		print date("d/m/Y H:i:s", $valueTradesFromJson->date)." ".$valueTradesFromJson->type. " ". $valueTradesFromJson->price. "<br>";
		$sum += $valueTradesFromJson->price;
	}
	
	$avg = $sum / count($tradesFromJson);
	
	$diff = $tradesFromJson[count($tradesFromJson)-1]->price - $tradesFromJson[0]->price;
	if($diff < 0){
		$tend = "0";
	}else{
		$tend = "1";
	}
	
	$perc = ( abs($diff) * 100 ) / $tradesFromJson[count($tradesFromJson)-1]->price;
	print $avg." ".$diff." ".$tend." ".$perc."%";
	
	$dao->saveBitValue($avg, $tend, $diff, $perc);
}else{
	$dao->saveBitValue($tradesFromJson[0]->price, 0, 0, 0);
}
?>