<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 22-Nov-18
 * Time: 9:35 PM
 */

require_once "../../includes/Constants.php";

$email = $_POST['email'];
$senha = $_POST['senha'];
$nome = $_POST['nome'];



// chama a API com os parametros
try{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,API_LOCAL."cadastroUsuario");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "nome=".$nome."&".
        "email=".$email."&".
        "senha=".$senha);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
}catch(Exception $e){
    echo $e;
    echo "Erro";
}

