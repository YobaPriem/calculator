<?php
namespace App\Bundle\Calculator;

use Bitrix\Highloadblock\HighloadBlockTable;
// use App\Bundle\Appointment2\ParamsException;

class HLElements
{
	public function createHLElement($props) {
		$table = $this->getTableData();

		$table['entityDataClass']::add([
			'UF_NAME' => $props['name'],
			'UF_PHONE' => $props['phone'],
			'UF_EMAIL' => $props['email'],
			'UF_SYSTEM' => $props['system'],
			'UF_MATERIALS_INFO' => $props['materialsInfo'],
		]);
	}

	public function getTableData($filter = []) {
		$tableName = 'calculator';
		$resHLBlock = HighloadBlockTable::getList(['filter' => ['TABLE_NAME' => $tableName]]);

		$hlBLock = $resHLBlock->fetch();

		$hlEntity = HighloadBlockTable::compileEntity($hlBLock);
		$entityDataClass = $hlEntity->getDataClass();

		$resData = $entityDataClass::getList([
			'filter' => $filter,
		]);
		$resData = new \CDBResult($resData, $tableName);

		return array('resData' => $resData, 'entityDataClass' => $entityDataClass);
	}
}
