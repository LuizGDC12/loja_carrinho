<?php
// Inicia a sessão para poder encerrá-la
session_start();

// Apaga todos os dados da sessão (o cliente "perde o crachá")
session_destroy();

// Volta para a página inicial
header("Location: index.php");
exit;
?>