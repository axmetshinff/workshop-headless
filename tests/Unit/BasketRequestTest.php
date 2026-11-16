<?php declare(strict_types=1);

use Bitrix\Main\Validation\ValidationService;
use Bxmax\Shop\Infrastructure\Controller\Request\BasketItemAddRequest;

test('принимает только целые productId и quantity из JSON', function () {
    $request = BasketItemAddRequest::fromPayload([
        'productId' => 225,
        'quantity' => 2,
        'PRICE' => 1,
    ]);

    expect($request->productId)->toBe(225)
        ->and($request->quantity)->toBe(2)
        ->and(property_exists($request, 'PRICE'))->toBeFalse();
});

test('возвращает ошибки обоих полей за один проход', function () {
    $request = BasketItemAddRequest::fromPayload([
        'productId' => '225',
        'quantity' => 101,
    ]);

    $errors = (new ValidationService())->validate($request)->getErrors();

    expect(array_map(static fn ($error) => $error->getCode(), $errors))
        ->toBe(['productId', 'quantity']);
});
