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
     * Handle request response
     *
     * @param \stdClass $result
     * @return PaymentRequestResponse
     */
    protected function handleRequestResponse(\stdClass $result)
    {
        // TODO: Implement handleRequestResponse() method.
    }

    /**
     * Handle verify response
     *
     * @param \stdClass $result
     * @return VerifyResponse
     */
    protected function handleVerifyResponse(\stdClass $result)
    {
        // TODO: Implement handleVerifyResponse() method.
    }

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
     * Serialize symfony request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    protected function serializeVerify(Request $request)
    {
        return [
            'Authority' => $request->get('Authority')
        ];
    }
}