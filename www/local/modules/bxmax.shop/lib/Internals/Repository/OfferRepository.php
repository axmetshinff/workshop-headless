<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Catalog\CatalogIblockTable;
use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\Elements\ElementClothesTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Query;
use Bxmax\Shop\Internals\Repository\Mapper\ProductMapper;

/** Свойства предложений демомагазина хранятся в общей таблице инфоблоков (версия 1). */
final class OfferRepository
{
    private ?array $properties = null;

    /** @param list<int> $productIds */
    public function findByProductIds(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $properties = $this->getProperties();
        if (!isset($properties['CML2_LINK'])) {
            return [];
        }

        $rows = ElementTable::getList([
            'select' => [
                'ID', 'NAME', 'PARENT_ID' => 'SKU_LINK.VALUE_NUM',
                'CLOTHES_SIZE' => 'CLOTHES.ENUM.VALUE', 'SHOE_SIZE' => 'SHOES.ENUM.VALUE',
                'COLOR_NAME' => 'COLOR_DIRECTORY.UF_NAME', 'IS_AVAILABLE' => 'CATALOG_PRODUCT.AVAILABLE',
                'PRICE_AMOUNT' => 'BASE_PRICE.PRICE', 'PRICE_CURRENCY' => 'BASE_PRICE.CURRENCY',
            ],
            'filter' => Query::filter()->where('ACTIVE', 'Y')->whereIn('SKU_LINK.VALUE_NUM', $productIds),
            'order' => ['ID' => 'ASC'],
            'runtime' => [
                $this->property('SKU_LINK', $properties['CML2_LINK']),
                $this->property('CLOTHES', $properties['SIZES_CLOTHES'] ?? 0),
                $this->property('SHOES', $properties['SIZES_SHOES'] ?? 0),
                $this->property('COLOR_VALUE', $properties['COLOR_REF'] ?? 0),
                new Reference('COLOR_DIRECTORY', HighloadBlockTable::compileEntity('ColorReference'),
                    Join::on('this.COLOR_VALUE.VALUE', 'ref.UF_XML_ID')),
                new Reference('CATALOG_PRODUCT', ProductTable::class, Join::on('this.ID', 'ref.ID')),
                new Reference('BASE_PRICE', PriceTable::class,
                    Join::on('this.ID', 'ref.PRODUCT_ID')
                        ->where('ref.CATALOG_GROUP_ID', GroupTable::getBasePriceTypeId())
                        ->whereNull('ref.QUANTITY_FROM')->whereNull('ref.QUANTITY_TO')),
            ],
        ]);

        $offers = [];
        while ($row = $rows->fetch()) {
            $offers[(int)$row['PARENT_ID']][] = ProductMapper::convertOfferFromRow($row);
        }

        return $offers;
    }

    private function property(string $alias, int $propertyId): Reference
    {
        return new Reference($alias, ElementPropertyTable::class,
            Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')->where('ref.IBLOCK_PROPERTY_ID', $propertyId));
    }

    private function getProperties(): array
    {
        if ($this->properties === null) {
            $rows = PropertyTable::getList([
                'select' => ['ID', 'CODE'],
                'filter' => [
                    '=SKU_CATALOG.PRODUCT_IBLOCK_ID' => ElementClothesTable::getEntity()->getIblock()->getId(),
                    '@CODE' => ['CML2_LINK', 'SIZES_CLOTHES', 'SIZES_SHOES', 'COLOR_REF'],
                ],
                'runtime' => [new Reference('SKU_CATALOG', CatalogIblockTable::class,
                    Join::on('this.IBLOCK_ID', 'ref.IBLOCK_ID'))],
            ])->fetchAll();

            $this->properties = array_map('intval', array_column($rows, 'ID', 'CODE'));
        }

        return $this->properties;
    }
}
