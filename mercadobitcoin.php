<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
* Mercado Bitcoin TAPI v3 Client
* 
* This is the default Mercado Bitcoin Trade API Client for the PHP language.
*/


class MercadoBitcoin
{

	private $key;
	private $secret;
	
	// Constructor
	public function __construct($key, $secret)
	{
		if (isset($key) && isset($secret))
		{
			$this->key = $key;
			$this->secret = $secret;
		}
		else
			die("You must provide the key and secret.");
	}

	public function request($method, array $params = array())
	{
		// Builds the request message
		$message_base = array(
			"tapi_method" => $method,
			"tapi_nonce" => time()
		);
		
		$message_array = array_merge($message_base, $params);
		$message = http_build_query($message_array, "", "&");

		$uri = "/tapi/v3/";
		$message_with_uri = $uri . "?" . $message;
	
		// Signs the message
		$signature = hash_hmac("sha512", $message_with_uri, $this->secret);

		// Builds the headers
		$headers = array(
			"TAPI-ID: " . $this->key,
			"TAPI-MAC: " . $signature
		);

		// cURL
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
		print "<pre>";
		print_r($message);
		print_r($headers);
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mercado Bitcoin TAPI Client");
		curl_setopt($ch, CURLOPT_URL, "https://www.mercadobitcoin.net" . $uri);
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//exit();
		// cURL SSL options for increased security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
		// Sends the request
		$response = curl_exec($ch);

		if ($response === false)
			throw new Exception("Error in server response: " . curl_error($ch));
		
		$decoded_response = json_decode($response);

		if (is_null($decoded_response))
			throw new Exception("Unknown response from server: " . $response);
		
		curl_close($ch);
        
		return $decoded_response;
	}

	//
	// Methods provided by TAPI v3
	//

	// list_system_messages
	public function list_system_messages($level = null)
	{
		$params = array();

		if (!is_null($level))
			$params["level"] = $level;

		return $this->request("list_system_messages", $params);
	}

	// get_account_info
	public function get_account_info()
	{
		return $this->request("get_account_info");
	}

 	// get_order
	public function get_order($coin_pair, $order_id)
	{
		$params = array(
			"coin_pair" => $coin_pair,
			"order_id" => $order_id
		);

		return $this->request("get_order", $params);
	}

	// list_orders
	// You may pass an array as second parameter for any filters wanted.
	// Ex.: list_orders("BRLBTC", array("status_list" => array(2, 3), "order_type" => 2);
	public function list_orders($coin_pair, array $options = array())
	{
		$params = array_merge(
			array("coin_pair" => $coin_pair),
			$options
		);

		// status_list special case
		if (array_key_exists("status_list", $params))
		{
			$params["status_list"] = "[" . implode(",", $params["status_list"]) . "]";
		}

		return $this->request("list_orders", $params);
	}

	// list_orderbook
	public function list_orderbook($coin_pair, $full = null)
	{
		$params = array();

		if (!is_null($full))
			$params["full"] = $full;

		return $this->request("list_orderbook", $params);
	}

	// place_buy_order
	public function place_buy_order($coin_pair, $quantity, $limit_price)
	{
		$params = array(
			"coin_pair" => $coin_pair,
			"quantity" => $quantity,
			"limit_price" => $limit_price
		);

		return $this->request("place_buy_order", $params);
	}

	// place_sell_order
	public function place_sell_order($coin_pair, $quantity, $limit_price)
	{
		$params = array(
			"coin_pair" => $coin_pair,
			"quantity" => $quantity,
			"limit_price" => $limit_price
		);

		return $this->request("place_sell_order", $params);
	}

	// cancel_order
	public function cancel_order($coin_pair, $order_id)
	{
		$params = array(
			"coin_pair" => $coin_pair,
			"order_id" => $order_id
		);

		return $this->request("cancel_order", $params);
	}

	// get_withdrawal
	public function get_withdrawal($coin, $withdrawal_id)
	{
		$params = array(
			"coin" => $coin,
			"withdrawal_id" => $withdrawal_id
		);

		return $this->request("get_withdrawal", $params);
	}

	// withdraw_coin
	public function withdraw_coin($coin, $quantity, $destiny, $description = "")
	{
		$params = array(
			"coin" => $coin,
			"quantity" => $quantity,
			"destiny" => $destiny,
			"description" => $description
		);

		return $this->request("withdraw_coin", $params);
	}
}


$m = new MercadoBitcoin("c31455aefa68f71a8d04b5b310fa2a93", "37409978c98a9ac4e7ad6d7406283eca6733a4e325d922d9105d04c844fd86ab");
$a = $m->get_account_info();
print_r( $a );

?>