<?php
// Aumenta ou diminui em 1 a quantidade de um item do carrinho.
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_item = $_GET["id_item"];
$acao = $_GET["acao"]; // "mais" ou "menos"
$id_cliente = $_SESSION["id_cliente"];

// Busca a quantidade atual do item E o estoque do produto relacionado
$stmt = $conexao->prepare("
    SELECT c.quantidade, p.quantidade_estoque
    FROM carrinho c
    JOIN produtos p ON c.id_produto = p.id
    WHERE c.id = ? AND c.id_cliente = ?
");
$stmt->bind_param("ii", $id_item, $id_cliente);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if ($item) {
    $nova_quantidade = $item["quantidade"];

    if ($acao == "mais" && $nova_quantidade < $item["quantidade_estoque"]) {
        $nova_quantidade++; // só aumenta se ainda tiver estoque disponível
    } elseif ($acao == "menos" && $nova_quantidade > 1) {
        $nova_quantidade--; // nunca deixa chegar a 0 (pra isso, usa o botão "Remover")
    }

    $atualizar = $conexao->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
    $atualizar->bind_param("ii", $nova_quantidade, $id_item);
    $atualizar->execute();
}

header("Location: carrinho.php");
exit;
?>