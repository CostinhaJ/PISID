<?php
    require('labratsdb.php');
    
    $db = new Experiencia;
    $db->Experiencia();
    $db->editarExperiencia($_POST["p_IDExperiencia"] ,$_POST["p_Descricao"], $_POST["p_DataHora"], $_POST["p_NumeroRatos"], $_POST["p_SegundosSemMovimento"], $_POST["p_LimiteRatosSala"], $_POST["p_TemperaturaIdeal"], $_POST["p_VariacaoTemperaturaMaxima"], $_POST["p_VariacaoPermitidaOutliers"], $_POST["p_AnomaliasPorMinuto"], $_POST["p_TempoConectividade"], $_POST["p_TemperaturaProximoLimite"], $_POST["p_RatosProximoLimite"]);
?>
