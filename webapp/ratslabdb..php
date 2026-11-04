<?php
/**Esta classe gere as operações realizadas sobre a base de dados do mundial do fifa.*/

class RATSLABDB {
  /**Variável da classe que permite guardar a ligação à base de dados.*/
  var $conn;

  /**Ligar à BD
   @return Um valor indicando qual o resultado da ligação à base de dados.*/
   function ligarBD() {
      $this->conn = mysqli_connect("localhost", "root", "", "ratslab"); 
	  if(!$this->conn){
		return -1;
	  }
	}
 
 /**Executa um determinado comando SQL, retornando o seu resultado.  
 @param sql_command Comando SQL a ser executado pela função
 @return O resultado do comando SQL.*/
  function executarSQL($sql_command) {
    $resultado = mysqli_query( $this->conn, $sql_command);
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
 
 /**Fecha a ligação à base de dados*/
 function fecharBD() {
 mysqli_close($this->conn);
 }

}

/**Esta classe implementa a gestão de produtos na base promundial. Permite
efectuar toda uma série de operações sobre a tabela de edicao, nomeadamente
operações de introdução, remoção, consulta e alteração de produtos.*/

class Experiencia extends RATSLABDB {
 /**Esta variável da classe é responsável pelas operações directas na Base de dados.*/
 var $db_ratslab;
 /**Inicializa os produtos da loja, e as variáveis da classe.*/
 
 function EdicaoMundial() {
    $this->db_ratslab = new RATSLABDB;
	  $this->db_ratslab->ligarBD(); 
 }
 
 /**Permite a introdução de um novo produto na base de dados.
 @param codigo O codigo do novo produto a introduzir.
 @param designação A designação do novo produto a introduzir.
 @param preco O preço do novo produto a introduzir.*/
 
 function novaExperiencia($Ano, $designacao, $orcamento, $numSelecoes, $PaisOrganizador1, $PaisOrganizador2) {
    $sql = "INSERT INTO edicao VALUES ($Ano, '$designacao', $orcamento, $numSelecoes, '$PaisOrganizador1', '$PaisOrganizador2')";
    $this->db_ratslab->executarSQL($sql);
 }
 
 /**Apaga um determinado produto da base de dados.
 @param codigo O codigo do produto a apagar.*/
 function apagarEdicao($Ano) {
 $sql = "DELETE FROM edicao WHERE  Ano = $Ano";
 $this->db_ratslab->executarSQL($sql);
 }

/**Altera um determinado produto na base de dados
  @param codigo O codigo do produto a ser alterado
  @param designacao A nova designacao do produto
  @param preco O novo preço do produto.*/
  
  function alterarEdicao($Ano, $designacao, $orcamento, $numSelecoes, $PaisOrg1, $PaisOrg2) {
  $sql = "UPDATE edicao SET Designacao = '$designacao', Orcamento = $orcamento, Selecoes_Participantes = $numSelecoes, PaisOrganizador1 = '$PaisOrg1', PaisOrganizador2 = '$PaisOrg2' WHERE Ano = $Ano ";
  $this->db_ratslab->executarSQL($sql);
  }

