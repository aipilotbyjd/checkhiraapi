<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Laravel\Passport\Console\ClientCommand;
// use Laravel\Passport\Console\InstallCommand;
// use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        // $this->commands([
        //     InstallCommand::class,
        //     ClientCommand::class,
        //     KeysCommand::class,
        // ]);
    }
}
