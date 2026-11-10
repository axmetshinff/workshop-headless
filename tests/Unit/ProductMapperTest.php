<?php declare(strict_types=1);

use Bxmax\Shop\Internals\Repository\Mapper\ProductMapper;

test('переводит строку инфоблока в товар контракта', function () {
    $product = ProductMapper::convertFromRow([
        'ID' => '9',
        'NAME' => 'Штаны "Цветочная поляна"',
        'CODE' => 'pants-flower-glade',
        'PRICE_AMOUNT' => 2199.0,
        'PRICE_CURRENCY' => 'RUB',
    ]);

    expect($product)->toBe([
        'id' => 9,
        'name' => 'Штаны "Цветочная поляна"',
        'code' => 'pants-flower-glade',
        'price' => ['amount' => '2199.00', 'currency' => 'RUB'],
    ]);
});

test('товар без цены приходит с price: null', function () {
    $product = ProductMapper::convertFromRow([
        'ID' => '1',
        'NAME' => 'Худи',
        'CODE' => 'hoodie',
        'PRICE_AMOUNT' => null,
        'PRICE_CURRENCY' => null,
    ]);

    expect($product['price'])->toBeNull();
});

test('сумма — строка с двумя знаками после точки', function (float|string $amount, string $expected) {
    $product = ProductMapper::convertFromRow([
        'ID' => '1',
        'NAME' => 'Худи',
        'CODE' => 'hoodie',
        'PRICE_AMOUNT' => $amount,
        'PRICE_CURRENCY' => 'RUB',
    ]);

    expect($product['price']['amount'])->toBe($expected);
})->with([
    'целая' => [1990.0, '1990.00'],
    'с копейками' => [1199.5, '1199.50'],
    'строка из базы' => ['899.00000000', '899.00'],
]);

test('карточка содержит все поля списка', function () {
    $row = [
        'ID' => '9',
        'NAME' => 'Штаны',
        'CODE' => 'pants',
        'PRICE_AMOUNT' => 2199.0,
        'PRICE_CURRENCY' => 'RUB',
        'IBLOCK_SECTION_ID' => '8',
        'ARTICLE' => '177-79-хх',
        'DETAIL_TEXT' => '<p>Брюки</p>',
        'DETAIL_TEXT_TYPE' => 'html',
    ];

    $detail = ProductMapper::convertDetailFromRow($row);

    expect($detail)->toMatchArray(ProductMapper::convertFromRow($row))
        ->and($detail['categoryId'])->toBe(8)
        ->and($detail['article'])->toBe('177-79-хх')
        ->and($detail['descriptionHtml'])->toBe('<p>Брюки</p>');
});

test('текстовое описание приходит экранированным HTML', function () {
    $detail = ProductMapper::convertDetailFromRow([
        'ID' => '9',
        'NAME' => 'Штаны',
        'CODE' => 'pants',
        'PRICE_AMOUNT' => null,
        'PRICE_CURRENCY' => null,
        'IBLOCK_SECTION_ID' => null,
        'ARTICLE' => null,
        'DETAIL_TEXT' => "Размер <M>\nХлопок",
        'DETAIL_TEXT_TYPE' => 'text',
    ]);

    expect($detail['descriptionHtml'])->toBe("Размер &lt;M&gt;<br>\nХлопок")
        ->and($detail['categoryId'])->toBeNull()
        ->and($detail['article'])->toBeNull();
});