  /**Lista todos os produtos da base de dados*/
  function listarEdicoes() {
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

/**Lista todos os produtos da base de dados cuja designação contiver o valor do parâmetro
 * Pesquisa básica
*/
  function pesquisarEdicoes($filtro) {
	  echo "<table border=1 cellpadding=0 cellspacing=0>\n";
	  $result_set = $this->db_ratslab->executarSQL("SELECT * FROM produto WHERE designacao LIKE '%$filtro%'");
	  $n_r =0;
	  while($row = mysqli_fetch_assoc($result_set)){
		$n_r =1;
		echo "<tr>\n";
		$this->escreveEdicao($row["codigo"], $row["designacao"], $row["preco"]);
		echo "</tr>\n";    
	  }
	  echo "</table>\n";
	  if($n_r ==0)
		  echo "N&atilde;o foram encontradoa edições";
  }
 
 
  /*Pesquisa avançada
  function pesquisarEdicoes($filtro) {
	  echo "<table border=1 cellpadding=0 cellspacing=0>\n";
	  $result_set = $this->db_loja->executarSQL("SELECT * FROM produto WHERE designacao LIKE '%$filtro%'");
	  $n_r =0;
	  while($row = mysqli_fetch_assoc($result_set)){
		$n_r =1;
		echo "<tr>\n";
		$this->escreveEdicao($row["codigo"], $row["designacao"], $row["preco"]);
		echo "</tr>\n";    
	  }
	  echo "</table>\n";
	  if($n_r ==0)
		  echo "N&atilde;o foram encontradoa edições";
	  
  }*/function escreveEdicao($Ano, $designacao, $orcamento, $numSelecoes, $PaisOrganizador1, $PaisOrganizador2) {
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
  function fecharBDEdicao() {
  $this->db_ratslab->fecharBD();
  }
}

class Jogo extends RATSLABDB{
  var $db_ratslab;

  function JogoMundial() {
    $this->db_ratslab = new RATSLABDB;
    $this->db_ratslab->ligarBD();
  }

  function pesquisarJogo($Numero){
    if (empty($Numero)){
      echo "Não escolheu nenhum jogo";
      return;
    }
    else{
      $result_set = $this->db_ratslab->executarSQL("SELECT * FROM jogo WHERE Numero = $Numero");
    }
    echo "<table border=1 cellpadding=0 cellspacing=0>\n";
    echo("<td><center><h4>Numero</h4></center></td><td><center><h4>Seleção1</h4></center></td><td><center><h4>Selção2</h4></center></td><td><center><h4>Penalizações</h4></center></td><td><center><h4>Golos</h4></center></td><td><center><h4>Detalhes</h4></center></td><td><center><h4>Alterar</h4></center></td><td><center><h4>Alterar</h4></center></td>");
    $n_r =0;
      while($row = mysqli_fetch_assoc($result_set)){
        $n_r =1;
        echo "<tr>\n";
        $this->escreveJogo($row["Numero"], $row["Equipa1"], $row["Equipa2"]);
        echo "</tr>\n";   
      }
      echo "</table>\n";
      if($n_r ==0){
        echo "Nenhum jogo correspondente";
      }
    }

function escreveJogo($Numero, $Equipa1, $Equipa2) {
      $penalizacoes = $this->db_ratslab->executarSQL("SELECT COUNT(*) FROM penalizacao WHERE Jogo_Numero = $Numero");
      $penalizacoes = mysqli_fetch_assoc($penalizacoes);
      $penalizacoes = (int)($penalizacoes["COUNT(*)"]);
      $golos = $this->db_ratslab->executarSQL("SELECT COUNT(*) FROM golo WHERE Jogo_Numero = $Numero");
      $golos = mysqli_fetch_assoc($golos);
      $golos = (int)($golos["COUNT(*)"]);
      $Equipa1 = $this->db_ratslab->executarSQL("SELECT Pais FROM selecao WHERE SelecaoID = $Equipa1");
      $Equipa1 = mysqli_fetch_assoc($Equipa1);
      $Equipa1 = $Equipa1["Pais"];
      $Equipa2 = $this->db_ratslab->executarSQL("SELECT Pais FROM selecao WHERE SelecaoID = $Equipa2");
      $Equipa2 = mysqli_fetch_assoc($Equipa2);
      $Equipa2 = $Equipa2["Pais"];
      printf("<td><center>$Numero</center></td>
      <td><center>$Equipa1</center></td>
      <td><center>$Equipa2</center></td>
      <td><center>$penalizacoes</center></td>
      <td><center>$golos</center></td>
      <form action=\"detalhesJogo.php\" method=post><td><input type=hidden name=Numero value=$Numero><input type=submit value=Detalhes></td></form>
      <form action=\"alterarJogo.php\" method=post><td><input type=hidden name=Numero value=$Numero><input type=submit value=Alterar></td></form>
      <form action=\"apagarJogo.php\" method=post><td><input type=hidden name=Numero value=$Numero><input type=submit value=Apagar></td></form>\n");
    }

    function pesquisaAvancada($Selecao1, $Selecao2, $Jogador, $Golos){     
       if(empty($Selecao1) && empty($Selecao2) && empty($Jogador) && empty($Golos)){
          echo "Adicione pelo menos um critério";
          return;
        }
        else{
          $SelID1 = $this->db_ratslab->executarSQL("SELECT SelecaoID FROM selecao WHERE Pais LIKE '%$Selecao1%'");
          $SelID1 = mysqli_fetch_assoc($SelID1);
          $SelID1 = $SelID1["SelecaoID"];
          $SelID2 = $this->db_ratslab->executarSQL("SELECT SelecaoID FROM selecao WHERE Pais LIKE '%$Selecao2%'");
          $SelID2 = mysqli_fetch_assoc($SelID2);
          $SelID2 = $SelID2["SelecaoID"];
          $result_set = $this->db_ratslab->executarSQL("SELECT * FROM jogo");
        if(!empty($Selecao1) && !empty($Selecao2)){
          $result_set = $this->db_ratslab->executarSQL("SELECT * FROM jogo WHERE Equipa1 = $SelID1 AND Equipa2 = $SelID2");
        }
        elseif(!empty($Jogador)){
          $result_set = $this->db_ratslab->executarSQL("SELECT * FROM jogo, jogadorjogo WHERE jogo.Numero = jogadorjogo.Jogo_numero AND jogadorjogo.JogadorSeID LIKE '%$Jogador%'");
        }
        elseif(!empty($Golos)){
          $result_set = $this->db_ratslab->executarSQL("SELECT * FROM jogo, golo WHERE (SELECT MAX(Golo_Numero) FROM golo WHERE jogo.Numero = golo.Jogo_Numero) = $Golos GROUP BY Numero");
        }
      }
    end:
      echo "<table border=1 cellpadding=0 cellspacing=0>\n";
      echo("<td><center><h4>Numero</h4></center></td><td><center><h4>Seleção1</h4></center></td><td><center><h4>Selção2</h4></center></td><td><center><h4>Penalizações</h4></center></td><td><center><h4>Golos</h4></center></td><td><center><h4>Detalhes</h4></center></td><td><center><h4>Alterar</h4></center></td><td><center><h4>Apagar</h4></center></td>");
      $n_r = 0;
      while($row = mysqli_fetch_assoc($result_set)){
          $n_r =1;
          echo "<tr>\n";
          $this->escreveJogo($row["Numero"], $row["Equipa1"], $row["Equipa2"]);
          echo "</tr>\n";    
        }
        echo "</table>\n";
        if($n_r ==0){
          echo "Nenhum jogo correspondente";
        }  
    }

    function detalhesJogo($Numero){
      $vencedor = $this->db_ratslab->executarSQL("SELECT ResultadoJogo($Numero)");
      $vencedor = mysqli_fetch_assoc($vencedor);
      $vencedor = $vencedor["ResultadoJogo($Numero)"];
      //echo var_dump($vencedor);
      $golosSel1 = $this->db_ratslab->executarSQL("SELECT GolosEquipa1($Numero)");
      $golosSel1 = mysqli_fetch_assoc($golosSel1);
      $golosSel1 = $golosSel1["GolosEquipa1($Numero)"];
      $golosSel2 = $this->db_ratslab->executarSQL("SELECT GolosEquipa2($Numero)");
      $golosSel2 = mysqli_fetch_assoc($golosSel2);
      $golosSel2 = $golosSel2["GolosEquipa2($Numero)"];
      //echo var_dump($golosSel2);
      if(strcasecmp($vencedor, "empate") == 0 ){
        echo "<h3>Empate</h3>";
      }
      else{
        echo "<h3>Vencedor:</h3>$vencedor";
      }
      echo "<h3>Resultado:</h3> $golosSel1 - $golosSel2<br></br>";
      $listaGolos = $this->db_ratslab->executarSQL("SELECT * FROM golo WHERE Jogo_Numero = $Numero");
      echo "<table border=1 cellpadding=0 cellspacing=0>\n";
      echo "<td><center>Momento</center></td><td><center>Marcador</center></td><td><center>Assistencia</center></td>\n";
      while($row = mysqli_fetch_assoc($listaGolos)){
          $n_r =1;
          echo "<tr>\n";
          $this->escreveGolo($row["Momento"], $row["JogadorMarcou"], $row["JogadorAssistio"]);
          echo "</tr>\n";    
        }
        echo "</table>\n";
        if($n_r ==0){
          echo "Nenhum golo correspondente";
        }  
    }
    
    function escreveGolo($Momento, $Marcador ,$Assistente){
      printf("<td><center>$Momento</center></td><td><center>$Marcador</center></td><td><center>$Assistente</center></td>");
    }

    function fecharBDJogo() {
      $this->db_ratslab->fecharBD();
    }

    function apagarJogo($Grupo_Edicao_Ano, $Numero){
      $this->db_ratslab->executarSQL("DELETE FROM jogo WHERE Grupo_Edicao_Ano = $Grupo_Edicao_Ano AND Numero = $Numero");
    }

    function alterarJogo( $Numero, $fase, $data, $estadio){
      $this->db_ratslab->executarSQL("UPDATE jogo SET Fase = '$fase' AND 'Data' = $data AND Estadio_Nome = '$estadio' WHERE Numero = $Numero ");
    }
  
    function devolveEdicao($Numero){
      $edicao = $this->db_ratslab->executarSQL("SELECT Grupo_Edicao_Ano FROM jogo WHERE Numero = $Numero");
      $row= mysqli_fetch_assoc($edicao);
      return $row["Grupo_Edicao_Ano"];
    }

    function devolveFase($Numero){
      $fase = $this->db_ratslab->executarSQL("SELECT Fase FROM jogo WHERE Numero = $Numero");
      $row = mysqli_fetch_assoc($fase);
      return $row["Fase"];
    }

    function devolveEstadio($Numero){
      $estadio = $this->db_ratslab->executarSQL("SELECT Estadio_Nome FROM jogo WHERE Numero = $Numero");
      $row = mysqli_fetch_assoc($estadio);
      return $row["Estadio_Nome"];
    }

    function devolveData($Numero){
      $data = $this->db_ratslab->executarSQL("SELECT 'Data' FROM jogo WHERE Numero = $Numero");
      $row = mysqli_fetch_assoc($data);
      return $row["Data"];
    }

    function devolveSelecao1($Numero){    
      $Equipa1 = $this->db_ratslab->executarSQL("SELECT Equipa1 FROM jogo WHERE Numero = $Numero");
      $Equipa1 = mysqli_fetch_assoc($Equipa1);
      $Equipa1 = $Equipa1["Equipa1"];
      $Equipa1 = $this->db_ratslab->executarSQL("SELECT Pais FROM selecao WHERE SelecaoID = $Equipa1");
      $Equipa1 = mysqli_fetch_assoc($Equipa1);
      return $Equipa1["Pais"];
    }

    function devolveSelecao2($Numero){
      $Equipa2 = $this->db_ratslab->executarSQL("SELECT Equipa2 FROM jogo WHERE Numero = $Numero");
      $Equipa2 = mysqli_fetch_assoc($Equipa2);
      $Equipa2 = $Equipa2["Equipa2"];
      $Equipa2 = $this->db_ratslab->executarSQL("SELECT Pais FROM selecao WHERE SelecaoID = $Equipa2");
      $Equipa2 = mysqli_fetch_assoc($Equipa2);
      return $Equipa2["Pais"];
    }

    function devolveVencedor($Numero){
      $vencedor = $this->db_ratslab->executarSQL("SELECT ResultadoJogo($Numero)");
      $row = mysqli_fetch_assoc($vencedor);
      return $row["ResultadoJogo($Numero)"];
    }
}
?>
