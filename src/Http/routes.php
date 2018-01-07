<?php

Route::group([
    'namespace' => 'Warlof\Seat\MiningLedger\Http\Controllers',
    'middleware' => ['web', 'auth', 'auth.email', 'locale'],
], function() {

    Route::group([
        'namespace' => 'Service',
        'prefix'    => 'auth',
    ], function() {

        Route::get('/mining-ledger/{entity_type}/{entity_id}/', [
            'as' => 'auth.mining_ledger',
            'uses' => 'EsiController@redirectToAuth',
        ]);

        Route::get('/mining-ledger/callback', [
            'as'   => 'auth.mining_ledger.callback',
            'uses' => 'EsiController@authCallback',
        ]);

    });

    Route::group([
        'namespace' => 'Character',
        'prefix'    => 'character',
    ], function() {

        Route::get('/view/mining-ledger/{character_id}', [
            'as' => 'character.view.mining_ledger',
            'middleware' => 'characterbouncer:warlof_mining',
            'uses' => 'MiningLedgerController@getLedger',
        ]);

    });

    Route::group([
        'namespace' => 'Corporation',
        'prefix'    => 'corporation',
    ], function() {

        Route::get('/view/mining-tracking/{corporation_id}', [
            'as' => 'corporation.view.mining_tracking',
            'middleware' => 'corporationbouncer:warlof_mining',
            'uses' => 'MiningLedgerController@getTracking',
        ]);

        Route::get('/view/mining-ledger/{corporation_id}/{year?}/{month?}', [
            'as' => 'coporation.view.mining_ledger',
            'middleware' => 'corporationbouncer:warlof_mining',
            'uses' => 'MiningLedgerController@getLedger',
        ]);

    });

});
