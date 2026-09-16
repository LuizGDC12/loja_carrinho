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

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Compra finalizada!</h1>
<div class="carrinho-lista">
<?php
$total = 0;

foreach ($itens_selecionados as $id_item) {
    // Agora também buscamos p.id (o id do PRODUTO), que precisamos para atualizar o estoque dele
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
        $total += $item["subtotal"];

        echo "<div class='carrinho-item'>";
        if ($item["imagem"]) {
            echo "<img src='" . $item["imagem"] . "' class='carrinho-item-foto'>";
        }
        echo "<div class='carrinho-item-info'>";
        echo "<p class='carrinho-item-nome'>" . htmlspecialchars($item["nome"]) . "</p>";
        echo "<p class='carrinho-item-qtd'>Quantidade: " . $item["quantidade"] . "</p>";
        echo "</div>";
        echo "<div class='carrinho-item-preco'><p class='subtotal'>R$ " . number_format($item["subtotal"], 2, ',', '.') . "</p></div>";
        echo "</div>";

        // 1) Remove o item do carrinho (compra concluída)
        $remover = $conexao->prepare("DELETE FROM carrinho WHERE id = ? AND id_cliente = ?");
        $remover->bind_param("ii", $id_item, $id_cliente);
        $remover->execute();

        // 2) Desconta a quantidade comprada do estoque desse produto
        $atualizar_estoque = $conexao->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?");
        $atualizar_estoque->bind_param("ii", $item["quantidade"], $item["id_produto"]);
        $atualizar_estoque->execute();
    }
}
?>
</div>
<h2 style="margin-top: 2rem;">Total pago: R$ <?php echo number_format($total, 2, ',', '.'); ?></h2>
<p style="margin-top: 1rem;"><a href="index.php" class="botao">Voltar para a loja</a></p>

<?php include "rodape.php"; ?>