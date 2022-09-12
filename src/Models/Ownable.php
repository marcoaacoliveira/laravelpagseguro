<?php

namespace Marcoaacoliveira\LaravelPagseguro\Models;

trait Ownable
{
    public function token() {
        return $this->morphOne(Token::class, 'ownable');
    }
}
