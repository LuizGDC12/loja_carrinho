<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_produto = $_POST["id_produto"] ?? $_GET["id_produto"];
$quantidade = $_POST["quantidade"] ?? $_GET["quantidade"] ?? 1;

// Verificação + desconto em UM ÚNICO comando: só desconta SE ainda houver estoque suficiente.
// A condição "AND quantidade_estoque >= ?" impede que o número fique negativo,
// mesmo que dois pedidos cheguem quase ao mesmo tempo.
$atualizar_estoque = $conexao->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ? AND quantidade_estoque >= ?");
$atualizar_estoque->bind_param("iii", $quantidade, $id_produto, $quantidade);
$atualizar_estoque->execute();

// affected_rows diz quantas linhas o UPDATE realmente mudou.
// Se for 0, significa que a condição "quantidade_estoque >= ?" falhou, ou seja: não tinha estoque.
if ($atualizar_estoque->affected_rows === 0) {
    header("Location: produto.php?id=" . $id_produto . "&erro=estoque");
    exit;
}

$stmt = $conexao->prepare("SELECT nome, valor FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

$subtotal = $produto["valor"] * $quantidade;

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