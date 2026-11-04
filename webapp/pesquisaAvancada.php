<html>
    <meta http-equiv="Content-Type" content="text/html:charset=UTF-8"> 
<head>
    <title>BD-PesquisaBásica</title>
    <h3>Lista de Jogos</h3>
</head>
    <?php
    require('dbfifa.php');
    //$Sel1 = $_POST["Selecao1"];
    //$Sel1 = (int)($Sel1);
    //$Sel2 = $_POST["Selecao2"];
    //$Sel2 = (int)($Sel2);
    //$golos = $_POST["numGolos"];
    //$golos= (int)($golos);
    //echo $golos;
    $db = new Jogo;
    $db->JogoMundial();
    $db->pesquisaAvancada($_POST["Selecao1"], $_POST["Selecao2"], $_POST["JogadorJogo"], $_POST["numGolos"]);
    $db->fecharBDJogo();
    ?>
<p>
    <a href="pesquisaAvancada.html">Voltar</a> | <a href="Menu.html">Menu</a>
</p>
</html>