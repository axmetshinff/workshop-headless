<?php declare(strict_types=1);

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\Routing\RoutingConfigurator;
use Bxmax\Shop\Infrastructure\Controller\Basket as BasketController;
use Bxmax\Shop\Infrastructure\Controller\Health;
use Bxmax\Shop\Infrastructure\Controller\Product;

return static function (RoutingConfigurator $routes): void {
    $routes->prefix('api/v1')->group(static function (RoutingConfigurator $routes): void {
        $routes->get('health', [Health::class, 'check']);
        $routes->get('products', [Product::class, 'list']);
        $routes->get('products/{id}', [Product::class, 'get'])->where('id', '[0-9]+');
        $routes->get('basket', [BasketController::class, 'get']);
        $routes->post('basket/items', [BasketController::class, 'add']);
        $routes->patch('basket/items/{id}', [BasketController::class, 'update'])->where('id', '[0-9]+');
        $routes->delete('basket/items/{id}', [BasketController::class, 'delete'])->where('id', '[0-9]+');

        $routes->any('{any}', static function (): Json {
            $response = new Json([
                'status' => 'error',
                'message' => 'Endpoint not found',
            ]);
            $response->setStatus(404);

            return $response;
        })->where('any', '.*');
    });
};
