<?php

require_once("Compra.php");

header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];


if($metodo === 'POST'){

    $entrada = json_decode(file_get_contents('php://input'), true);

    $compra = new Compra($entrada);

    

} else{
    http_response_code(405);
    echo json_encode(['Aviso' => 'Método não permitido!']);
}

?>