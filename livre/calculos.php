<?php

// saldo
$sql = "SELECT IFNULL(SUM(vl_conta_atual), 0) AS total
        FROM Contas
        WHERE id_usuario = $id_usuario";
$linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
$saldo = $linha['total'];

// comprometido
$sql = "SELECT IFNULL(SUM(vl_transacao), 0) AS total
        FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        WHERE id_usuario = $id_usuario AND tp_transacao = 'S' AND st_transacao = 'P'";
$linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
$comprometido = $linha['total'];

// guardado
$sql = "SELECT IFNULL(SUM(vl_guardado), 0) AS total
        FROM Metas
        WHERE id_usuario = $id_usuario";
$linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
$guardado = $linha['total'];

// livre
$livre = $saldo - $comprometido - $guardado;
?>
