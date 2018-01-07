<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:37
 */

namespace Warlof\Seat\MiningLedger;


use Illuminate\Support\ServiceProvider;
use Warlof\Seat\MiningLedger\Commands\MarketPricesUpdate;
use Warlof\Seat\MiningLedger\Commands\MiningLedgerUpdate;

class MiningLedgerProvider extends ServiceProvider {

    public function boot()
    {
        $this->addCommands();
        $this->addRoutes();
        $this->addViews();
        $this->addPublications();
        $this->addTranslations();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/mining-ledger.config.php', 'mining_ledger.config');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/character.permissions.php', 'web.permissions.character');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/corporation.permissions.php', 'web.permissions.corporation');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/package.character.menu.php', 'package.character.menu');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/package.corporation.menu.php', 'package.corporation.menu');
    }

    private function addCommands()
    {
        $this->commands([
            MiningLedgerUpdate::class,
            MarketPricesUpdate::class,
        ]);
    }

    private function addTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'mining-ledger');
    }

    private function addRoutes()
    {
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'mining-ledger');
    }

    private function addPublications()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ]);
    }

}