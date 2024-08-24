<?php

class Compra{
    private $valor_total;
    private $qtd_parcelas;
    private $data_primeiro_vencimento;
    private $periodicidade;
    private $lista_entrada;

    private $tipo_erro;
    private $mensagem_erro;
    private $padrao_data;

    public function __construct($entrada_dados){
        $this->valor_total = $this->receber_valor($entrada_dados, "valor_total");
        $this->qtd_parcelas = $this->receber_valor($entrada_dados, "qtd_parcelas");
        $this->data_primeiro_vencimento = $this->receber_valor($entrada_dados, "data_primeiro_vencimento");
        $this->periodicidade = $this->receber_valor($entrada_dados, "periodicidade");
        $this->valor_entrada = $this->receber_valor($entrada_dados, "valor_entrada");

        
    }


    public function receber_valor($entrada, $chave){
        return !isset($entrada[$chave]) ? null : $entrada[$chave];
    }

    public function checar_valores(){
        $this->lista_entrada = [
            'valor_total' => $this->valor_total,
            'qtd_parcelas' => $this->qtd_parcelas,
            'data_primeiro_vencimento' => $this->data_primeiro_vencimento,
            'periodicidade' => $this->periodicidade
        ];


        if (in_array(null, $this->lista_entrada)){
            $entradaAusente = array_filter($this->lista_entrada, function ($valor){
                return is_null($valor);
            });

            $this->tipo_erro = 'Valores Ausentes';
            $this->mensagem_erro = $entradaAusente;
            return false;
            
        } else if(!is_int($this->valor_total) or $this->valor_total < 0){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'valor_total';
            return false;

        } else if(!is_int($this->qtd_parcelas) or $this->qtd_parcelas < 0){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'qtd_parcelas';
            return false;

        } else if($this->periodicidade !== 'semanal' and $this->periodicidade !== 'mensal' and $this->periodicidade !== 'anual'){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'periodicidade';
            return false;

        } else if(!$this->validarData($this->data_primeiro_vencimento)){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'data_primeiro_vencimento';
            return false;

        } else if(isset($this->valor_entrada) and (!is_int($this->valor_entrada) or $this->valor_entrada < 0)
            ){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'valor_entrada';
            return false;

        }
         else{
            return true;
        }
    }

    
    public function get_tipo_erro(){
        return $this->tipo_erro;
    }
    
    public function get_mensagem_erro(){
        return $this->mensagem_erro;
    }

    function validarData($data) {
        $this->padrao_data = DateTime::createFromFormat('Y-m-d', $data);
        if ($this->padrao_data && $this->padrao_data->format('Y-m-d') === $data) {
            return true;
        } else {
            return false;
        }
    }

}

?>