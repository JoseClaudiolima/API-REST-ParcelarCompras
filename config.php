<?php
//Insira as informações compatíveis conforme o sistema de gerenciamento de banco de dados de sua máquina
$host = 'localhost';
$dbname = 'compras';
$user = 'root';
$pass ='';

// Criação de objeto PDO permitindo conexão com o banco de dados no php
$conn = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);

?>