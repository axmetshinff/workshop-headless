<?php declare(strict_types=1);

use Bxmax\Shop\Public\Query\IncludeQuery;

test('разбирает список связей и убирает повторы', function () {
    $result = IncludeQuery::parse('offers, images,offers', ['offers', 'images']);
    expect($result->isSuccess())->toBeTrue()
        ->and($result->getData())->toBe(['offers', 'images']);
});

test('отсутствующий или пустой include оставляет прежнюю форму', function ($value) {
    expect(IncludeQuery::parse($value, ['offers'])->getData())->toBe([]);
})->with([null, '']);

test('отклоняет неизвестную связь или неверную форму include', function ($value) {
    $result = IncludeQuery::parse($value, ['offers']);
    expect($result->getErrors()[0]->getCode())->toBe('INVALID_INCLUDE');
})->with([['offers,'], ['Offers'], [['offers']]]);
