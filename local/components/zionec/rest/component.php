<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class PointsApi extends ApiHL
{
    public $apiName = 'points';
    public $hlbl1 = 1; // Указываем ID нашего highloadblock блока к которому будем делать запросы.

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН//rest/points/
     * @return string
     */
    public function indexAction()
    {
    	$hlblock = HL\HighloadBlockTable::getById($this->hlbl1)->fetch(); 

		$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
		$entity_data_class = $entity->getDataClass(); 

		$rsData = $entity_data_class::getList(array(
		   "select" => ["*"],
		   "order" => ["ID" => "ASC"],
		   "filter" => []
		));
		$allPoints = [];
		while($arData = $rsData->Fetch()){
			$arData['UF_CREATE_POINT'] = $arData['UF_CREATE_POINT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI:SS")));
			$arData['UF_UPDATE_POINT'] = $arData['UF_UPDATE_POINT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI:SS")));
			$allPoints[] = $arData;
		}
		if (count($allPoints > 0)) {
			return $this->response($allPoints, 200);
		}
        return $this->response('Data not found index', 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/rest/points/1
     * @return string
     */
    public function readAction()
    {
        //id должен быть первым параметром после /points/x
        $id = array_shift($this->requestUri);

        if($id){
            $hlblock = HL\HighloadBlockTable::getById($this->hlbl1)->fetch(); 
            $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
            $entity_data_class = $entity->getDataClass(); 

            $rsData = $entity_data_class::getList(array(
               "select" => ["*"],
               "order" => [],
               "filter" => ["ID" => $id]
            ));
            if ($arData = $rsData->Fetch()){
                $arData['UF_CREATE_POINT'] = $arData['UF_CREATE_POINT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI:SS")));
                $arData['UF_UPDATE_POINT'] = $arData['UF_UPDATE_POINT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI:SS")));
                return $this->response($arData, 200);
            }
        }
        return $this->response('This id not found Read', 404);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/rest/points/ + параметры запроса name, address
     * @return string
     */
    public function createAction()
    {
        $name = $this->requestParams['name'] ?? '';
        $address = $this->requestParams['address'] ?? '';
        if($name && $address){
            $hlblock = HL\HighloadBlockTable::getById($this->hlbl1)->fetch(); 
            $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
            $entity_data_class = $entity->getDataClass();  

               // Массив полей для добавления
               $data = array(
                  "UF_NAME_POINT" => $name,
                  "UF_ADDRESS_POINT" => $address,
                  "UF_CREATE_POINT" => date("d.m.Y H:i:s")
               );

               $result = $entity_data_class::add($data);
               if ($result) {
                    return $this->response('Data saved.', 200);
               }
        }
        return $this->response("Saving error", 500);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/rest/points/1 + параметры запроса name, address
     * @return string
     */
    public function updateAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;
        if ($id > 0) {
            $hlblock = HL\HighloadBlockTable::getById($this->hlbl1)->fetch(); 
            $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
            $entity_data_class = $entity->getDataClass(); 

            $rsData = $entity_data_class::getList(array(
               "select" => ["*"],
               "order" => [],
               "filter" => ["ID" => $id]
            ));
            if ($arData = $rsData->Fetch()){
                $data["UF_NAME_POINT"] = $this->requestParams['name'] ?? '';
                $data["UF_ADDRESS_POINT"] = $this->requestParams['address'] ?? '';
                if ($data["UF_NAME_POINT"] || $data["UF_ADDRESS_POINT"]) {
                    $data["UF_UPDATE_POINT"] = date("d.m.Y H:i:s");
                    if ($result = $entity_data_class::update($arData['ID'], $data)) {
                        return $this->response('Data updated.', 200);
                    }
                }
            } else {
                return $this->response("Data with id=$id not found", 404);
            }
        }
        return $this->response("Update error", 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/rest/points/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;
        if ($id > 0) {
            $hlblock = HL\HighloadBlockTable::getById($this->hlbl1)->fetch(); 
            $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
            $entity_data_class = $entity->getDataClass(); 

            $rsData = $entity_data_class::getList(array(
               "select" => ["*"],
               "order" => [],
               "filter" => ["ID" => $id]
            ));
            if ($arData = $rsData->Fetch()){
                if ($result = $entity_data_class::Delete($arData['ID'])) {
                    return $this->response('Data deleted.', 200);
                }
            } else {
                return $this->response("Data with id=$id not found", 404);
            }
        }
        return $this->response("Delete error", 500);
    }
}
	$this->IncludeComponentTemplate();
?>