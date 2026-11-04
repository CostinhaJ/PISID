<?php
/**Esta classe gere as operações realizadas sobre a base de dados do mundial do fifa.*/

class LABRATSDB {
  /**Variável da classe que permite guardar a ligação à base de dados.*/
  var $conn;
  
  /**Ligar à BD
   @return Um valor indicando qual o resultado da ligação à base de dados. (TENHO DE PASSAR AS CREDENCIAIS)*/
   function ligarBD() {
      $this->conn = mysqli_connect("localhost", "root", "", "labrats"); 
	  if(!$this->conn){
		return -1;
	  }
	}

  /**Fecha a ligação à base de dados*/
  function fecharBD() {
    mysqli_close($this->conn);
  }
 /**Executa um determinado comando SQL, retornando o seu resultado.  
 @param sql_command Comando SQL a ser executado pela função
 @return O resultado do comando SQL.*/
  function executarSQL($sql_command) {
    $resultado = mysqli_query($this->conn, $sql_command);
    return $resultado;
 }
 
 /**Devolve o número de registos de uma determinada tabela numa base de dados
 @param tabela O nome da tabela onde se deseja verificar o numero de registos.
 @return O numero de registos da tabela.*/
 function numero_tuplos($tabela) {
     $tuplos=0;
	 $rs=$this->executarSQL("SELECT * FROM $tabela");
	 return mysqli_num_rows($rs);  
 }

}

/**Esta classe implementa a gestão de produtos na base promundial. Permite
efectuar toda uma série de operações sobre a tabela de edicao, nomeadamente
operações de introdução, remoção, consulta e alteração de produtos.*/

class Experiencia extends LABRATSDB {
 /**Esta variável da classe é responsável pelas operações directas na Base de dados.*/
 var $db_ratslab;
 /**Inicializa os produtos da loja, e as variáveis da classe.*/
 
 function Experiencia() {
    $this->db_ratslab = new LABRATSDB;
	  $this->db_ratslab->ligarBD(); 
 }
 
 /**Permite a introdução de um novo produto na base de dados.
 @param codigo O codigo do novo produto a introduzir.
 @param designação A designação do novo produto a introduzir.
 @param preco O preço do novo produto a introduzir.*/
 
 function novaExperiencia($p_IDExperiencia, $p_Descricao, $p_DataHora, $p_NumeroRatos, $p_LimiteRatosSala, $p_TemperaturaIdeal, $p_VariacaoTemperaturaMaxima, $p_SegundosSemMovimento, $p_VariacaoPermitidaOutliers, $p_TempoConectividade, $p_AnomaliasPorMinuto) {
    $sql ="SET @p0='$p_IDExperiencia'; SET @p1='$p_Descricao'; SET @p2='$p_DataHora'; SET @p3='$p_NumeroRatos'; SET @p4='$p_LimiteRatosSala'; SET @p5='$p_TemperaturaIdeal'; SET @p6='$p_VariacaoTemperaturaMaxima'; SET @p7='$p_SegundosSemMovimento'; SET @p8='$p_VariacaoPermitidaOutliers'; SET @p9='$p_TempoConectividade'; SET @p10='$p_AnomaliasPorMinuto'; CALL `EditarExperiencia`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9, @p10)";
    $this->db_ratslab->executarSQL($sql);
 }
 
 /**Apaga um determinado produto da base de dados.
 @param codigo O codigo do produto a apagar.*/
 function apagarExperiencia($Ano) {
 $sql = "DELETE FROM edicao WHERE  Ano = $Ano";
 $this->db_ratslab->executarSQL($sql);
 }

/**Altera um determinado produto na base de dados
  @param codigo O codigo do produto a ser alterado
  @param designacao A nova designacao do produto
  @param preco O novo preço do produto.*/
  
  function editarExperiencia($p_IDExperiencia, $p_Descricao, $p_DataHora, $p_NumeroRatos, $p_SegundosSemMovimento, $p_LimiteRatosSala, $p_TemperaturaIdeal, $p_VariacaoTemperaturaMaxima, $p_VariacaoPermitidaOutliers, $p_AnomaliasPorMinuto, $p_TempoConectividade, $p_TemperaturaProximoLimite, $p_RatosProximoLimite) {
    $sql ="CALL `EditarExperiencia`('$p_IDExperiencia', '$p_Descricao', '$p_DataHora', '$p_NumeroRatos', '$p_SegundosSemMovimento', '$p_LimiteRatosSala', '$p_TemperaturaIdeal', '$p_VariacaoTemperaturaMaxima', '$p_VariacaoPermitidaOutliers', '$p_AnomaliasPorMinuto', '$p_TempoConectividade', '$p_TemperaturaProximoLimite', '$p_RatosProximoLimite')";
    $this->db_ratslab->executarSQL($sql);
 }

