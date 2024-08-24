<?php

class Compra{
    private $valor_total;
    private $qtd_parcelas;
    private $data_primeiro_vencimento;
    private $periodicidade;
    private $lista_entrada;

    public function __construct($entrada_dados){
        $this->valor_total = $this->receber_valor($entrada_dados, "valor_total");
        $this->qtd_parcelas = $this->receber_valor($entrada_dados, "qtd_parcelas");
        $this->data_primeiro_vencimento = $this->receber_valor($entrada_dados, "data_primeiro_vencimento");
        $this->periodicidade = $this->receber_valor($entrada_dados, "periodicidade");
        $this->lista_entrada = $this->receber_valor($entrada_dados, "valor_entrada");
    }


    public function receber_valor($entrada, $chave){
        return !isset($entrada[$chave]) ? null : $entrada[$chave];
    }
}

?>