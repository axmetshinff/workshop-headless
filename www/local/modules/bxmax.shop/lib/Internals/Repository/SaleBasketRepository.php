<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository;

use Bitrix\Main\Context;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bxmax\Shop\Internals\Contract\Repository\BasketRepositoryInterface;
use Bxmax\Shop\Internals\Repository\Mapper\BasketMapper;

/**
 * Корзина текущего посетителя из модуля sale.
 */
final class SaleBasketRepository implements BasketRepositoryInterface
{
    public function getCurrent(): array
    {
        return BasketMapper::convert($this->loadCurrent());
    }

    private function loadCurrent(): Basket
    {
        $fuserId = Fuser::getId();
        $siteId = Context::getCurrent()->getSite();

        if ($fuserId === null || $siteId === null) {
            throw new \RuntimeException('Could not resolve current basket owner or site.');
        }

        return Basket::loadItemsForFUser($fuserId, $siteId);
    }
}
