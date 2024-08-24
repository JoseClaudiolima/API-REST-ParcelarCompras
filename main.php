<?php

require_once("Compra.php");

header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];


if($metodo === 'POST'){

    $entrada = json_decode(file_get_contents('php://input'), true);

    $compra = new Compra($entrada);

    if ($compra->checar_valores()){
        echo json_encode([
            'Aviso' => 'Parcelamento concluido com sucesso!',
            'Mensagem' => '',
            'Previsão do último pagamento' => ''
        ]);

    } else{
        echo json_encode([
            'Aviso' => 'Valores obrigatórios de entrada ausentes!',
            $compra->get_tipo_erro() => $compra->get_mensagem_erro()
        ]);
    }

} else{
    http_response_code(405);
    echo json_encode(['Aviso' => 'Método não permitido!']);
}

?>