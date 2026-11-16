<?php
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id'];
?>
