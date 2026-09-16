<?php
session_start();
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $stmt = $conexao->prepare("SELECT id, nome, email FROM clientes WHERE email = ? AND senha = ?");
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $cliente = $resultado->fetch_assoc();

    if ($cliente) {
        $_SESSION["id_cliente"] = $cliente["id"];
        $_SESSION["nome"] = $cliente["nome"];
        $_SESSION["email"] = $cliente["email"]; // guardamos o email também, para checagens futuras
        header("Location: index.php");
        exit;
    } else {
        include "cabecalho.php";
        echo "<h2>Email ou senha incorretos.</h2><p><a href='login.php' class='botao'>Tentar novamente</a></p>";
        include "rodape.php";
        exit;
    }
} else {
    include "cabecalho.php";
?>
    <h1>Login</h1>
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Senha</label>
        <input type="password" name="senha" required>
        <button type="submit">Entrar</button>
    </form>
<?php
    include "rodape.php";
}
?>