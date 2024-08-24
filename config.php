<?php
//Insira as informações compatíveis conforme o mysql de sua máquina
$host = 'localhost';
$dbname = 'compras';
$user = 'root';
$pass ='';

// Criação de objeto PDO permitindo conexão com o banco de dados no php
$conn = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);

?>