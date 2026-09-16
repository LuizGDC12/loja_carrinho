<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "conexao.php";
include "cabecalho.php";

$eh_admin = isset($_SESSION["email"]) && $_SESSION["email"] == "luiz@gmail.com";
?>

<h1>Produtos da loja</h1>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "estoque"): ?>
    <p class="aviso-erro">Quantidade indisponível em estoque para esse produto.</p>
<?php endif; ?>

<div class="produtos">
    <?php
    $resultado = $conexao->query("SELECT id, nome, valor, quantidade_estoque, imagem FROM produtos");
    while ($produto = $resultado->fetch_assoc()) {
        echo "<div class='produto-card'>";

        echo "<a href='produto.php?id=" . $produto["id"] . "' class='produto-link'>";
        if ($produto["imagem"]) {
            echo "<img src='" . $produto["imagem"] . "' class='foto-produto'>";
        }
        echo "</a>";

        echo "<div class='produto-cabecalho'>";
        echo "<a href='produto.php?id=" . $produto["id"] . "' class='produto-link'><h3>" . $produto["nome"] . "</h3></a>";

        if ($eh_admin) {
            echo "<div class='menu-card'>";
            echo "<button type='button' class='dots-btn' onclick=\"toggleMenu(" . $produto["id"] . ")\">&#8943;</button>";
            echo "<div class='dropdown' id='menu-" . $produto["id"] . "'>";
            echo "<a href='produto_editar.php?id=" . $produto["id"] . "'>Editar</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";

        echo "<p class='preco'>R$ " . $produto["valor"] . "</p>";
        echo "<p class='estoque'>Estoque: " . $produto["quantidade_estoque"] . "</p>";

        echo "<div class='acoes-produto'>";
        if ($produto["quantidade_estoque"] <= 0) {
            // Sem estoque: nem mostra os botões de compra, só um aviso desabilitado
            echo "<button class='botao-desabilitado' disabled>Fora de estoque</button>";
        } elseif (isset($_SESSION["id_cliente"])) {
            echo "<a href='carrinho_adicionar.php?id_produto=" . $produto["id"] . "' class='botao'>Adicionar ao carrinho</a>";
            echo "<a href='comprar_agora.php?id_produto=" . $produto["id"] . "' class='botao'>Comprar agora</a>";
        } else {
            echo "<a href='login.php' class='botao'>Faça login para comprar</a>";
        }
        echo "</div>";

        echo "</div>";
    }
    ?>
</div>

<script>
function toggleMenu(id) {
    var menu = document.getElementById('menu-' + id);
    menu.classList.toggle('aberto');
}

document.addEventListener('click', function (evento) {
    if (!evento.target.classList.contains('dots-btn')) {
        document.querySelectorAll('.dropdown.aberto').forEach(function (menu) {
            menu.classList.remove('aberto');
        });
    }
});
</script>

<?php include "rodape.php"; ?>