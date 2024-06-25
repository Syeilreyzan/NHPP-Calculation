<?php

namespace App\Http\Livewire\Multiple;

use Livewire\Component;
use App\Traits\HasCalculationMultipleTrait;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Result extends Component
{
    use LivewireAlert, HasCalculationMultipleTrait;

    public $checkBoxBounds = false;
    public $openTable = false;
    public $labels = [];
    public $data = [];
    public $data1 = [];
    // public $times = [];
    public $instantenousMtbfs = [];
    public $cumulativeMtbfs = [];
    public $predictedNumberFailures = [];
    // public $time = [];
    public $allData = [];
    public $refresh = false;

    protected $listeners = [
        'mount',
    ];

    public function mount()
    {

        // dd('hello');
        $this->initData();
        // $this->calculatePredictionNextFailure();
        // $this->generateTable();

    }

    public function initData()
    {
        if (session()->has('nhpp_multiple_data')) {
            $data = session()->get('nhpp_multiple_data');

            if (isset($data['failureTimes1'])) {
                $this->failureTimes1 = $data['failureTimes1'];
            }

            if (isset($data['failureTimes2'])) {
                $this->failureTimes2 = $data['failureTimes2'];
            }

            if (isset($data['results'])) {
                // if (isset($results['numberOfFailure1']) && $results['numberOfFailure1'] <=0) {
                //     //throw error
                //     return;
                // }

                $results = $data['results'];

                $this->endObservationTime = $results['endObservationTime'] ?? 0;
                $this->numberOfFailure1 = $results['numberOfFailure1'] ?? 0;
                $this->numberOfFailure2 = $results['numberOfFailure2'] ?? 0;
                $this->numberOfFailure3 = $results['numberOfFailure3'] ?? 0;

                $this->totalNumberOfFailureAllSystem = $results['totalNumberOfFailureAllSystem'] ?? 0;
                $this->numberOfSystem = $results['numberOfSystem'] ?? 0;

                $this->total1 = $results['total1'] ?? 0;
                $this->total2 = $results['total2'] ?? 0;
                $this->total3 = $results['total3'] ?? 0;
                $this->total = $results['total'] ?? 0;

                $this->totalFailure1 = $results['totalFailure1'] ?? 0;
                $this->totalFailure2 = $results['totalFailure2'] ?? 0;
                $this->totalFailure3 = $results['totalFailure3'] ?? 0;

                $this->slope = $results['slope'] ?? 0;
                $this->lambda = $results['lambda'] ?? 0;
                $this->eta = $results['eta'] ?? 0;

                $this->inputFailureRate = $results['inputFailureRate'] ?? 0;
                $this->valueFailureRate = $results['valueFailureRate'] ?? 0;

                $this->instantenousMtbf = $results['instantenousMtbf'] ?? 0;

                $this->inputCumulativeFailure = $results['inputCumulativeFailure'] ?? 0;
                $this->valueCumulativeFailure = $results['valueCumulativeFailure'] ?? 0;

                $this->inputCumFailureRate = $results['inputCumFailureRate'] ?? 0;
                $this->valueCumFailureRate = $results['valueCumFailureRate'] ?? 0;

                $this->cumMtbf = $results['cumMtbf'] ?? 0;

                $this->inputExpectedNumberFailure1 = $results['inputExpectedNumberFailure1'] ?? 0;
                $this->inputExpectedNumberFailure2 = $results['inputExpectedNumberFailure2'] ?? 0;
                $this->valueExpectedNumberFailure = $results['valueExpectedNumberFailure'] ?? 0;

                $this->inputExpectedReliabilityBetween1 = $results['inputExpectedReliabilityBetween1'] ?? 0;
                $this->inputExpectedReliabilityBetween2 = $results['inputExpectedReliabilityBetween2'] ?? 0;
                $this->valueExpectedReliabilityBetween = $results['valueExpectedReliabilityBetween'] ?? 0;

                $this->inputMtbfBetween1 = $results['inputMtbfBetween1'] ?? 0;
                $this->inputMtbfBetween2 = $results['inputMtbfBetween2'] ?? 0;
                $this->valueMtbfBetween = $results['valueMtbfBetween'] ?? 0;

                $this->currentAge = $results['currentAge'] ?? 0;
                $this->additionalMissionTime = $results['additionalMissionTime'] ?? 0;
                $this->valueSurviveToAge = $results['valueSurviveToAge'] ?? 0;
                $this->valueFailToAge = $results['valueFailToAge'] ?? 0;

                $this->inputNextCumulativeFailure = $results['inputNextCumulativeFailure'] ?? 0;
                $this->valueNextCumulativeFailure = $results['valueNextCumulativeFailure'] ?? 0;

                $this->nextNumberOfFailure = $results['nextNumberOfFailure'] ?? 0;
                $this->resultNextNumberOfFailure = $results['resultNextNumberOfFailure'] ?? 0;
                $this->timeToNextFailure = $results['timeToNextFailure'] ?? 0;

                $this->costOverhaul = $results['costOverhaul'] ?? 0;
                $this->costUnplainedFailure = $results['costUnplainedFailure'] ?? 0;
                $this->optimumTimeToOverhaul = $results['optimumTimeToOverhaul'] ?? 0;
                $this->resultOptimumTimeToOverhaul = $results['resultOptimumTimeToOverhaul'] ?? 0;

                $this->inputTimeMajorChange = $results['inputTimeMajorChange'] ?? 0;
                $this->countTimeMajorChange = $results['countTimeMajorChange'] ?? 0;
                $this->resulLnCumFailureTime = $results['resulLnCumFailureTime'] ?? 0;
                $this->value1 = $results['value1'] ?? 0;
                $this->value2 = $results['value2'] ?? 0;
                $this->betaBeforeChange = $results['betaBeforeChange'] ?? 0;
                $this->betaAfterChange = $results['betaAfterChange'] ?? 0;
                $this->lambdaBeforeChange = $results['lambdaBeforeChange'] ?? 0;
                $this->lambdaAfterChange = $results['lambdaAfterChange'] ?? 0;

                $this->newFailureRate = $results['newFailureRate'] ?? 0;
                $this->newMtbf = $results['newMtbf'] ?? 0;

                $this->time = $results['time'] ?? 0;
                $this->increment = $results['increment'] ?? 0;
                $this->tableRows = $results['tableRows'] ?? 0;


                $this->instantenousMtbfs = $results['instantenousMtbfs'] ?? [];
                $this->cumulativeMtbfs = $results['cumulativeMtbfs'] ?? [];
                $this->predictedNumberFailures = $results['predictedNumberFailures'] ?? [];
                $this->times = $results['times'] ?? [];
            } else {
                // throw error
            }
        } else {
            // throw error
        }
    }

    public function setSession()
    {
        // if (session()->has('nhpp_multiple_data')) {
            $data = session()->get('nhpp_multiple_data');

            $new_data = [
                'failureTimes1' => $data['failureTimes1'],
                'failureTimes2' => $data['failureTimes2'],
                'failureTimes3' => $data['failureTimes3'],
                'results' => [
                    'endObservationTime' => $this->endObservationTime,
                    'numberOfFailure1' => $this->numberOfFailure1,
                    'numberOfFailure2' => $this->numberOfFailure2,
                    'numberOfFailure3' => $this->numberOfFailure3,

                    'totalNumberOfFailureAllSystem' => $this->totalNumberOfFailureAllSystem,
                    'numberOfSystem' => $this->numberOfSystem,

                    'total' => $this->total,
                    'total1' => $this->total1,
                    'total2' => $this->total2,

                    'totalFailure1' => $this->totalFailure1,
                    'totalFailure2' => $this->totalFailure2,
                    'totalFailure3' => $this->totalFailure3,
                    'total3' => $this->total3,

                    'slope' => $this->slope,
                    'lambda' => $this->lambda,
                    'eta' => $this->eta,

                    'inputFailureRate' => $this->inputFailureRate,
                    'valueFailureRate' => $this->valueFailureRate,
                    'instantenousMtbf' => $this->instantenousMtbf,
                    'inputCumulativeFailure' => $this->inputCumulativeFailure,
                    'valueCumulativeFailure' => $this->valueCumulativeFailure,
                    'inputCumFailureRate' => $this->inputCumFailureRate,
                    'valueCumFailureRate' => $this->valueCumFailureRate,
                    'cumMtbf' => $this->cumMtbf,
                    'inputExpectedNumberFailure1' => $this->inputExpectedNumberFailure1,
                    'inputExpectedNumberFailure2' => $this->inputExpectedNumberFailure2,
                    'valueExpectedNumberFailure' => $this->valueExpectedNumberFailure,

                    'inputExpectedReliabilityBetween1' => $this->inputExpectedReliabilityBetween1,
                    'inputExpectedReliabilityBetween2' => $this->inputExpectedReliabilityBetween2,
                    'valueExpectedReliabilityBetween' => $this->valueExpectedReliabilityBetween,

                    'inputMtbfBetween1' => $this->inputMtbfBetween1,
                    'inputMtbfBetween2' => $this->inputMtbfBetween2,
                    'valueMtbfBetween' => $this->valueMtbfBetween,

                    'currentAge' => $this->currentAge,
                    'additionalMissionTime' => $this->additionalMissionTime,
                    'valueSurviveToAge' => $this->valueSurviveToAge,
                    'valueFailToAge' => $this->valueFailToAge,

                    'inputNextCumulativeFailure' => $this->inputNextCumulativeFailure,
                    'valueNextCumulativeFailure' => $this->valueNextCumulativeFailure,

                    'nextNumberOfFailure' => $this->nextNumberOfFailure,
                    'resultNextNumberOfFailure' => $this->resultNextNumberOfFailure,
                    'timeToNextFailure' => $this->timeToNextFailure,

                    'costOverhaul' => $this->costOverhaul,
                    'costUnplainedFailure' => $this->costUnplainedFailure,
                    'optimumTimeToOverhaul' => $this->optimumTimeToOverhaul,
                    'resultOptimumTimeToOverhaul' => $this->resultOptimumTimeToOverhaul,

                    'inputTimeMajorChange' => $this->inputTimeMajorChange,
                    'countTimeMajorChange' => $this->countTimeMajorChange,
                    'resulLnCumFailureTime' => $this->resulLnCumFailureTime,
                    'value1' => $this->value1,
                    'value2' => $this->value2,
                    'betaBeforeChange' => $this->betaBeforeChange,
                    'betaAfterChange' => $this->betaAfterChange,
                    'lambdaBeforeChange' => $this->lambdaBeforeChange,
                    'lambdaAfterChange' => $this->lambdaAfterChange,

                    'newFailureRate' => $this->newFailureRate,
                    'newMtbf' => $this->newMtbf,

                    'time' => $this->time,
                    'increment' => $this->increment,
                    'tableRows' => $this->tableRows,

                    'instantenousMtbfs' => $this->instantenousMtbfs,
                    'cumulativeMtbfs' => $this->cumulativeMtbfs,
                    'predictedNumberFailures' => $this->predictedNumberFailures,
                    'times' => $this->times,
                ]
            ];

            session()->put('nhpp_multiple_data', $new_data);
        // } else {
        //     //throw error
        // }
    }

    public function checkBoxBounds()
    {
        $this->checkBoxBounds = !$this->checkBoxBounds;
    }

    public function updatedEndObservationTime()
    {
        $this->inputEndObservationTime();
    }

    public function updatedNumberOfSystem()
    {
        $this->inputNumberOfFailure();
        $this->calculatePredictionNextFailureMultiple();
    }

    public function updatedInputFailureRate()
    {
        $this->calculateFailureRate();
    }

    public function updatedInputCumulativeFailure()
    {
        $this->calculateCumulativeFailure();
    }

    public function updatedInputCumFailureRate()
    {
        $this->calculateCumFailureRate();
    }

    public function updatedInputExpectedNumberFailure1()
    {
        $this->calculateExpectedNumberFailureBetween();
    }

    public function updatedInputExpectedNumberFailure2()
    {
        $this->calculateExpectedNumberFailureBetween();
    }

    public function updatedInputExpectedReliabilityBetween1()
    {
        $this->calculateExpectedReliabilityBetween();
    }

    public function updatedInputExpectedReliabilityBetween2()
    {
        $this->calculateExpectedReliabilityBetween();
    }

    public function updatedInputMtbfBetween1()
    {
        $this->calculateMtbfBetween();
    }

    public function updatedInputMtbfBetween2()
    {
        $this->calculateMtbfBetween();
    }

    public function updatedCurrentAge()
    {
        $this->probabilitySurviveToAge();
    }

    public function updatedAdditionalMissionTime()
    {
        $this->probabilitySurviveToAge();
    }

    public function updatedInputNextCumulativeFailure()
    {
        $this->calculateNextCumulativeFailure();
    }

    public function updatedCostUnplainedFailure()
    {
        $this->calculateTimeToOverhaul();
    }

    public function updatedCostOverhaul()
    {
        $this->calculateTimeToOverhaul();
    }

    public function updatedInputTimeMajorChange()
    {
        $this->calculateTimeMajorChange();
    }

    public function updatedTime()
    {
        $this->generateTable();
        $this->updateInstantenousMtbfs();
    }

    public function updatedIncrement()
    {
        $this->generateTable();
        $this->updateInstantenousMtbfs();
    }

    public function updatedTableRows()
    {
        $this->generateTable();
        $this->updateInstantenousMtbfs();
    }

    public function generateTable()
    {
        if ($this->time > 0 && $this->increment > 0 && $this->tableRows > 0) {
            $this->openTable = true;
        }
        else{
            $this->openTable = false;
        }

        $this->setSession();
    }

    public function render()
    {
        return view('livewire.multiple.result');
    }
}
