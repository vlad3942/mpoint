<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:ProductTest.php
 */

use api\classes\core\Product;
require_once '/opt/cpm/mPoint/webroot/inc/include.php';
require_once '/opt/cpm/mPoint/test/inc/testinclude.php';

class ProductTest extends baseAPITest
{
    public bool $isDBSetupRequired = true;
    public function setUp() : void
    {
        parent::setUp($this->isDBSetupRequired);
    }

    public function testProduceProducts()
    {
        $this->isDBSetupRequired = false;
        $_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (1, 'P1', 'Product1', 100, 10099, true)");
        $this->queryDB("INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (2, 'P2', 'Product2', 200, 10099, true)");
        $this->queryDB("INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (3, 'P3', 'Product2', 200, 10099, false)");

        //$_OBJ_DB

        $response = Product::produceProducts($_OBJ_DB, 10099);
        self::assertIsArray($response);
        self::assertCount(3,$response);
    }

    public function test__construct()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertInstanceOf(Product::class, $product);
    }

    public function testGetId()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertEquals(1, $product->getId());
    }

    public function testGetDescription()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertEquals('Product 1', $product->getDescription());
    }

    public function testGetCode()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertEquals('P1', $product->getCode());
    }

    public function testGetProductCategoryId()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertEquals(100, $product->getProductCategoryId());
    }

    public function testIsEnabled()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertTrue($product->isEnabled());
    }

    public function testToXML()
    {
        $product = new Product(1, 'P1', 'Product 1', 100, true);
        self::assertEquals('<product><id>1</id><code>P1</code><description>Product 1</description><product_category_id>100</product_category_id><enabled>true</enabled></product>', $product->toXML());
    }
}
