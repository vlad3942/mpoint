<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.core
 * File Name:Product.php
 */

namespace api\classes\core;


class Product
{
    private int $id;

    private string $code;

    private string $description;

    private string $product_category_id;

    private bool $enabled;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Product constructor.
     * @param int $id
     * @param string $code
     * @param string $description
     * @param string $product_category_id
     * @param bool $enabled
     */
    public function __construct(int $id, string $code, string $description, string $product_category_id, bool $enabled)
    {
        $this->id = $id;
        $this->code = $code;
        $this->description = $description;
        $this->product_category_id = $product_category_id;
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getProductCategoryId(): string
    {
        return $this->product_category_id;
    }

    /**
     * @return string
     */
    public function toXML(): string
    {
        $xml = '<product>';
        $xml .= '<id>' . $this->getID() . '</id>';
        $xml .= '<code>' . $this->getCode() . '</code>';
        $xml .= '<description>' . $this->getDescription() . '</description>';
        $xml .= '<product_category_id>' . $this->getProductCategoryId() . '</product_category_id>';
        $xml .= '<enabled>' . \General::bool2xml($this->isEnabled()) . '</enabled>';
        $xml .= '</product>';
        return $xml;
    }

    /**
     * @param int $clientId
     * @return Product[]
     */
    public static function produceProducts(\RDB $obj_DB, int $clientId): array
    {
        $products = [];
        $sql = 'SELECT ID, CODE, DESCRIPTION, PRODUCTTYPEID, ENABLED
				FROM Client'. sSCHEMA_POSTFIX .'.Product_Tbl
				WHERE ClientId = '. $clientId;
        try {
            $res = $obj_DB->query($sql);
            while ($RS = $obj_DB->fetchName($res)) {
                $products[] = new Product ($RS['ID'], $RS['CODE'], $RS['DESCRIPTION'], $RS['PRODUCTTYPEID'], $RS['ENABLED']);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $products;
    }

}