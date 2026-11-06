# PISID local environment (Docker Compose)

## Start

```bash
cp .env.example .env        # local development values
docker compose up -d --build
docker compose ps
```

## Services

| Service | Host port | Function |
|---------|-----------|----------|
| `mqtt` | 1883 | Mosquitto. Replaces `broker.mqttdashboard.com`. |
| `mongo1`, `mongo2`, `mongo3` | 27019, 25019, 23019 | MongoDB 4.4, replica set `replicaPisid`. |
| `mongo-init` | – | Runs once and initiates the replica set. |
| `mariadb` | 3306 | MariaDB 10.4, database `pisid` (schema in `infra/mariadb/init/`). |
| `php` | 8080 | Apache + PHP. API for the app at `/scripts/*.php`. |
| `pc1` | – | MQTT → MongoDB. |
| `sensor1`, `sensor2`, `movimentos`, `anomalias` | – | MongoDB → MariaDB. |
| `sim-temp`, `sim-maze` | – | Sensor simulators. |

## Check the flow

```bash
# 1. MQTT messages
docker compose exec mqtt mosquitto_sub -t '#' -C 5 -v

# 2. MongoDB
docker compose exec mongo1 mongo --port 27019 --quiet --eval \
  'db.getSiblingDB("experiencia").medicoesTemperaturas1.find().sort({_id:-1}).limit(3).forEach(printjson)'

# 3. MariaDB
docker compose exec mariadb mysql -uroot -pabc pisid -e \
  'SELECT * FROM medicoestemperatura ORDER BY IDMedicao DESC LIMIT 5; SELECT * FROM medicoessalas;'

# 4. PHP API (the same calls as the app)
curl -s -X POST -d username=java1 -d password=abc http://localhost:8080/scripts/validateLogin.php
curl -s -X POST http://localhost:8080/scripts/getTemp1.php
curl -s -X POST http://localhost:8080/scripts/getMouseRoom.php
```

## Flutter app

Log in with username `java1`, password `abc`, the IP address of this machine, and port `8080`.

Status: the app build is deferred. This VM cannot run an Android emulator (no `/dev/kvm`).

Option: a real phone on the network.
- With VirtualBox NAT, the phone cannot reach the VM. Use a bridged adapter, or forward host port 8080 to guest port 8080 and use the host IP.
- Open port 8080 in the host firewall.
- Android blocks cleartext HTTP by default. Allow it in `AndroidManifest.xml` (`android:usesCleartextTraffic="true"`) or in a network security config.

## Reset

```bash
docker compose down -v      # removes all data volumes; the init scripts run again on the next start
```

## Assumptions

See the comments in `infra/mariadb/init/*.sql` and `PISID_recovery_plan.md` (OPEN QUESTIONS Q1 to Q4).
