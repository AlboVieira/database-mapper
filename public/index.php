<?php
require_once "../bootstrap.php";


validadata();

function validadata(){
    $mapper = new \Code\System\Mapper\LiliMapper();

    $conn = $mapper->getConn();
    $query = $conn->query(
        "SELECT YEAR(data_entrada) entrada, YEAR(data_saida) saida, nom,data_entrada,data_saida
          from tb_lili order by entrada
          ");

    $data = $query->fetchAll();

    $actives = [];
    $inactives = [];
    foreach($data as $employe){

        $actives[$employe['entrada']][] = $employe['nom'];
        $inactives [$employe['saida']][] = $employe['nom'];
    }

    foreach($actives as $year=>$item){

        $toCompare = $inactives[$year];
        foreach($item as $key => $name){
            foreach ($toCompare as $s ){
                if($s == $name){
                    unset($actives[$year][$key]);
                }
            }
        }
    }

    var_dump($actives);die;

}

function import(){

    $file = fopen("csv.csv","r");

    $mapper = new \Code\System\Mapper\LiliMapper();
    $fields = $mapper->getFields();
    while(! feof($file))
    {
        $current = fgetcsv($file);
        $lili = new \Code\System\Entity\Lili();

        if(!isset($current[0]) || !isset($current[1])){
            continue;
        }

        $lili->setNome($current[0]);

        $dateIni = DateTime::createFromFormat('m/d/Y', $current[1]);
        $dateOut = DateTime::createFromFormat('m/d/Y', $current[2]);

        $lili->setDataEntrada($dateIni->format('Y-m-d'));

        if($dateOut){
            $lili->setDataSaida($dateOut->format('Y-m-d'));
        }else{
            $dateOut = new DateTime();
            $lili->setDataSaida($dateOut->format('Y-m-d'));
        }

        $values = ["'{$lili->getId()}'",  "'{$lili->getNome()}'",
            "'{$lili->getDataEntrada()}'",$lili->getDataSaida()];

        $mapper->insert($lili);
    }


    fclose($file);


}