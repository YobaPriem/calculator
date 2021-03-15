<?php
namespace App\Bundle\Google;

class Recaptcha
{
	const SECRET_KEY = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
	const URL = 'https://www.recaptcha.net/recaptcha/api/siteverify';

	public function __construct($secretKey)
	{
		$this->secretKey = $secretKey;
	}

	public function isValidResponse($recaptchaResponse, $remoteIP)
	{
		$request = curl_init();
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_POST, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, [
			'secret' => $this->secretKey,
			'response' => $recaptchaResponse,
			'remoteip' => $remoteIP
		]);
		curl_setopt($request, CURLOPT_TIMEOUT, 10);
		curl_setopt($request, CURLOPT_URL, self::URL);
		$response = curl_exec( $request );
		$response = json_decode( $response, true );
		curl_close( $request );

		if ($response["score"] < 0.9 && $response["score"] != null) {
			return false;
		}

		return true;
	}
}

