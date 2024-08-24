# API REST de Parcelamento de compras

Utilizando de PHP e SQL, o projeto fornece uma API capaz de salvar novas compras no banco de dados por meio do metodo POST, assim como fazer a busca de um parcelamento já cadastrado, uma vez informado o id correspondente.

## Funcionalidades

- **Criação de parcelamentos via POST**: Os usuários podem criar novos parcelamentos, informando valor total, quantidade de parcelas, data do primeiro vencimento, peridiocidade e o valor de entrada.
- **Busca de parcelamentos já cadastrados**: Informando o 'id' via POST, é possivel receber todos os dados citados acima cadastrados no BD caso o identificador já esteja cadastrado no banco de dados.

## Tecnologias Utilizadas

- **PHP**: Linguagem principal do projeto.
- **MySQL**: Banco de dados utilizado para armazenar informações de usuários, filmes e comentários.
- **PDO (PHP Data Objects)**: Utilizado para a conexão segura com o banco de dados.
- **POO (Programação orientada a objetos)**, foi desenvolvido dentro desse paradigma de programação.

## Instalação

Para executar este projeto localmente, siga as instruções abaixo:

1. Clone o repositório:
    ``` bash
    git clone https://github.com/JoseClaudiolima/MovieHub
    ```

2. Navegue até o diretório do projeto:
    ``` bash
    cd path-do-repositório-clonado
    ```

3. Importe o arquivo `database.sql` para criar as tabelas no banco de dados.
    Pode ser importado dentro de softwares similares a SQL Workbench, ou por meio de comandos via prompt, usando:
    ```sql
    USE database.sql;
    source caminho/para/database.sql;
    ```
  Obs: em "USE database.sql", é notório que precisa criar o database antes, com o comando CREATE DATABASE database_name.

4. Configure o arquivo de conexão com o banco de dados `config.php` com suas credenciais:
    Comumente segue o padrão abaixo
    ```php
    $host = 'localhost';
    $dbname = 'compras';
    $user = 'root';
    $pass = '';
    ```

5. Recomendo que utilize de software como POSTMAN para realizar a utilização do código.
Desta forma, crie uma nova coleção, insira a url do projeto em questão sobre a área de link e selecione a requisição POST.


# UTILIZAÇÃO - POST
- **Criar novo parcelamento**: 'valor_total', 'qtd_parcelas', 'data_primeiro_vencimento', 'periodicidade' são entradas obrigatórias, 'valor_entrada' opcional.
```
{
    "valor_total" : 100.00,
    "qtd_parcelas" : 12,
    "data_primeiro_vencimento" : "2024-08-01",
    "periodicidade" : "mensal"
}
```
- **Buscar parcelamento cadastrado**: Apenas informe o id e terá toda a informação do parcelamento retornada, caso esta tiver cadastro.
```
{
    "id" : 4
}
```

## Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Contato

Para mais informações, entre em contato através de [jose.c.lima.sp@gmail.com](mailto:jose.c.lima.sp@gmail.com).

