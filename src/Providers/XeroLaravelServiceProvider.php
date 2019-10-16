<?php
namespace LangleyFoxall\XeroLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use LangleyFoxall\XeroLaravel\Constants;
use LangleyFoxall\XeroLaravel\Facades\Xero;

class XeroLaravelServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            Constants::CONFIG_PATH, Constants::CONFIG_KEY
        );

        $this->app->singleton('Xero', function () {
            return (new \LangleyFoxall\XeroLaravel\Xero())->app();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            Constants::CONFIG_PATH => config_path(Constants::CONFIG_KEY.'.php'),
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Xero::class,
        ];
    }
}
