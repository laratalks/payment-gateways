<?php

namespace Laratalks\PaymentGateways\Providers\Soap;


use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Laratalks\PaymentGateways\PaymentRequestResponse;
use Laratalks\PaymentGateways\VerifyResponse;
use Symfony\Component\HttpFoundation\Request;

class ZarinpalProvider extends BaseSoapProvider implements SoapProviderInterface
{


    /**
     * Get an array from the needs.
     *
     * @param \Laratalks\PaymentGateways\PaymentRequestNeeds $needs
     * @return array
     */
    protected function serializePaymentRequest(PaymentRequestNeeds $needs)
    {
        $array = [
            'MerchantID' => array_get($this->config, 'MerchantID', function () {
                throw new InvalidArgumentException('No MerchantID set for Zarinpal.');
            }),
            'Amount' => $needs->getAmount(),
            'CallbackURL' => $needs->getReturnUrl()
        ];

        if ($email = $needs->getCustomAttribute('Email')) {
            $array['Email'] = $email;
        }

        if ($desc = $needs->getCustomAttribute('Description')) {
            $array['Description'] = $desc;
        }

        if ($mob = $needs->getCustomAttribute('Mobile')) {
            $array['Mobile'] = $mob;
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    protected function serializeVerify($payload)
    {
        if ($payload instanceof Request) {
            return [
                'Authority' => $payload->get('Authority')
            ];
        }

    }

    /**
     * Handle request response
     *
     * @param $result
     * @return \Laratalks\PaymentGateways\Providers\PaymentRequestResponse
     */
    protected function handleRequestResponse($result)
    {
        // TODO: Implement handleRequestResponse() method.
    }

    /**
     * Handle verify response
     *
     * @param $result
     * @return \Laratalks\PaymentGateways\Providers\VerifyResponse
     */
    protected function handleVerifyResponse($result)
    {
        // TODO: Implement handleVerifyResponse() method.
    }

    /**
     * Get gateway payment URL :)
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        // TODO: Implement getPaymentUrl() method.
    }
}