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
use Warlof\Seat\MiningLedger\Jobs\UpdateEsiAuthenticated;
use Warlof\Seat\MiningLedger\Models\Api\EsiTokens;

class MiningLedgerUpdate extends Command {

	use JobManager;

	protected $signature = 'esi:mining-ledger:update';

	protected $description = 'Queues all enabled Esi Account for mining ledger update';

	public function __construct() {
		parent::__construct();
	}

	public function handle(JobPayloadContainer $job)
	{
		$queued_tokens = 0;

		// Query the ESI tokens
		EsiTokens::where('active', 1)->chunk(10, function($accounts) use ($job, &$queued_tokens) {
			foreach ($accounts as $account) {

				$job->scope                     = 'Character';
				$job->api                       = 'ESI Scheduler';
				$job->owner_id                  = $account->character_id;
				$job->esi_access                = $account;

				$job_id = $this->addUniqueJob(UpdateEsiAuthenticated::class, $job);

				$this->info('Job ' . $job_id . ' dispatched!');

				$queued_tokens++;

			}
		});

		dispatch((new Analytics((new AnalyticsContainer())
			->set('type', 'event')
			->set('ec', 'queues')
			->set('ea', 'queue_tokens')
			->set('el', 'console')
			->set('ev', $queued_tokens)))
		->onQueue('medium'));
	}

}