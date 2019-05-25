<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27-Nov-18
 * Time: 3:07 AM
 */
require_once "../../includes/Constants.php";

    if(isset($_GET["session"])) {

        session_start();
        if (isset($_SESSION["idUsuario"])) {
            try {
                // recupera os dados da API
                $ch = curl_init(API_LOCAL . "getNomeUsuario?idUsuario=" . $_SESSION["idUsuario"]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                // converte o resultado da API para json
                $output = json_decode($response, true);

                // salva o nome de usuario na sessão
                $_SESSION["nomeUsuario"] = $output["message"];

                $response = null;

                $data = array("idUsuario" => $_SESSION["idUsuario"], "status" => "online", "nomeUsuario" => $output["message"]);
                $response = json_encode($data, true);
                echo $response;


            } catch (Exception $e) {
                $data = array("status" => "offline");
                $response = json_encode($data, true);
                echo $response;
            }
        }
        else{
            $data = array("status" => "offline");
            $response = json_encode($data, true);
            echo $response;
        }
    }