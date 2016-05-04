<?php
namespace Laratalks\PaymentGateways\Providers\Rest;

use Laratalks\PaymentGateways\Exceptions\InvalidPaymentNeedsException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayBadRequestException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayException;
use Laratalks\PaymentGateways\Exceptions\PaymentGatewayResponseException;
use Laratalks\PaymentGateways\Exceptions\PaymentNotVerifiedException;
use Laratalks\PaymentGateways\Providers\ProviderInterface;
use Laratalks\PaymentGateways\ValueObjects\PaymentNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestNeeds;
use Laratalks\PaymentGateways\ValueObjects\PaymentRequestResponse;
use Laratalks\PaymentGateways\ValueObjects\PaymentVerifyResponse;
use Symfony\Component\HttpFoundation\Request;

class UpalProvider extends BaseRestProvider implements ProviderInterface
{

    const PAYMENT_REQUEST_URL = 'http://upal.ir//transaction/create';
    const PAYMENT_URL = 'http://upal.ir/transaction/submit?id=%d';

    /**
     * @return string
     */
    public function getName()
    {
        return 'upal';
    }

    /**
     * @inheritdoc
     */
    public function callPaymentRequest(PaymentRequestNeeds $needs)
    {
        try {

            $payload = [
                'gateway_id' => $this->getProviderConfig('gateway_id'),
                'amount' => $needs->getAmount(),
                'rand' => substr(md5(time() . microtime()), 0, 16),
                'redirect_url' => urlencode($needs->getReturnUrl()),
                'description' => $needs->get('description', '')
            ];

            // create request and
            // get response
            $response = $this->getClient()
                ->request('POST', static::PAYMENT_REQUEST_URL, [
                    'form_params' => $payload
                ])
                ->getBody()
                ->getContents();

            // check response is valid
            if (is_numeric($response) == false || $response < 0) {
                throw new PaymentGatewayResponseException($response);
            }

            // returns PaymentResponse with
            // `payment_url`, `payment_id` and `valid` fields
            return (new PaymentRequestResponse($this->generatePaymentUrl((int)$response)))
                ->set('payment_id', (int)$response)
                ->set('valid', $this->generateValid($payload['amount'], $payload['rand']));

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
        $request = $request === null ? Request::createFromGlobals() : $request;

        if ($needs->has('valid') === false) {
            throw new InvalidPaymentNeedsException;
        }

        if ($request->get('valid') === $needs->get('valid')) {
            return new PaymentVerifyResponse($request->get('trans_id'));
        }

        throw new PaymentNotVerifiedException;
    }

    /**
     * @param $paymentId
     * @return string
     */
    protected function generatePaymentUrl($paymentId)
    {
        return sprintf(static::PAYMENT_URL, $paymentId);
    }

    /**
     * @param $amount
     * @param $rand
     * @return string
     */
    protected function generateValid($amount, $rand)
    {
        return md5($this->getProviderConfig('gateway_id') . $amount . $this->getProviderConfig('api') . $rand);
    }

}