<?php

abstract class mPointBaseAPITest extends mPointBaseDatabaseTest
{

    public function setUp()
    {
        if (!file_exists(sPROJECT_BASE_DIR. '/log') )
        {
            mkdir(sPROJECT_BASE_DIR. '/log');
            @chmod(sPROJECT_BASE_DIR. '/log', octdec(777) );
        }
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

}