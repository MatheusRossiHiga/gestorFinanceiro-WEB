<?php
session_start();

$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'sistemaFinanceiro';

$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);
mysqli_set_charset($conexao, 'utf8mb4');

function moeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>
