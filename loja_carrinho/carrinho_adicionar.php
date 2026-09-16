<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_produto = $_POST["id_produto"] ?? $_GET["id_produto"];
$quantidade_nova = $_POST["quantidade"] ?? $_GET["quantidade"] ?? 1;
$id_cliente = $_SESSION["id_cliente"];

// Verifica se esse cliente JÁ TEM esse produto no carrinho
$stmt = $conexao->prepare("SELECT id, quantidade FROM carrinho WHERE id_cliente = ? AND id_produto = ?");
$stmt->bind_param("ii", $id_cliente, $id_produto);
$stmt->execute();
$item_existente = $stmt->get_result()->fetch_assoc();

// Descobre quanto já estaria no carrinho, somando o que já tinha + o que está sendo adicionado agora
$quantidade_total = $quantidade_nova + ($item_existente ? $item_existente["quantidade"] : 0);

// Confere se o total (já tinha + está adicionando) cabe no estoque
$stmt = $conexao->prepare("SELECT quantidade_estoque FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto || $quantidade_total > $produto["quantidade_estoque"]) {
    header("Location: produto.php?id=" . $id_produto . "&erro=estoque");
    exit;
}

if ($item_existente) {
    // Já existia: só ATUALIZA a quantidade da linha existente
    $atualizar = $conexao->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
    $atualizar->bind_param("ii", $quantidade_total, $item_existente["id"]);
    $atualizar->execute();
} else {
    // Não existia: cria uma linha nova
    $inserir = $conexao->prepare("INSERT INTO carrinho (id_cliente, id_produto, quantidade) VALUES (?, ?, ?)");
    $inserir->bind_param("iii", $id_cliente, $id_produto, $quantidade_nova);
    $inserir->execute();
}

header("Location: index.php");
exit;
?>