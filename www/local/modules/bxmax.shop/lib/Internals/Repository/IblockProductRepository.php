<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Catalog\GroupTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bxmax\Shop\Internals\Contract\Repository\ProductRepositoryInterface;
use Bxmax\Shop\Internals\Repository\Mapper\ProductMapper;

/**
 * Товары из каталога «Одежда» мастера магазина. Единственный класс модуля, который знает про инфоблок.
 */
final class IblockProductRepository implements ProductRepositoryInterface
{
    private const IBLOCK_API_CODE = 'clothes';

    /** @var class-string<DataManager>|null */
    private ?string $elementDataClass = null;

    public function findActive(int $limit, int $offset): array
    {
        $rows = $this->getElementDataClass()::getList([
            'select' => [
                'ID',
                'NAME',
                'CODE',
                'PRICE_AMOUNT' => 'BASE_PRICE.PRICE',
                'PRICE_CURRENCY' => 'BASE_PRICE.CURRENCY',
            ],
            'filter' => ['=ACTIVE' => 'Y'],
            'order' => ['SORT' => 'ASC', 'ID' => 'ASC'],
            'limit' => $limit,
            'offset' => $offset,
            'runtime' => [$this->getBasePriceReference()],
        ])->fetchAll();

        return array_map(ProductMapper::convertFromRow(...), $rows);
    }

    public function countActive(): int
    {
        return $this->getElementDataClass()::getCount(['=ACTIVE' => 'Y']);
    }

    /**
     * ORM-класс элементов каталога. compileEntity() каждый раз ищет инфоблок в базе, поэтому класс запоминаем.
     *
     * @return class-string<DataManager>
     */
    private function getElementDataClass(): string
    {
        if ($this->elementDataClass !== null) {
            return $this->elementDataClass;
        }

        Loader::requireModule('iblock');
        Loader::requireModule('catalog');

        $entity = IblockTable::compileEntity(self::IBLOCK_API_CODE);
        if ($entity === false) {
            throw new \RuntimeException(
                'Catalog iblock with API_CODE "' . self::IBLOCK_API_CODE . '" not found. Set it in the iblock settings.'
            );
        }

        return $this->elementDataClass = $entity->getDataClass();
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
