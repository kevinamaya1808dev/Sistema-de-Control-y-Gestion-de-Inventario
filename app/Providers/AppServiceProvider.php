<?php

namespace App\Providers;

use App\Models\User;
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
        // =========================================================================
        // 1. SUPER ADMINISTRADOR (Acceso total automático sin restricciones)
        // =========================================================================
        Gate::before(function (User $user) {
            // Si es el ID 1 o tiene un método que valide que es Admin, salta todas las reglas
            if ($user->id === 1 || $user->isAdmin()) {
                return true;
            }
        });

        // =========================================================================
        // 2. GESTIÓN DE USUARIOS (Independiente del estado de la caja)
        // =========================================================================
        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('manage-users');
        });

        // =========================================================================
        // 3. GATES OPERATIVOS (Exigen de forma obligatoria que la CAJA ESTÉ ABIERTA)
        // =========================================================================

        // Permiso para Categorías
        Gate::define('manage-categories', function (User $user) {
            return $user->hasPermission('manage-categories') && $user->cajaActiva() !== null;
        });

        // Permiso para Productos
        Gate::define('manage-products', function (User $user) {
            return $user->hasPermission('manage-products') && $user->cajaActiva() !== null;
        });

        // Permiso para Movimientos de Inventario / Stock
        Gate::define('register-movements', function (User $user) {
            return $user->hasPermission('register-movements') && $user->cajaActiva() !== null;
        });
    }
}
