<?php

namespace App\Bundle\Google;

use Bitrix\Main\Page\Asset;

define('GOOGLE_CAPTCHA_SECRET_KEY','6LfSta0UAAAAAGK1DV5bXbpPdLZ4nriVeeFw1_G0');

class Bundle extends \TAO\Bundle
{
	public function init()
	{
		$this->useScript('recaptcha.js');
		Asset::getInstance()->addString("<script src='https://www.recaptcha.net/recaptcha/api.js?onload=onloadCaptchaCallback&render=explicit&hl=ru' async defer></script>");

	}
}