  /**Lista todos os produtos da base de dados*/
  function listarExperiencias() {
	  echo "<table border=1 cellpadding=0 cellspacing=0>\n";
	  $result_set = $this->db_ratslab->executarSQL("SELECT * FROM edicao");
    echo("<td><center><h4>Ano</h4></center></td><td><center><h4>Designação</h4></td><td></center><h4>Orçamento</h4></td><td><h4>#Selecoes</h4></td><td><h4>PaisOrganizador1</h4></td><td><h4>PaisOrganizador2</h4></td><td><center><h4>Remove</h4></center></td><td><center><h4>Altera</h4></center></td>");
	  $tuplos = mysqli_num_rows($result_set);  // old: $this->db_loja->numero_tuplos("produto");
	  if ($tuplos > 0) {
		  for($registo=0; $registo < $tuplos; $registo++) {
			  echo "<tr>\n";
			  $row = mysqli_fetch_assoc($result_set);
			  $this->escreveEdicao($row["Ano"], $row["Designacao"], $row["Orcamento"], $row["Selecoes_Participantes"], $row["PaisOrganizador1"], $row["PaisOrganizador2"]);
			  echo "</tr>\n";    
		  }
	   echo "</table>\n";
	  }
	  else {
		  echo "N&atilde;o foram encontrados produtos";
	  }
  }

function escreveExperiencia($Ano, $designacao, $orcamento, $numSelecoes, $PaisOrganizador1, $PaisOrganizador2) {
    printf("<td><center>$Ano</center></td>
    <td><center>$designacao</center></td>
    <td><center>$orcamento</center></td>
    <td><center>$numSelecoes</center></td>
    <td><center>$PaisOrganizador1</center></td>
    <td><center>$PaisOrganizador2</center></td>
    <form action=\"apagarEdicao.php\" method=post><td><input type=hidden name=Ano value=$Ano><input type=submit value=Apagar></td></form>
    <form action=\"alterarEdicao.php\" method=post><td><input type=hidden name=Ano value=$Ano><input type=submit value=Alterar></td></form>\n");
	  
  /**Escreve os detalhes de um determinado produto
  @param codigo O codigo do produto
  @param designacao A designacao do produto
  @param preco O preço do produto*/
  
  }

  /**Devolve o campo designacao de um determinado produto
  @param codigo O codigo do produto
  @return A designacao de um determinado produto*/
  function devolveDesignacao($Ano) {
    $sql="SELECT Designacao FROM edicao WHERE Ano=$Ano";
    $result_set = $this->db_ratslab->executarSQL($sql);
    $row = mysqli_fetch_assoc($result_set);
  return $row["Designacao"];
  }

  /**Devolve o campo preco de um determinado produto
  @param codigo O codigo do produto
  @return O preco de um determinado produto*/
  function devolveOrcamento($Ano) {
      $sql="SELECT Orcamento FROM edicao WHERE Ano=$Ano";
	  $result_set = $this->db_ratslab->executarSQL($sql);
	  $row = mysqli_fetch_assoc($result_set);
	  return $row["Orcamento"];
  }

  function devolveNumSelecoes($Ano) {
    $sql="SELECT Selecoes_Participantes FROM edicao WHERE Ano=$Ano";
    $result_set = $this->db_ratslab->executarSQL($sql);
    $row = mysqli_fetch_assoc($result_set);
  return $row["Selecoes_Participantes"];
}

function devolvePais1($Ano) {
  $sql="SELECT PaisOrganizador1 FROM edicao WHERE Ano=$Ano";
  $result_set = $this->db_ratslab->executarSQL($sql);
  $row = mysqli_fetch_assoc($result_set);
return $row["PaisOrganizador1"];
}

function devolvePais2($Ano) {
  $sql="SELECT PaisOrganizador2 FROM edicao WHERE Ano=$Ano";
  $result_set = $this->db_ratslab->executarSQL($sql);
  $row = mysqli_fetch_assoc($result_set);
return $row["PaisOrganizador2"];
}
  
  /**Corta a ligação à base de dados*/
  function CortarLigcaoBD() {
  $this->db_ratslab->fecharBD();
  }
}