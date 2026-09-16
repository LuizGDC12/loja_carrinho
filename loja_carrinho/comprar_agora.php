<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_produto = $_POST["id_produto"] ?? $_GET["id_produto"];
$quantidade = $_POST["quantidade"] ?? 1;

$stmt = $conexao->prepare("SELECT nome, valor, quantidade_estoque FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto || $quantidade > $produto["quantidade_estoque"]) {
    header("Location: produto.php?id=" . $id_produto . "&erro=estoque");
    exit;
}

$subtotal = $produto["valor"] * $quantidade;

$atualizar_estoque = $conexao->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?");
$atualizar_estoque->bind_param("ii", $quantidade, $id_produto);
$atualizar_estoque->execute();

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Compra realizada!</h1>
<div class="produto-card">
    <h3><?php echo $produto["nome"]; ?></h3>
    <p class="preco">R$ <?php echo $produto["valor"]; ?> x <?php echo $quantidade; ?></p>
    <p class="estoque">Subtotal: R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></p>
</div>
<p style="margin-top: 1.5rem;"><a href="index.php" class="botao">Voltar para a loja</a></p>

<?php include "rodape.php"; ?>