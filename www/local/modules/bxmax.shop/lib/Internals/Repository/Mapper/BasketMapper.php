<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository\Mapper;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale\BasketBase;
use Bitrix\Sale\BasketItemBase;

/**
 * Корзина sale → форма контракта API.
 */
final class BasketMapper
{
    public static function convert(BasketBase $basket): array
    {
        $currency = CurrencyManager::getBaseCurrency();
        $items = [];

        /** @var BasketItemBase $item */
        foreach ($basket->getBasketItems() as $item) {
            $currency = $item->getCurrency();
            $items[] = [
                'id' => (int)$item->getId(),
                'productId' => $item->getProductId(),
                'name' => (string)$item->getField('NAME'),
                'quantity' => $item->getQuantity(),
                'price' => self::money($item->getPrice(), $currency),
                'total' => self::money($item->getFinalPrice(), $currency),
            ];
        }

        return [
            'items' => $items,
            'total' => self::money($basket->getPrice(), $currency),
        ];
    }

    /**
     * Сумма в JSON остаётся строкой: float не подходит для денег.
     *
     * @return array{amount: string, currency: string}
     */
    private static function money(float $amount, string $currency): array
    {
        return [
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => $currency,
        ];
    }
}
