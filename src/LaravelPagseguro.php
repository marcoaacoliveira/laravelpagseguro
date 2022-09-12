<?php

namespace Marcoaacoliveira\LaravelPagseguro;

use App\Models\Order;
use Illuminate\Http\Request;
use Marcoaacoliveira\LaravelPagseguro\DTO\BasePayment;
use Marcoaacoliveira\LaravelPagseguro\Models\Authorization;
use Marcoaacoliveira\LaravelPagseguro\Models\Token;

class LaravelPagseguro
{
    public static function test()
    {

        dd(self::getAuthorizationUrl());
        $order = Order::find(8);
        $items = $order->cart->items;
        $mapItems = BasePayment::mapItems($items, ['id' => 'id', 'description' => 'sellable.name', 'amount'=>'custom_price', 'quantity'=>'qty']);

        $student = $order->cart->student;

        $exploded = explode(',', $student->address);
        $street = $exploded[0];
        $number = $exploded[1];
        $mapShipping = BasePayment::mapShipping(
            [
                ...$student->only(['cep', 'state', 'city']),
                'street' => $street,
                'number' => $number,
                'complement' => '',
                'district' => 'Vila Olimpia',
            ],
            [
                'street'=>'street',
                'number'=>'number',
                'complement'=>'complement',
                'district' => 'district',
                'postalCode' => 'cep' ,
                'city' => 'city',
                'state' => 'state'
            ]
        );

        preg_match('/\(([^)]+)\)/', $student->phone, $matches);
        $code = $matches[1];

        $phone = preg_replace('/[^0-9]/', '', preg_split('/\(([^)]+)\)/', $student->phone)[1]);

        $mapSender = BasePayment::mapSender(
            [
                ...$student->only(['name', 'email', 'cpf']),
                'areaCode'=> $code,
                'phone'=> $phone,
            ],
            [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'phone',
                'cpf' => 'cpf',
                'areaCode'=> 'areaCode'
            ]
        );

        $amount = $order->total_amount;

        $cardNumber = '4111111111111111';
        $cvv = '123';
        $cardExpirationMonth = '12';
        $token = self::storeCreditCardToken($amount, $cardNumber, 'visa', $cvv, $cardExpirationMonth, '2022');

        $mapCreditCard = BasePayment::mapCreditCard(
            [
                ...$student->only(['name', 'cpf']),
                'areaCode' => $code,
                'phone' => $phone,
                'token' => $token->token,
                'birthDate' => '01/01/1990',
            ],
            [
                'name' => 'name',
                'cpf' => 'cpf',
                'phone' => 'phone',
                'areaCode'=> 'areaCode',
                'token' => 'token',
                'birthDate' => 'birthDate',
            ]
        );

        $mapBillingAddress = BasePayment::mapBillingAddress(
            [
                'street' => $street,
                'number' => $number,
                'complement' => '',
                'district' => 'Vila Olimpia',
                ...$student->only(['cep', 'state', 'city']),
            ],
            [
                'street'=>'street',
                'number'=>'number',
                'complement'=>'complement',
                'district' => 'district',
                'postalCode' => 'cep' ,
                'city' => 'city',
                'state' => 'state'
            ]
        );
        $totalInstallments = 2;
        $mapInstallment = BasePayment::mapInstallment([
            'quantity' => (string) $totalInstallments,
            'value' => $order->total_amount/$totalInstallments,
            'noInterestInstallmentQuantity' => '2',
        ],
        [
            'quantity' => 'quantity',
            'value' => 'value',
            'noInterestInstallmentQuantity' => 'noInterestInstallmentQuantity'
        ]);

        $payment = BasePayment::map($mapItems, $mapShipping, $mapSender, $mapCreditCard, $mapBillingAddress, $mapInstallment);

        dd(self::processPayment($payment));
    }

    public static function processPayment($payment) {
        return self::getClient()->processPayment($payment);
    }
    public static function getClient()
    {
        return app()->make(Client::class);
    }

    public static function createSession() {
        return self::getClient()->createSession();
    }

    public static function getPaymentMethods ($amount) {
        return self::getClient()->getPaymentMethods($amount);
    }

    public static function getAuthorizationUrl() {
        return self::getClient()->getUrlAuthorization();
    }


    public static function createCreditCardToken($amount, $cardNumber, $cardBrand, $cvv, $expirationMonth, $expirationYear) {
        return self::getClient()->createCreditCardToken($amount, $cardNumber, $cardBrand, $cvv, $expirationMonth, $expirationYear);
    }

    public static function storeCreditCardToken($amount, $cardNumber, $cardBrand, $cvv, $expirationMonth, $expirationYear) {
        $token = self::createCreditCardToken($amount, $cardNumber, $cardBrand, $cvv, $expirationMonth, $expirationYear);
        $tokenModel = new Token(['token'=>$token, 'number'=>$cardNumber]);
        return auth()->guard('dashboard')->user()->token()->save($tokenModel);
    }

    public function notification(Request $request)
    {
        header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
        header("access-control-allow-methods: POST");
        logger()->debug($request->all());
    }

    public function redirect(Request $request)
    {
        $publicKey = $request->get('publicKey');

        logger()->debug($publicKey);

        $authorization = new Authorization(['code'=>$publicKey]);
        $authorization->save();
    }
}
