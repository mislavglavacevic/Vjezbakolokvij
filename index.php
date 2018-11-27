<?php
require 'flight/Flight.php';

Flight::route('/', function(){
    echo 'hello world!';
});

//SELECT
Flight::route('/mjerenja', function(){
    $veza = Flight::db();
	$izraz = $veza->prepare("select sifra, lokacija, datum_mjerenja, temperatura, osoba from mjerenje");
    $izraz->execute();
    echo json_encode($izraz->fetchAll(PDO::FETCH_OBJ));
});

Flight::route('/mjerenja/@id', function($sifra){
	$veza = Flight::db();
	$izraz = $veza->prepare("select sifra, lokacija, datum_mjerenja, temperatura, osoba from mjerenje where sifra=:sifra");
	$izraz->execute(array("sifra" => $sifra));
	echo json_encode($izraz->fetch(PDO::FETCH_OBJ));
});

//INSERT
Flight::route('POST /novoMjerenje', function(){
	$o = json_decode(file_get_contents('php://input'));
	$veza = Flight::db();
	$izraz = $veza->prepare("insert into mjerenje (lokacija, datum_mjerenja, temperatura, osoba) values (:lokacija, :datum_mjerenja, :temperatura, :osoba)");
	$izraz->execute((array)$o);
	echo "OK";
});

//UPDATE
Flight::route('POST /promijeniMjerenje', function(){
	$o = json_decode(file_get_contents('php://input'));
	$veza = Flight::db();
	$izraz = $veza->prepare("update mjerenje set lokacija=:lokacija, datum_mjerenja=:datum_mjerenja, temperatura=:temperatura, osoba=:osoba  where sifra=:sifra;");
	$izraz->execute((array)$o);
	echo "OK";
});

//DELETE
Flight::route('POST /obrisiMjerenje', function(){
	$o = json_decode(file_get_contents('php://input'));
	$veza = Flight::db();
	$izraz = $veza->prepare("delete from mjerenje where sifra=:sifra;");
	$izraz->execute((array)$o);
	echo "OK";
});

//SEARCH
Flight::route('/search/@uvjet', function($uvjet){
	$veza = Flight::db();
	$izraz = $veza->prepare("select sifra, lokacija, datum_mjerenja, temperatura, osoba  from mjerenje where concat(lokacija,' ', osoba) like :uvjet");
	$izraz->execute(array("uvjet" => "%" . $uvjet . "%"));
	echo json_encode($izraz->fetchAll(PDO::FETCH_OBJ));
});

//utility
Flight::map('notFound', function(){
	$poruka=new stdClass();
	$poruka->status="404";
	$poruka->message="Not found";
	echo json_encode($poruka);
 });

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=kolokvij_api;charset=UTF8','root',''));

Flight::start();
