<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "conexao.php";

// Só o admin pode editar produtos
if (!isset($_SESSION["email"]) || $_SESSION["email"] != "luiz@gmail.com") {
    include "cabecalho.php";
    echo "<h2>Você não tem permissão para acessar esta página.</h2>";
    echo "<p><a href='index.php' class='botao'>Voltar para a loja</a></p>";
    include "rodape.php";
    exit;
}

$id_produto = $_GET["id"];

// Se o formulário foi enviado, processa a atualização
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $valor = $_POST["valor"];
    $estoque = $_POST["estoque"];
    $descricao = $_POST["descricao"];

    // Se uma nova foto foi enviada, processa o upload e troca o caminho
    if (!empty($_FILES["imagem"]["name"])) {
        $nome_arquivo = $_FILES["imagem"]["name"];
        $caminho_destino = "imagens_produtos/" . time() . "_" . $nome_arquivo;
        move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_destino);

        $stmt = $conexao->prepare("UPDATE produtos SET nome=?, valor=?, quantidade_estoque=?, descricao=?, imagem=? WHERE id=?");
        $stmt->bind_param("sdissi", $nome, $valor, $estoque, $descricao, $caminho_destino, $id_produto);
    } else {
        // Se não enviou foto nova, mantém a foto que já existia
        $stmt = $conexao->prepare("UPDATE produtos SET nome=?, valor=?, quantidade_estoque=?, descricao=? WHERE id=?");
        $stmt->bind_param("sdisi", $nome, $valor, $estoque, $descricao, $id_produto);
    }

    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Busca os dados atuais do produto, para preencher o formulário
$stmt = $conexao->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

include "cabecalho.php";
?>

<p><a href="index.php" class="voltar">&larr; Voltar para a loja</a></p>
<h1>Editar produto</h1>

<?php if ($produto["imagem"]): ?>
    <img src="<?php echo $produto['imagem']; ?>" class="foto-produto" style="max-width: 220px;">
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Nome do produto</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

    <label>Valor</label>
    <input type="number" step="0.01" name="valor" value="<?php echo $produto['valor']; ?>" required>

    <label>Quantidade em estoque</label>
    <input type="number" name="estoque" value="<?php echo $produto['quantidade_estoque']; ?>" required>

    <label>Descrição</label>
    <textarea name="descricao" rows="4"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

    <label>Trocar foto (deixe em branco para manter a atual)</label>
    <input type="file" name="imagem" accept="image/*">

    <button type="submit">Salvar alterações</button>
</form>

<?php include "rodape.php"; ?>