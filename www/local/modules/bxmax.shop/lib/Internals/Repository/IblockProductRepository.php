<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\Elements\ElementClothesTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;
use Bxmax\Shop\Internals\Repository\Mapper\ProductMapper;

/**
 * Товары из каталога «Одежда» мастера магазина. Связи загружаем для уже выбранной страницы.
 */
final class IblockProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly OfferRepository $offers = new OfferRepository(),
        private readonly ProductImageRepository $images = new ProductImageRepository(),
    ) {}

    public function findActive(int $limit, int $offset, array $include = [], array $orderBy = []): array
    {
        $rows = ElementClothesTable::getList([
            'select' => [
                'ID',
                'NAME',
                'CODE',
                'PRICE_AMOUNT' => 'BASE_PRICE.PRICE',
                'PRICE_CURRENCY' => 'BASE_PRICE.CURRENCY',
            ],
            'filter' => ['=ACTIVE' => 'Y'],
            'order' => $orderBy ?: ['SORT' => 'ASC', 'ID' => 'ASC'],
            'limit' => $limit,
            'offset' => $offset,
            'runtime' => [$this->getBasePriceReference()],
        ])->fetchAll();

        return $this->withRelations(array_map(ProductMapper::convertFromRow(...), $rows), $include);
    }

    public function countActive(): int
    {
        return ElementClothesTable::getCount(['=ACTIVE' => 'Y']);
    }

    public function findActiveById(int $id, array $include = []): ?array
    {
        $row = ElementClothesTable::getList([
            'select' => [
                'ID',
                'NAME',
                'CODE',
                'PRICE_AMOUNT' => 'BASE_PRICE.PRICE',
                'PRICE_CURRENCY' => 'BASE_PRICE.CURRENCY',
                'IBLOCK_SECTION_ID',
                'ARTICLE' => 'ARTNUMBER.VALUE',
                'DETAIL_TEXT',
                'DETAIL_TEXT_TYPE',
            ],
            'filter' => ['=ID' => $id, '=ACTIVE' => 'Y'],
            'limit' => 1,
            'runtime' => [$this->getBasePriceReference()],
        ])->fetch();

        if ($row === false) {
            return null;
        }

        return $this->withRelations([ProductMapper::convertDetailFromRow($row)], $include)[0];
    }

    private function withRelations(array $products, array $include): array
    {
        $ids = array_column($products, 'id');
        foreach ($include as $name) {
            $grouped = match ($name) {
                'offers' => $this->offers->findByProductIds($ids),
                'images' => $this->images->findByProductIds($ids),
            };
            foreach ($products as $index => $product) {
                $products[$index][$name] = $grouped[$product['id']] ?? [];
            }
        }

        return $products;
    }

    /**
     * Цена базового типа одним JOIN: товар без цены приходит с PRICE_AMOUNT = null.
     */
    private function getBasePriceReference(): Reference
    {
        return new Reference(
            'BASE_PRICE',
            PriceTable::class,
            Join::on('this.ID', 'ref.PRODUCT_ID')
                ->where('ref.CATALOG_GROUP_ID', GroupTable::getBasePriceTypeId()),
            ['join_type' => Join::TYPE_LEFT],
        );
    }
}
