<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 22:03
 */

namespace Warlof\Seat\MiningLedger\Commands;


use Illuminate\Console\Command;
use Seat\Eveapi\Helpers\JobPayloadContainer;
use Seat\Eveapi\Traits\JobManager;
use Seat\Services\Helpers\AnalyticsContainer;
use Seat\Services\Jobs\Analytics;
use Warlof\Seat\MiningLedger\Jobs\UpdateEsiPublic;

class MarketPricesUpdate extends Command {

	use JobManager;

	protected $signature = 'esi:market-prices:update';

	protected $description = 'Queue an update for market prices';

	public function __construct() {
		parent::__construct();
	}

	public function handle(JobPayloadContainer $job)
	{
		$queued_tokens = 0;

		$job->scope                     = 'Character';
		$job->api                       = 'ESI Scheduler';
		$job->owner_id                  = 0;
		$job->esi_access                = null;

		$job_id = $this->addUniqueJob(UpdateEsiPublic::class, $job);

		$this->info('Job ' . $job_id . ' dispatched!');

		$queued_tokens++;

		dispatch((new Analytics((new AnalyticsContainer())
			->set('type', 'event')
			->set('ec', 'queues')
			->set('ea', 'queue_tokens')
			->set('el', 'console')
			->set('ev', $queued_tokens)))
		->onQueue('medium'));
	}

}