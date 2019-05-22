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

// Exemplos de POST e GET

/*

// recupera as credencias da API a partir do login de um usuário
$app->get('/getApiAut', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array('login', 'senha'))) {
        $headers = $request->getQueryParams();
        $login = $headers['login'];
        $senha = $headers['senha'];

        $util = new Util();
        $result = $util->getApiAuth($login, $senha);


        $responseData = Array();
        if ($result == ERRO_BUSCA) {
            $responseData['error'] = true;
            $responseData['message'] = "usuario nao encontrado";

        }elseif ($result == ERRO_AUTH){
            $responseData['error'] = true;
            $responseData['message'] = "auth failure";
        }
        else {
            $responseData['error'] = false;
            $responseData['message'] = "login e senha recuperados";
            $responseData['status'] = $result[2];
        }
    }

    // API auth error
    else{
        $responseData['error'] = true;
        $responseData['message'] = "erro ao autenticar, acesso negado!";
    }

    $response->getBody()->write(json_encode($responseData));

});

// altera dados de um usuario
$app->post('/alterarDadosPessoais', function (Request $request, Response $response){
    if (isTheseParametersAvailable(array( 'telefone','email','login','senha','endereco', 'API_login', 'API_pw'))){
        $requestData = $request->getParsedBody();
        //API auth params
        $login = $requestData['API_login'];
        $pw = $requestData['API_pw'];
        $util = new Util();
        $API_data = $util->getApiAuth($login, $pw);
        $API_login = $API_data[0];
        $API_pw = $API_data[1];
        $status = $API_data[2];

        $db = new DbOperations($API_login, $API_pw);
        $responseData = array();

        //API verificação de autorização e login
        if (($db->checkDNS() != null && $status == 1) || ($status == 99)){

            //atualiza os dados
            $loginFunc = $requestData['login'];

            $telefone = $requestData['telefone'];
            if($telefone == "Manter"){
                $telefone = null;
            }
            $email = $requestData['email'];
            if($email == "Manter"){
                $email = null;
            }
            $senha = $requestData['senha'];
            if($senha == "Manter"){
                $senha = null;
                $pw = null;
            }else{
                $pw = password_hash($senha, PASSWORD_BCRYPT);
            }
            $endereco = $requestData['endereco'];
            if($endereco == "Manter"){
                $endereco = null;
            }


            $result = $db->alterarDadosPessoais($telefone, $email, $loginFunc, $endereco, $pw, $login, $status);

            if ($result == UPDATE_SUCCESS){
                $responseData['error'] = false;
                $responseData['message'] = "dados alterados com sucesso";
            }
            else {
                $responseData['error'] = true;
                $responseData['message'] = "erro ao alterar dados, confira o login de seu funcionario";
            }
        }

        // API auth error
        else{
            $responseData['error'] = true;
            $responseData['message'] = "erro ao autenticar, acesso negado!";
        }

        $response->getBody()->write(json_encode($responseData));
    }
});

*/


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
