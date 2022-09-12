<?php

namespace Marcoaacoliveira\LaravelPagseguro\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model {
    protected $table = 'laravelpagseguro_credit_card_tokens';

    protected $fillable = [
        'number',
        'token',
        'ownable_id',
        'ownable_type',
    ];

    public function ownable()
    {
        return $this->morphTo();
    }

    public function setNumberAttribute($number) {
        $this->attributes['number'] = substr($number, 0, 3) . str_repeat('x', strlen($number) - 6) . substr($number, -3);
    }
}
