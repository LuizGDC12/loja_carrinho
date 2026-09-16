<?php
// Remove um item específico do carrinho.
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_item = $_GET["id_item"];
$id_cliente = $_SESSION["id_cliente"];

// O "AND id_cliente = ?" garante que ninguém consiga remover um item que não é seu,
// mesmo tentando forjar o número na URL.
$stmt = $conexao->prepare("DELETE FROM carrinho WHERE id = ? AND id_cliente = ?");
$stmt->bind_param("ii", $id_item, $id_cliente);
$stmt->execute();

header("Location: carrinho.php");
exit;
?>