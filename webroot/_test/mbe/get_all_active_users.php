<?php
require_once ("inc/include.php");

$arr = getAllActiveUsers($_OBJ_DB_MBE);

echo "jsonpCallbackActiveUsers(".json_encode($arr).")";

