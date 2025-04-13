<?php 

try
{
	$db = new PDO("mysql:host=localhost;dbname=peraetki_badiakademi;charset=utf8",'peraetki_erdem','eA5431596417_17*');
	//echo "Veritabanı baglantısı basarili";
}
catch (PDOException $e)
{
	echo $e->getMessage();
}
 ?>
