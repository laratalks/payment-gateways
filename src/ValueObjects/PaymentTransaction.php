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
     * PaymentTransaction constructor.
     *
     * @param null $transField
     * @param null $transId
     * @param null $paymentField
     * @param null $paymentId
     */
    public function __construct($transField = null, $transId = null, $paymentField = null, $paymentId = null)
    {
        $this->setTransactionField($transField);
        $this->setTransactionId($transId);
        $this->setPaymentField($paymentField);
        $this->setPaymentId($paymentId);
    }

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
    protected function setTransactionId($transactionId)
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
    protected function setTransactionField($transactionField)
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
    protected function setPaymentId($paymentId)
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
    protected function setPaymentField($paymentField)
    {
        $this->paymentField = $paymentField;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];

        if (null !== $this->getPaymentField()) {
            $array[$this->getPaymentField()] = $this->getPaymentId();
        }

        if (null !== $this->getTransactionField()) {
            $array[$this->getTransactionField()] = $this->getTransactionId();
        }


        return $array;
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