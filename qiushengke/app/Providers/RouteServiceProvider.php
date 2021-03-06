<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        $this->adminWebRoutes();
        //
        $this->mapPCRoutes();
        $this->mapStaticRoutes();

        $this->mapWapRoutes();
        $this->mapAppApiRoutes100();
    }

    protected function mapPCRoutes()
    {
        Route::namespace($this->namespace.'\PC')
            ->group(base_path('routes/pc.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    protected function adminWebRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->namespace . '\Admin')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapStaticRoutes()
    {
        Route::prefix('api/static')
            ->namespace($this->namespace)
            ->group(base_path('routes/static.php'));
    }

    protected function mapWapRoutes()
    {
        Route::prefix('wap')
            ->namespace($this->namespace.'\Phone')
            ->group(base_path('routes/phone.php'));
    }

    /**
     * app 1.0.1版本接口
     */
    protected function mapAppApiRoutes100()
    {
        Route::prefix('app/v100')
            ->middleware('api')
            ->namespace($this->namespace. '\App')
            ->group(base_path('routes/app/v100.php'));
    }
}
