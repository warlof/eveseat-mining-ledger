<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 23:28
 */

namespace Warlof\Seat\MiningLedger\Jobs;


use Exception;
use Seat\Eseye\Exceptions\EsiScopeAccessDeniedException;
use Seat\Eseye\Exceptions\InvalidContainerDataException;
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eveapi\Jobs\Base;
use Warlof\Seat\MiningLedger\Jobs\Workers\CharacterMiningLedgerUpdate;

class UpdateEsiAuthenticated extends Base {


    /**
     * Force defining the handle method for the Job worker to call.
     *
     * @return mixed
     */
    public function handle() {

        if (!$this->trackOrDismiss())
            return;

        $this->updateJobStatus(['status' => 'Working']);

        $workers = collect([
            CharacterMiningLedgerUpdate::class,
        ]);

        $this->writeInfoJobLog('Started ESI Updates with ' . $workers->count() . ' workers.');

        $job_start = microtime(true);

        foreach ($workers as $worker) {

            try {

                $this->updateJobStatus([
                    'Processing: ' . class_basename($worker),
                ]);

                $this->writeInfoJobLog('Started Worker: ' . class_basename($worker));

                $worker_start = microtime(true);

                (new $worker)->setAuthentication($this->job_payload->esi_access)
                             ->setCharacterID($this->job_payload->owner_id)->call();
                $this->decrementErrorCounters();

                $this->writeInfoJobLog(class_basename($worker) .
                    ' took ' . number_format(microtime(true) - $worker_start, 2) . 's to complete');

            } catch (InvalidContainerDataException $e) {

                $this->writeErrorJobLog('An InvalidContainerDataException occured while processing ' .
                    class_basename($worker) . '. This normally means that an ESI token record in the database is corrupted.');

                $this->reportJobError($e);

                return;

            } catch (EsiScopeAccessDeniedException $e) {

                $this->writeErrorJobLog('An EsiScopeAccessDeniedException occurred while processing ' .
                    class_basename($worker) . '. This normally means the key does not have access.');

                $this->reportJobError($e);

                return;

            } catch (RequestFailedException $e) {

                $this->writeErrorJobLog('A RequestFailedException occured while processing ' .
                    class_basename($worker) . '. This normally means the request is wrong.');

                $this->reportJobError($e);

                return;

            } catch (Exception $e) {

                $this->writeErrorJobLog('An Exception occurred while processing ' .
                                        class_basename($worker) . '. Something terrible append !');

                $this->reportJobError($e);

                return;

            }

        }

        $this->writeInfoJobLog('The full update run took ' .
           number_format(microtime(true) - $job_start, 2) . 's to complete');

        $this->updateJobStatus([
            'status' => 'Done',
            'output' => null,
        ]);

        return;
    }
}
