<?php

if (defined('sPROJECT_BASE_DIR') === false) { define('sPROJECT_BASE_DIR', __DIR__. '/../..'); }
chdir(sPROJECT_BASE_DIR);

if (defined('sAPI_INTERFACE_PATH') === false) { define('sAPI_INTERFACE_PATH', '/opt/php5api'); }

include_once 'api/classes/exceptions.php';
include_once 'api/classes/general.php';
include_once 'api/classes/home.php';
include_once 'api/classes/enduser_account.php';
include_once sAPI_INTERFACE_PATH .'/classes/validate_base.php';
include_once 'api/classes/validate.php';
include_once 'api/classes/callback.php';
include_once 'api/classes/basicconfig.php';
include_once 'api/classes/constants.php';

define("TESTDB_TOKEN", "test_". time() );

require_once 'conf/global.php';
require_once 'conf/global_unittest.php';

include_once __DIR__ . '/basedatabasetest.php';
include_once __DIR__ . '/baseapitest.php';

echo "Using testDB-token: ". TESTDB_TOKEN ."\n\n";
