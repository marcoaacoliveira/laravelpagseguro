<?php
namespace Marcoaacoliveira\LaravelPagseguro\DTO;

use Illuminate\Support\Facades\URL;
use Marcoaacoliveira\LaravelPagseguro\Models\Authorization;

class BasePayment {

    public static function validateKeys($keys, $array) {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                throw new \Exception("Key $key not found. You must map the $key in your array");
            }
        }
    }

    /**
     * Create an array with the keys and values of the object using the dot notation
     * Example:
     * ['id' => 'id', 'description' => 'sellable.name', 'amount'=>'custom_price', 'quantity'=>'qty']
     * For more details check the https://laravel.com/docs/9.x/helpers#method-data-get
     *
     * @param iterable $items
     * @param array $option
     * @return array
     * @throws \Exception
     */
    public static function mapItems(Iterable $items, array $option): array {
        $keysToValidate = ['id', 'description', 'amount', 'quantity'];

        self::validateKeys($keysToValidate, $option);

        $position = 1;
        $result = [];
        foreach ($items as $item) {
            $result = [
                ...$result,
                'item['.$position.'].id' => data_get($item, $option['id']),
                'item['.$position.'].description' => data_get($item, $option['description']),
                'item['.$position.'].amount' => number_format(data_get($item, $option['amount']), 2, '.', ''),
                'item['.$position.'].quantity' => data_get($item, $option['quantity']),
            ];
        }
        return $result;
    }

    /**
     * Create an array with the keys and values of the object using the dot notation
     * Example:
     * [
     *  'street'=>'street',
     *  'number'=>'number',
     *  'complement'=>'complement',
     *  'district' => 'district',
     *  'postalCode' => 'cep' ,
     *  'city' => 'city',
     *  'state' => 'state'
     * ]
     *
     * For more details check the https://laravel.com/docs/9.x/helpers#method-data-get
     *
     * @param $shipping
     * @param $option
     * @return array
     * @throws \Exception
     */
    public static function mapShipping($shipping, $option): array {
        $keysToValidate = [
            'street',
            'number',
            'complement',
            'district',
            'postalCode',
            'city',
            'state'
        ];

        self::validateKeys($keysToValidate, $option);

        return [
            'shipping.address.street' => data_get($shipping, $option['street']),
            'shipping.address.number' => data_get($shipping, $option['number']),
            'shipping.address.complement' => data_get($shipping, $option['complement']),
            'shipping.address.district' => data_get($shipping, $option['district']),
            'shipping.address.postalCode' => data_get($shipping, $option['postalCode']),
            'shipping.address.city' => data_get($shipping, $option['city']),
            'shipping.address.state' => data_get($shipping, $option['state']),
            'shipping.address.country' => 'BRA',
            'shipping.type' => '3',
            'shipping.cost' => '0.00',
        ];
    }

    public static function mapSender($sender, $option): array {

        $keysToValidate = [
            'name',
            'cpf',
            'areaCode',
            'phone',
            'email',
        ];


        self::validateKeys($keysToValidate, $option);

        return [
            'sender.name' => data_get($sender, $option['name']),
            'sender.CPF' => data_get($sender, $option['cpf']),
            'sender.areaCode' => data_get($sender, $option['areaCode']),
            'sender.phone' => data_get($sender, $option['phone']),
            'sender.email' => data_get($sender, $option['email']),
        ];
    }

    public static function mapCreditCard($creditCard, $option): array {
        $keysToValidate = [
            'token',
            'name',
            'cpf',
            'birthDate',
            'areaCode',
            'phone',
        ];


        self::validateKeys($keysToValidate, $option);

        return [
            'creditCard.token' => data_get($creditCard, $option['token']),
            'creditCard.holder.name' => data_get($creditCard, $option['name']),
            'creditCard.holder.CPF' => data_get($creditCard, $option['cpf']),
            'creditCard.holder.birthDate' => data_get($creditCard, $option['birthDate']),
            'creditCard.holder.areaCode' => data_get($creditCard, $option['areaCode']),
            'creditCard.holder.phone' => data_get($creditCard, $option['phone']),
        ];
    }

    public static function mapBillingAddress($billingAddress, $option): array {
        $keysToValidate = [
            'street',
            'number',
            'complement',
            'district',
            'postalCode',
            'city',
            'state',
        ];

        self::validateKeys($keysToValidate, $option);

        return [
            'billingAddress.street' => data_get($billingAddress, $option['street']),
            'billingAddress.number' => data_get($billingAddress, $option['number']),
            'billingAddress.complement' => data_get($billingAddress, $option['complement']),
            'billingAddress.district' => data_get($billingAddress, $option['district']),
            'billingAddress.postalCode' => data_get($billingAddress, $option['postalCode']),
            'billingAddress.city' => data_get($billingAddress, $option['city']),
            'billingAddress.state' => data_get($billingAddress, $option['state']),
            'billingAddress.country' => 'BRA',
        ];
    }

    public static function mapInstallment($installment, $option) {
        $keysToValidate = [
            'quantity',
            'value',
            'noInterestInstallmentQuantity'
        ];

        self::validateKeys($keysToValidate, $option);

        return [
            'installment.quantity' => data_get($installment, $option['quantity']),
            'installment.value' => number_format(data_get($installment, $option['value']), '2', '.', ''),
            'installment.noInterestInstallmentQuantity' => data_get($installment, $option['noInterestInstallmentQuantity']),
        ];
    }
    public static function map($items, $shipping, $sender, $creditCard, $billingAddress, $installment, $reference): array {

        $authorization = Authorization::firstOrFail();
        return [
            ...$items,
            ...$shipping,
            ...$sender,
            ...$creditCard,
            ...$billingAddress,
            ...$installment,
            'payment.mode' => 'default',
            'payment.method' => 'creditCard',
            'currency' => 'BRL',
            'notificationURL' => route('laravel-pagseguro.notification'),
            'reference' => $reference,
            'primaryReceiver.publicKey' => $authorization->code,
            'receiver[1].publicKey' => env('RECEIVER_KEY'),
            'receiver[1].split.amount' => '20.00'
        ];
    }
}
