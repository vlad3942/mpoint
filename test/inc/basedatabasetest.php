<?php

use PHPUnit\Framework\TestCase;

abstract class BaseDatabaseTest extends TestCase
{
    //Cleanup after each testcase runs
    const TRUNCATE_DB_SCHEMAS     = ["'client'", "'enduser'", "'log'"];
    const EXCLUDE_TRUNCATE_TABLES = ["'client.infotype_tbl'", "'log.operation_tbl'", "'log.state_tbl'"];
    
    protected $mPointDBInfo;
    
    /**
     * @var resource
     */
    private $_db;
    
    /**
     * @var HTTPClient
     */
    protected $_httpClient;
    
    /**
     * @var _isDBSetuped bool
     */
    private $_isDBSetuped;
    
    public function setup($isDBSetupRequired): void
    {
        parent::setup();
        $this->_isDBSetuped = $isDBSetupRequired;
        $this->applyTestConfiguration();
        
        global $aDB_CONN_INFO;
        $this->mPointDBInfo = $aDB_CONN_INFO["mpoint"];
        if ($isDBSetupRequired === true) 
        {
            if (!is_resource($this->_db)) 
            {
                $this->_db = pg_connect($this->_constDBConnString() . " dbname=" . $this->mPointDBInfo['path']);
                
                $tables = pg_query($this->_db,
                    sprintf("SELECT table_schema, table_name
                           FROM information_schema.tables
                          WHERE table_schema IN (%s) AND table_schema||'.'||table_name NOT IN (%s)
                            AND RIGHT(table_name, 4) = '_tbl'
                            AND count_rows(table_schema, table_name) > 0
                       ORDER BY table_schema, table_name",
                        implode(',', self::TRUNCATE_DB_SCHEMAS),
                        implode(',', self::EXCLUDE_TRUNCATE_TABLES)
                    )
                );
                
                
                $tables = pg_fetch_all($tables);
                
                if ($tables !== false) {
                    //disables all foreign key checks
                    $stmt = "SET session_replication_role = 'replica';\n";
                    foreach ($tables as $table) {
                        $stmt .= "DELETE FROM " . $table['table_schema'] . '.' . $table['table_name'] . ";\n";
                        $stmt .= "SELECT SetVal('" . $table['table_schema'] . '.' . $table['table_name'] . "_id_seq', 1, false);\n";
                    }
                    $stmt .= "SET session_replication_role = 'origin';\n";                    
                    pg_query($this->_db, $stmt);
                    
                    
                    if (!empty($error = pg_last_error($this->_db))) {
                        throw new ErrorException("Truncating" . $table['table_schema'] . '.' . $table['table_name'] . " failed: " . $error);
                    }
                }
            }
        }
    }
    
    private function _constDBConnString()
    {
        return "host=". $this->mPointDBInfo['host']. " port=". $this->mPointDBInfo['port']. " user=". $this->mPointDBInfo['username']. " password=" . $this->mPointDBInfo['password'];
    }
    
    protected function applyTestConfiguration()
    {
        $confDir = __DIR__. '/../../conf/';
        
        // Backup existing conf/global.php
        if(file_exists($confDir. 'global.php.backup')) {
            @unlink(realpath($confDir . 'global.php.backup'));
        }
        copy($confDir. 'global.php', $confDir. 'global.php.backup');
        touch($confDir. 'global.php.backup', filemtime($confDir. 'global.php') );
        
        // Add test configuration to global.php
        $h = fopen($confDir. 'global.php', 'a');
        fwrite($h, '<?php define("TESTDB_TOKEN", "'. TESTDB_TOKEN. '"); ?>'. file_get_contents($confDir. 'global_unittest.php') );
        fclose($h);
    }
    
    protected function restoreOriginalConfiguration()
    {
        $confDir = __DIR__. '/../../conf/';
        if(file_exists($confDir. 'global.php')) {
            unlink(realpath($confDir . 'global.php'));
        }
        copy($confDir. 'global.php.backup', $confDir. 'global.php');
        touch($confDir. 'global.php', filemtime($confDir. 'global.php.backup') );
    }
    
    public function tearDown(): void
    {
        $this->restoreOriginalConfiguration();
        if(isset($this->_db) === TRUE) {
            pg_close($this->_db);
        }
        parent::tearDown();
    }
    
    protected function queryDB($query)
    {
        if (!is_resource($this->_db) )
        {
            $this->_db = pg_connect($this->_constDBConnString(). " dbname=". $this->mPointDBInfo['path']);
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