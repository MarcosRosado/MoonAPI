<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27-Nov-18
 * Time: 1:47 AM
 */

    if(isset($_GET["session"])) {
        session_start();
        if (isset($_SESSION["idUsuario"])) {
            echo "SessionOnline";
        } else {
            echo "SessionOffline";
        }
    }