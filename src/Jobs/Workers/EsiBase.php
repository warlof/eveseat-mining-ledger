<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 23:17
 */

namespace Warlof\Seat\MiningLedger\Jobs\Workers;


use Monolog\Logger;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\InvalidAuthencationException;
use Seat\Eveapi\Api\Base;
use Warlof\Seat\MiningLedger\Helpers\EsiGuzzleFetcher;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;

abstract class EsiBase extends Base {

    /**
     * @var Eseye null
     */
    protected $esi_instance = null;

    protected $characterID = null;

    /**
     * @var EsiTokens null
     */
    protected $token = null;

    /**
     * EsiBase constructor.
     */
    public function __construct() {

        $configuration = Configuration::getInstance();
        $configuration->http_user_agent = 'eveseat-mining-ledger/' . config('mining_ledger.config.version') . ' (Clan Daerie;Warlof Tutsimo;Daerie Inc.;Get Off My Lawn)';
        $configuration->logger_level = Logger::DEBUG;
        $configuration->logfile_location = storage_path() . '/logs/eseye.log';
        $configuration->file_cache_location = storage_path() . '/app/eseye/';
        $configuration->datasource = env('WEML_ESI_SERVER');
        $configuration->fetcher = EsiGuzzleFetcher::class;

        $this->esi_instance = new Eseye();
    }

    /**
     * @param EsiTokens $token
     *
     * @return $this
     * @throws \Seat\Eseye\Exceptions\InvalidContainerDataException
     */
    public function setAuthentication(EsiTokens $token)
    {
        $this->token = $token;

        $authentication = new EsiAuthentication([
            'client_id'     => env('WEML_EVE_CLIENT_ID'),
            'secret'        => env('WEML_EVE_CLIENT_SECRET'),
            'access_token'  => $this->token->access_token,
            'refresh_token' => $this->token->refresh_token,
            'scopes'        => [
                'esi-industry.read_character_mining.v1',
            ],
            'token_expires' => $this->token->expires_at,
        ]);

        $this->esi_instance->setAuthentication($authentication);

        return $this;
    }

    public function getCharacterID() : int
    {
        return $this->characterID;
    }

    public function setCharacterID(int $character_id)
    {
        $this->characterID = $character_id;

        return $this;
    }

    protected function updateEsiToken()
    {
        try {
            $authentication = $this->esi_instance->getAuthentication();
        } catch (InvalidAuthencationException $e) {
            return;
        }

        if ($authentication->access_token != $this->token->access_token ||
            $authentication->refresh_token != $this->token->refresh_token) {

            $token = EsiTokens::find($this->getCharacterID());
            $token->access_token = $authentication->access_token;
            $token->refresh_token = $authentication->refresh_token;
            $token->expires_at = $authentication->token_expires;
            $token->save();

        }
    }

}
