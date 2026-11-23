<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Catalog\Product\Basket as CatalogBasket;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bxmax\Shop\Internals\Contract\Repository\BasketRepositoryInterface;
use Bxmax\Shop\Internals\Repository\Mapper\BasketMapper;
use Bxmax\Shop\Public\Enum\ErrorCode;

/**
 * Корзина текущего посетителя из модуля sale.
 */
final class SaleBasketRepository implements BasketRepositoryInterface
{
    public function getCurrent(): array
    {
        return BasketMapper::convert($this->loadCurrent());
    }

    public function add(int $productId, int $quantity): Result
    {
        $basket = $this->loadCurrent();
        $addResult = CatalogBasket::addProductToBasket($basket, [
            'PRODUCT_ID' => $productId,
            'QUANTITY' => $quantity,
        ], [
            'SITE_ID' => $this->getSiteId(),
        ], [
            'USE_MERGE' => 'Y',
        ]);

        if (!$addResult->isSuccess()) {
            return (new Result())->addError(new Error(
                'Товар нельзя добавить в корзину',
                ErrorCode::ProductNotAvailable->value,
            ));
        }

        return $this->save($basket);
    }

    public function update(int $itemId, int $quantity): Result
    {
        $basket = $this->loadCurrent();
        $item = $basket->getItemById($itemId);
        if ($item === null) {
            return $this->itemNotFound();
        }

        $setResult = $item->setField('QUANTITY', $quantity);
        if (!$setResult->isSuccess()) {
            return $this->saveFailed();
        }

        return $this->save($basket);
    }

    public function delete(int $itemId): Result
    {
        $basket = $this->loadCurrent();
        $item = $basket->getItemById($itemId);
        if ($item === null) {
            return $this->itemNotFound();
        }

        $item->delete();

        return $this->save($basket);
    }

    private function loadCurrent(): Basket
    {
        $fuserId = Fuser::getId();

        if ($fuserId === null) {
            throw new \RuntimeException('Could not resolve current basket owner.');
        }

        return Basket::loadItemsForFUser($fuserId, $this->getSiteId());
    }

    private function getSiteId(): string
    {
        $siteId = Context::getCurrent()->getSite();
        if ($siteId === null) {
            throw new \RuntimeException('Could not resolve current site.');
        }

        return $siteId;
    }

    private function save(Basket $basket): Result
    {
        $saveResult = $basket->save();
        if (!$saveResult->isSuccess()) {
            return $this->saveFailed();
        }

        return (new Result())->setData(BasketMapper::convert($basket));
    }

    private function itemNotFound(): Result
    {
        return (new Result())->addError(new Error(
            'Позиция корзины не найдена',
            ErrorCode::BasketItemNotFound->value,
        ));
    }

    private function saveFailed(): Result
    {
        return (new Result())->addError(new Error(
            'Не удалось сохранить корзину',
            ErrorCode::BasketSaveFailed->value,
        ));
    }
}
