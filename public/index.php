<?php
require("../vendor/autoload.php");
 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 
require '../vendor/autoload.php';
require_once '../includes/DbOperations.php';


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);



// cadastra um novo usuario no banco de dados
$app->post('/cadastroUsuario', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('nome','email','senha'))){
        $requestData = $request->getParsedBody();
        //API auth params

        $db = new DbOperations();
        $responseData = array();

        //cadastra o funcionário
        $nome = $requestData['nome'];
        $email = $requestData['email'];
        $senha = $requestData['senha'];
        $pw = password_hash($senha, PASSWORD_BCRYPT);

        $result = $db->criarUsuario($nome, $email, $pw);

        if ($result == CADASTRADO_COM_SUCESSO){
            $responseData['response'] = SUCESS;
            $responseData['message'] = "registrado com sucesso";
        }
        else {
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "usuario ja cadastrado";
        }

        $response->getBody()->write(json_encode($responseData));

    }
});


// faz o login e recupera o id do usuário (utilizar como sessão no sistema)
$app->get('/login', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('email', 'senha'))) {
        $headers = $request->getQueryParams();
        $email = $headers['email'];
        $senha = $headers['senha'];

        $db = new DbOperations();
        $result = $db->login($email, $senha);


        $responseData = Array();
        if ($result == ERRO_BUSCA) {
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "usuario nao encontrado";

        }elseif ($result == ERRO_AUTH){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "auth failure";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result[0];
        }


        $response->getBody()->write(json_encode($responseData));
    }

});

// cadastra um novo dispositivo no banco de dados
$app->post('/cadastrarDevice', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario','nomeDevice'))){
        $requestData = $request->getParsedBody();

        $db = new DbOperations();
        $responseData = array();

        //cadastra o Device
        $idUsuario = $requestData['idUsuario'];
        $nomeDevice = $requestData['nomeDevice'];

        $result = $db->cadastrarDevice($idUsuario, $nomeDevice);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro ao cadastrar";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));

    }
});

// recupera o id do dispositivo (hashkey)
$app->get('/getDeviceId', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario', 'nomeDevice'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];
        $nomeDevice = $headers['nomeDevice'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->getDeviceId($idUsuario, $nomeDevice);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro ao cadastrar";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});

// recupera o shareID do dispositivo
$app->get('/getDeviceShareId', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario', 'nomeDevice'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];
        $nomeDevice = $headers['nomeDevice'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->getDeviceShareId($idUsuario, $nomeDevice);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro ao cadastrar";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});



// atualiza o shareID do dispositivo
$app->post('/updateDeviceShareId', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario','nomeDevice'))){
        $requestData = $request->getParsedBody();

        $db = new DbOperations();
        $responseData = array();

        //cadastra o Device
        $idUsuario = $requestData['idUsuario'];
        $nomeDevice = $requestData['nomeDevice'];

        $result = $db->updateDeviceShareId($idUsuario, $nomeDevice);

        if ($result == UPDATE_ERROR){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro ao atualizar chave";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = "update bem sucedido";
        }

        $response->getBody()->write(json_encode($responseData));

    }
});


// cadastra um novo dispositivo no banco de dados
$app->post('/inserirDado', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('hashDevice', 'valor', 'tipoSensor', 'tag'))){
        $requestData = $request->getParsedBody();

        $db = new DbOperations();
        $responseData = array();

        //cadastra o Device
        $hashDevice = $requestData['hashDevice'];
        $valor = $requestData['valor'];
        $tipoSensor = $requestData['tipoSensor'];
        $tag = $requestData['tag'];
        if (strstr($tipoSensor, "#") || strstr($valor, "#") || strstr($tag, "#")){ // busca por erros nos valores de inserção
                $responseData['response'] = BAD_REQUEST;
                $responseData['message'] = "Caracter invalido encontrado";
        }
        elseif($valor == "-127" || $valor == "85"){ // verifica se os valores são os erros do sensor
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "Caracter invalido encontrado";
        }
        else {

            $result = $db->inserirDados($hashDevice, $valor, $tipoSensor, $tag);

            if ($result == ERRO_BUSCA) {
                $responseData['response'] = BAD_REQUEST;
                $responseData['message'] = "erro ao cadastrar";
            } else {
                $responseData['response'] = SUCESS;
                $responseData['message'] = "dado inserido com sucesso";
            }
        }

        $response->getBody()->write(json_encode($responseData));

    }
});


// recupera o shareID do dispositivo
$app->get('/getNomeUsuario', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->getNameUser($idUsuario);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});

// recupera o shareID do dispositivo
$app->get('/getDispositivos', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('idUsuario'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->listarDispositivos($idUsuario);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = BAD_REQUEST;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});


// recupera o shareID do dispositivo
$app->get('/getDadosDia', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('shareId'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];
        $sharedId = $headers['shareId'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->listarDadosDia($sharedId);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = NO_CONTENT;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});


// recupera o shareID do dispositivo
$app->get('/getDados', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('shareId'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $idUsuario = $headers['idUsuario'];
        $sharedId = $headers['shareId'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->listarDados($sharedId);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = NO_CONTENT;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});

// recupera os sensores de um tipo específico
$app->get('/getSensorsByType', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('shareId', 'type'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $type = $headers['type'];
        $sharedId = $headers['shareId'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->getSensorsByType($sharedId, $type);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = NO_CONTENT;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});

// recupera os dados de um sensor específico
$app->get('/getDataBySensor', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('shareId', 'name'))) {
        $headers = $request->getQueryParams();

        //cadastra o Device
        $name = $headers['name'];
        $sharedId = $headers['shareId'];

        $db = new DbOperations();
        $responseData = array();

        $result = $db->getDataBySensor($sharedId, $name);

        if ($result == ERRO_BUSCA){
            $responseData['response'] = NO_CONTENT;
            $responseData['message'] = "erro busca";
        }
        else {
            $responseData['response'] = SUCESS;
            $responseData['message'] = $result;
        }

        $response->getBody()->write(json_encode($responseData));
    }

});

//function to check parameters
function isTheseParametersAvailable($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echo json_encode($response);
        return false;
    }
    return true;
}


$app->run();
?>
