<?php

namespace Laratalks\PaymentGateways\Providers\Soap;

use Laratalks\PaymentGateways\Exceptions\InvalidPaymentNeedsException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayBadRequestException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;
use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentTransaction;
use Laratalks\PaymentGateways\ValueObjects\ZarinpalPaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

class ZarinpalProvider extends BaseSoapProvider implements SoapProviderInterface
{

    public function getName()
    {
        return 'zarinpal';
    }

    /**
     * @param PaymentNeeds|ZarinpalPaymentRequestNeeds $needs
     * @return array
     * @throws InvalidPaymentNeedsException
     */
    protected function serializePaymentRequest(PaymentNeeds $needs)
    {
        return [
            'MerchantID' => $this->getProviderConfig('merchant_id'),
            'Amount' => $needs->getAmount(),
            'CallbackURL' => $needs->getReturnUrl(),
            'Description' => $needs->get('description'),
            'Email' => $needs->get('email'),
            'Mobile' => $needs->get('mobile')
        ];
    }


    /**
     * @param PaymentNeeds $needs
     * @return array
     */
    protected function serializeVerifyPayload(PaymentNeeds $needs)
    {
        return [
            'MerchantID' => $this->getProviderConfig('merchant_id'),
            'Amount' => $needs->get('amount'),
            'Authority' => $needs->get('authority')
        ];
    }

    /**
     * @param PaymentRequestNeeds $needs
     * @return PaymentResponse
     * @throws InvalidPaymentNeedsException
     * @throws PaymentGatewayBadRequestException
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {
        if (!$needs->isVerified()) {
            throw new InvalidPaymentNeedsException;
        }


        $response = $this->getClient()->PaymentRequest($this->serializePaymentRequest($needs));

        // Handle response and set Authority field
        return $this->createPaymentRequestResponse($response);
    }

    /**
     * @param PaymentNeeds $needs
     * @return PaymentResponse
     * @throws InvalidPaymentNeedsException
     * @throws PaymentGatewayException
     */
    public function callVerifyRequest(PaymentNeeds $needs)
    {
        if (!static::checkPaymentStatusIsOK()) {
            throw new PaymentGatewayException;
        }
        
        if (!$needs->isVerified()) {
            throw new InvalidPaymentNeedsException;
        }

        $response = $this->getClient()->PaymentVerification($this->serializeVerifyPayload($needs));

        return $this->createPaymentVerifyResponse($response);
    }


    /**
     * @param Request|null $request
     * @return bool
     */
    public static function checkPaymentStatusIsOK($request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        return $request->get('Status') === 'OK';
    }

    /**
     * @param $authority
     * @return mixed
     */
    protected function generatePaymentUrl($authority)
    {
        return sprintf($this->getProviderConfig('gateway_send_url'), $authority);
    }

    /**
     * @param $response
     * @return PaymentResponse
     * @throws PaymentGatewayBadRequestException
     */
    protected function createPaymentRequestResponse($response)
    {
        if ($response->Status !== 100) {
            throw new PaymentGatewayBadRequestException();
        }

        $paymentResponse = new PaymentResponse();
        $paymentResponse->set('authority', $response->Authority);
        $paymentResponse->set('payment_url', $this->generatePaymentUrl($response->Authority));
        
        return $paymentResponse;
    }

    protected function createPaymentVerifyResponse($response)
    {
        $paymentResponse = new PaymentResponse();
        $paymentResponse
            ->set('status', $response->Status)
            ->set('ref_id', $response->RefId);
        
        return $paymentResponse;
    }
}