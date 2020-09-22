<?php

namespace model;

require 'config/DB.php';
use config\DB;

class FillDB
{
	private const DIR_FILES = 'data'; # директория данных с файлами
	private const FIRST_FILES = 'imports';
	private const SECOND_FILES = 'offers';
	private $cities = [
			  		 'Москва'=> 'msk',
					 'Санкт-Петербург' => 'spb',
					 'Казань' => 'kzn',
					 'Самара' => 'smr',
					 'Саратов' => 'srv',
					 'Новосибирск' => 'nsk',
					 'Деловые линии Челябинск' => 'bschbk', 
					 'Челябинск' => 'chbk'
					];# список коротких имен городов
	private $db;
	private $files = [];
	private $fullArray = [];# Главный массив

	public function __construct()
	{
		$this->db = DB::connector();
	}

	# Получение файлов из директории и  разделение по назначение
	# Возвращается массив 
	private function getFiles()
	{
		$files = [];
		$pattern = '#import.+\.xml#';
		if($handle = opendir(self::DIR_FILES)){
   		while(false !== ($file = readdir($handle))) {
      		if($file != "." && $file != ".."){
					if(preg_match($pattern, $file)){
						 $files[self::FIRST_FILES][] = self::DIR_FILES . '/' . $file;
					}
					else
					{
						 $files[self::SECOND_FILES][] = self::DIR_FILES . '/' . $file;
					}
     			}
			}
		}
		return $files;
	}

	# Создание главного массива и добавление данных из файлов типа import
	private function fillImport()
	{
		foreach($this->files[self::FIRST_FILES] as $file)
		{
			$xml = simplexml_load_file($file);
			$products = $xml->{'Каталог'}->{'Товары'}->{'Товар'}; # массив товаров
			foreach($products as $product) 
			{
				$key = (int)$product->{'Код'}; # поле'code' в качестве ключя подмассива
				if(!array_key_exists($key, $this->fullArray)) #если товар не существует в общем массиве , то он добавляется
				{
					$this->fullArray[$key]['name'] = (string)$product->{'Наименование'};
					$this->fullArray[$key]['weight'] = (string)$product->{'Вес'};
					
					if(isset($product->{'Взаимозаменяемости'})) # добавляется стек Взаимозаменяемости, если таковой имеется в данном товаре
					{
						foreach($product->{'Взаимозаменяемости'}->{'Взаимозаменяемость'} as $interchangeability)
						{
							if(isset($interchangeability))
							{
								if(!isset($this->fullArray[$key]['usage']))
								{
									$this->fullArray[$key]['usage'] = $interchangeability->{'Марка'} . '-' . $interchangeability->{'Модель'} . '-' . $interchangeability->{'КатегорияТС'};  # если первый в стеке
								}
								else
								{
									$this->fullArray[$key]['usage'] .= '|' . $interchangeability->{'Марка'} . '-' . $interchangeability->{'Модель'} . '-' . $interchangeability->{'КатегорияТС'}; # последующие
								}
							}
						}
					}
				}
			}		
		}
	}

	# Добавление к главному массиву данных из файлов типа offer
	private function fillOffers()
	{
		foreach($this->files[self::SECOND_FILES] as $file)
		{
			$xml = simplexml_load_file($file);
			$city = $this->choiceCity($xml->{'Классификатор'}->{'Наименование'}); # получение короткого имени города
			$offers = $xml->{'ПакетПредложений'}->{'Предложения'}->{'Предложение'}; #массив предложений
			foreach($offers as $offer)
			{
				$key = (int)$offer->{'Код'};
				if(array_key_exists($key, $this->fullArray)) #если товар  существует в общем массиве , то добавляется данные
				{	$quantity = (int)$offer->{'Количество'};
					$this->fullArray[$key]['quantity_' . $city] = $quantity < 0 ? 0 : $quantity; # Если данные меньше 0 , добавляется 0
					$price = (int)$offer->{'Цены'}->{'Цена'}[0]->{'ЦенаЗаЕдиницу'};
					$this->fullArray[$key]['price_' . $city] = $price;
				}
			}
		}
	}

