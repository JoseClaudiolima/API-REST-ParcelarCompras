<?php

class ParcelamentoDAO{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }
    
    //Abaixo será executado uma query para inserir dados no BD de um novo parcelamento, e com retorno do id inserido
    public function criar($valor_total, $qtd_parcelas, $data_primeiro_vencimento, $periodicidade, $valor_entrada){
        $stmt = $this->conn->prepare('INSERT parcelamentos (valor_total, qtd_parcelas, data_primeiro_vencimento, periodicidade, valor_entrada) VALUES (:valor_total, :qtd_parcelas, :data_primeiro_vencimento, :periodicidade, :valor_entrada)');
        $stmt->bindParam(':valor_total', $valor_total, PDO::PARAM_STR);
        $stmt->bindParam(':qtd_parcelas', $qtd_parcelas, PDO::PARAM_INT);
        $stmt->bindParam(':data_primeiro_vencimento', $data_primeiro_vencimento, PDO::PARAM_STR);
        $stmt->bindParam(':periodicidade', $periodicidade, PDO::PARAM_STR);
        $stmt->bindParam(':valor_entrada', $valor_entrada, PDO::PARAM_STR);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    //Abaixo será buscado no BD o parcelamento, colocado como parametro 'id', e retornado falso caso não ache
    public function buscar($id){
        $stmt = $this->conn->prepare('SELECT * FROM parcelamentos WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $id_buscado = $stmt->fetch();
        
        if (!$id_buscado){
            return false;
        } else{
            return $id_buscado;
        }
    }
}

?>