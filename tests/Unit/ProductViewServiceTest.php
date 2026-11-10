<?php declare(strict_types=1);

use Bxmax\Shop\Public\Service\ProductViewService;
use Tests\Fake\InMemoryProductRepository;

test('отдаёт карточку товара', function () {
    $result = (new ProductViewService(new InMemoryProductRepository([product(7), product(9)])))->getById(9);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getData()['id'])->toBe(9);
});

test('на неизвестный id отвечает кодом PRODUCT_NOT_FOUND', function () {
    $result = (new ProductViewService(new InMemoryProductRepository([product(9)])))->getById(999);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->getErrors()[0]->getCode())->toBe('PRODUCT_NOT_FOUND');
});
