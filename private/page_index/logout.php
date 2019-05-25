<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27-Nov-18
 * Time: 2:19 AM
 */
// termina e limpa uma sessão
    session_start();
    $_SESSION = array();
    session_destroy();
    echo "sucesso";