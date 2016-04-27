<?php
namespace Laratalks\PaymentGateways\Providers\Rest;

use Laratalks\PaymentGateways\Exceptions\InvalidArgumentException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayResponseException;
use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentTransaction;
use Symfony\Component\HttpFoundation\Request;

class PaylineProvider extends BaseRestProvider
{
    public function getName()
    {
        return 'payline';
    }

    /**
     * {@inheritdoc}
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {

        $response = $this->getClient()->request(
            'POST',
            $this->getProviderConfig('gateway_send_url'),
            [
                'form_params' => $this->serializePaymentRequest($needs)
            ]
        );


        return $this->createPaymentRequestResponse($response->getBody()->getContents());
    }

    public function createPaymentRequestResponse($results)
    {
        if ($results <= 0) {
            throw new PaymentGatewayResponseException;
        }

        $response = new PaymentResponse();
        $response->set('payment_id', $results);
        $response->set('payment_url', $this->generatePaymentUrl($results));

        return $response;
    }


    /**
     * @param $paymentId
     * @return string
     */
    public function generatePaymentUrl($paymentId)
    {
        return sprintf($this->getProviderConfig('gateway_payment_url'), $paymentId);
    }


    /**
     * {@inheritdoc}
     */
    public function callVerifyRequest(PaymentNeeds $paymentNeeds)
    {
        $serializedPayload = $this->serializeVerify($paymentNeeds);

        $request = $this
            ->getClient()
            ->post(
                $this->getProviderConfig('gateway_verify_url'),
                $serializedPayload
            );

        if ($request->getBody()->getContents() !== 1) {
            throw new PaymentGatewayResponseException;
        }

        return new PaymentTransaction(
            'trans_id',
            $serializedPayload['trans_id'],
            'id_get',
            $serializedPayload['id_get']
        );
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
            'api' => $this->getProviderConfig('api'),
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
        $apiKey = $this->getProviderConfig('api');

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

}