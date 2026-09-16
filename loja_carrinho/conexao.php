<?php
// Este arquivo é responsável por abrir a conexão com o banco de dados.
// Ele é "incluído" em todas as outras páginas que precisam falar com o banco.

$host = "localhost";        // Onde o MySQL está rodando (nossa própria máquina)
$usuario = "root";          // Usuário padrão do MySQL
$senha = "luizgustavoBR12";  // Senha do MySQL
$banco = "carrinho_compras"; // Nome do banco de dados que vamos usar

// Cria a conexão com o banco usando os dados acima
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Se der erro na conexão, interrompe a página e mostra a mensagem
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>