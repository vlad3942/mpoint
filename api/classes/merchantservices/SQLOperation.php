<?php

/*
interface BaseConfig
{

    public function getConfiguration();

    public function getServiceType();

    public function getProperties();

}
*/
namespace api\classes\merchantservices;

class SQLOperation
{
   private int $_iOperationStatus = 0;
   private string $_sErrorMsg;

    /**
     * @return int
     */
    public function getOperationStatus(): int
    {
        return $this->_iOperationStatus;
    }

    /**
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->_sErrorMsg;
    }

    /**
     * @param string $sErrorMsg
     */
    public function setErrorMsg(string $sErrorMsg): void
    {
        $this->_sErrorMsg = $sErrorMsg;
    }

    /**
     * @param int $iOperationStatus
     */
    public function setOperationStatus(int $iOperationStatus): void
    {
        $this->_iOperationStatus = $iOperationStatus;
    }
   public function __construct(){}



}
abstract class OperationStatus
{
    const eSuccessful = 1;
    const eFailed = 2;
    const eDuplicate = 3;
    public static function toString(int $status)
    {
        switch ($status)
        {
            case self::eSuccessful:
                return "Successful";
            case self::eFailed:
                return "Failed";
            case self::eDuplicate:
                return "Duplicated";
        }
    }
}