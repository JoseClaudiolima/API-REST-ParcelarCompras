<?php

require_once("Parcelamento.php");

class Compra{
    private $conn;
    private $valor_total;
    private $qtd_parcelas;
    private $data_primeiro_vencimento;
    private $periodicidade;
    private $valor_entrada;

    private $entrada_dados_inteira;
    private $lista_entrada;
    private $tipo_erro_criacao_parcelamento;
    private $mensagem_erro_criacao_parcelamento;
    private $mensagem_sucesso;
    private $padrao_data;
    private $data_termino;
    private $id_parcelamento_criado;

    private $mensagem_consulta;
    private $tipo_erro_realizar_busca;
    private $mensagem_erro_realizar_busca;

    public function __construct($conn, $entrada_dados){
        $this->conn = $conn;
        $this->valor_total = $this->receber_valor($entrada_dados, "valor_total");
        $this->qtd_parcelas = $this->receber_valor($entrada_dados, "qtd_parcelas");
        $this->data_primeiro_vencimento = $this->receber_valor($entrada_dados, "data_primeiro_vencimento");
        $this->periodicidade = $this->receber_valor($entrada_dados, "periodicidade");
        $this->valor_entrada = $this->receber_valor($entrada_dados, "valor_entrada");

        $this->entrada_dados_inteira = $entrada_dados;
    }

    public function get_mensagem_consulta(){
        return $this->mensagem_consulta;
    }

    public function get_mensagem_erro_criacao_parcelamento(){
        return $this->mensagem_erro_criacao_parcelamento;
    }

    public function get_tipo_erro_criacao_parcelamento(){
        return $this->tipo_erro_criacao_parcelamento;
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

        //Abaixo, será checado se os valores de entrada estão condizentes para criação de um novo parcelamento
        //Com tipo de erro personalizado
        if (in_array(null, $this->lista_entrada)){
            $entradaAusente = array_filter($this->lista_entrada, function ($valor){
                return is_null($valor);
            });

            $this->tipo_erro_criacao_parcelamento = 'Valores Ausentes para criar parcelamento';
            $this->mensagem_erro_criacao_parcelamento = $entradaAusente;
            return false;
            
        } else if(!is_numeric($this->valor_total) or $this->valor_total < 0){
            $this->tipo_erro_criacao_parcelamento = 'Valores não compatíveis para criar novo parcelamento';
            $this->mensagem_erro_criacao_parcelamento = 'valor_total';
            return false;

        } else if(!is_numeric($this->qtd_parcelas) or $this->qtd_parcelas < 0){
            $this->tipo_erro_criacao_parcelamento = 'Valores não compatíveis para criar novo parcelamento';
            $this->mensagem_erro_criacao_parcelamento = 'qtd_parcelas';
            return false;

        } else if($this->periodicidade !== 'semanal' and $this->periodicidade !== 'mensal' and $this->periodicidade !== 'anual'){
            $this->tipo_erro_criacao_parcelamento = 'Valores não compatíveis para criar novo parcelamento';
            $this->mensagem_erro_criacao_parcelamento = 'periodicidade';
            return false;

        } else if(!$this->validar_data($this->data_primeiro_vencimento)){
            $this->tipo_erro_criacao_parcelamento = 'Valores não compatíveis para criar novo parcelamento';
            $this->mensagem_erro_criacao_parcelamento = 'data_primeiro_vencimento';
            return false;

        } else if(isset($this->valor_entrada) and (!is_numeric($this->valor_entrada) or $this->valor_entrada < 0)
            ){
            $this->tipo_erro_criacao_parcelamento = 'Valores não compatíveis para criar novo parcelamento';
            $this->mensagem_erro_criacao_parcelamento = 'valor_entrada';
            return false;

        }
         else{
            return true;
        }
    }

    public function validar_data($data) { //Nesta função, será validado se o parâmetro está na regra de data estabelecida
        $this->padrao_data = DateTime::createFromFormat('Y-m-d', $data);
        if ($this->padrao_data && $this->padrao_data->format('Y-m-d') === $data) {
            return true;
        } else {
            return false;
        }
    }

    public function adicionar_tempo($data, $intervalo) { //Será adicionado (meses, semanas ou anos) sobre a data inserida
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

        //Abaixo será chamado a função criar da classe Parcelamento (em outro arquivo separado somente para manipulação do BD), e com retorno de id
        $parcelamento = new ParcelamentoDAO($this->conn);
        $this->id_parcelamento_criado = $parcelamento->criar($this->valor_total, $this->qtd_parcelas, $this->data_primeiro_vencimento, $this->periodicidade, $this->valor_entrada);

        $this->mensagem_sucesso = "O parcelamento de $this->valor_total, foi dividido em $this->qtd_parcelas parcela(s) de $parcelas real(is) $this->periodicidade(is), tendo como $this->data_primeiro_vencimento como primeira data de vencimento.";
    }

    public function checar_consulta(){
        $this->id_busca = $this->receber_valor($this->entrada_dados_inteira, "id");
        
        //Abaixo será chamado a função buscar classe Parcelamento (em outro arquivo separado somente para manipulação do BD)
        $parcelamento = new ParcelamentoDAO($this->conn);
        $informacoes_busca = $parcelamento->buscar($this->id_busca);

        if(isset($this->id_busca) && !$informacoes_busca){ //Caso haja entrada de 'id' porém não seja encontrado nenhum parcelamento neste
            $this->mensagem_consulta = "Consulta Realizada com sucesso!";
            $this->mensagem_sucesso = "Não foi encontrado resultado de pesquisa";
            return true;

        } else if (isset($this->id_busca) && $informacoes_busca){ // Casa haja entrada de 'id' e também os dados de parcelamento
            $valor_total = $informacoes_busca['valor_total'];
            $qtd_parcelas = $informacoes_busca['qtd_parcelas'];
            $data_primeiro_vencimento = $informacoes_busca['data_primeiro_vencimento'];
            $periodicidade = $informacoes_busca['periodicidade'];
            $valor_entrada = is_null($informacoes_busca['valor_entrada']) ? "0.00" : $informacoes_busca['valor_entrada']; 
            
            $parcelas = round(($valor_total - $valor_entrada) / $qtd_parcelas,2);
    
            $this->mensagem_consulta = "Consulta Realizada com sucesso!";
            $this->mensagem_sucesso = "O parcelamento de $valor_total, foi dividido em $qtd_parcelas parcela(s) de $parcelas real(is) $periodicidade(is), tendo como $data_primeiro_vencimento como primeira data de vencimento.";
    
            return true;

        } else { //Retorna falso, caso não haja sequer entrada de 'id'
            return false;
        }
    }


}

?>