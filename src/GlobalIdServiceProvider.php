<?php

declare(strict_types=1);

namespace DennisCuijpers\GlobalId;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class GlobalIdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'global_id');

        $this->app->singleton('global_id', function (Container $app) {
            return new GlobalId($app['config']['global_id']);
        });

        $this->app->alias('global_id', GlobalId::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('global_id.php')], 'global_id');
        }
    }

    public function provides()
    {
        return [
            'global_id',
        ];
    }

    private function configPath(): string
    {
        return __DIR__ . '/../config/global_id.php';
    }
}
