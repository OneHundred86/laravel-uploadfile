<?php

namespace Oh86\UploadFile;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UploadFileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/uploadfile.php' => config_path('uploadfile.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        $routes = config('uploadfile.routes', []);

        /** @var array{method:string, uri:string, action:array, middlewares:string[]} $route */
        foreach ($routes as $route) {
            Route::match($route['method'], $route['uri'], $route['action'])->middleware($route['middlewares']);
        }
    }
}