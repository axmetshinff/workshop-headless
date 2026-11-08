<?php declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Bxmax\Shop\Infrastructure\Controller\Health;

return static function (RoutingConfigurator $routes): void {
    $routes->prefix('api/v1')->group(static function (RoutingConfigurator $routes): void {
        $routes->get('health', [Health::class, 'check']);
    });
};
