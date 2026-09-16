<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "conexao.php";

if (!isset($_SESSION["id_cliente"])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION["id_cliente"];

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Seu carrinho</h1>
<form method="POST" action="finalizar_compra.php">
<div class="carrinho-lista">
    <?php
    $stmt = $conexao->prepare("
        SELECT c.id AS id_item, p.nome, p.valor, p.imagem, p.quantidade_estoque, c.quantidade, (p.valor * c.quantidade) AS subtotal
        FROM carrinho c
        JOIN produtos p ON c.id_produto = p.id
        WHERE c.id_cliente = ?
    ");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $itens = $stmt->get_result();

    $total = 0;
    while ($item = $itens->fetch_assoc()) {
        $total += $item["subtotal"];
        echo "<div class='carrinho-item'>";

        echo "<input type='checkbox' name='itens[]' value='" . $item["id_item"] . "' data-subtotal='" . $item["subtotal"] . "' onchange='atualizarTotal()'>";

        if ($item["imagem"]) {
            echo "<img src='" . $item["imagem"] . "' class='carrinho-item-foto'>";
        }

        echo "<div class='carrinho-item-info'>";
        echo "<p class='carrinho-item-nome'>" . htmlspecialchars($item["nome"]) . "</p>";

        echo "<div class='carrinho-item-controles'>";
        echo "<a href='carrinho_atualizar.php?id_item=" . $item["id_item"] . "&acao=menos' class='stepper-btn'>&minus;</a>";
        echo "<span>" . $item["quantidade"] . "</span>";
        echo "<a href='carrinho_atualizar.php?id_item=" . $item["id_item"] . "&acao=mais' class='stepper-btn'>&plus;</a>";
        echo "</div>";

        echo "<a href='carrinho_remover.php?id_item=" . $item["id_item"] . "' class='remover-link'>Remover do carrinho</a>";
        echo "</div>";

        echo "<div class='carrinho-item-preco'>";
        echo "<p>R$ " . $item["valor"] . "</p>";
        echo "<p class='subtotal'>R$ " . number_format($item["subtotal"], 2, ',', '.') . "</p>";
        echo "</div>";

        echo "</div>";
    }
    ?>
</div>

<h2 style="margin-top: 2rem;">Total selecionado: R$ <span id="total-selecionado">0,00</span></h2>
<button type="submit" style="margin-top: 1rem;">Finalizar compra dos itens selecionados</button>
</form>

<script>
function atualizarTotal() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    let total = 0;

    checkboxes.forEach(function (checkbox) {
        if (checkbox.checked) {
            total += parseFloat(checkbox.dataset.subtotal);
        }
    });

    document.getElementById('total-selecionado').textContent = total.toFixed(2).replace('.', ',');
}
</script>

<?php include "rodape.php"; ?>