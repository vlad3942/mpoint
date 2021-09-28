<?php

/*
interface IConfig
{

    public function getConfiguration();

    public function getServiceType();

    public function getProperties();

}
*/

class SQLOperation
{
   private int $_iOperationStatus;
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
    const eFailed = 1;
}