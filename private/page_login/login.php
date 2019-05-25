<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 22-Nov-18
 * Time: 9:35 PM
 */

    require_once "../../includes/Constants.php";

    $login = $_GET['login'];
    $senha = $_GET['senha'];


    try {
        // recupera os dados da API
        $ch = curl_init(API_LOCAL."login?email=".$login."&senha=".$senha);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1 );
        $response = curl_exec($ch);
        curl_close($ch);
        // converte o resultado da API para json
        $output = json_decode($response, true);


        // inicio de sessão e grava os dados do usuário
        session_start();
        if($output["response"] == 200) {
                $_SESSION["idUsuario"] = $output["message"];
        }

        // retorna os dados da API para o ajax
        $response = json_encode($output);
        echo $response;

    }catch(Exception $e){
        echo "Erro";
        echo $e;
    }

