<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 02/12/2017
 * Time: 15:27
 */

namespace Warlof\Seat\MiningLedger\Helpers;


use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Fetchers\GuzzleFetcher;

class EsiGuzzleFetcher extends GuzzleFetcher {

	public function __construct( EsiAuthentication $authentication = null ) {
		parent::__construct( $authentication );
		$this->sso_base = env('WEML_SSO_BASE');
	}

}