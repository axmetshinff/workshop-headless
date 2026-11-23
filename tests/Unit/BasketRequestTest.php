<?php declare(strict_types=1);

use Bitrix\Main\Validation\ValidationService;
use Bxmax\Shop\Infrastructure\Controller\Request\BasketItemAddRequest;
use Bxmax\Shop\Infrastructure\Controller\Request\BasketItemUpdateRequest;

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

test('количество для PATCH — целое число от 1 до 100', function (mixed $quantity, bool $valid) {
    $request = BasketItemUpdateRequest::fromPayload(['quantity' => $quantity]);
    $result = (new ValidationService())->validate($request);

    expect($result->isSuccess())->toBe($valid);
})->with([
    'минимум' => [1, true],
    'максимум' => [100, true],
    'ноль' => [0, false],
    'больше максимума' => [101, false],
    'дробное' => [1.5, false],
    'строка' => ['2', false],
]);
