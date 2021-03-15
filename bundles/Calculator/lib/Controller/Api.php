<?php
namespace App\Bundle\Calculator\Controller;

use Bitrix\Main\Mail\Event;
use App\Bundle\Calculator\HLElements;
use App\Bundle\Google\Recaptcha;

class Api extends \TAO\Controller
{
	const SECRET_KEY = '6LfSta0UAAAAAGK1DV5bXbpPdLZ4nriVeeFw1_G0';

	public function sendTo() {
		$request = $this->request();
		$filesToSend = [];
		$token = $request['recaptchaToken'];
		$recaptcha = new Recaptcha(GOOGLE_CAPTCHA_SECRET_KEY);

		if(!$recaptcha->isValidResponse($token, $_SERVER['REMOTE_ADDR'])) {
			http_response_code(403);
			return json_encode([
				'status' => 'recaptcha error'
			]);
		}

		$profile = json_decode($request['profile']);
		$system = $request['system'];
		$materialsInfo = $request['materialsInfo'];
		$area = $request['area'];
		$ratio = $request['ratio'];
		$imagePath = $this->base64toFile($request['image']);
		$image = \CFile::MakeFileArray($imagePath);
		$filesForm[] = \CFile::SaveFile($image , 'iblock');

		$filesDB = $this->getDocuments(json_decode($system)->documents);

		$filesToSend = array_merge($filesForm, $filesDB);

		$decodedSystem = json_decode($system);

		$fields = [
			'EMAIL' => $profile->email,
			'PHONE' => $profile->phone,
			'NAME' => $profile->name,
			'SYSTEM' =>$decodedSystem->name,
			'AREA' => $area,
			'RATIO' => $ratio,
		];

		if ($profile->getConsultation) {
			$this->sendMail('SEND_CALCULATOR_ADMIN', $fields, $filesToSend);
		}

		if ($profile->sendToOwner) {
			$this->sendMail('SEND_CALCULATOR', $fields, $filesToSend);
		}

		$props = [
			'email' => $profile->email,
			'phone' => $profile->phone,
			'name' => $profile->name,
			'system' => $system,
			'materialsInfo' => $materialsInfo,
		];

		$HLDB = new HLElements;
		$HLDB->createHLElement($props);

		return json_encode([
			'status' => 'ok'
		]);
	}

	private function getDocuments($documentsIDS) {
		$documents = \TAO::infoblock('documents')->getItems(array(
			'filter' => array(
				'ACTIVE' => 'Y',
				'ID' => $documentsIDS
			),
		));

		$filesIds = [];

		foreach($documents as $document) {
			$filesIds[] = intval($document->property('FILE_PDF')->value()->fieldsData['ID']);
		}

		return $filesIds;
	}

	private function base64ToFile($encodedbase64) {
		$decodedBase64 = base64_decode($encodedbase64);
		$filename = 'Расчет количества материала TAIKOR для объекта';
		$outputFile = $_SERVER["DOCUMENT_ROOT"] . '/upload/' . $filename . '.pdf';

		$file = fopen($outputFile, "w+");

		fwrite($file, $decodedBase64);
		fclose($file);

		return $outputFile;

	}

	private function sendMail($evenName, $fields, $files) {
		return Event::send(
			array(
				"EVENT_NAME" => $evenName,
				"LID" => "s1",
				"C_FIELDS" => $fields,
				'FILE' => $files
			)
		);
	}

	protected function app()
	{
		return \Bitrix\Main\Application::getInstance();
	}

	protected function request()
	{
		return $this->app()->getContext()->getRequest();
	}
}
