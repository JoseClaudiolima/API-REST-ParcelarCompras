<?php

require_once("config.php");
require_once("Compra.php");

header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];


if($metodo === 'POST'){

    $entrada = json_decode(file_get_contents('php://input'), true);

    $compra = new Compra($conn, $entrada);

    if ($compra->checar_parcelamento()){

        $compra->criar_parcelamento();

        echo json_encode([
            'Aviso' => 'Parcelamento concluido com sucesso!',
            'Mensagem' => $compra->get_mensagem_sucesso(),
            'Previsão do último pagamento' => $compra->get_previsao_termino(),
            'ID do parcelamento' => $compra->get_id_parcelamento_criado()
        ]);

    } else{ //checar_consulta()
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