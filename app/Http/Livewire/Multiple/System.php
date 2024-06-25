<?php

namespace App\Http\Livewire\Multiple;

use App\Traits\HasCalculationMultipleTrait;
use App\Traits\HasCalculationTrait;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class System extends Component
{
    use LivewireAlert, HasCalculationMultipleTrait;

    public $labels = [];
    public $data = [];
    public $data1 = [];


    protected $listeners = [
        'refreshPage',
        'refreshSystem',
        'mount',
        'addRowHehe',
        'updateChartMtbf1' => 'updateDataMtbf',
        'updateDataPredictedNumberOfFailure1' => 'updateDataPredictedNumberOfFailure',
    ];

    public function mount()
    {
        $this->initData();
        // $this->emit("refreshComponent");
    }

    public function initData()
    {
        if (session()->has('nhpp_multiple_data')) {
            $data = session()->get('nhpp_multiple_data');

            // if (isset($data['failureTimes1'])) {
                $this->failureTimes1 = $data['failureTimes1'] ?? [['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0]];
            // }

            // if (isset($data['failureTimes2'])) {
                $this->failureTimes2 = $data['failureTimes2'] ?? [['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0]];
            // }

            // if (isset($data['failureTimes3'])) {
                $this->failureTimes3 = $data['failureTimes3'] ?? [['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0]];
            // }

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

                $this->time = $results['time'] ?? 0;
                $this->increment = $results['increment'] ?? 0;
                $this->tableRows = $results['tableRows'] ?? 0;

                $this->instantenousMtbfs = $results['instantenousMtbfs'] ?? [];
                $this->cumulativeMtbfs = $results['cumulativeMtbfs'] ?? [];
                $this->predictedNumberFailures = $results['predictedNumberFailures'] ?? [];
                $this->times = $results['times'] ?? [];
            }

            $this->calculateRowSystem1();
            $this->calculateRowSystem2();
            $this->calculateRowSystem3();
            // if ($this->failureTimes1 > 0 && $this->failureTimes1 != 0) {
                $this->failureTimes1[] = [
                    'id' => 0,
                    'cumulative_failure_time' => null,
                    'time_between_failures' => 0,
                    'natural_log_cum_failure_time' => 0,
                ];
            // }
            // if (isset($this->failureTimes2) && end($this->failureTimes2) === null) {
                # code...
                $this->failureTimes2[] = [
                    'id' => 0,
                    'cumulative_failure_time' => null,
                    'time_between_failures' => 0,
                    'natural_log_cum_failure_time' => 0,
                ];
            // }
            // if (isset($this->failureTimes3) && end($this->failureTimes3) === null) {
                # code...
                $this->failureTimes3[] = [
                    'id' => 0,
                    'cumulative_failure_time' => null,
                    'time_between_failures' => 0,
                    'natural_log_cum_failure_time' => 0,
                ];
            // }
            // $this->calculateRowSystem($this->failureTimes2);
        } else {
            // $this->failureTimes1[0] = [
            //     'id' => 0,
            //     'cumulative_failure_time' => null,
            //     'time_between_failures' => 0,
            //     'natural_log_cum_failure_time' => 0,
            // ];
            // $this->failureTimes2[0] = [
            //     'id' => 0,
            //     'cumulative_failure_time' => null,
            //     'time_between_failures' => 0,
            //     'natural_log_cum_failure_time' => 0,
            // ];
            // $this->failureTimes3[0] = [
            //     'id' => 0,
            //     'cumulative_failure_time' => null,
            //     'time_between_failures' => 0,
            //     'natural_log_cum_failure_time' => 0,
            // ];
            $this->failureTimes1 = [
                ['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0],
            ];
            $this->failureTimes2 = [
                ['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0],
            ];
            $this->failureTimes3 = [
                ['id' => 0, 'cumulative_failure_time' => null, 'time_between_failures' => 0, 'natural_log_cum_failure_time' => 0],
            ];
        }
        $this->updateDataMtbf();
        $this->updateDataPredictedNumberOfFailure();
    }

    public function setSession()
    {
        $data = [
            'failureTimes1' => $this->failureTimes1,
            'failureTimes2' => $this->failureTimes2,
            'failureTimes3' => $this->failureTimes3,
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
                'total3' => $this->total3,

                'totalFailure1' => $this->totalFailure1,
                'totalFailure2' => $this->totalFailure2,
                'totalFailure3' => $this->totalFailure3,

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

        session()->put('nhpp_multiple_data', $data);
    }

    public function refreshSystem($system)
    {
        if (!in_array($system, ['failureTimes1', 'failureTimes2', 'failureTimes3'])) {
            return;
        }
        $data = session()->get('nhpp_multiple_data', []);

        unset($data[$system]);

        $data[$system] = [];

        session()->put('nhpp_multiple_data', $data);

        $this->$system = [];

        $this->initData();
    }

    public function isRefreshSystem($system)
    {
        $this->alert('warning', 'Refresh System?', [
            'position' => 'center',
            'toast' => false,
            'timer' => false,
            'text' => 'You will lose all the data.',
            'timerProgressBar' => false,
            'showCancelButton' => true,
            'onDismissed' => '',
            'cancelButtonText' => 'Cancel',
            'showConfirmButton' => true,
            'onConfirmed' => 'refreshSystem',
            'confirmButtonText' => 'Confirm',
            'system' => ['system'=> $system],
        ]);
    }

    public function updateDataMtbf()
    {
        $labels = [];
        $dataInstantenousMtbfs = [];
        $dataCumulativeMtbfs = [];
        $time[] = $this->time;

        foreach($this->instantenousMtbfs as $index => $instantenousMtbf) {
            $labels[] = $this->times[$index];
            $dataInstantenousMtbfs[] = $instantenousMtbf;
        }

        foreach($this->cumulativeMtbfs as $index => $cumulativeMtbf) {
            $dataCumulativeMtbfs[] = $cumulativeMtbf;
        }

        $this->labels = json_encode($labels);
        $this->data = json_encode($dataInstantenousMtbfs);
        $this->data1 = json_encode($dataCumulativeMtbfs);

        $this->emit('updateChartMtbf', ['labels' => $this->labels, 'data' => $this->data, 'data1' => $this->data1 ]);
    }

    public function updateDataPredictedNumberOfFailure()
    {
        $times = [];
        $dataPredictedNumberOfFailure = [];
        $time[] = $this->time;

        foreach($this->predictedNumberFailures as $index => $predictedNumberFailure) {
            $times[] = $this->times[$index];
            $dataPredictedNumberOfFailure[] = $predictedNumberFailure;
        }

        $this->times = $times;
        $this->data = json_encode($dataPredictedNumberOfFailure);

        $this->emit('updateChartPredicted', ['times' => json_encode($times), 'data' => $this->data, 'data1' => $this->data1 ]);
    }

    public function isRefreshPage()
    {
        $this->alert('warning', 'Refresh Page?', [
            'position' => 'center',
            'toast' => false,
            'timer' => false,
            'text' => 'You will lose all the data.',
            'timerProgressBar' => false,
            'showCancelButton' => true,
            'onDismissed' => '',
            'cancelButtonText' => 'Cancel',
            'showConfirmButton' => true,
            'onConfirmed' => 'refreshPage',
            'confirmButtonText' => 'Confirm',
        ]);
    }

    public function refreshtable()
    {

    }

    public function refreshPage()
    {
        $this->dispatchBrowserEvent('refresh-page');
        session()->forget('nhpp_multiple_data');
    }

    public function render()
    {
        return view('livewire.multiple.system');
    }
}
