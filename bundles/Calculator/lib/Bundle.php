<?php
namespace App\Bundle\Calculator;

class Bundle extends \TAO\Bundle
{
	public function init() {
		//
	}

	public function routes()
	{
		return [
			'{^/calculator/send/$}' => ['controller' => 'Api', 'action' => 'sendTo'],
		];
	}
}
