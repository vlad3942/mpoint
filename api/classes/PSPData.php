<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:PSPData.php
 */

namespace api\classes;

use JsonSerializable;
use api\interfaces\XMLSerializable;

/**
 * Class PSPData
 *
 * @package api\classes
 * @xmlName psp
 */
class PSPData implements JsonSerializable, XMLSerializable
{
    private int $id;

    private ?string $name;

    private ?string $external_id;

    /**
     * PSPData constructor.
     *
     * @param int $id
     * @param string|null $name
     * @param string|null $external_id
     */
    public function __construct(int $id, ?string $name = NULL, ?string $external_id = NULL)
    {
        if($id > 0) {
            $this->id = $id;
            $this->name = $name;
            $this->external_id = $external_id;
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }

    /**
     * @return array
     */
    public function xmlSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }
}