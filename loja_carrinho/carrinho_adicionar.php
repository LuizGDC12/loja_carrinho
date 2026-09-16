<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_produto = $_POST["id_produto"] ?? $_GET["id_produto"];
$quantidade = $_POST["quantidade"] ?? 1;
$id_cliente = $_SESSION["id_cliente"];

// Confere quanto tem em estoque AGORA, antes de aceitar o pedido
$stmt = $conexao->prepare("SELECT quantidade_estoque FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto || $quantidade > $produto["quantidade_estoque"]) {
    // Pedido maior que o disponível: rejeita e volta com aviso
    header("Location: produto.php?id=" . $id_produto . "&erro=estoque");
    exit;
}

$stmt = $conexao->prepare("INSERT INTO carrinho (id_cliente, id_produto, quantidade) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $id_cliente, $id_produto, $quantidade);
$stmt->execute();

header("Location: index.php");
exit;
?>