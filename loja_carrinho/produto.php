<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "conexao.php";

$id_produto = $_GET["id"];

$stmt = $conexao->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "estoque"): ?>
    <p class="aviso-erro">Quantidade indisponível em estoque para esse produto.</p>
<?php endif; ?>

<div class="produto-detalhe">
    <div class="produto-detalhe-imagem">
        <?php if ($produto["imagem"]): ?>
            <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
        <?php endif; ?>
    </div>

    <div>
        <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
        <p class="preco-grande">R$ <?php echo $produto['valor']; ?></p>
        <p class="estoque">Estoque disponível: <?php echo $produto['quantidade_estoque']; ?></p>

        <?php if (!empty($produto['descricao'])): ?>
            <p class="descricao"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
        <?php endif; ?>

        <?php if ($produto["quantidade_estoque"] <= 0): ?>
            <button class="botao-desabilitado" disabled>Fora de estoque</button>
        <?php elseif (isset($_SESSION["id_cliente"])): ?>
            <form method="POST" action="carrinho_adicionar.php" class="form-compra">
                <input type="hidden" name="id_produto" value="<?php echo $produto['id']; ?>">
                <div>
                    <label>Quantidade</label>
                    <input type="number" name="quantidade" value="1" min="1" max="<?php echo $produto['quantidade_estoque']; ?>">
                </div>
                <button type="submit">Adicionar ao carrinho</button>
                <button type="submit" formaction="comprar_agora.php" class="botao-secundario">Comprar agora</button>
            </form>
        <?php else: ?>
            <a href="login.php" class="botao">Faça login para comprar</a>
        <?php endif; ?>
    </div>
</div>

<?php include "rodape.php"; ?>