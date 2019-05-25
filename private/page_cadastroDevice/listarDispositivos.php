<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 28-Nov-18
 * Time: 1:38 AM
 */
require_once "../../includes/Constants.php";

session_start();
$idUsuario = $_SESSION["idUsuario"];

if(isset($_GET["dispositivos"])){
    try {
        // recupera os dados da API
        $ch = curl_init(API_LOCAL."getDispositivos?idUsuario=".$idUsuario);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1 );
        $response = curl_exec($ch);
        curl_close($ch);
        // converte o resultado da API para json
        $output = json_decode($response, true);

        // retorna os dados da API para o ajax
        $response = json_encode($output);
        echo $response;

    }catch(Exception $e){
        echo "Erro";
        echo $e;
    }





}