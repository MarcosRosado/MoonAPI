<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 14-Sep-18
 * Time: 4:18 PM
 */
    require_once 'DbConnect.php';
    class DbOperations{
        private $con;

        // se conecta ao banco de dados
        function __construct()
        {
            $db = new DbConnect();
            $this->con = $db->connect();

        }

        /*
        // altera a visibilidade de um produto
        function alterarVisiblidadeProduto ($idProduto, $novaVisibilidade){
            $stmt = $this->con->prepare('UPDATE produto SET visibilidade = :visibilidade WHERE idProduto = :idProduto');
            $stmt->bindValue(':idProduto', $idProduto);
            $stmt->bindValue(':visibilidade', $novaVisibilidade);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        */
    }


