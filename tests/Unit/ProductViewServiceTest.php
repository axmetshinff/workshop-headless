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

test('карточка без предложений и фото совпадает с эталоном пустых коллекций', function () {
    $product = [
        'id' => 1, 'name' => 'Футболка', 'code' => 't-shirt', 'price' => null,
        'categoryId' => null, 'article' => null, 'descriptionHtml' => '',
    ];
    $service = new ProductViewService(new InMemoryProductRepository([$product]));
    $data = $service->getById(1, ['offers', 'images'])->getData();
    $reference = json_decode(file_get_contents(dirname(__DIR__, 2) . '/contract/product-empty-relations.json'), true, flags: JSON_THROW_ON_ERROR);

    expect($data)->toBe($reference['data'])
        ->and($data['offers'])->toBe([])
        ->and($data['images'])->toBe([]);
});
