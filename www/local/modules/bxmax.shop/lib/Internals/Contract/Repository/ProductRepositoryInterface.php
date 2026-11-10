<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Contract\Repository;

interface ProductRepositoryInterface
{
    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     price: ?array{amount: string, currency: string},
     * }>
     */
    public function findActive(int $limit, int $offset): array;

    public function countActive(): int;

    /**
     * Карточка: поля из списка плюс подробности. null — товара нет или он неактивен.
     *
     * @return ?array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     price: ?array{amount: string, currency: string},
     *     categoryId: ?int,
     *     article: ?string,
     *     descriptionHtml: string,
     * }
     */
    public function findActiveById(int $id): ?array;
}
