<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 21:53
 */

namespace Warlof\Seat\MiningLedger\Http\Controllers\Service;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Seat\Web\Http\Controllers\Controller;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;

class EsiController extends Controller {

    public function redirectToAuth(string $entity_type, int $entity_id)
    {
        session(['weml-auth-id'   => $entity_id]);
        session(['weml-auth-type' => $entity_type]);

        return redirect($this->buildAuthUri());
    }

    public function authCallback()
    {
        $code = request()->query('code');
        $state = request()->query('state');
        $entity_id = session('weml-auth-id');

        if (is_null(session('weml-auth-type')))
            return redirect()->route('character.list')
                             ->with('error', 'Entity type has been lost in the flow');

        if (is_null(session('weml-auth-id')))
            return redirect()->route('character.list')
                             ->with('error', 'Entity id has been lost in the flow');

        if (is_null($state) || $state != session('weml-auth-state'))
            return redirect()->route('character.view.mining_ledger', ['character_id' => $entity_id])
                             ->with('error', 'No State transferred or state does not match.');

        if (is_null($code) || $code == '')
            return redirect()->route('character.view.mining_ledger', ['character_id' => $entity_id])
                             ->with('error', 'No code transferred or code is empty.');

        $esiToken = $this->exchangeCode($code);

        session(['weml-auth-type' => null]);
        session(['weml-auth-id'   => null]);

        return redirect()->route('character.view.mining_ledger', ['character_id' => $esiToken->character_id])
                         ->with('success', 'Character has been successfully coupled.');
    }

    private function exchangeCode(string $code) : EsiTokens
    {
        $request_time = Carbon::now('UTC');

        $client = new Client([
            'base_uri' => env('WEML_SSO_BASE'),
            'headers'  => [
                'User-Agent' => 'eveseat-mining-ledger/2.0.0 (Clan Daerie;Warlof Tutsimo;Daerie Inc.;Get Off My Lawn)',
            ],
        ]);

        $response = $client->request('POST', '/oauth/token', [
            'auth' => [
                env('WEML_EVE_CLIENT_ID'),
                env('WEML_EVE_CLIENT_SECRET'),
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
            ],
        ]);

        $token = json_decode($response->getBody());

        $response = $client->request('GET', '/oauth/verify', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token->access_token,
            ],
        ]);

        $verify = json_decode($response->getBody());

        $esiToken = EsiTokens::create([
            'character_id'  => $verify->CharacterID,
            'scopes'        => 'esi-industry.read_character_mining.v1',
            'access_token'  => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_at'    => $request_time->addSeconds($token->expires_in),
        ]);

        return $esiToken;
    }

    private function buildAuthUri() : string
    {
        $url = parse_url(env('WEML_SSO_BASE'));

        $base_uri = $url['scheme'] . '://' . $url['host'];

        if (array_key_exists('port', $url) && $url['port'] != 80)
            $base_uri .= ':' . $url['port'];

        $path   = '/oauth/authorize';
        $scopes = ['esi-industry.read_character_mining.v1'];
        $state  = base64_encode(time() . implode(' ', $scopes));
        $query_parameters = [
            'response_type' => 'code',
            'redirect_uri'  => route('auth.mining_ledger.callback', [
                session('weml-auth-type'),
                session('weml-auth-id'),
            ]),
            'client_id'     => env('WEML_EVE_CLIENT_ID'),
            'scope'         => implode(' ', $scopes),
            'state'         => $state,
        ];

        session(['weml-auth-state' => $state]);

        return $base_uri . $path . '?' . http_build_query($query_parameters);
    }

}
