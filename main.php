<?php

require_once("config.php");
require_once("Compra.php");

//define o tipo de conteúdo que o servidor está enviando de volta para o cliente no formato JSON
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];

if($metodo === 'POST'){
    // Recebe os dados de entrada
    $entrada = json_decode(file_get_contents('php://input'), true);

    $compra = new Compra($conn, $entrada);

    //Verifica se entrada é condizente a criar novo parcelamento
    if ($compra->checar_parcelamento()){

        $compra->criar_parcelamento();

        echo json_encode([
            'Aviso' => 'Parcelamento concluido com sucesso!',
            'Mensagem' => $compra->get_mensagem_sucesso(),
            'Previsão do último pagamento' => $compra->get_previsao_termino(),
            'ID do parcelamento' => $compra->get_id_parcelamento_criado()
        ]);

    //Verifica se entrada é condizente a realizar busca
    } else if ($compra->checar_consulta()){
        echo json_encode([
            'Aviso' => $compra->get_mensagem_consulta(),
            'Mensagem' => $compra->get_mensagem_sucesso()
        ]);

    //Avisa que possui valores ausentes para criar busca
    } else{ 
        echo json_encode([
            'Aviso' => 'Valores obrigatórios de entrada ausentes!',
            $compra->get_tipo_erro_criacao_parcelamento() => $compra->get_mensagem_erro_criacao_parcelamento(),
            'Valores Ausentes para realizar busca' => ['id' => null]
        ]);
    }

//Aviso de que o metodo não possui suporte no sistema
} else{
    http_response_code(405);
    echo json_encode(['Aviso' => 'Método não permitido!']);
}

?>