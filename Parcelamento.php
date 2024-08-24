<?php

class ParcelamentoDAO{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }
    
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

    public function buscar($id){
        
    }
}

?>