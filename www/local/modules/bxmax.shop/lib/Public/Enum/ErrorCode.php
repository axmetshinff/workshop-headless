<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Enum;

/**
 * Коды ошибок API. Значение кода — часть контракта: по нему фронт выбирает экран.
 * Каталог кодов с описанием — contract/errors.md.
 */
enum ErrorCode: string
{
    case ProductNotFound = 'PRODUCT_NOT_FOUND';

    /**
     * HTTP-статус ответа с этой ошибкой. Сервисы его не читают, статус ставит контроллер.
     */
    public function httpStatus(): int
    {
        return match ($this) {
            self::ProductNotFound => 404,
        };
    }
}
