<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <title>BD-ApagarEdição</title>
    </head>
    <body background=#ffffff>
    <p><h3>Apagar Edição </h3></p>

    <?php
        require('dbfifa.php');
        $produtos = new Edicao;
        $produtos->EdicaoMundial();
        $produtos->apagarEdicao($_POST["Ano"]);
        $produtos->fecharBDEdicao();
    ?>
    <br>
    Edição removida com sucesso!
    <br><br>
    <a href="listarEdicoes.php">voltar</a> | <a href="Menu.html">voltar ao menu</a>
    </body>
</html>
