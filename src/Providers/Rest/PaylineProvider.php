<?php
namespace Laratalks\PaymentGateways\Providers\Rest;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayResponseException;
use Laratalks\PaymentGateways\PaymentRequestNeeds;
use Symfony\Component\HttpFoundation\Request;

class PaylineProvider extends BaseRestProvider
{

    const GATEWAY_SEND_URL = 'http://payline.ir/payment/gateway-send';
    const PROVIDER_NAME = 'Payline';

    /**
     * Call endpoint and get return url
     *
     * @param \Laratalks\PaymentGateways\PaymentRequestNeeds $needs
     * @return string
     * @throws PaymentGatewayResponseException
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs)
    {
        $request = $this->getHttpClient()->post(self::GATEWAY_SEND_URL, [
            'api' => array_get($this->config, 'api', function () {
                throw new InvalidArgumentException('Api key required.');
            }),
            'amount' => $needs->getAmount(),
            'redirect' => urlencode($needs->getReturnUrl())
        ]);

        $idGet = $request->getBody()->getContents();

        if ($idGet <= 0) {
            throw new PaymentGatewayResponseException($idGet, self::PROVIDER_NAME);
        }

        header('Location: ' . $this->getPaymentUrl($idGet), true);
        die;
    }

    /**
     * Call and verify for given symfony request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    public function callAndVerify(Request $request)
    {

    }

    /**
     * @author Morteza Parvini <m.parvini@outlook.com>
     * @param $args
     * @return string
     */
    protected function getPaymentUrl($args)
    {
        return sprintf("http://paline.ir/payment/gateway-%d", $args);
    }
}