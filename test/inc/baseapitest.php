<?php

abstract class baseAPITest extends BaseDatabaseTest
{
	protected $bIgnoreErrors = false;
	private static $aVisited = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp($isDBSetupRequired = true):void
    {
        if (!file_exists(sLOG_PATH) )
        {
            mkdir(sLOG_PATH);
            @chmod(sLOG_PATH, octdec(777) );
        }
		//empty error log file prior to test-run
		if (count(self::$aVisited) == 0) { @copy(sERROR_LOG, sERROR_LOG .'.before-test.backup'); }
		file_put_contents(sERROR_LOG, '');

		echo " ". get_class($this) ."::". $this->getName() ."()\n";
        parent::setup($isDBSetupRequired);
    }

    /**
     * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
     *
     * @return string
     */
    protected function constHTTPHeaders($authUser=null,$authPass=null)
    {
        /* ----- Construct HTTP Header Start ----- */
        $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
        $h .= "host: {HOST}" .HTTPClient::CRLF;
        $h .= "referer: {REFERER}" .HTTPClient::CRLF;
        $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
        $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
        $h .= "user-agent: mPoint" .HTTPClient::CRLF;
		if (isset($authUser) === true && isset($authPass) === true)
		{
			$h .= "Authorization: Basic ". base64_encode($authUser. ":". $authPass) .HTTPClient::CRLF;
		}
        /* ----- Construct HTTP Header End ----- */

        return $h;
    }

	public function getErrorLogContent()
	{
		return file(sERROR_LOG, FILE_IGNORE_NEW_LINES);
	}

	public function tearDown(): void
	{
		parent::tearDown();

		$aLogLines = $this->getErrorLogContent();

		$me = get_class($this);
		$mode = array_search($me, self::$aVisited) === false ? 'w' : 'a';
		self::$aVisited[] = $me;
		$handle = fopen(sERROR_LOG .'.'. $me. '.backup', $mode);
		fwrite($handle, "\n");
		fwrite($handle, "---------------------------------------------------\n");
		fwrite($handle, $this->getName() ."\n");
		fwrite($handle, "---------------------------------------------------\n");
		foreach ($aLogLines as $line)
		{
			fwrite($handle, $line. "\n");
		}
		fclose($handle);

		if ($this->bIgnoreErrors === false)
		{
			// Check for errors and warnings in app_error log file
			$this->assertNotContains("USER WARNING", $aLogLines);
			$this->assertNotContains("USER ERROR", $aLogLines);
			$this->assertNotContains("ERROR", $aLogLines);
			$this->assertNotContains("WARNING", $aLogLines);
			$this->assertNotContains("UNCAUGHT EXCEPTION", $aLogLines);
		}
	}

    protected function getReflectionMethod($className,$methodName)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

}