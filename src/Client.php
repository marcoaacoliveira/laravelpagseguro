<?php

namespace Marcoaacoliveira\LaravelPagseguro;

use App\Models\Order;
use FluidXml\FluidXml;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\URL;
use Marcoaacoliveira\LaravelPagseguro\DTO\BasePayment;

class Client
{
    const DF_URL = 'https://df.uol.com.br/v2/';
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * @var mixed
     */
    private $url;

    public function __construct(\GuzzleHttp\Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->url = env('PAGSEGURO_URL', 'https://ws.sandbox.pagseguro.uol.com.br/');
    }

    public function getAuthorizationCode()
    {
        $requestXml = FluidXml::new('authorizationRequest');

        $requestXml->add('reference', 'REF1234')
            ->add('permissions', true)
            ->add('code', 'CREATE_CHECKOUTS')
            ->add('code', 'RECEIVE_TRANSACTION_NOTIFICATIONS')
            ->add('code', 'SEARCH_TRANSACTIONS')
            ->add('code', 'MANAGE_PAYMENT_PRE_APPROVALS')
            ->add('code', 'DIRECT_PAYMENT')
            ->append('notificationURL', route('laravel-pagseguro.notification'))
            ->append('redirectURL', route('laravel-pagseguro.redirect'));

        $response = $this->guzzle->request('POST', $this->url . 'v2/authorizations/request/?appId=' . env('PAGSEGURO_APP_ID') . '&appKey=' . env('PAGSEGURO_APP_KEY'), [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
            'body' => $requestXml->xml(),
        ]);
        $responseXml = new FluidXml($response->getBody()->getContents());
        $codeNode = isset($responseXml->query('//authorizationRequest//code')->array()[0]) ? $responseXml->query('//authorizationRequest//code')->array()[0] : null;
        if (!$codeNode) throw new \Exception('Não foi possível criar a autorização');

        return $codeNode->nodeValue;
    }

    public function getUrlAuthorization()
    {
        $code = $this->getAuthorizationCode();
        return 'https://sandbox.pagseguro.uol.com.br/v2/authorization/request.jhtml?code=' . $code;
    }

    public function createSession()
    {
        $response = $this->guzzle->request('POST', $this->url . 'sessions?appId=' . env('PAGSEGURO_APP_ID') . '&appKey=' . env('PAGSEGURO_APP_KEY'), [
            'headers' => [
                'Accept' => 'application/vnd.pagseguro.com.br.v3+xml',
            ],
            'form_params' => [
                'email' => env('PAGSEGURO_EMAIL'),
                'token' => env('PAGSEGURO_TOKEN'),
            ]
        ]);

        $responseXml = new FluidXml($response->getBody()->getContents());
        $sessionId = isset($responseXml->query('//session//id')->array()[0]) ? $responseXml->query('//session//id')->array()[0] : null;
        return $sessionId->nodeValue;
    }

    public function processPayment($payment)
    {
        $client = new \GuzzleHttp\Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/vnd.pagseguro.com.br.v3+xml'
        ];
        $options = [
            'form_params' => $payment
        ];

        $request = new Request('POST', $this->url . 'transactions?appId=' . env('PAGSEGURO_APP_ID') . '&appKey=' . env('PAGSEGURO_APP_KEY'), $headers);
        try {
            $response = $client->sendAsync($request, $options)->wait();
        } catch (\Exception $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
        $responseXml = new FluidXml($response->getBody()->getContents());

        dd($responseXml->xml());
    }

    public function createCreditCardToken($amount, $cardNumber, $cardBrand, $cvv, $expirationMonth, $expirationYear)
    {
        $headers = [
            'Accept' => 'application/x-www-form-urlencoded, application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'headers' => $headers,
            'form_params' => [
                'sessionId' => $this->createSession(),
                'amount' => $amount,
                'cardNumber' => $cardNumber,
                'cardBrand' => $cardBrand,
                'cardCvv' => $cvv,
                'cardExpirationMonth' => $expirationMonth,
                'cardExpirationYear' => $expirationYear
            ]];

        $response = $this->guzzle->request('POST', self::DF_URL . 'cards/?email=' . env('PAGSEGURO_EMAIL') . '&token=' . env('PAGSEGURO_TOKEN'), $options);
        return json_decode($response->getBody()->getContents())->token ?? null;
    }


    public function getPaymentMethods($amount)
    {
        try {
            $response = $this->guzzle->request('GET', $this->url . 'payment-methods?amount=' . number_format($amount, '2', '.', '') . '&sessionId=' . $this->createSession(), [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v1+xml;charset=ISO-8859-1',
                ],
            ]);
        } catch (\Exception $e) {
            logger()->error($e->getResponse()->getBody()->getContents());
        }
        $responseXml = new FluidXml($response->getBody()->getContents());
        return $responseXml->query('//paymentMethods//paymentMethod')->array();
    }
}
