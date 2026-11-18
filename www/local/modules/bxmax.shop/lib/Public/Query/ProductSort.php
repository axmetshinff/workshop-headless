<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Query;

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bxmax\Shop\Public\Enum\ErrorCode;

final class ProductSort
{
    private const FIELDS = ['id' => 'ID', 'name' => 'NAME'];

    /** Пустой массив сохраняет порядок каталога мастера. */
    public static function parse(mixed $field, mixed $direction): Result
    {
        $result = new Result();
        if ($field === null && $direction === null) {
            return $result->setData([]);
        }
        if ($field === null) {
            return $result->addError(new Error('Для order укажите sort', ErrorCode::InvalidOrder->value));
        }
        if (!is_string($field) || !isset(self::FIELDS[$field])) {
            return $result->addError(new Error('Недопустимое поле sort', ErrorCode::InvalidSort->value));
        }

        $direction ??= 'asc';
        if (!is_string($direction) || !in_array(strtolower($direction), ['asc', 'desc'], true)) {
            return $result->addError(new Error('Недопустимое направление order', ErrorCode::InvalidOrder->value));
        }

        return $result->setData([self::FIELDS[$field] => strtoupper($direction)] + ['ID' => 'ASC']);
    }
}
