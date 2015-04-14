<?php

abstract class mPointBaseDatabaseTest extends PHPUnit_Framework_TestCase
{
    private $_mPointDBInfo;

    /**
     * @var resource
     */
    private $_db;

    protected $_httpClient;


    public function setup()
    {
        parent::setup();

        error_reporting(E_ALL - E_STRICT);

        $this->applyTestConfiguration();

        global $aDB_CONN_INFO;
        $this->_mPointDBInfo = $aDB_CONN_INFO["mpoint"];
        $this->setupMpointDB();
    }

    private function _constDBConnString()
    {
        return "host=". $this->_mPointDBInfo['host']. " port=". $this->_mPointDBInfo['port']. " user=". $this->_mPointDBInfo['username']. " password=" . $this->_mPointDBInfo['password'];
    }

    private function setupMpointDB()
    {
        $conn = pg_connect($this->_constDBConnString() );
        $dbName = $this->_mPointDBInfo['path'];
        $schemaOwner = $this->_mPointDBInfo['username'];
        pg_query($conn, "CREATE DATABASE $dbName OWNER = $schemaOwner");

        $error = pg_last_error($conn);
        if (!empty($error))
        {
            echo $error;
            throw new ErrorException("Initialize MPoint DB for testing failed: ". $error);
        }

        $this->queryDB(file_get_contents(__DIR__. '/../db/mpoint_db.sql') );
        pg_close($conn);
    }

    private function dropMpointDB()
    {
        if (is_resource($this->_db) ) { @pg_close($this->_db); }
        $conn = pg_connect($this->_constDBConnString() );
        pg_query($conn, "DROP DATABASE ". $this->_mPointDBInfo['path']);
        pg_close($conn);
    }

    protected function applyTestConfiguration()
    {
        $confDir = __DIR__. '/../../conf/';

        // Backup existing conf/global.php
        @unlink($confDir. 'global.php.backup');
        copy($confDir. 'global.php', $confDir. 'global.php.backup');
        touch($confDir. 'global.php.backup', filemtime($confDir. 'global.php') );

        // Add test configuration to global.php
        $h = fopen($confDir. 'global.php', 'a');
        fwrite($h, '<?php define("TESTDB_TOKEN", "'. TESTDB_TOKEN. '"); ?>'. file_get_contents($confDir. 'global_unittest.php') );
        fclose($h);

        echo "Using DB token: ". TESTDB_TOKEN. "\n";
    }

    protected function restoreOriginalConfiguration()
    {
        $confDir = __DIR__. '/../../conf/';
        unlink($confDir. 'global.php');
        copy($confDir. 'global.php.backup', $confDir. 'global.php');
        touch($confDir. 'global.php', filemtime($confDir. 'global.php.backup') );
    }

    public function tearDown()
    {
        $this->dropMpointDB();
        $this->restoreOriginalConfiguration();
        parent::tearDown();
    }

    protected function queryDB($query)
    {
        if (!is_resource($this->_db) )
        {
            $this->_db = pg_connect($this->_constDBConnString(). " dbname=". $this->_mPointDBInfo['path']);
        }

        $res = pg_query($this->_db, $query);

        $error = pg_last_error($this->_db);
        if (!empty($error) )
        {
            throw new ErrorException("Querying test MPoint DB failed: ". $error);
        }
        return $res;
    }

}