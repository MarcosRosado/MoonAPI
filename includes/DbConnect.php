<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 14-Sep-18
 * Time: 3:56 PM
 */

class DbConnect{
    private $con;

    function __construct()
    {
    }

    // recebe o banco de dados a ser acessado e retorna uma conexão
    function connect($dns){
        include_once dirname(__FILE__).'/Constants.php';
        try {
            $this->con = new PDO($dns, DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e){
            return null;
        }
        return $this->con;

    }

    // FIXME alterar esse trecho em caso de alteração no servidor
    //se conecta ao banco de dados de usuarios
    function connect_users(){
        include_once dirname(__FILE__).'/Constants.php';
        try {
            $this->con = new PDO('mysql:dbname=claud347_gerenciausers;host=localhost', DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e){
            echo "error connection to database";
            return null;
        }
        return $this->con;
    }


}