<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27-Nov-18
 * Time: 1:33 AM
 */
 //####### EXEMPLO DE REQUISIÇÃO CURL PARA POST COM X-WWW-FORM ########
    try{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,API_LOCAL."cadastroCartao");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "API_login=".$login."&".
            "API_pw=".$senha."&".
            "numCartao=".'8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
    }catch(Exception $e){
        echo $e;
        echo "Erro";
    }

