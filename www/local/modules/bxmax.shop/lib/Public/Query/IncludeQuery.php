<?php declare(strict_types=1);

namespace Bxmax\Shop\Public\Query;

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bxmax\Shop\Public\Enum\ErrorCode;

final class IncludeQuery
{
    /** @param list<string> $allowed */
    public static function parse(mixed $value, array $allowed): Result
    {
        $result = new Result();
        if ($value === null || $value === '') {
            return $result->setData([]);
        }

        if (is_string($value)) {
            $fields = array_map('trim', explode(',', $value));
            if (!array_diff($fields, $allowed)) {
                return $result->setData(array_values(array_unique($fields)));
            }
        }

        return $result->addError(new Error('Недопустимое значение include', ErrorCode::InvalidInclude->value));
    }
}
