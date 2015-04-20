<?php

abstract class mPointBaseAPITest extends mPointBaseDatabaseTest
{
	protected $bIgnoreErrors = false;

    public function setUp()
    {
        if (!file_exists(sLOG_PATH) )
        {
            mkdir(sLOG_PATH);
            @chmod(sLOG_PATH, octdec(777) );
        }
		//empty error log file prior to test-run
		file_put_contents(sERROR_LOG, '');
        parent::setup();
    }

    /**
     * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
     *
     * @return string
     */
    protected function constHTTPHeaders()
    {
        /* ----- Construct HTTP Header Start ----- */
        $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
        $h .= "host: {HOST}" .HTTPClient::CRLF;
        $h .= "referer: {REFERER}" .HTTPClient::CRLF;
        $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
        $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
        $h .= "user-agent: mPoint" .HTTPClient::CRLF;
        /* ----- Construct HTTP Header End ----- */

        return $h;
    }

	public function tearDown()
	{
		parent::tearDown();

		if ($this->bIgnoreErrors === false)
		{
			// Check for errors and warnings in app_error log file
			$aLogLines = file(sERROR_LOG, FILE_IGNORE_NEW_LINES);
			$this->assertNotContains("USER WARNING", $aLogLines);
			$this->assertNotContains("USER ERROR", $aLogLines);
			$this->assertNotContains("ERROR", $aLogLines);
			$this->assertNotContains("WARNING", $aLogLines);
		}
	}

}