	# Выбор города из файла
	private function choiceCity($nameCity)
	{
		foreach($this->cities as $key=>$city)
		{
			$pattern = '#.+(\s?' . $key . '\s?).+#';
			if(preg_match($pattern, $nameCity))
			{
				$choiceCity = $city;
				break;
			}	  
		}			 
		return $choiceCity;
	}
	
	# Метод для определения действий с БД
	private function addDB()
	{	
		$insertArray = []; # Массив добавления в БД
		$updateArray = []; # Массив обновления данных в БД
		$insertSql = ("INSERT INTO `test` (`name`, `code`, `weight`, `quantity_msk`, `quantity_spb`, `quantity_smr`, `quantity_srv`, `quantity_kzn`, `quantity_nsk`, `quantity_chbk`, `quantity_bschbk`, `price_msk`, `price_spb`, `price_smr`, `price_srv`, `price_kzn`, `price_nsk`, `price_chbk`, `price_bschbk`, `usage`) VALUES (:name, :code, :weight, :quantity_msk, :quantity_spb, :quantity_smr, :quantity_srv, :quantity_kzn, :quantity_nsk, :quantity_chbk, :quantity_bschbk, :price_msk, :price_spb, :price_smr, :price_srv, :price_kzn, :price_nsk, :price_chbk, :price_bschbk, :usage)"); # Запрос добавления в БД 

		$updateSql = ("UPDATE `test` SET `name` = :name, `weight` = :weight, `quantity_msk` = :quantity_msk, `quantity_spb` = :quantity_spb, `quantity_smr` = :quantity_smr, `quantity_srv` = :quantity_srv, `quantity_kzn` = :quantity_kzn, `quantity_nsk` = :quantity_nsk, `quantity_chbk` = :quantity_chbk, `quantity_bschbk` = :quantity_bschbk, `price_msk` = :price_msk, `price_spb` = :price_spb, `price_smr` = :price_smr, `price_srv` = :price_srv, `price_kzn` = :price_kzn, `price_nsk` = :price_nsk, `price_chbk` = :price_chbk, `price_bschbk` = :price_bschbk, `usage` = :usage WHERE `id` = :code");# Запрос обновления данных в БД 
		$sql = ("SELECT `id`,`code` FROM `test`"); 
		$db = $this->db->query($sql);
		$result = $db->fetchAll(); # Сырой массив id и code из БД
		if(empty($result)) # если БД пустая - заполнение 
		{
			$this->actionDB($insertSql, $this->fullArray);
		}
		else
		{
			$fetchResult = [];
			foreach($result as $value) # массив вида id=>code
			{
				$fetchResult[$value['id']] = $value['code'];
			}
			foreach($this->fullArray as $key=>$each) # разбирает главный массив
			{
				$is_arr = true; # флаг
				foreach($fetchResult as $fkey=>$value) 
				{
					if($key == $value)
					{
						$updateArray[$fkey] = $each; # подменяет ключ массива 'code' на  'id'
						$is_arr = false;
						continue;
					}
				}
				if($is_arr)
				{
					$insertArray[$key] = $each;	  
				}
			}
			$this->actionDB($updateSql, $updateArray);
			$this->actionDB($insertSql, $insertArray);
		}
	}

