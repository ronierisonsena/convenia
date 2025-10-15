<?php

namespace App\Providers;

use App\Models\UserType;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(1));
        Passport::tokensExpireIn(CarbonInterval::months(1));
        Passport::enablePasswordGrant();
        Passport::tokensCan(UserType::SCOPES);
    }
}
