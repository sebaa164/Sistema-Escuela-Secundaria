<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Compartir nota mínima de aprobación en TODAS las vistas
        View::composer('*', function ($view) {
            $view->with('notaMinima', config_sistema('nota_minima_aprobacion', 60));
        });
    }
}
