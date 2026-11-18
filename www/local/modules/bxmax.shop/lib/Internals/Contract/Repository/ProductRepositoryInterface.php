<?php declare(strict_types=1);

namespace Bxmax\Shop\Internals\Contract\Repository;

interface ProductRepositoryInterface
{
    /**
     * @param list<'offers'|'images'> $include
     * @param array<string, 'ASC'|'DESC'> $orderBy Порядок из ProductSort::parse().
     * @return list<array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     price: ?array{amount: string, currency: string},
     *     offers?: list<array{id: int, name: string, size: ?string, color: ?string, price: ?array{amount: string, currency: string}, available: bool}>,
     *     images?: list<string>,
     * }>
     */
    public function findActive(int $limit, int $offset, array $include = [], array $orderBy = []): array;

    public function countActive(): int;

    /**
     * Карточка: поля из списка плюс подробности. null — товара нет или он неактивен.
     *
     * @param list<'offers'|'images'> $include
     * @return ?array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     price: ?array{amount: string, currency: string},
     *     offers?: list<array{id: int, name: string, size: ?string, color: ?string, price: ?array{amount: string, currency: string}, available: bool}>,
     *     images?: list<string>,
     *     categoryId: ?int,
     *     article: ?string,
     *     descriptionHtml: string,
     * }
     */
    public function findActiveById(int $id, array $include = []): ?array;
}