	# Заполние БД по определенному дествию
	private function actionDB($sql, $array)
	{
		foreach($array as $key=>$each)
		{
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':name', $each['name'], \PDO::PARAM_STR);
			$stmt->bindValue(':code', $key, \PDO::PARAM_INT);
			isset($each['weight']) ? $stmt->bindValue(':weight', $each['weight'], \PDO::PARAM_STR) : $stmt->bindValue(':weight', 0, \PDO::PARAM_STR);
			isset($each['quantity_msk']) ? $stmt->bindValue(':quantity_msk', $each['quantity_msk'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_msk', 0, \PDO::PARAM_INT);
			isset($each['quantity_spb']) ? $stmt->bindValue(':quantity_spb', $each['quantity_spb'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_spb', 0, \PDO::PARAM_INT);
			isset($each['quantity_smr']) ? $stmt->bindValue(':quantity_smr', $each['quantity_smr'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_smr', 0, \PDO::PARAM_INT);
			isset($each['quantity_srv']) ? $stmt->bindValue(':quantity_srv', $each['quantity_srv'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_srv' , 0, \PDO::PARAM_INT);
			isset($each['quantity_kzn']) ? $stmt->bindValue(':quantity_kzn', $each['quantity_kzn'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_kzn', 0, \PDO::PARAM_INT);
			isset($each['quantity_nsk']) ? $stmt->bindValue(':quantity_nsk', $each['quantity_nsk'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_nsk', 0, \PDO::PARAM_INT);
			isset($each['quantity_chbk']) ? $stmt->bindValue(':quantity_chbk', $each['quantity_chbk'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_chbk', 0, \PDO::PARAM_INT);
			isset($each['quantity_bschbk']) ? $stmt->bindValue(':quantity_bschbk', $each['quantity_bschbk'], \PDO::PARAM_INT) : $stmt->bindValue(':quantity_bschbk', 0, \PDO::PARAM_INT);
			isset($each['price_msk']) ? $stmt->bindValue(':price_msk', $each['price_msk'], \PDO::PARAM_INT) : $stmt->bindValue(':price_msk', 0, \PDO::PARAM_INT);
			isset($each['price_spb']) ? $stmt->bindValue(':price_spb', $each['price_spb'], \PDO::PARAM_INT) : $stmt->bindValue(':price_spb', 0, \PDO::PARAM_INT);
			isset($each['price_smr']) ? $stmt->bindValue(':price_smr', $each['price_smr'], \PDO::PARAM_INT) : $stmt->bindValue(':price_smr', 0, \PDO::PARAM_INT);
			isset($each['price_srv']) ? $stmt->bindValue(':price_srv', $each['price_srv'], \PDO::PARAM_INT) : $stmt->bindValue(':price_srv', 0, \PDO::PARAM_INT);
			isset($each['price_kzn']) ? $stmt->bindValue(':price_kzn', $each['price_kzn'], \PDO::PARAM_INT) : $stmt->bindValue(':price_kzn', 0, \PDO::PARAM_INT);
			isset($each['price_nsk']) ? $stmt->bindValue(':price_nsk', $each['price_nsk'], \PDO::PARAM_INT) : $stmt->bindValue(':price_nsk', 0, \PDO::PARAM_INT);
			isset($each['price_chbk']) ? $stmt->bindValue(':price_chbk', $each['price_chbk'], \PDO::PARAM_INT) : $stmt-bindValue(':price_chbk', 0, \PDO::PARAM_INT);
			isset($each['price_bschbk']) ? $stmt->bindValue(':price_bschbk', $each['price_bschbk'], \PDO::PARAM_INT) : $stmt->bindValue(':price_bschbk', 0, \PDO::PARAM_INT);
			isset($each['usage']) ? $stmt->bindValue(':usage', $each['usage'], \PDO::PARAM_STR) : $stmt->bindValue(':usage', NULL, \PDO::PARAM_STR);
			
			$stmt->execute();
		}
	}
	
	# Метод инициализации
	public function fill()
	{
		$this->files = $this->getFiles();
		$this->fillImport();
		$this->fillOffers();
		$this->addDB();
		return true;
	}
	
	# Получение данных из БД
	public function getData()
	{
		$sql = ("SELECT * FROM `test` ORDER BY `id` DESC");
		$db = $this->db->query($sql);
		return $db->fetchAll();
	}
}
