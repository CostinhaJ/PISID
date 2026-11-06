-- Source: Codigo/scripts/BD/User/Roles.txt and Outros_Comandos.txt.
-- Changes for recovery:
--   * the GRANT EXECUTE ON PROCEDURE lines are not here: the procedures are not in the repository (OPEN QUESTION Q2).
--   * the user java1 uses the password from the environment (default in the repository: 'abc').
USE pisid;

CREATE ROLE Administrador;
CREATE ROLE Investigador;
CREATE ROLE TecnicoDeManutencao;
CREATE ROLE Java;

GRANT SELECT ON pisid.alerta TO Administrador;
GRANT SELECT, INSERT, UPDATE, DELETE ON pisid.experiencia TO Administrador;
GRANT SELECT, INSERT, UPDATE, DELETE ON pisid.medicoesSalas TO Administrador;
GRANT SELECT, INSERT, UPDATE, DELETE ON pisid.utilizador TO Administrador;
GRANT SELECT, INSERT, UPDATE ON pisid.configalertas TO Administrador;
GRANT SELECT ON pisid.medicoespassagens TO Administrador;
GRANT SELECT ON pisid.medicoestemperatura TO Administrador;

GRANT SELECT ON pisid.alerta TO Investigador;
GRANT SELECT ON pisid.experiencia TO Investigador;
GRANT SELECT ON pisid.medicoesSalas TO Investigador;
GRANT SELECT ON pisid.configAlertas TO Investigador;
GRANT SELECT ON pisid.medicoesPassagens TO Investigador;
GRANT SELECT ON pisid.medicoesTemperatura TO Investigador;

GRANT SELECT ON pisid.alerta TO TecnicoDeManutencao;
GRANT SELECT ON pisid.experiencia TO TecnicoDeManutencao;
GRANT SELECT ON pisid.medicoesSalas TO TecnicoDeManutencao;
GRANT SELECT ON pisid.configAlertas TO TecnicoDeManutencao;
GRANT SELECT ON pisid.medicoesPassagens TO TecnicoDeManutencao;
GRANT SELECT ON pisid.medicoesTemperatura TO TecnicoDeManutencao;

GRANT SELECT, INSERT ON pisid.alerta TO Java;
GRANT SELECT, INSERT ON pisid.experiencia TO Java;
GRANT SELECT, INSERT ON pisid.medicoesSalas TO Java;
GRANT SELECT ON pisid.utilizador TO Java;
GRANT SELECT ON pisid.configalertas TO Java;
GRANT SELECT, INSERT ON pisid.medicoespassagens TO Java;
GRANT SELECT, INSERT ON pisid.medicoestemperatura TO Java;
-- Recovery: Movimentos now reads the maze from the local database.
GRANT SELECT ON pisid.configuracaolabirinto TO Java;
GRANT SELECT ON pisid.corredor TO Java;
GRANT SELECT ON pisid.odoresexperiencia TO Java, Investigador, TecnicoDeManutencao;
GRANT SELECT, INSERT, UPDATE, DELETE ON pisid.odoresexperiencia TO Administrador;
