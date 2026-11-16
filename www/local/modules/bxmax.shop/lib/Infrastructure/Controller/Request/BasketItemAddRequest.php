<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller\Request;

use Bitrix\Main\Validation\Rule\PositiveNumber;
use Bitrix\Main\Validation\Rule\Range;

/**
 * JSON-тело добавления позиции. Лишние поля в объект не попадают.
 */
final readonly class BasketItemAddRequest
{
    public function __construct(
        #[PositiveNumber(errorMessage: 'productId: положительное целое число')]
        public int $productId,
        #[Range(min: 1, max: 100, errorMessage: 'quantity: целое число от 1 до 100')]
        public int $quantity,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromPayload(array $data): self
    {
        return new self(
            productId: is_int($data['productId'] ?? null) ? $data['productId'] : 0,
            quantity: is_int($data['quantity'] ?? null) ? $data['quantity'] : 0,
        );
    }
}
