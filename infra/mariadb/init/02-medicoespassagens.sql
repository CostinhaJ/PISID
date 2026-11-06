-- Source: Codigo/scripts/BD/User/medicoespassagens.sql (unchanged, except:
--   * added USE pisid;
--   * removed the sample INSERT rows: they reference experiments 24 and 25, which do not exist here,
--     and would fail the foreign key.)
USE pisid;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Maio-2024 às 21:09
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pisid`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `medicoespassagens`
--

CREATE TABLE `medicoespassagens` (
  `IDMedicao` int(11) NOT NULL,
  `Hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `SalaOrigem` int(11) NOT NULL,
  `SalaDestino` int(11) NOT NULL,
  `IDExperiencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `medicoespassagens`
--


--
-- Acionadores `medicoespassagens`
--
DELIMITER $$
CREATE TRIGGER `trg_UpdateRatosSalas` BEFORE INSERT ON `medicoespassagens` FOR EACH ROW BEGIN
    DECLARE vRatosSalaOrigem INT;
    DECLARE vRatosSalaDestino INT;
    DECLARE vNumRatos INT;

    -- Obter o número de ratos na experiencia
    SELECT NumeroRatos INTO vNumRatos
    FROM experiencia
    WHERE IDExperiencia = NEW.IDExperiencia;

    -- Obter o número de ratos na sala de origem
    SELECT NumeroRatosSala INTO vRatosSalaOrigem FROM medicoessalas WHERE SalaID = NEW.SalaOrigem AND IDExperiencia = NEW.IDExperiencia ;

    -- Obter o número de ratos na sala de destino
    SELECT NumeroRatosSala INTO vRatosSalaDestino FROM medicoessalas WHERE SalaID = NEW.SalaDestino AND IDExperiencia = NEW.IDExperiencia;

    -- Verificar se a sala de origem tem 0 ratos ou a sala de destino tem 20 ratos
    IF vRatosSalaOrigem = 0 OR vRatosSalaDestino >= vNumRatos THEN
        -- Impedir a inserção sem enviar mensagem
        SET NEW.SalaOrigem = NULL;
    ELSE
        -- Decrementar a quantidade de ratos na sala de origem
        UPDATE medicoessalas SET NumeroRatosSala = NumeroRatosSala - 1 WHERE SalaID = NEW.SalaOrigem;

        -- Incrementar a quantidade de ratos na sala de destino
        UPDATE medicoessalas SET NumeroRatosSala = NumeroRatosSala + 1 WHERE SalaID = NEW.SalaDestino;

    END IF;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `medicoespassagens`
--
ALTER TABLE `medicoespassagens`
  ADD PRIMARY KEY (`IDMedicao`),
  ADD KEY `medpassexp` (`IDExperiencia`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `medicoespassagens`
--
ALTER TABLE `medicoespassagens`
  MODIFY `IDMedicao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `medicoespassagens`
--
ALTER TABLE `medicoespassagens`
  ADD CONSTRAINT `medpassexp` FOREIGN KEY (`IDExperiencia`) REFERENCES `experiencia` (`IDExperiencia`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
