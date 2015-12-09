<?php
namespace Laratalks\PaymentGateways\Providers\Rest;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayResponseException;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\Providers\PaymentRequestResponse;
use Laratalks\PaymentGateways\Providers\VerifyResponse;
use Symfony\Component\HttpFoundation\Request;

class PaylineProvider extends BaseRestProvider
{
    protected $paymentId;

    /**
     * Call endpoint and get return url
     *
     * @param \Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds $needs
     * @return string
     * @throws PaymentGatewayResponseException
     */
    public function callAndGetReturnUrl(PaymentRequestNeeds $needs)
    {
        $request = $this
            ->getHttpClient()
            ->post(
                $this->getFromConfig('providers.payline.gateway_send_url'),
                $this->serializePaymentRequest($needs)
            );

        $this->paymentId = $request->getBody()->getContents();

        if ($this->paymentId <= 0) {
            throw new PaymentGatewayResponseException($this->paymentId, 'payline');
        }


        header('Location: ' . $this->getPaymentUrl(), true);
        die;
    }


    /**
     * @return string
     */
    public function getPaymentUrl()
    {
        return sprintf($this->getFromConfig('providers.payline.formatted_payment_url'), $this->paymentId);
    }


    /**
     * {@inheritdoc}
     */
    public function callAndVerify($payload)
    {
        $request = $this
            ->getHttpClient()
            ->post(
                $this->getFromConfig('providers.payline.gateway_verify_url'),
                $this->serializeVerify($payload)
            );

        if ($request->getBody()->getContents() !== 1) {
            throw new PaymentGatewayResponseException;
        }

    }

    /**
     * Get an array from the needs.
     *
     * @param PaymentRequestNeeds $needs
     * @return array
     */
    protected function serializePaymentRequest(PaymentRequestNeeds $needs)
    {
        return [
            'api' => $this->getFromConfig('providers.payline.api'),
            'amount' => $needs->getAmount(),
            'redirect' => urlencode($needs->getReturnUrl())
        ];
    }

    /**
     * Serialize Verify request payload
     *
     * @param array|Request $payload
     * @return array
     */
    protected function serializeVerify($payload)
    {
        $apiKey = $this->getFromConfig('providers.payline.api');

        if ($payload instanceof Request) {
            return  [
                'trans_id' => $payload->get('trans_id'),
                'id_get' => $payload->get('id_get'),
                'api' => $apiKey,
            ];
        }

        if (is_array($payload)) {
            return [
                'trans_id' => array_get($payload, 'trans_id'),
                'id_get' => array_get($payload, 'id_get'),
                'api' => $apiKey
            ];
        }

        throw new InvalidArgumentException('Verify payload required.');
    }

    /**
     * Handle request response
     *
     * @param $result
     * @return PaymentRequestResponse
     */
    protected function handleRequestResponse($result)
    {
        // TODO: Implement handleRequestResponse() method.
    }

    /**
     * Handle verify response
     *
     * @param $result
     * @return VerifyResponse
     */
    protected function handleVerifyResponse($result)
    {
        // TODO: Implement handleVerifyResponse() method.
    }
}