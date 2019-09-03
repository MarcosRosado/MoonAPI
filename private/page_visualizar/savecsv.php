<?php

/**
 * Created by PhpStorm.
 * User: marco
 * Date: 22-Nov-18
 * Time: 9:35 PM
 */

require_once "../../includes/Constants.php";
require_once "../../includes/DbOperations.php";

$shareId = $_GET['shareId'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
try {
    // recupera os dados da API
    $ch = curl_init(API_LOCAL . "getDados?shareId=" . $shareId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    // converte o resultado da API para json
    $output = json_decode($response, true);



} catch (Exception $e) {
    echo $e;
}
*/
$db = new DbOperations();
$result = $db->listarDados($shareId);

if ($result == ERRO_BUSCA){
    $responseData['response'] = NO_CONTENT;
    $responseData['message'] = "erro busca";
}
else {
    $responseData['response'] = SUCESS;
    $responseData['message'] = $result;
}
$output = $responseData;

$f = fopen('php://memory', 'w');
foreach ($output["message"] as $line) {
    // generate csv lines from the inner arrays
    fputcsv($f, $line, ',');
}

$date = new DateTime();


$filename = "export".$date->getTimestamp()."."."csv";

// reset the file pointer to the start of the file
fseek($f, 0);
// tell the browser it's going to be a csv file
header('Content-Type: application/csv');
// tell the browser we want to save it instead of displaying it
header('Content-Disposition: attachment; filename="'.$filename.'";');
// make php send the generated csv lines to the browser
fpassthru($f);



