<?php

use Marcoaacoliveira\LaravelPagseguro\DTO\BasePayment;

class Mapper {
    public static function paymentToDTO(BasePayment $payment) {
        return BasePayment::map();
    }
}
