<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/authorizeAPITest.php';

class NetaxeptAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        parent::testSuccessfulAuthorize(Constants::iNETAXEPT_PSP);
    }

}