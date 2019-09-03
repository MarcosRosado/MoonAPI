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


        // operação para cadastrar um novo Usuário
        function criarUsuario($nome, $email, $senha){
            // verifica se o Usuario já está cadastrado no sistema
            $stmt = $this->con->prepare('SELECT * FROM users where email = :email');
            $stmt->bindValue(':email', $email);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null){
                    return DUPLICATE;
                }
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }


            $idUser = md5(uniqid(rand(), true));
            // caso o funcionario não esteja no sistema, tenta cadastra-lo
            $stmt = $this->con->prepare('INSERT INTO users (nome,email,senha, idUser) 
            VALUES (:nome,:email, :senha, :idUser)');
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senha);
            $stmt->bindValue(':idUser', $idUser);

            try {
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }


        // pega os dados de login da API a partir dos dados do usuário
        function login($email, $senha)
        {

            //#### esse trecho eh responsavel pro recuperar e comparar a senha contida no banco de dados ####
            $stmt = $this->con->prepare('SELECT senha FROM users WHERE email = :email');
            $stmt->bindValue(':email', $email);
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                echo $e;
                return ERRO_BUSCA;
            }
            $data = null;
            foreach ($stmt as $row) {
                $data = $row;
            }
            // #### se a senha for a mesma, o if entra em ação e realiza o processo de autenticação ####
            if (password_verify($senha, $data[0])) {

                $stmt = $this->con->prepare('SELECT idUser FROM users WHERE email = :email');
                $stmt->bindValue(':email', $email);
                $data = null;
                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo $e;
                    return ERRO_BUSCA;
                }

                foreach ($stmt as $row) {
                    $data = $row;
                }
                if ($data != null) {
                    return $data;
                } else {
                    return ERRO_BUSCA;
                }
            }

            return ERRO_AUTH;
        }


        // operação para cadastrar um novo Usuário
        function cadastrarDevice($idUsuario, $nomeDevice){

            // verifica se o device já está cadastrado no sistema
            $stmt = $this->con->prepare('SELECT * FROM device where nome = :nome AND idUsuario = :idUsuario');
            $stmt->bindValue(':nome', $nomeDevice);
            $stmt->bindValue(':idUsuario', $idUsuario);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null){
                    return DUPLICATE;
                }
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

            // Gera a hash do Device
            $hash = md5(uniqid(rand(), true));
            $display = md5(uniqid(rand(), true));
            $time = mktime();

            // cadastra o device no sistema
            $stmt = $this->con->prepare('INSERT INTO device (nome,idUsuario,HashKey, displayKey, timeCreation) 
            VALUES (:nome,:idUsuario, :hash, :display, :hora)');
            $stmt->bindValue(':nome', $nomeDevice);
            $stmt->bindValue(':idUsuario', $idUsuario);
            $stmt->bindValue(':hash', $hash);
            $stmt->bindValue(':display', $display);
            $stmt->bindValue(':hora', $time);
            try {
                $stmt->execute();
                return $this->getDeviceId($idUsuario,$nomeDevice);
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        function getDeviceId ($idUsuario, $nomeDevice){
            // verifica se o device já está cadastrado no sistema
            $stmt = $this->con->prepare('SELECT HashKey FROM device where nome = :nome AND idUsuario = :idUsuario');
            $stmt->bindValue(':nome', $nomeDevice);
            $stmt->bindValue(':idUsuario', $idUsuario);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null){
                    return $data[0];
                }
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

            return ERRO_BUSCA;
        }

        function getDeviceShareId ($idUsuario, $nomeDevice){
            // verifica se o device já está cadastrado no sistema
            $stmt = $this->con->prepare('SELECT displayKey FROM device where nome = :nome AND idUsuario = :idUsuario');
            $stmt->bindValue(':nome', $nomeDevice);
            $stmt->bindValue(':idUsuario', $idUsuario);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null){
                    return $data[0];
                }
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

            return ERRO_BUSCA;
        }

        function updateDeviceShareId ($idUsuario, $nomeDevice){

            // Atualiza o shareID do dispositivo
            $display = md5(uniqid(rand(), true));

            $stmt = $this->con->prepare('UPDATE device SET displayKey = :displayKey WHERE idUsuario = :idUsuario AND nome = :nomeDevice');
            $stmt->bindValue(':nomeDevice', $nomeDevice);
            $stmt->bindValue(':idUsuario', $idUsuario);
            $stmt->bindValue(':displayKey', $display);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        function inserirDados ( $hashDevice, $valor, $tipoSensor, $tag){



            // insere os dados no sistema
            $time = mktime();
            $stmt = $this->con->prepare('INSERT INTO dados (HashKey_device, valor, tipoSensor, time, tag)
            VALUES (:hashDevice, :valor, :tipoSensor, :hora, :tag)');
            $stmt->bindValue(':hashDevice', $hashDevice);
            $stmt->bindValue(':valor', $valor);
            $stmt->bindValue(':tipoSensor', $tipoSensor);
            $stmt->bindValue(':hora', $time);
            $stmt->bindValue(':tag', $tag);

            try {
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        function getNameUser ($idUsuario){

            // verifica se o device já está cadastrado no sistema
            $stmt = $this->con->prepare('SELECT nome FROM users where idUser = :idUsuario');
            $stmt->bindValue(':idUsuario', $idUsuario);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null){
                    return $data[0];
                }
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

            return ERRO_BUSCA;
        }

        function listarDispositivos($idUsuario){
            $stmt = $this->con->prepare('SELECT * FROM device WHERE idUsuario = :idUsuario ');
            $stmt->bindValue(':idUsuario', $idUsuario);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $dt = new DateTime("@$row[5]");
                    $dt = $dt->setTimezone(new DateTimeZone("America/Sao_Paulo"))->format('d/m/Y H:i:s');
                    $tmp = array(
                        'nome' => $row[1],
                        'HashKey' => $row[3],
                        'displayKey' => $row[4],
                        'timeCreation' => $dt);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return ERRO_BUSCA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        function listarDadosDia($shareId){
            $time = time();
            $time = $time - 24*60*60;

            $stmt = $this->con->prepare('SELECT dados.id, HashKey_device, valor, tipoSensor, time, tag FROM dados,device 
                                                   WHERE dados.HashKey_device = device.HashKey AND device.displayKey = :shareId AND dados.time > :time');
            $stmt->bindValue(':shareId', $shareId);
            $stmt->bindValue(':time', $time);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    //$dt = new DateTime("@$row[4]");
                    //$dt = $dt->setTimezone(new DateTimeZone("America/Sao_Paulo"))->format('d/m/Y H:i:s');
                    $tmp = array(
                        'valor' => $row[2],
                        'tipoSensor' => $row[3],
                        'timeCreation' => $row[4],
                        'tag' => $row[5]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return ERRO_BUSCA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        function listarDados($shareId){
            ini_set('memory_limit', '1024M'); // or you could use 1G
            $stmt = $this->con->prepare('SELECT dados.id, HashKey_device, valor, tipoSensor, time, tag FROM dados,device 
                                                   WHERE dados.HashKey_device = device.HashKey AND device.displayKey = :shareId');
            $stmt->bindValue(':shareId', $shareId);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    //$dt = new DateTime("@$row[4]");
                    //$dt = $dt->setTimezone(new DateTimeZone("America/Sao_Paulo"))->format('d/m/Y H:i:s');
                    $tmp = array(
                        'valor' => $row[2],
                        'tipoSensor' => $row[3],
                        'timeCreation' => $row[4],
                        'tag' => $row[5]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return ERRO_BUSCA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        function getDataBySensor($shareId, $name){
            $stmt = $this->con->prepare('SELECT dados.id, HashKey_device, valor, tipoSensor, time, tag FROM dados,device 
                                                   WHERE dados.HashKey_device = device.HashKey AND device.displayKey = :shareId AND dados.tag = :nome');
            $stmt->bindValue(':shareId', $shareId);
            $stmt->bindValue(':nome', $name);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    //$dt = new DateTime("@$row[4]");
                    //$dt = $dt->setTimezone(new DateTimeZone("America/Sao_Paulo"))->format('d/m/Y H:i:s');
                    $tmp = array(
                        'valor' => $row[2],
                        'tipoSensor' => $row[3],
                        'timeCreation' => $row[4],
                        'tag' => $row[5]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return ERRO_BUSCA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        function getSensorsByType($shareId, $type){
            $stmt = $this->con->prepare('SELECT DISTINCT dados.tag FROM dados,device 
                                                   WHERE dados.HashKey_device = device.HashKey AND device.displayKey = :shareId AND dados.tipoSensor = :type ORDER BY LENGTH(tag), tag');
            $stmt->bindValue(':shareId', $shareId);
            $stmt->bindValue(':type', $type);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    //$dt = new DateTime("@$row[4]");
                    //$dt = $dt->setTimezone(new DateTimeZone("America/Sao_Paulo"))->format('d/m/Y H:i:s');
                    $tmp = array(
                        'tag' => $row[0]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return ERRO_BUSCA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

    }


