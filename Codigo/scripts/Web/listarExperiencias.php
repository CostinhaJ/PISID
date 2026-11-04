<html>
    <meta http-equiv="Content-Type" content="text/html:charset=UTF-8"> 
<head>
    <title>BD-Edições</title>
</head>
<h3>Listagem de Edições</h3>
    <?php
    require('dbfifa.php');

    $db = new Edicao;
    $db->EdicaoMundial();
    $db->listarEdicoes();
    $db->fecharBDEdicao();
    ?>
    <p><a href="Menu.html">Menu</a></p>
</html>