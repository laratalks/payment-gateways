<?php

namespace Laratalks\PaymentGateways\Providers\Soap;

use Laratalks\PaymentGateways\Exceptions\InvalidPaymentNeedsException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayBadRequestException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayResponseException;
use Laratalks\PaymentGateways\Exceptions\PaymentNotVerifiedException;
use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentVerifyResponse;
use Symfony\Component\HttpFoundation\Request;

class ZarinpalProvider extends BaseSoapProvider implements SoapProviderInterface
{
    const ZARINPAL_PAYMENT_URL = '‫‪https://www.zarinpal.com/pg/StartPay/%s';

    /**
     * Get wsdl endpoint
     *
     * @return string
     */
    public function getWsdl()
    {
        return 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zarinpal';
    }

    /**
     * @inheritdoc
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {
        try {
            if (!$needs->isVerified()) {
                throw new InvalidPaymentNeedsException;
            }

            $response = $this
                ->getClient()
                ->PaymentRequest($this->serializePaymentRequest($needs));

            if ($response->Status !== 100) {
                throw new PaymentGatewayResponseException();
            }

            // Create PaymentResponse with
            // `payment_url` and `authority` fields
            return (new PaymentRequestResponse($this->generatePaymentUrl($response->Authority)))
                ->set('authority', $response->Authority);

        } catch (\Exception $e) {

            if ($e instanceof PaymentGatewayException) {
                throw $e;
            }

            throw new PaymentGatewayBadRequestException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function callVerifyRequest(PaymentNeeds $needs, Request $request = null)
    {
        try {

            $request = $request === null ? Request::createFromGlobals() : $request;

            // Check payment process is successful or not
            if ($request->get('Status') !== 'OK') {
                throw new PaymentNotVerifiedException;
            }

            // Make Request and get Request
            $response = $this
                ->getClient()
                ->PaymentVerification($this->serializeVerifyPayload($needs));


            // Create PaymentResponse with
            // `transaction_id` and `status` fields
            return (new PaymentVerifyResponse($response->RefId))
                ->set('status', $response->Status);

        } catch (\Exception $e) {
            if ($e instanceof PaymentGatewayException) {
                throw $e;
            }

            throw new PaymentGatewayBadRequestException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * @param PaymentRequestNeeds $needs
     * @return array
     * @throws InvalidPaymentNeedsException
     */
    protected function serializePaymentRequest(PaymentRequestNeeds $needs)
    {
        if ($needs->hasAll('amount', 'return_url', 'description') === false) {
            throw new InvalidPaymentNeedsException;
        }

        return [
            'MerchantID' => $this->getProviderConfig('api_key'),
            'Amount' => $this->calculateAmount($needs->getAmount()),
            'CallbackURL' => $needs->getReturnUrl(),
            'Description' => $needs->get('description'),
            'Email' => $needs->get('email'),
            'Mobile' => $needs->get('mobile')
        ];
    }

    /**
     * @param PaymentNeeds $needs
     * @return array
     * @throws InvalidPaymentNeedsException
     */
    protected function serializeVerifyPayload(PaymentNeeds $needs)
    {
        if ($needs->hasAll('amount', 'authority')) {
            throw new InvalidPaymentNeedsException;
        }

        return [
            'MerchantID' => $this->getProviderConfig('api_key'),
            'Amount' => $this->calculateAmount($needs->get('amount')),
            'Authority' => $needs->get('authority')
        ];
    }


    /**
     * @param $authority
     * @return mixed
     */
    protected function generatePaymentUrl($authority)
    {
        return sprintf(static::ZARINPAL_PAYMENT_URL, $authority);
    }

    /**
     * @inheritdoc
     */
    protected function calculateAmount($amount)
    {
        return $amount / 10;
    }
}