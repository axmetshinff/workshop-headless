<?php declare(strict_types=1);

use Bxmax\Shop\Public\Query\ProductSort;

test('сохраняет порядок каталога или переводит известное поле в сортировку ORM', function ($field, $order, $expected) {
    expect(ProductSort::parse($field, $order)->getData())->toBe($expected);
})->with([
    [null, null, []],
    ['name', null, ['NAME' => 'ASC', 'ID' => 'ASC']],
    ['name', 'DESC', ['NAME' => 'DESC', 'ID' => 'ASC']],
    ['id', 'desc', ['ID' => 'DESC']],
]);

test('неизвестные поля и направления возвращают устойчивый код', function ($field, $order, $code) {
    $result = ProductSort::parse($field, $order);
    expect($result->getErrors()[0]->getCode())->toBe($code);
})->with([
    ['price', 'asc', 'INVALID_SORT'],
    [['id'], null, 'INVALID_SORT'],
    ['id', ['asc'], 'INVALID_ORDER'],
    ['id', 'up', 'INVALID_ORDER'],
    [null, 'asc', 'INVALID_ORDER'],
]);
