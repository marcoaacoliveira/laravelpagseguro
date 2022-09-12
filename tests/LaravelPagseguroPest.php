<?php

use Marcoaacoliveira\LaravelPagseguro\Facades\LaravelPagseguro;
use Marcoaacoliveira\LaravelPagseguro\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

it('has the correct pagseguro url', function() {
    $this->assertMatchesRegularExpression('/https:\/\/ws(\.sandbox\.pagseguro|\.pagseguro)\.uol\.com.br\//', config('laravelpagseguro.url'));
});

it('can get authorization URL', function () {
    $this->assertMatchesRegularExpression('/https:\/\//',LaravelPagseguro::getAuthorizationUrl());
});

it('can create session', function () {
    expect(LaravelPagseguro::createSession())->toBeString()->toHaveLength(32);
});

it('can tokenize credit card', function () {
    expect(LaravelPagseguro::createCreditCardToken())->toBeString()->toHaveLength(32);
});
