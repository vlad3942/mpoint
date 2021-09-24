<?php
namespace api\classes\merchantservices\Repositories;

use api\classes\merchantservices\Repositories\IRepository;

class MerchantConfigRepository implements IRepository
{
    private $conn;

    public function __construct($conn)     
    {
        $this->conn = $conn;
    }

    public function find($cond = array()) {

        $sql = "Select id FROM client.client_tbl LIMIT 1";
        $RS = $this->conn->getName($sql);
        
        return $RS;

    }
}