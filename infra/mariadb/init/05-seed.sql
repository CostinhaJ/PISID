-- PISID recovery: seed data for local runs.
-- Invented layout (the project owner allows this, 2026-11-05): 4 rooms (the simulator default)
-- and a one-way corridor between every pair of different rooms.
USE pisid;

INSERT INTO configuracaolabirinto (numerodesalas) VALUES (4);

INSERT INTO corredor (salaa, salab)
SELECT a.n, b.n
FROM (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) a
JOIN (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) b ON a.n <> b.n;

-- ASSUMPTION (OPEN QUESTION Q4): experiment values.
-- NumeroRatos = 5 (simulator default). VariacaoPermitidaOutliers = 3, TempoConectividade = 120 and
-- AnomaliasPorMinuto = 25 are the default values in the Java code. SegundosSemMovimento = 60 has no source.
-- The procedure IniciarExperiencia is missing, so the experiment starts in state 'ADecorrer'.
-- Movimentos creates the medicoessalas rows when it finds this experiment.
INSERT INTO experiencia
  (IDExperiencia, Descricao, NumeroRatos, VariacaoPermitidaOutliers, TempoConectividade,
   SegundosSemMovimento, AnomaliasPorMinuto, Estado, TemperaturaProgramada, SalaSaida)
VALUES
  (1, 'Recovery test experiment', 5, 3, 120, 60, 25, 'ADecorrer', 22.0, 4);

-- Invented odor for each room of experiment 1.
INSERT INTO odoresexperiencia (IDExperiencia, Sala, CodigoOdor) VALUES
  (1, 1, 'Neutro'),
  (1, 2, 'Baunilha'),
  (1, 3, 'Hortela'),
  (1, 4, 'Queijo');
