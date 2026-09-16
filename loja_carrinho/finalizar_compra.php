<?php
session_start();
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION["id_cliente"];

if (!isset($_POST["itens"]) || count($_POST["itens"]) == 0) {
    header("Location: carrinho.php");
    exit;
}

$itens_selecionados = $_POST["itens"];

// Primeiro, processamos TUDO (compra + estoque), guardando os resultados
$total = 0;
$itens_comprados = [];
$itens_sem_estoque = [];

foreach ($itens_selecionados as $id_item) {
    $stmt = $conexao->prepare("
        SELECT p.id AS id_produto, p.nome, p.valor, p.imagem, c.quantidade, (p.valor * c.quantidade) AS subtotal
        FROM carrinho c
        JOIN produtos p ON c.id_produto = p.id
        WHERE c.id = ? AND c.id_cliente = ?
    ");
    $stmt->bind_param("ii", $id_item, $id_cliente);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if ($item) {
        $atualizar_estoque = $conexao->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ? AND quantidade_estoque >= ?");
        $atualizar_estoque->bind_param("iii", $item["quantidade"], $item["id_produto"], $item["quantidade"]);
        $atualizar_estoque->execute();

        if ($atualizar_estoque->affected_rows === 0) {
            $itens_sem_estoque[] = $item["nome"];
            continue;
        }

        $total += $item["subtotal"];
        $itens_comprados[] = $item;

        $remover = $conexao->prepare("DELETE FROM carrinho WHERE id = ? AND id_cliente = ?");
        $remover->bind_param("ii", $id_item, $id_cliente);
        $remover->execute();
    }
}

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Compra finalizada!</h1>

<?php if (count($itens_sem_estoque) > 0): ?>
    <p class="aviso-erro">
        ⚠️ Estoque esgotado para: <?php echo implode(", ", $itens_sem_estoque); ?>.<br>
        Esses itens continuam no seu carrinho e não foram cobrados.
    </p>
<?php endif; ?>

<?php if (count($itens_comprados) > 0): ?>
<div class="carrinho-lista">
    <?php foreach ($itens_comprados as $item): ?>
        <div class="carrinho-item">
            <?php if ($item["imagem"]): ?>
                <img src="<?php echo $item['imagem']; ?>" class="carrinho-item-foto">
            <?php endif; ?>
            <div class="carrinho-item-info">
                <p class="carrinho-item-nome"><?php echo htmlspecialchars($item["nome"]); ?></p>
                <p class="carrinho-item-qtd">Quantidade: <?php echo $item["quantidade"]; ?></p>
            </div>
            <div class="carrinho-item-preco">
                <p class="subtotal">R$ <?php echo number_format($item["subtotal"], 2, ',', '.'); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<h2 style="margin-top: 2rem;">Total pago: R$ <?php echo number_format($total, 2, ',', '.'); ?></h2>
<?php endif; ?>

<p style="margin-top: 1.5rem;"><a href="index.php" class="botao">Voltar para a loja</a></p>

<?php include "rodape.php"; ?>