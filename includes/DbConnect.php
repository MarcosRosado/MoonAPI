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
    function connect(){
        include_once dirname(__FILE__).'/Constants.php';
        try {
            $this->con = new PDO(DNS, DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e){
            return null;
        }
        return $this->con;

    }



}