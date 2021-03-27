<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:RouteFeatureTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/RouteFeature.php';

class RouteFeatureTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetRouteFeature()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $aObj_ClientRouteFeatureConfig = RouteFeature::produceConfig($this->_OBJ_DB, 10099);
        $this->assertIsArray($aObj_ClientRouteFeatureConfig);
        $xml = '<route_features>';
        foreach ($aObj_ClientRouteFeatureConfig as $obj_RF)
        {
            $this->assertInstanceOf(RouteFeature::class, $obj_RF);

            if ( ($obj_RF instanceof RouteFeature) === true)
            {
                $xml .= $obj_RF->toXML();
            }
        }
        $xml .= '</route_features>';

        $this->assertStringContainsString('<route_features><route_feature><id>1</id><name>Pre-Auth</name></route_feature><route_feature><id>2</id><name>Delayed Capture</name></route_feature><route_feature><id>3</id><name>Batch Capture</name></route_feature><route_feature><id>4</id><name>Partial Capture</name></route_feature><route_feature><id>5</id><name>Refund</name></route_feature><route_feature><id>6</id><name>Partial Refund</name></route_feature><route_feature><id>7</id><name>Settlement</name></route_feature><route_feature><id>8</id><name>Recurring</name></route_feature><route_feature><id>9</id><name>3DS</name></route_feature><route_feature><id>10</id><name>Installment</name></route_feature><route_feature><id>11</id><name>Store Card</name></route_feature><route_feature><id>12</id><name>Channel Mobile</name></route_feature><route_feature><id>13</id><name>Channel Web</name></route_feature><route_feature><id>14</id><name>Channel Others</name></route_feature><route_feature><id>15</id><name>Pre-auth Fraud</name></route_feature><route_feature><id>16</id><name>Post-Auth Fraud</name></route_feature><route_feature><id>17</id><name>Voids</name></route_feature></route_features>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
