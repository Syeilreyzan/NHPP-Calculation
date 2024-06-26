<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Traits\HasCalculationTrait;

class Index extends Component
{
    use LivewireAlert, HasCalculationTrait;

    public $labels = [];
    public $data = [];
    public $data1 = [];
    // public $times = [];
    // public $instantenousMtbfs = [];
    // public $cumulativeMtbfs = [];
    // public $predictedNumberFailures = [];
    // public $time = [];
    public $allData = [];
    public $refresh = false;
    public $testing = 'Hello World';
    public $isDisabled = false;

    protected $listeners = [
        'calculateRow' => 'calculateRow',
        'generatePdf',
        'refreshComponent' => '$refresh',
        'refreshPage',
        'updateChartMtbf1' => 'updateDataMtbf',
        'updateDataPredictedNumberOfFailure1' => 'updateDataPredictedNumberOfFailure',
        'mount',
    ];

    public function mount()
    {
        $this->initData();
        // $this->emit("refreshComponent");
    }

    public function initData()
    {
        if (session()->has('nhpp_data')) {
            $data = session()->get('nhpp_data');

            if (isset($data['failureTimes'])) {
                $this->failureTimes = $data['failureTimes'] ?? [
                    [
                        'id' => 0,
                        'cumulative_failure_time' => null,
                        'time_between_failures' => 0,
                        'cum_mtbf' => 0,
                        'natural_log_cum_failure_time' => 0,
                        'natural_log_tti' => 0,
                    ]
                ];
            }

            if (isset($data['results'])) {
                $results = $data['results'];

                $this->endObservationTime = $results['endObservationTime'] ?? 0;
                $this->numberOfFailure = $results['numberOfFailure'] ?? 0;
                $this->total = $results['total'] ?? 0;
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

                $this->costOverhaul = $results['costOverhaul'] ?? 0;
                $this->costUnplainedFailure = $results['costUnplainedFailure'] ?? 0;
                $this->optimumTimeToOverhaul = $results['optimumTimeToOverhaul'] ?? 0;
                $this->resultOptimumTimeToOverhaul = $results['resultOptimumTimeToOverhaul'] ?? 0;

                $this->inputTimeMajorChange = $results['inputTimeMajorChange'] ?? 0;
                // $this->countTimeMajorChange = $results['countTimeMajorChange'] ?? 0;
                // $this->resulLnCumFailureTime = $results['resulLnCumFailureTime'] ?? 0;
                // $this->value1 = $results['value1'] ?? 0;
                // $this->value2 = $results['value2'] ?? 0;
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
                $this->isDisabled = $results['isDisabled'] ?? false;
            }
            $this->calculateRow();

            $this->failureTimes[] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'cum_mtbf' => 0,
                'natural_log_cum_failure_time' => 0,
                'natural_log_tti' => 0,
            ];
        } else {
            $this->failureTimes[0] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'cum_mtbf' => 0,
                'natural_log_cum_failure_time' => 0,
                'natural_log_tti' => 0,
            ];
        }
        $this->updateDataMtbf();
        $this->updateDataPredictedNumberOfFailure();
    }

    public function setSession()
    {
        $data = [
            'failureTimes' => $this->failureTimes,
            'results' => [
                'endObservationTime' => $this->endObservationTime,
                'numberOfFailure' => $this->numberOfFailure,
                'total' => $this->total,
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

                'costOverhaul' => $this->costOverhaul,
                'costUnplainedFailure' => $this->costUnplainedFailure,
                'optimumTimeToOverhaul' => $this->optimumTimeToOverhaul,
                'resultOptimumTimeToOverhaul' => $this->resultOptimumTimeToOverhaul,

                'inputTimeMajorChange' => $this->inputTimeMajorChange,
                // 'countTimeMajorChange' => $this->countTimeMajorChange,
                // 'resulLnCumFailureTime' => $this->resulLnCumFailureTime,
                // 'value1' => $this->value1,
                // 'value2' => $this->value2,
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
                'isDisabled' => $this->isDisabled,
            ]
        ];

        session()->put('nhpp_data', $data);
    }

    public function refreshSystem($system)
    {
        if (!in_array($system, ['failureTimes'])) {
            return;
        }
        $data = session()->get('nhpp_data', []);

        unset($data[$system]);
        $data[$system] = [];

        unset($data['results'][$this->numberOfFailure]); // Remove from session results
        $data['results'][$this->numberOfFailure] = 0;

        session()->put('nhpp_data', $data);

        $this->$system = [];
        $this->numberOfFailure = 0;

        $this->initData();
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

    ## For table
    // public function updatePredictedNumberOfFailure()
    // {
    //     for ($index = 1; $index <= $this->tableRows; $index++) {
    //         $time = $this->time;
    //         $this->time[$index] = $index * $time;

    //         $valuePredictedNumberFailure = $this->lambda * pow($time * $index, $this->slope);
    //         $this->predictedNumberFailures[$index] = number_format($valuePredictedNumberFailure, 4, '.', '');
    //     }
    // }

    // public function updateInstantenousMtbfs()
    // {
    //     for ($index = 1; $index <= $this->tableRows; $index++) {
    //         $time = $this->time;
    //         $this->time[$index] = $index * $time;

    //         $valueInstantenousMtbfs = 1 / ($this->lambda * $this->slope * pow(($time * $index), $this->slope - 1));
    //         $this->instantenousMtbfs[$index] = number_format($valueInstantenousMtbfs, 4, '.', '');

    //         $valueCumulativeMtbfs = 1 / ($this->lambda * pow(($time * $index), $this->slope - 1));
    //         $this->cumulativeMtbfs[$index] = number_format($valueCumulativeMtbfs, 4, '.', '');
    //     }
    // }

    public function isGeneratePdf()
    {
        if ($this->numberOfFailure == 0) {
            return $this->alert('error', 'Please enter The Cumulative Failure Time field.');
        }elseif($this->endObservationTime == 0) {
            return $this->alert('error', 'Please enter The End of Observation Time field.');
        }else{
            $this->alert('warning', 'Export to PDF?', [
                'position' => 'center',
                'toast' => false,
                'timer' => false,
                'timerProgressBar' => false,
                'showCancelButton' => true,
                'onDismissed' => '',
                'cancelButtonText' => 'Cancel',
                'showConfirmButton' => true,
                'onConfirmed' => 'generatePdf',
                'confirmButtonText' => 'Generate PDF',
                'data'=> [
                    'data' => $this->allData,
                ],
            ]);
        }
        Log::info('Generate PDF');
    }

    public function generatePdf($data)
    {
        $date = Carbon::now();
        $data = [
            'title' => 'NHPP Single System',
            'date' => $date->format('j F Y, g:i:s a'),
            'failureTimes' => $this->failureTimes,
            'numberOfFailure' => $this->numberOfFailure,
            'endObservationTime' => $this->endObservationTime,
            'total' => $this->total,
            'slope' => $this->slope,
            'lambda' => $this->lambda,
            'eta' => $this->eta,
            'instantenousMtbfs' => $this->instantenousMtbfs,
            'cumulativeMtbfs' => $this->cumulativeMtbfs,
            'predictedNumberFailures' => $this->predictedNumberFailures,
            'times' => $this->times,
            'labels' => $this->labels,
            'data' => $this->data,
            'data1' => $this->data1,
            'isDisabled' => $this->isDisabled,
        ];
        $this->allData = $data;

        Session::flash('pdf_data', $data);
        redirect()->route('generate-pdf');
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

    public function refreshPage()
    {
        $this->dispatchBrowserEvent('refresh-page');
        session()->forget('nhpp_data');
    }

    public function render()
    {
        return view('livewire.index');
    }
}
