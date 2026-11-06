# Java processes

Sources are copies of `Codigo/scripts/java/*.txt`. The original files stay unchanged.

| Class | Original file | Function |
|-------|---------------|----------|
| `Pc1` | `PC1.txt` | MQTT -> MongoDB (`experiencia.*`) |
| `Sensor1Novo` | `Sensor1.txt` | `medicoesTemperaturas1` -> `medicoestemperatura` |
| `Sensor2Novo` | `Sensor2.txt` | `medicoesTemperaturas2` -> `medicoestemperatura` |
| `Movimentos` | `movimentos.txt` | `medicoesMovimentos` -> `medicoespassagens` |
| `Anomalias` | `message (4).txt` | `anomalia` -> `alerta` |

Recovery changes:
- Hosts, users, passwords, and topics come from environment variables (`Env.java`).
- The `Pc1` maze topic is now `MQTT_TOPIC_MAZE` (default `ratos`).
- `Movimentos` reads the maze (`configuracaolabirinto`, `corredor`) from the local MariaDB, not from the external cloud server.
- The migrators create the `lastProcessedId` state document only once. Before, the second process crashed on start.

Libraries (`lib/`) come from `Codigo/scripts/java/jar/`.
