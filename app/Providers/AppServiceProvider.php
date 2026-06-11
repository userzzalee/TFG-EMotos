<?php

namespace App\Providers;

use App\Models\Anuncio;
use App\Models\User;
use App\Policies\ValoracionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ability para valorar al vendedor de un anuncio (feature 9).
        Gate::define('valorar-anuncio', function (User $user, Anuncio $anuncio) {
            return (new ValoracionPolicy())->crear($user, $anuncio);
        });
    }
}
