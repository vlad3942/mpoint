<?php

chdir(__DIR__. '/../..');

include_once 'api/classes/general.php';
include_once 'api/classes/home.php';
include_once 'api/classes/enduser_account.php';
include_once 'api/classes/validate.php';
include_once 'api/classes/callback.php';
include_once 'api/classes/basicconfig.php';
include_once 'api/classes/constants.php';

// Require API for parsing Templates with text tags: {TEXT_TAG}
require_once("../php5api/classes/template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once("../php5api/classes/http_client.php");

define("TESTDB_TOKEN", "test_". time());

require_once 'conf/global.php';
require_once 'conf/global_unittest.php';

include_once __DIR__. '/mpointBaseDatabaseTest.php';
include_once __DIR__. '/mpointBaseAPITest.php';
