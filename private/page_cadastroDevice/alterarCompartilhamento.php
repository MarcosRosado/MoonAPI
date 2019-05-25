<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11-Dec-18
 * Time: 1:12 AM
 * cadastro de produtos
 */

require_once "../../includes/Constants.php";

// recupera os dados da sessão para utilizar a API
session_start();

$idUsuario = $_SESSION['idUsuario'];
$deviceID = $_GET['device'];

// chama a API com os parametros
try{
    // recupera os dados da API
    $ch = curl_init(API_LOCAL."updateDeviceShareId?idUsuario=".$idUsuario."&nomeDevice=".$deviceID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1 );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    $response = curl_exec($ch);
    curl_close($ch);
    // converte o resultado da API para json
    $output = json_decode($response, true);

    // retorna os dados da API para o ajax
    $response = json_encode($output);
    echo $response;
}catch(Exception $e){
    echo $e;
    echo "Erro";
}
