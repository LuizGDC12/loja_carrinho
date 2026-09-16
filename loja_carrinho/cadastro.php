<?php
session_start();
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $stmt = $conexao->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);
    $stmt->execute();

    include "cabecalho.php";
    echo "<h2>Conta criada com sucesso!</h2><p><a href='login.php' class='botao'>Ir para o login</a></p>";
    include "rodape.php";
} else {
    include "cabecalho.php";
?>
    <h1>Criar conta</h1>
    <form method="POST">
        <label>Nome</label>
        <input type="text" name="nome" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Senha</label>
        <input type="password" name="senha" required>
        <button type="submit">Criar conta</button>
    </form>
<?php
    include "rodape.php";
}
?>