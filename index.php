<?php

require 'model/FillDB.php';
use model\FillDB;

	$DBClass = new FillDB();
	$fullArr = $DBClass->getData();
	$perPage = 20;
	$count = (int)ceil(count($fullArr) / $perPage);
	$data['page'] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$data['array']= array_slice($fullArr, ($data['page'] - 1) * $perPage, $perPage);
	$data['count'] = $count;
			
?>

<html>
	<head>
		<meta charset="utf-8" />
		<title>TEST</title>
		<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	<body>
		<table>
			<thead>
				<tr>
					<td>ИД</td>
					<td> Наименоваине</td>
					<td>Код</td>
					<td>Вес</td>
					<td>Кол-во Москва</td>
					<td>Кол-во Санкт-Петербург</td>
					<td>Кол-во Самара</td>
					<td>Кол-во Саратов</td>
					<td>Кол-во Казань</td>
					<td>Кол-во Новосибирск</td>
					<td>Кол-во Челябинск</td>
					<td>Кол-во ДЛ Челябинск</td>
					<td>Цена Москва</td>
					<td>Цена Санкт-Петербург</td>
					<td>Цена Самара</td>
					<td>Цена Саратов</td>
					<td>Цена Казань</td>
					<td>Цена Новосибирск</td>
					<td>Цена Челябинск</td>
					<td>Цена ДЛ Челябинск</td>
					<td>Взаимозаменяемости</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach($data['array'] as $each) :?>
				<tr>
					<td><?php echo $each['id']; ?></td>
					<td><?php echo $each['name']; ?></td>
					<td><?php echo $each['code']; ?></td>
					<td><?php echo $each['weight']; ?></td>
					<td><?php echo $each['quantity_msk']; ?></td>
					<td><?php echo $each['quantity_spb']; ?></td>
					<td><?php echo $each['quantity_smr']; ?></td>
					<td><?php echo $each['quantity_srv']; ?></td>
					<td><?php echo $each['quantity_kzn']; ?></td>
					<td><?php echo $each['quantity_nsk']; ?></td>
					<td><?php echo $each['quantity_chbk']; ?></td>
					<td><?php echo $each['quantity_bschbk']; ?></td>
					<td><?php echo $each['price_msk']; ?></td>
					<td><?php echo $each['price_spb']; ?></td>
					<td><?php echo $each['price_smr']; ?></td>
					<td><?php echo $each['price_srv']; ?></td>
					<td><?php echo $each['price_kzn']; ?></td>
					<td><?php echo $each['price_nsk']; ?></td>
					<td><?php echo $each['price_chbk']; ?></td>
					<td><?php echo $each['price_bschbk']; ?></td>
					<td><?php echo $each['usage']; ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>		
		<div class="pagination">
		<?php if($data['page'] > 1):?>
			<a href="/?page=1"><<</a>
			<a href="/?page=<?=$data['page'] - 1 ;?>"><</a>
		<?php endif;?>

		<?php if(isset($data['page'])):?>
			<span><?=$data['page'];?> стр</span>
		<?php else:?>
			<span>1 стр</span>
		<?php endif;?>

		<?php if($data['page'] < $data['count']) :?>
			<a href ="/?page=<?=$data['page'] + 1;?>">></a>
			<a href ="/?page=<?=$data['count'];?>">>></a>
		<?php endif;?>
		</div>
	</body>
</html>

