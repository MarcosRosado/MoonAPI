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
        private $con_users;
        private $dns;
        private $API_login;
        private $API_pw;

        // se conecta ao banco de dados
        function __construct($API_login, $API_pw)
        {
            $this->API_login = $API_login;
            $this->API_pw = $API_pw;
            $db = new DbConnect();

            // conecta ao db de users
            $this->con_users = $db->connect_users();

            //recupera o DNS da empresa acessada
            $this->dns = $this->getDNS($this->API_login, $this->API_pw);

            // acessa do DB da empresa especifica
            $this->con = $db->connect($this->dns);

        }

        // returna o DNS do banco de dados
        function checkDNS(){
            return $this->dns;
        }

        // recupera o dns da empresa acessada
        private function getDNS($API_login, $API_pw){
            $stmt = $this->con_users->prepare('SELECT connectiondb, acess FROM users  WHERE email = :email AND senha = :senha ');

            $stmt->bindValue(':email', $API_login);
            $stmt->bindValue(':senha', $API_pw);
            $data = null;

            try{
                $stmt->execute();
            }
            catch (PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

            foreach ($stmt as $row){
                $data = $row;
            }
            // verifica se houve retorno, e se o usuário está autorizado a efetuar o acesso
            if ($data == null || $data['acess'] == false ){
                return null;
            }
            else{
                return $data['connectiondb'];
            }

        }


        function criarEmpresa($email, $nome, $senha){
            // verifica se a empresa já está cadastrada no sistema
            $stmt = $this->con_users->prepare('SELECT * FROM users where email = :email');
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

            // caso a empresa não esteja no sistema, tenta cadastra-la
            $stmt = $this->con_users->prepare('INSERT INTO users (nome,email,senha) 
            VALUES (:nome,:email, :senha)');
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senha);
            try {
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        function alterarAcessoEmpresa($empresa ,$acess){
            // verifica se a empresa já está cadastrada no sistema
            $stmt = $this->con_users->prepare('UPDATE users SET acess = :acess where email = :email');
            $stmt->bindValue(':email', $empresa);
            $stmt->bindValue(':acess', $acess);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }



        // operação para cadastrar um novo funcionário
        function criarFuncionario($nome, $cpf, $telefone, $email, $login, $status, $endereco, $empresa, $pw){
            // verifica se o funcionario já está cadastrado no sistema
            $stmt = $this->con_users->prepare('SELECT * FROM pessoal where login = :login');
            $stmt->bindValue(':login', $login);
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

            // caso o funcionario não esteja no sistema, tenta cadastra-lo
            $stmt = $this->con_users->prepare('INSERT INTO pessoal (cpf, nome,email, telefone, empresa, login, senha, status, endereco) 
            VALUES (:cpf, :nome,:email, :telefone, :empresa, :login, :senha, :status, :endereco)');
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':login', $login);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':endereco', $endereco);
            $stmt->bindValue('empresa', $empresa);
            $stmt->bindValue(':senha', $pw);

            try {
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }
            catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        // cadastra um novo produto no sistema
        function cadastrarProduto($nomeProduto, $precoProduto, $categoria){
            $stmt = $this->con->prepare('SELECT * FROM produto WHERE nomeProduto = :nome');
            $stmt->bindValue(':nome', $nomeProduto);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null){
                    return DUPLICATE;
                }
            }catch (PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }

            $stmt = $this->con->prepare('INSERT INTO produto (nomeProduto, precoProduto,  Categoria) VALUES (:nome,:preco, :categoria)');
            $stmt->bindValue(':nome', $nomeProduto);
            $stmt->bindValue(':preco', $precoProduto);
            $stmt->bindValue(':categoria', $categoria);
            try{
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }catch (PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        //recupera o valor[0] e categoria[1] do produto
        function getValorCategoriaProduto($idProduto){
            $stmt = $this->con->prepare('SELECT precoProduto,Categoria FROM produto WHERE idProduto = :idProduto');
            $stmt->bindValue(':idProduto', $idProduto);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if($data != null)
                    return $data;
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }
        }

        // insere uma nova mesa no sistema
        function criarMesa($numeroMesa){
            $stmt = $this->con->prepare('SELECT * FROM mesas WHERE numeroMesa = :numero');
            $stmt->bindValue(':numero', $numeroMesa);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null){
                    return DUPLICATE;
                }
            }catch (PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
            $stmt = $this->con->prepare('INSERT INTO mesas (numeroMesa) VALUES (:numero)');
            $stmt->bindValue(':numero', $numeroMesa);
            try{
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }catch (PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        //atualiza o status da mesa
        function alterarStatusMesa ($numeroMesa, $novoStatus){
            $stmt = $this->con->prepare('UPDATE mesas SET MesaStatus = :novoStatus WHERE numeroMesa = :numero');
            $stmt->bindValue(':numero', $numeroMesa);
            $stmt->bindValue(':novoStatus', $novoStatus);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        // insere um novo cartão no sistema
        function criarCartao ($numCartao){
            $stmt = $this->con->prepare('INSERT INTO cartoes (numCartao) VALUES (:numCartao)');
            $stmt->bindValue(':numCartao', $numCartao);
            try{
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        // atualiza o status do cartao;
        function  updateStatusCartao($numCartao, $status){
            $stmt = $this->con->prepare('UPDATE cartoes SET statusCartao = :status WHERE numCartao = :numCartao');
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':numCartao', $numCartao);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch (PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        function getStatusCartao($numCartao){
            $stmt = $this->con->prepare('SELECT statusCartao FROM cartoes WHERE numCartao = :numCartao');
            $stmt->bindValue(':numCartao', $numCartao);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null)
                    return $data[0];
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

        }
        function checkMesa($numMesa){
            $stmt = $this->con->prepare('SELECT * FROM mesas WHERE numeroMesa = :numMesa');
            $stmt->bindValue(":numMesa", $numMesa);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null)
                    return $data[0];
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

        }


        // cria uma nova comanda no sistema
        function criarComanda ($idFuncionario, $dataPedido, $numMesa, $numCartao){
            if($this->getStatusCartao($numCartao) != "Livre")
                return CARTAO_OCUPADO;

            if($this->checkMesa($numMesa) == ERRO_BUSCA )
                return ERRO_BUSCA;

            $stmt = $this->con->prepare('INSERT INTO comanda (Funcionarios_idFuncionarios, dataPedido, numMesa, numCartao) 
                                        VALUES (:idFuncionario, :dataPedido, :numMesa, :numCartao) ');
            $stmt->bindValue(':idFuncionario', $idFuncionario);
            $stmt->bindValue(':dataPedido', $dataPedido);
            $stmt->bindValue(':numMesa', $numMesa);
            $stmt->bindValue(':numCartao', $numCartao);
            try{
                $stmt->execute();
                $this->alterarStatusMesa($numMesa, "Ocupada");
                // o cartão fica ocupado até que a comanda seja fechada
                $this->updateStatusCartao($numCartao,"Ocupado");
                return CADASTRADO_COM_SUCESSO;
            }
            catch(PDOException $e){
                echo $e;
                $this->con->rollBack();
                return ERRO_AO_CADASTRAR;
            }
        }

        // recupera a comanda cujo cartão está vinculado no momento (como apenas uma comanda deve estar aberta por vez com o cartão
        //essa função sempre irá retornar apenas um idComanda)
        function getIdComanda ($numCartao){
            $stmt = $this->con->prepare('SELECT idComanda FROM comanda WHERE ComandaStatus = "Aberto" AND numCartao = :numCartao ');
            $stmt->bindValue(':numCartao', $numCartao);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null)
                    return $data[0];
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }
        }

        // recupera o id da comanda a partir do numero do pedido
        function getIdComandaPedido($numPedido){
            $stmt = $this->con->prepare('SELECT Comanda_idComanda FROM produtos_nos_pedidos WHERE idItemComanda = :numPedido ');
            $stmt->bindValue(':numPedido', $numPedido);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null)
                    return $data[0];
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }
        }


        // atualiza o valor da comanda sempre que um pedido for anexado a ela, e já tiver sido feito pela cozinha.
        function updateValorComanda($valor, $idComanda){
            $stmt = $this->con->prepare('UPDATE comanda SET valorTotal = valorTotal + :valor WHERE idComanda = :idComanda');
            $stmt->bindValue(':idComanda', $idComanda);
            $stmt->bindValue(':valor', $valor);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        // atualiza o valor da comanda sempre que um pedido for anexado a ela, e já tiver sido feito pela cozinha.
        function updateValorComandaCancelamento($valor, $idComanda){
            $stmt = $this->con->prepare('UPDATE comanda SET valorTotal = valorTotal - :valor WHERE idComanda = :idComanda');
            $stmt->bindValue(':idComanda', $idComanda);
            $stmt->bindValue(':valor', $valor);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        // insere um item na comanda
        function inserirPedidoComanda($numCartao, $idProduto, $hora, $observacoes, $valorAdicionais){
            $valor = $this->getValorCategoriaProduto($idProduto)[0];
            $localProducao = $this->getValorCategoriaProduto($idProduto)[1];
            $comandaID = $this->getIdComanda($numCartao);
            if($comandaID == ERRO_BUSCA){
                return ERRO_AO_CADASTRAR;
            }
            $stmt = $this->con->prepare('INSERT INTO produtos_nos_pedidos (Comanda_idComanda, Produto_idProduto, hora, ValorTotalProduto, localProducao, PedidoStatus, Observacoes, Valor_de_adicionais)
                                        VALUES (:idComanda, :idProduto, :hora, :valor, :localProducao, :statusPedido, :observacoes, :valorDeAdicionais)');
            $stmt->bindValue('idComanda', $comandaID);
            $stmt->bindValue(':idProduto', $idProduto);
            $stmt->bindValue(':hora', $hora);
            $stmt->bindValue(':localProducao', $localProducao);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':valorDeAdicionais', $valorAdicionais);
            $valor = (float) $valor +  (float) $valorAdicionais;
            $stmt->bindValue(':valor', $valor);

            if($localProducao == "Cozinha")
                $stmt->bindValue(':statusPedido', "Aberto");
            else {
                $stmt->bindValue(':statusPedido', "Fechado");
                $this->updateValorComanda($valor,$comandaID);
            }



            try{
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }
        }

        // recupera o valor do item da comanda
        function getValorItemComanda($idItemComanda){
            $stmt = $this->con->prepare('SELECT ValorTotalProduto FROM produtos_nos_pedidos WHERE idItemComanda = :id ');
            $stmt->bindValue(':id', $idItemComanda);
            try{
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null)
                    return $data[0];
                else
                    return ERRO_BUSCA;
            }catch(PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }
        }

        // recupera o status do pedido na comanda (aberto, fechado)
        function getStatusPedido($idItemComanda){
            $stmt = $this->con->prepare('SELECT PedidoStatus FROM produtos_nos_pedidos WHERE idItemComanda = :idItem');
            $stmt->bindValue(':idItem', $idItemComanda);
            $stmt->execute();
            $data = null;
            foreach ($stmt as $row){
                $data = $row;
            }
            if ($data != null) {
                return $data[0];

            }

            else
                return ERRO_BUSCA;
        }

        function getCartaoIdItemComanda($idItemComanda){
            $stmt = $this->con->prepare("SELECT numCartao, numMesa FROM comanda, produtos_nos_pedidos WHERE comanda.idComanda = produtos_nos_pedidos.Comanda_idComanda AND idItemComanda = :idItemComanda");
            $stmt->bindValue(":idItemComanda", $idItemComanda);
            $stmt->execute();
            $data = null;
            foreach ($stmt as $row){
                $data = $row;
            }
            if ($data != null) {
                return $data;

            }

            else
                return ERRO_BUSCA;
        }

        function getMesaNumCartao($numCartao){
            $stmt = $this->con->prepare('SELECT numMesa FROM comanda WHERE numCartao = :numCartao');
            $stmt->bindValue(":numCartao", $numCartao);
            $stmt->execute();
            $data = null;
            foreach ($stmt as $row){
                $data = $row;
            }
            if ($data != null) {
                return $data[0];

            }

            else
                return ERRO_BUSCA;
        }

        function salvarNotificacao($userName, $time, $message){
            $stmt = $this->con->prepare('INSERT INTO notification (userNotification, timeNotification, messageNotification)
                                        VALUES (:userNotification, :timeNotification, :messageNotification)');
            $stmt->bindValue(':userNotification', $userName);
            $stmt->bindValue(':timeNotification', $time);
            $stmt->bindValue(':messageNotification', $message);


            try{
                $stmt->execute();
                return CADASTRADO_COM_SUCESSO;
            }catch(PDOException $e){
                echo $e;
                return ERRO_AO_CADASTRAR;
            }

        }

        function recuperNotificacao($userName){

            $stmt = $this->con->prepare('SELECT * FROM notification WHERE userNotification=:userName ORDER BY timeNotification DESC');
            $stmt ->bindValue(':userName', $userName);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $nomeProduto = $this->getNomeProduto($row[2]);
                    $tmp = array(
                        'timeNotification' => $row[2],
                        'messageNotification' => $row[3]);

                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return SEM_PEDIDOS_EM_ABERTO;

            }catch (PDOException $e){
                echo $e;
                return false;
            }

        }


        //conclui um item da comanda (cozinha) atualiza o valor da comanda e altera o status para concluido
        function concluirPedido($idItemComanda){
            //recupera o id da comanda a partir do id do pedido
            $idComanda = $this->getIdComandaPedido($idItemComanda);
            // verifica se o pedido já estava fechado para evitar duplicação no valor
            if($this->getStatusPedido($idItemComanda) == "Fechado")
                return UPDATE_ERROR;

              // fecha o pedido e atualiza o valor
            $this->updateValorComanda($this->getValorItemComanda($idItemComanda), $idComanda);
            $stmt = $this->con->prepare('UPDATE produtos_nos_pedidos SET PedidoStatus = "Fechado" WHERE idItemComanda = :idItemComanda');
            $stmt->bindParam(':idItemComanda', $idItemComanda);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch (PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        //conclui um item da comanda (cozinha) atualiza o valor da comanda e altera o status para concluido
        function cancelarPedido($idItemComanda){
            //recupera o id da comanda a partir do id do pedido
            $idComanda = $this->getIdComandaPedido($idItemComanda);
            // verifica se o pedido já estava fechado para evitar duplicação no valor
            if($this->getStatusPedido($idItemComanda) == "Cancelado")
                return UPDATE_ERROR;

            if($this->getStatusPedido($idItemComanda) == "Fechado")
                $this->updateValorComandaCancelamento($this->getValorItemComanda($idItemComanda), $idComanda);

            // fecha o pedido e atualiza o valor
            $stmt = $this->con->prepare('UPDATE produtos_nos_pedidos SET PedidoStatus = "Cancelado" WHERE idItemComanda = :idItemComanda');
            $stmt->bindParam(':idItemComanda', $idItemComanda);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch (PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        // atualiza o status da mesa dependendo se ela possui alguma comanda em aberto ou não
        function updateStatusMesa($numMesa){
            $stmt = $this->con->prepare('SELECT * FROM comanda WHERE ComandaStatus = "Aberto" AND numMesa = :numMesa ');
            $stmt->bindValue(':numMesa', $numMesa);
            $stmt->execute();
            $data = null;
            foreach ($stmt as $row){
                $data = $row;
            }
            if ($data == null) {
                $stmt = $this->con->prepare('UPDATE mesas SET MesaStatus = "Livre" WHERE numeroMesa = :numMesa');
                $stmt->bindValue(':numMesa', $numMesa);
                $stmt->execute();
                return true;

            }
            else return false;

        }

        // recupera a mesa cuja comanda está vinculada
        function getMesaComanda($idComanda){
            $stmt = $this->con->prepare('SELECT numMesa FROM comanda WHERE idComanda = :idComanda');
            $stmt->bindValue(':idComanda', $idComanda);
            try {
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null) {
                    return $data[0];

                }else
                    return ERRO_BUSCA;
            }catch (PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }

        }

        // verifica se a comanda possui pedidos em aberto, impedindo seu fechamento (deve-se cancelar pedidos não feitos para fecha-la)
        function verificarPedidosAberto($idComanda){
            $stmt = $this->con->prepare('SELECT * FROM produtos_nos_pedidos WHERE PedidoStatus = "Aberto" AND Comanda_idComanda = :idComanda');
            $stmt->bindParam(':idComanda', $idComanda);
            try {
                $stmt->execute();
                $data = null;
                foreach ($stmt as $row){
                    $data = $row;
                }
                if ($data != null) {
                    return true;

                }else
                    return false;
            }catch (PDOException $e){
                echo $e;
                return ERRO_BUSCA;
            }
        }

        // fecha a comanda e suas dependencias
        function fecharComanda($numeroCartao){
            $idComanda = $this->getIdComanda($numeroCartao);

            // verifica se a comanda não possui pedidos em aberto
            if($this->verificarPedidosAberto($idComanda))
                return FECHAMENTO_ERRO;

            // fecha a comanda
            $stmt = $this->con->prepare('UPDATE comanda SET ComandaStatus = "Fechado" WHERE idComanda = :idComanda');
            $stmt->bindValue(':idComanda', $idComanda);
            try {
                $stmt->execute();

                // atualiza o status da mesa se não houver mais nenhuma comanda vinculada a ela
                $numMesa = $this->getMesaComanda($idComanda);

                //
                if($numMesa != ERRO_BUSCA) {
                    $this->updateStatusMesa($this->getMesaComanda($idComanda));
                }

                // atualiza o status do cartão referente a comanda sendo fecahda
                $this->updateStatusCartao($numeroCartao, "Livre");

                return FECHAMENTO_SUCESS;
            }catch(PDOException $e){
                return FECHAMENTO_ERRO;
            }
        }

        // retorna todos os pedidos em aberto (cozinha)
        function getPedidosAberto($localProducao){
            $stmt = $this->con->prepare('SELECT * FROM produtos_nos_pedidos WHERE PedidoStatus = "Aberto" AND localProducao=:localProducao ORDER BY hora');
            $stmt ->bindValue(':localProducao', $localProducao);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $nomeProduto = $this->getNomeProduto($row[2]);
                    $tmp = array(
                        'idItemComanda' => $row[0],
                        'Comanda_idComanda' => $row[1],
                        'Produto_idProduto' => $nomeProduto[0],
                        'hora' => $row[3],
                        'PedidoStatus' => $row[4],
                        'ValorTotalProduto' => $row[5],
                        'Valor_de_adicionais' => $row[6],
                        'Observacoes' => $row[7]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return SEM_PEDIDOS_EM_ABERTO;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        // pega o nome do produto pelo seu id
        function getNomeProduto($id_produto){
            $stmt = $this->con->prepare('SELECT nomeProduto FROM produto WHERE idProduto=:idProduto');
            $stmt->bindValue(':idProduto', $id_produto);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                        return $row;
                }

            }catch (PDOException $e){
                echo $e;
                return false;
            }

        }


        function getPedidoPorId($idItemComanda){
            $stmt = $this->con->prepare('SELECT idItemComanda,Comanda_idComanda,Produto_idProduto,hora,PedidoStatus,ValorTotalProduto,Valor_de_adicionais,Observacoes  FROM produtos_nos_pedidos WHERE idItemComanda = :idComanda');
            $stmt->bindValue(':idComanda', $idItemComanda);
            try{
                $stmt->execute();
                $tmp = array();
                foreach ($stmt as $row){
                    $nomeProduto = $this->getNomeProduto($row[2]);
                    $tmp = array(
                        'idItemComanda' => $row[0],
                        'Comanda_idComanda' => $row[1],
                        'Produto_idProduto' => $nomeProduto[0],
                        'hora' => $row[3],
                        'PedidoStatus' => $row[4],
                        'ValorTotalProduto' => $row[5],
                        'Valor_de_adicionais' => $row[6],
                        'Observacoes' => $row[7]);
                }
                if ( !empty($tmp)) {
                    return $tmp;
                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }

        }


        // recupera os pedidos de uma comanda pelo numero do cartao
        function getPedidosComanda($numCartao){
            $idComanda = $this->getIdComanda($numCartao);
            $stmt = $this->con->prepare('SELECT idItemComanda,Comanda_idComanda,Produto_idProduto,hora,PedidoStatus,ValorTotalProduto,Valor_de_adicionais,Observacoes  FROM produtos_nos_pedidos, comanda WHERE Comanda_idComanda = :idComanda AND ComandaStatus = "Aberto" AND Comanda_idComanda = idComanda');
            $stmt->bindValue(':idComanda', $idComanda);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $nomeProduto = $this->getNomeProduto($row[2]);
                    $tmp = array(
                        'idItemComanda' => $row[0],
                        'Comanda_idComanda' => $row[1],
                        'Produto_idProduto' => $nomeProduto[0],
                        'hora' => $row[3],
                        'PedidoStatus' => $row[4],
                        'ValorTotalProduto' => $row[5],
                        'Valor_de_adicionais' => $row[6],
                        'Observacoes' => $row[7]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }

        }

        // recupera o nome de um funcionario pelo seu id
        function getNomeFuncionario($idFuncionario){
            $stmt = $this->con_users->prepare('SELECT nome  FROM pessoal WHERE ID = :idFuncionario');
            $stmt->bindValue(':idFuncionario', $idFuncionario);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    return $row;
                }

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        // recupera o nome de um funcionario pelo seu id
        function getLoginFuncionario($idFuncionario){
            $stmt = $this->con_users->prepare('SELECT login  FROM pessoal WHERE ID = :idFuncionario');
            $stmt->bindValue(':idFuncionario', $idFuncionario);
            try{
                $stmt->execute();
                foreach ($stmt as $row){
                    return $row;
                }

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }


        // recupera o nome de um funcionario pelo seu id
        function getIdFuncionario($userLogin){
            $stmt = $this->con_users->prepare('SELECT ID  FROM pessoal WHERE login = :userLogin');
            $stmt->bindValue(':userLogin', $userLogin);
            try{
                $stmt->execute();
                foreach ($stmt as $row){
                    return $row;
                }

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        // recupera  todas as comandas em aberto
        function getComandasAberto(){
            $stmt = $this->con->prepare('SELECT idComanda, Funcionarios_idFuncionarios,dataPedido,valorTotal,numMesa,numCartao FROM comanda WHERE ComandaStatus = "Aberto"');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $nomeFuncionario = $this->getNomeFuncionario($row[1]);
                    $tmp = array(
                        'idComanda' => $row[0],
                        'idFuncionario' => $nomeFuncionario["nome"],
                        'dataPedido' => $row[2],
                        'valorTotal' => round($row[3],2),
                        'numMesa' => $row[4],
                        'numCartao' => $row[5]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }
        function getIdFuncionarioComanda($numCartao){
            $stmt = $this->con->prepare('SELECT idComanda, Funcionarios_idFuncionarios,dataPedido,valorTotal,numMesa,numCartao FROM comanda WHERE numCartao = :numCartao AND ComandaStatus="Aberto"');
            $stmt->bindValue(":numCartao", $numCartao);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'idFuncionario' => $row[1]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }

        }

        function getDadosComanda($numCartao){
            $stmt = $this->con->prepare('SELECT idComanda, Funcionarios_idFuncionarios,dataPedido,valorTotal,numMesa,numCartao FROM comanda WHERE numCartao = :numCartao AND ComandaStatus="Aberto"');
            $stmt->bindValue(":numCartao", $numCartao);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $nomeFuncionario = $this->getNomeFuncionario($row[1]);
                    $tmp = array(
                        'idComanda' => $row[0],
                        'idFuncionario' => $nomeFuncionario["nome"],
                        'dataPedido' => $row[2],
                        'valorTotal' => round($row[3],2),
                        'numMesa' => $row[4],
                        'numCartao' => $row[5]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }


        // recupera todas as mesas da empresa
        function getMesas(){
            $stmt = $this->con->prepare('SELECT idMesas, numeroMesa, MesaStatus FROM mesas ORDER BY numeroMesa');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'idMesa' => $row[0],
                        'numeroMesa' => $row[1],
                        'MesaStatus' => $row[2]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        // recupera todos os cartões da empresa
        function getCartoes(){
            $stmt = $this->con->prepare('SELECT numCartao, statusCartao FROM cartoes ORDER BY numCartao');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'numCartao' => $row[0],
                        'statusCartao' => $row[1]);
                    array_push($data, $tmp);
                }
                if ( !empty($data)) {
                    return $data;

                }else
                    return COMANDA_VAZIA;

            }catch (PDOException $e){
                echo $e;
                return false;
            }
        }

        // lista todos os funcionarios
        function listarPessoal($login){
            $empresa = $this->getEmpresaUsuario($login);
            $stmt = $this->con_users->prepare('SELECT * FROM pessoal WHERE empresa = :empresa ORDER BY status DESC');
            $stmt->bindValue(":empresa", $empresa);
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'nome' => $row[2],
                        'email' => $row[3],
                        'login' => $row[4],
                        'telefone' => $row[6],
                        'cpf' => $row[7],
                        'endereco' => $row[8],
                        'status' => $row[9]);
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


        // lista todos as empresas
        function listarEmpresas(){
            $stmt = $this->con_users->prepare('SELECT * FROM users');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'email' => $row[0],
                        'nome' => $row[1],
                        'acess' => $row[3]);
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

        // lista todos os produtos
        function listarProdutos(){
            $stmt = $this->con->prepare('SELECT * FROM produto');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'idProduto' => $row[0],
                        'nomeProduto' => $row[1],
                        'precoProduto' => $row[2],
                        'Categoria' => $row[3],
                        'visibilidade' => $row[4]);
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

        function listarProdutosApp(){
            $stmt = $this->con->prepare('SELECT * FROM produto WHERE visibilidade = true ');
            try{
                $stmt->execute();
                $data = [];
                foreach ($stmt as $row){
                    $tmp = array(
                        'idProduto' => $row[0],
                        'nomeProduto' => $row[1],
                        'precoProduto' => $row[2],
                        'Categoria' => $row[3],
                        'visibilidade' => $row[4]);
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

        // altera o valor de um produto
        function alterarValorProduto ($idProduto, $novoPreco){
            $stmt = $this->con->prepare('UPDATE produto SET precoProduto = :novoPreco WHERE idProduto = :idProduto');
            $stmt->bindValue(':idProduto', $idProduto);
            $stmt->bindValue(':novoPreco', $novoPreco);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        // altera a categoria de um funcionario
        function alterarCategoriaFuncionario ($loginFunc, $novoValor, $login){
            $empresaAdmin = $this->getEmpresaUsuario($login);
            $empresaFunc = $this->getEmpresaUsuario($loginFunc);

            if($empresaAdmin != $empresaFunc){
                return UPDATE_ERROR;
            }

            $stmt = $this->con_users->prepare('UPDATE pessoal SET status = :novoStatus WHERE login = :loginFunc');
            $stmt->bindValue(':loginFunc', $loginFunc);
            $stmt->bindValue(':novoStatus', $novoValor);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

        function getEmpresaUsuario ($login){
            $stmt = $this->con_users->prepare('SELECT empresa FROM pessoal WHERE login = :login ');
            $stmt->bindValue(':login', $login);
            try{
                $stmt->execute();
                foreach ($stmt as $row){
                    return $row[0];
                }

            }catch (PDOException $e){
                echo $e;
                return false;
            }


        }


        // altera dados de uma pessoa
        function alterarDadosPessoais ($telefone, $email, $login, $endereco, $pw, $loginAdmin, $privilege){

            // verifica se o administrador não está tentando alterar o login de outro administrador ou usuário
            $empresaFunc = $this->getEmpresaUsuario($login);
            $empresaAdmin = $this->getEmpresaUsuario($loginAdmin);

            //caso seja o sysadmin, permite que a senha de qualquer usuário seja alterada
            if (($empresaAdmin != $empresaFunc) && ($privilege != 99)){
                return UPDATE_ERROR;
            }


            $stmt = $this->con_users->prepare('UPDATE pessoal SET senha = IFNULL (:senha, senha), endereco = IFNULL (:endereco, endereco), telefone = IFNULL (:telefone, telefone), email = IFNULL (:email, email) WHERE login = :loginFunc');
            $stmt->bindValue(':loginFunc', $login);
            $stmt->bindValue(':senha', $pw, PDO::PARAM_INT);
            $stmt->bindValue(':endereco', $endereco, PDO::PARAM_INT);
            $stmt->bindValue(':telefone', $telefone , PDO::PARAM_INT);
            $stmt->bindValue(':email', $email, PDO::PARAM_INT);
            try{
                $stmt->execute();
                return UPDATE_SUCCESS;
            }catch(PDOException $e){
                echo $e;
                return UPDATE_ERROR;
            }
        }

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

    }


