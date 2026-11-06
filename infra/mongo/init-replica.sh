#!/bin/bash
# Initiates the replica set replicaPisid (name from the Java code) on the three mongod services.
# The member host names are the Compose service names, so clients must run in the Compose network.
set -euo pipefail

until mongo --quiet --host mongo1 --port 27019 --eval 'db.adminCommand("ping").ok' | grep -q 1; do
  echo "waiting for mongo1..."; sleep 2
done

mongo --quiet --host mongo1 --port 27019 <<'JS'
// The legacy shell returns {ok: 0} (it does not throw) when the set is not initiated.
var st = rs.status();
if (st.ok === 1) {
  print("replica set already initiated");
} else {
  var res = rs.initiate({
    _id: "replicaPisid",
    members: [
      { _id: 0, host: "mongo1:27019", priority: 2 },
      { _id: 1, host: "mongo2:25019" },
      { _id: 2, host: "mongo3:23019" }
    ]
  });
  printjson(res);
  if (res.ok !== 1) { quit(1); }
}
JS

until mongo --quiet --host mongo1 --port 27019 --eval 'db.isMaster().ismaster' | grep -q true; do
  echo "waiting for primary..."; sleep 2
done
echo "replicaPisid ready"
