<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Helpers\UtilsHelper;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request): \Illuminate\Http\JsonResponse
            {
                // return logged in user Info
                $user = auth()->user();
                return UtilsHelper::apiResponseConstruct('payload', $user, 201);
            }
        });
        // Also the registration response
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request): \Illuminate\Http\JsonResponse
            {
                // return logged in user Info
                $user = auth()->user();
                return UtilsHelper::apiResponseConstruct('payload', $user, 201);
            }
        });
        // Other Responses can go down below eg Lockout Responses or Password Reset Responses etc : The above should suffice for now
        // vendor/laravel/fortify/src/Contracts
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) request('email');
            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
