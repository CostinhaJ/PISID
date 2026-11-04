<html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head><title>Registar Edição</title>
</head>
<body background=#ffffff>
<?php
require('dbfifa.php');

$edicao = new Edicao;
$edicao -> EdicaoMundial();
$edicao -> novaEdicao($_POST["Ano"], $_POST["designacao"], $_POST["orcamento"], $_POST["numSelecoes"], $_POST["PaisOrganizador1"], $_POST["PaisOrganizador2"]);
$edicao -> fecharBDEdicao();
?>
<br>
<a href="listarEdicoes.php">Listar</a> | <a href="Menu.html">Voltar ao menu</a>
</body>
</html>