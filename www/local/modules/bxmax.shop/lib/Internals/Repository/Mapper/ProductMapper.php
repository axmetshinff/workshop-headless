<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Repository\Mapper;

/**
 * Товар, карточка и предложение в форме контракта API.
 */
final class ProductMapper
{
    /**
     * @param array{ID: int|string, NAME: string, CODE: ?string, PRICE_AMOUNT: float|string|null, PRICE_CURRENCY: ?string} $row
     * @return array{id: int, name: string, code: string, price: ?array{amount: string, currency: string}}
     */
    public static function convertFromRow(array $row): array
    {
        return [
            'id' => (int)$row['ID'],
            'name' => (string)$row['NAME'],
            'code' => (string)$row['CODE'],
            'price' => self::convertPrice($row['PRICE_AMOUNT'] ?? null, $row['PRICE_CURRENCY'] ?? null),
        ];
    }

    /** Разные записи b_file могут указывать на один и тот же URL. */
    public static function imageUrls(array $fileIds, array $urlsById): array
    {
        $images = [];
        foreach ($fileIds as $id) {
            if (isset($urlsById[$id])) {
                $url = $urlsById[$id];
                $images[$url] = $url;
            }
        }

        return array_values($images);
    }

    public static function convertOfferFromRow(array $row): array
    {
        $price = self::convertPrice($row['PRICE_AMOUNT'], $row['PRICE_CURRENCY']);
        return [
            'id' => (int)$row['ID'],
            'name' => (string)$row['NAME'],
            'size' => $row['CLOTHES_SIZE'] ?? $row['SHOE_SIZE'],
            'color' => $row['COLOR_NAME'],
            'price' => $price,
            'available' => $row['IS_AVAILABLE'] === 'Y' && $price !== null,
        ];
    }

    /**
     * Карточка товара — те же поля, что в списке, плюс подробности.
     *
     * @param array{ID: int|string, NAME: string, CODE: ?string, PRICE_AMOUNT: float|string|null, PRICE_CURRENCY: ?string,
     *     IBLOCK_SECTION_ID: int|string|null, ARTICLE: ?string, DETAIL_TEXT: ?string, DETAIL_TEXT_TYPE: string} $row
     * @return array{id: int, name: string, code: string, price: ?array{amount: string, currency: string},
     *     categoryId: ?int, article: ?string, descriptionHtml: string}
     */
    public static function convertDetailFromRow(array $row): array
    {
        return [
            ...self::convertFromRow($row),
            'categoryId' => $row['IBLOCK_SECTION_ID'] === null ? null : (int)$row['IBLOCK_SECTION_ID'],
            'article' => ($row['ARTICLE'] ?? '') === '' ? null : (string)$row['ARTICLE'],
            'descriptionHtml' => self::convertDescription((string)$row['DETAIL_TEXT'], (string)$row['DETAIL_TEXT_TYPE']),
        ];
    }

    /**
     * @return ?array{amount: string, currency: string}
     */
    private static function convertPrice(float|string|null $amount, ?string $currency): ?array
    {
        if ($amount === null || $currency === null) {
            return null;
        }

        return [
            'amount' => number_format((float)$amount, 2, '.', ''),
            'currency' => $currency,
        ];
    }

    /**
     * В инфоблоке описание бывает HTML или текстом. Фронт всегда получает HTML.
     */
    private static function convertDescription(string $text, string $type): string
    {
        if ($type === 'html') {
            return trim($text);
        }

        return nl2br(htmlspecialchars(trim($text), ENT_QUOTES), false);
    }
}
