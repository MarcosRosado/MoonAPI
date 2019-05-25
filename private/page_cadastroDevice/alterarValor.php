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
$login = $_SESSION["usuario"];
$senha = $_SESSION["senha"];

// recebe os parametros do post
$valor = $_POST["novoValor"];
$idProduto = $_POST["idProduto"];



// chama a API com os parametros
try{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,API_LOCAL."alterarValorProduto");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "API_login=".$login."&".
        "API_pw=".$senha."&".
        "novoValor=".$valor."&".
        "idProduto=".$idProduto);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
}catch(Exception $e){
    echo $e;
    echo "Erro";
}
