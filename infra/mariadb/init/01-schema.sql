-- PISID recovery: rebuilt schema.
-- The repository has only the table `medicoespassagens` (see 02-medicoespassagens.sql).
-- ASSUMPTION: all other tables and columns below come from the SQL statements in
-- Codigo/scripts/java/*.txt and Codigo/scripts/scripts/*.php. Types are not in the repository.
-- The server runs with lower_case_table_names=1, because the code mixes the case of
-- table names (Alerta/alerta, medicoesSalas/medicoessalas, ...). The original ran on Windows (XAMPP).

USE pisid;

CREATE TABLE experiencia (
  IDExperiencia             INT NOT NULL AUTO_INCREMENT,
  Descricao                 TEXT NULL,            -- Outros_Comandos.txt: EditarExperiencia(1, 'Descricao', ...)
  NumeroRatos               INT NOT NULL,         -- trigger trg_UpdateRatosSalas, Movimentos.getNumRatos
  VariacaoPermitidaOutliers DOUBLE NULL,          -- Sensor1Novo/Sensor2Novo (code default: 3)
  TempoConectividade        INT NULL,             -- seconds; Sensor1Novo/Movimentos (code default: 120)
  SegundosSemMovimento      INT NULL,             -- seconds; Movimentos.inatividadeAndConectividade
  AnomaliasPorMinuto        INT NULL,             -- Anomalias (code default: 25)
  Estado                    VARCHAR(20) NULL,     -- values in code: 'ADecorrer', 'Terminada'
  -- Required by the project owner (2026-11-05): temperature, odor, and chosen exit.
  TemperaturaProgramada     DOUBLE NULL,          -- air-conditioning set point (Notas/ComponentesPrincipais.txt)
  SalaSaida                 INT NULL,             -- chosen exit room of the maze
  PRIMARY KEY (IDExperiencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE medicoessalas (
  IDExperiencia   INT NOT NULL,
  SalaID          INT NOT NULL,
  NumeroRatosSala INT NOT NULL DEFAULT 0,
  PRIMARY KEY (IDExperiencia, SalaID),
  CONSTRAINT medsalasexp FOREIGN KEY (IDExperiencia) REFERENCES experiencia (IDExperiencia)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Notas/Semana7.txt: temperatures are not related to an experiment, so IDExperiencia is nullable.
CREATE TABLE medicoestemperatura (
  IDMedicao     INT NOT NULL AUTO_INCREMENT,
  SensorID      INT NOT NULL,
  Hora          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Leitura       DOUBLE NOT NULL,
  isOutlier     TINYINT(1) NOT NULL DEFAULT 0,
  IDExperiencia INT NULL,
  PRIMARY KEY (IDMedicao),
  KEY idx_sensor_hora (SensorID, Hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE alerta (
  IDAlerta      INT NOT NULL AUTO_INCREMENT,
  TipoAlerta    VARCHAR(50) NOT NULL,
  Hora          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Mensagem      VARCHAR(255) NULL,
  HoraEscrita   TIMESTAMP NULL,
  Sala          INT NULL,
  Sensor        INT NULL,
  Leitura       DOUBLE NULL,
  IDExperiencia INT NULL,
  PRIMARY KEY (IDAlerta),
  KEY idx_exp_hora (IDExperiencia, Hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- OPEN QUESTION Q2: the columns of `utilizador` and `configalertas` are not in the repository.
-- These placeholder tables exist so that the GRANT statements in 03-roles-users.sql work.
CREATE TABLE utilizador (
  IDUtilizador INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (IDUtilizador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE configalertas (
  IDConfig INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (IDConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Local copy of the maze tables. Originally on the external cloud server (pisid2024).
-- Columns from Movimentos.getMazeInformation.
CREATE TABLE configuracaolabirinto (
  numerodesalas INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE corredor (
  salaa INT NOT NULL,
  salab INT NOT NULL,
  PRIMARY KEY (salaa, salab)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Required by the project owner (2026-11-05): the odor of each room in an experiment.
-- Notas/ResumoEspecificacao.txt: "registar ... os odores de cada sala".
CREATE TABLE odoresexperiencia (
  IDExperiencia INT NOT NULL,
  Sala          INT NOT NULL,
  CodigoOdor    VARCHAR(50) NOT NULL,
  PRIMARY KEY (IDExperiencia, Sala),
  CONSTRAINT odorexp FOREIGN KEY (IDExperiencia) REFERENCES experiencia (IDExperiencia)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
