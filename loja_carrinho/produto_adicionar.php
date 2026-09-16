<?php
session_start();
include "conexao.php";

// Bloqueia o acesso de quem não é o email autorizado
if (!isset($_SESSION["email"]) || $_SESSION["email"] != "luiz@gmail.com") {
    include "cabecalho.php";
    echo "<h2>Você não tem permissão para acessar esta página.</h2>";
    echo "<p><a href='index.php' class='botao'>Voltar para a loja</a></p>";
    include "rodape.php";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $valor = $_POST["valor"];
    $estoque = $_POST["estoque"];
    $descricao = $_POST["descricao"];

    $nome_arquivo = $_FILES["imagem"]["name"];
    $caminho_destino = "imagens_produtos/" . time() . "_" . $nome_arquivo;
    move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_destino);

    $stmt = $conexao->prepare("INSERT INTO produtos (nome, valor, quantidade_estoque, imagem, descricao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiss", $nome, $valor, $estoque, $caminho_destino, $descricao);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Adicionar produto</h1>
<form method="POST" enctype="multipart/form-data">
    <label>Nome do produto</label>
    <input type="text" name="nome" required>

    <label>Valor (ex: 49.90)</label>
    <input type="number" step="0.01" name="valor" required>

    <label>Quantidade em estoque</label>
    <input type="number" name="estoque" required>

    <label>Descrição</label>
    <textarea name="descricao" rows="4"></textarea>

    <label>Foto do produto</label>
    <input type="file" name="imagem" accept="image/*" required>

    <button type="submit">Cadastrar produto</button>
</form>

<?php include "rodape.php"; ?>