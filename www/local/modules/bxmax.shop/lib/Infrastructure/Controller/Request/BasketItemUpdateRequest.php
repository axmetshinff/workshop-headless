<?php declare(strict_types=1);

namespace Bxmax\Shop\Infrastructure\Controller\Request;

use Bitrix\Main\Validation\Rule\Range;

/**
 * JSON-тело изменения количества позиции.
 */
final readonly class BasketItemUpdateRequest
{
    public function __construct(
        #[Range(min: 1, max: 100, errorMessage: 'quantity: целое число от 1 до 100')]
        public int $quantity,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromPayload(array $data): self
    {
        return new self(
            quantity: is_int($data['quantity'] ?? null) ? $data['quantity'] : 0,
        );
    }
}
