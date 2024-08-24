<?php

require_once("Parcelamento.php");

class Compra{
    private $conn;
    private $valor_total;
    private $qtd_parcelas;
    private $data_primeiro_vencimento;
    private $periodicidade;
    private $valor_entrada;

    private $lista_entrada;
    private $tipo_erro;
    private $mensagem_erro;
    private $mensagem_sucesso;
    private $padrao_data;
    private $data_termino;
    private $id_parcelamento_criado;

    public function __construct($conn, $entrada_dados){
        $this->conn = $conn;
        $this->valor_total = $this->receber_valor($entrada_dados, "valor_total");
        $this->qtd_parcelas = $this->receber_valor($entrada_dados, "qtd_parcelas");
        $this->data_primeiro_vencimento = $this->receber_valor($entrada_dados, "data_primeiro_vencimento");
        $this->periodicidade = $this->receber_valor($entrada_dados, "periodicidade");
        $this->valor_entrada = $this->receber_valor($entrada_dados, "valor_entrada");

        
    }


    public function receber_valor($entrada, $chave){
        return !isset($entrada[$chave]) ? null : $entrada[$chave];
    }

    public function checar_parcelamento(){
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
            
        } else if(!is_numeric($this->valor_total) or $this->valor_total < 0){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'valor_total';
            return false;

        } else if(!is_numeric($this->qtd_parcelas) or $this->qtd_parcelas < 0){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'qtd_parcelas';
            return false;

        } else if($this->periodicidade !== 'semanal' and $this->periodicidade !== 'mensal' and $this->periodicidade !== 'anual'){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'periodicidade';
            return false;

        } else if(!$this->validar_data($this->data_primeiro_vencimento)){
            $this->tipo_erro = 'Valores não compatíveis';
            $this->mensagem_erro = 'data_primeiro_vencimento';
            return false;

        } else if(isset($this->valor_entrada) and (!is_numeric($this->valor_entrada) or $this->valor_entrada < 0)
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

    public function get_mensagem_sucesso(){
        return $this->mensagem_sucesso;
    }

    public function get_previsao_termino(){
        return $this->data_termino;
    }

    public function get_id_parcelamento_criado(){
        return $this->id_parcelamento_criado;
    }

    public function validar_data($data) {
        $this->padrao_data = DateTime::createFromFormat('Y-m-d', $data);
        if ($this->padrao_data && $this->padrao_data->format('Y-m-d') === $data) {
            return true;
        } else {
            return false;
        }
    }

    public function adicionar_tempo($data, $intervalo) {
        $d = DateTime::createFromFormat('Y-m-d', $data);
        if ($d && $d->format('Y-m-d') === $data) {
            $d->modify($intervalo);
            return $d->format('Y-m-d');
        } else {
            return false;
        }
    }

    public function criar_parcelamento(){
        if($this->periodicidade === 'mensal'){
            $this->periodicidade_soma = 'months';
        } else if ($this->periodicidade === 'anual'){
            $this->periodicidade = 'years';
        } else if ($this->periodicidade === 'semanal'){
            $this->periodicidade_soma = 'weeks';
        }

        $parcelas = round(($this->valor_total - $this->valor_entrada) / $this->qtd_parcelas,2);

        $this->data_termino = $this->adicionar_tempo($this->data_primeiro_vencimento, "+ $this->qtd_parcelas $this->periodicidade_soma");

        $parcelamento = new ParcelamentoDAO($this->conn);
        $this->id_parcelamento_criado = $parcelamento->criar($this->valor_total, $this->qtd_parcelas, $this->data_primeiro_vencimento, $this->periodicidade, $this->valor_entrada);

        $this->mensagem_sucesso = "O parcelamento de $this->valor_total, foi dividido em $this->qtd_parcelas parcela(s) de $parcelas real(is) $this->periodicidade(is), tendo como $this->data_primeiro_vencimento como primeira data de vencimento.";
    }


}

?>