<?php
namespace Laratalks\PaymentGateways\ValueObjects;

/**
 * Class PaymentTransaction
 * @package Laratalks\PaymentGateways\ValueObjects
 */
class PaymentTransaction implements \JsonSerializable
{
    /**
     * @var
     */
    protected $transactionId;

    /**
     * @var
     */
    protected $transactionField;

    /**
     * @var
     */
    protected $paymentId;

    /**
     * @var
     */
    protected $paymentField;

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionField()
    {
        return $this->transactionField;
    }

    /**
     * @param mixed $transactionField
     */
    public function setTransactionField($transactionField)
    {
        $this->transactionField = $transactionField;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getPaymentField()
    {
        return $this->paymentField;
    }

    /**
     * @param mixed $paymentField
     */
    public function setPaymentField($paymentField)
    {
        $this->paymentField = $paymentField;
    }


    /**
     * @return array
     */
    public function toArray()
    {

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}