<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>BD-AlterarEdição</title>
</head>
<?php
require('dbfifa.php');
$edicao = new Edicao;
$edicao->EdicaoMundial();
?>
<body bgcolor="#FFFFFF">
<p><h3>Alterar uma Edição:</h3></p>
<form method="post" action="efetua_alteracaoEdicao.php">

  <label for="Ano">Ano:<?php echo $_POST["Ano"]; ?></label>
  <input type=hidden name="Ano" value=<?php echo $_POST["Ano"]; ?>>
  <br/><br/>

  <label for="designacao">Designação:</label><br/>
  <textarea name="designacao" cols="50" rows="3"><?php echo $edicao->devolveDesignacao($_POST["Ano"]);?></textarea>
  <br/><br/>
  
  <label for="orcamento">Orcamento:</label><br/>
  <input type="text" name="orcamento" size="20" maxlength="20" value="<?php echo $edicao->devolveOrcamento($_POST["Ano"])?>">
  <br/><br/>

  <label for="numSelecoes">Numero de Seleções Participantes:</label><br/>
  <input type="text" name="numSelecoes" size="20" maxlength="20" value ="<?php echo $edicao->devolveNumSelecoes($_POST["Ano"]);?>">
  <br/><br/>

  <label for="Pais1">PaisOrganizador1:</label><br/>
  <input type="text" name="Pais1" size="20" maxlength="20" value ="<?php echo $edicao->devolvePais1($_POST["Ano"]);?>">
  <br/><br/>

  <label for="Pais2">PaisOrganizador2:</label><br/>
  <input type="text" name="Pais2" size="20" maxlength="20" value ="<?php echo $edicao->devolvePais2($_POST["Ano"]);?>">
  <br/><br/>

  <?php $edicao->fecharBDEdicao(); ?>
  <input type="submit" name="Submit" value="Alterar">
	<input type="reset" name="Submit2" value="Limpar">
  
</form>
</body>
</html>