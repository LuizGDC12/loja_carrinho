<?php
// Este arquivo é o topo (menu) repetido em todas as páginas.
// Ele espera que $conexao e session_start() já tenham sido chamados antes dele.

// Calcula quantos itens (somando as quantidades) o cliente logado tem no carrinho
$qtd_carrinho = 0;
if (isset($_SESSION["id_cliente"])) {
    $stmt_badge = $conexao->prepare("SELECT SUM(quantidade) AS total FROM carrinho WHERE id_cliente = ?");
    $stmt_badge->bind_param("i", $_SESSION["id_cliente"]);
    $stmt_badge->execute();
    $resultado_badge = $stmt_badge->get_result()->fetch_assoc();
    $qtd_carrinho = $resultado_badge["total"] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">Loja</a>
        <nav>
            <?php if (isset($_SESSION["nome"])): ?>
                <span>Olá, <?php echo $_SESSION["nome"]; ?></span>

                <?php if (isset($_SESSION["email"]) && $_SESSION["email"] == "luiz@gmail.com"): ?>
                    <a href="produto_adicionar.php" class="botao-add" title="Adicionar produto">+</a>
                <?php endif; ?>

                                <a href="carrinho.php" class="carrinho-link" title="Carrinho">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <?php if ($qtd_carrinho > 0): ?>
                        <span class="badge-carrinho"><?php echo $qtd_carrinho; ?></span>
                    <?php endif; ?>
                </a>

                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="cadastro.php">Criar conta</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>