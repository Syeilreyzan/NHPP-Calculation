<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;
    public $failureTimes = [], $endObservationTime = 0, $numberOfFailure = 0, $total, $slope, $lambda, $eta;
    public $cumulativeFailureTime;
    public $labels = [];
    public $data = [];
    public $data1 = [];
    public $times = [];
    public $instantenousFailureRate, $instantenousMtbfs = [];
    public $cumulativeFailureRate, $cumulativeMtbfs = [];
    public $predictedNumberFailures = [];
    public $time = [];

    protected $listeners = [
        'calculateRow' => 'calculateRow',
    ];

    public function updateDataMtbf()
    {
        $labels = [];
        $dataInstantenousMtbfs = [];
        $dataCumulativeMtbfs = [];
        $time[] = $this->time;

        foreach($this->instantenousMtbfs as $index => $instantenousMtbf) {
            $labels[] = $this->time[$index];
            $dataInstantenousMtbfs[] = $instantenousMtbf;
            // dd($data);
        }

        foreach($this->cumulativeMtbfs as $index => $cumulativeMtbf) {
            $dataCumulativeMtbfs[] = $cumulativeMtbf;
            // dd($data);
        }

        // foreach ($this->failureTimes as $index => $failureTime) {
        //     $labels[] = $index + 1; //mtbf y axis
        //     $data[] = $failureTime['cumulative_failure_time']; //time x axis
        // }
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
            $times[] = $this->time[$index];
            $dataPredictedNumberOfFailure[] = $predictedNumberFailure;
        }

        $this->times = json_encode($times);
        $this->data = json_encode($dataPredictedNumberOfFailure);

        $this->emit('updateChartPredicted', ['times' => $this->times, 'data' => $this->data, 'data1' => $this->data1 ]);
    }

    public function rules()
    {
        return [
        'failureTimes' => 'required|array',
        'failureTimes.*.cumulative_failure_time' => 'numeric',// Define your validation rules here.
        'endObservationTime' => 'numeric',
        // Add more rules for other fields if needed.
        ];
    }

    protected $messages = [ // Define your validation messages here.
        'failureTimes.*.cumulative_failure_time.numeric' => 'The cumulative failure time field is number.', // Define your validation messages here.
        'endObservationTime.numeric' => 'The end observation time field is number.',

        // Add more messages for other fields if needed.
    ];

    public function mount()
    {
        $this->failureTimes[] = [
            'id' => 0,
            'cumulative_failure_time' => 0,
            'time_between_failures' => 0,
            'cum_mtbf' => 0,
            'natural_log_cum_failure_time' => 0,
            'natural_log_tti' => 0,
        ];
    }

    public function isAnyCumulativeFailureTimeNull()
    {
        foreach ($this->failureTimes as $failureTime) {
            if (empty($failureTime['cumulative_failure_time'])) {
                return true; // If any cumulative_failure_time is null, return true
            }
        }

        return false; // If all cumulative_failure_time values are not null, return false
    }

    public function addRow($index)
    {
        $this->validate();

        if (empty($this->failureTimes[$index]['cumulative_failure_time'])) {
            $this->alert('error', 'Please enter the cumulative failure time field.');
            return;
        }
        //ni check new input lebih sikit dari atas
        if ($index > 0 && $this->failureTimes[$index]['cumulative_failure_time'] <= $this->failureTimes[$index - 1]['cumulative_failure_time']) {
            $this->alert('error', 'Please enter the value greater than the previous value.');
            return;
        } //Todo:: do validation

        //input sedia ada lebih besar daripada bawah
        if (isset($this->failureTimes[$index + 1]) && $this->failureTimes[$index + 1]['cumulative_failure_time'] > 0 && $this->failureTimes[$index] >= $this->failureTimes[$index + 1] ) {
            $this->alert('error', 'Please enter the value greater than the previous value and lower than next value.');
            return; // todo: validation
        }

        if ($index > 0 && $this->failureTimes[$index - 1]['cumulative_failure_time'] == 0) {
            $this->alert('error', 'Please enter the cumulative failure time field before.');
        }

        $this->calculateRow();

        if (isset($this->failureTimes[$index]['cumulative_failure_time']) &&
            $this->failureTimes[$index]['cumulative_failure_time'] > 0 &&
            $index == count($this->failureTimes) - 1) {
            $this->failureTimes[] = [
                'id' => 0,
                'cumulative_failure_time' => 0,
                'time_between_failures' => 0,
                'cum_mtbf' => 0,
                'natural_log_cum_failure_time' => 0,
                'natural_log_tti' => 0,
            ];
        }
    }

    public function inputEndObservationTime()
    {
        $this->validate();

        if ($this->endObservationTime > 0) {
            if($this->numberOfFailure > 0) {
                $this->calculateRow();
            }else{
                $this->alert('error', 'Please enter the cumulative failure time field before.');
                return;
            }
        }
        else{
            $this->alert('error', 'Please enter the end observation time field.');
            return;
        }
    }

    public function calculateRow()
    {
        $this->total = 0;

        if ($this->failureTimes > 0) {
            foreach ($this->failureTimes as $index => $failureTime) {
                if ($index == 0) {
                    $this->failureTimes[$index]['time_between_failures'] = $this->failureTimes[$index]['cumulative_failure_time'] - 0;
                    $this->failureTimes[$index]['cum_mtbf'] = $this->failureTimes[$index]['cumulative_failure_time'] / ($index + 1);
                    $this->failureTimes[$index]['natural_log_cum_failure_time'] = log($this->failureTimes[$index]['cumulative_failure_time']);
                    if($this->endObservationTime > 0 && $this->failureTimes[$index]['cumulative_failure_time'] > 0) {
                        $this->failureTimes[$index]['natural_log_tti'] = log($this->endObservationTime / $this->failureTimes[$index]['cumulative_failure_time']);
                    }elseif($this->endObservationTime == 0 && $this->failureTimes[$index]['cumulative_failure_time'] > 0) {
                        $this->failureTimes[$index]['natural_log_tti'] = 0;
                    }elseif($this->endObservationTime > 0 && $this->failureTimes[$index]['cumulative_failure_time'] == 0) {
                        $this->failureTimes[$index]['natural_log_tti'] = 0;
                    }elseif($this->endObservationTime == 0 && $this->failureTimes[$index]['cumulative_failure_time'] == 0) {
                        $this->failureTimes[$index]['natural_log_tti'] = 0;
                    }
                    $this->numberOfFailure = $index + 1;
                    $this->total += $this->failureTimes[$index]['natural_log_cum_failure_time'];
                } else {
                    if ($this->failureTimes[$index]['cumulative_failure_time'] == 0) {
                        $this->failureTimes[$index]['time_between_failures'] = 0;
                    } else {
                        $this->failureTimes[$index]['time_between_failures'] = $this->failureTimes[$index]['cumulative_failure_time'] - $this->failureTimes[$index - 1]['cumulative_failure_time'];
                        $this->failureTimes[$index]['cum_mtbf'] = $this->failureTimes[$index]['cumulative_failure_time'] / ($index + 1);
                        $this->failureTimes[$index]['natural_log_cum_failure_time'] = log($this->failureTimes[$index]['cumulative_failure_time']);
                        // $this->endObservationTime != 0 ? $this->failureTimes[$index]['natural_log_tti'] = log($this->endObservationTime / $this->failureTimes[$index]['cumulative_failure_time']) : $this->failureTimes[$index]['natural_log_tti'] = 0;
                        if($this->endObservationTime > 0 && $this->failureTimes[$index]['cumulative_failure_time'] > 0) {
                            $this->failureTimes[$index]['natural_log_tti'] = log($this->endObservationTime / $this->failureTimes[$index]['cumulative_failure_time']);
                        }elseif($this->endObservationTime == 0 && $this->failureTimes[$index]['cumulative_failure_time'] > 0) {
                            $this->failureTimes[$index]['natural_log_tti'] = 0;
                        }elseif($this->endObservationTime > 0 && $this->failureTimes[$index]['cumulative_failure_time'] == 0) {
                            $this->failureTimes[$index]['natural_log_tti'] = 0;
                        }elseif($this->endObservationTime == 0 && $this->failureTimes[$index]['cumulative_failure_time'] == 0) {
                            $this->failureTimes[$index]['natural_log_tti'] = 0;
                        }
                        $this->numberOfFailure = $index + 1;
                        $this->total += $this->failureTimes[$index]['natural_log_cum_failure_time'];
                    }
                }
            }
            $this->calculateSlopeLambdaEta();
        }
    }

    public function calculateSlopeLambdaEta()
    {
        if($this->endObservationTime > 0 && $this->numberOfFailure > 0) {
            $this->slope = $this->numberOfFailure / ($this->numberOfFailure * log($this->endObservationTime) - $this->total);
            $this->lambda = $this->numberOfFailure / pow($this->endObservationTime, $this->slope);
            $this->eta = pow((1/$this->lambda), (1/$this->slope));

            $this->updateInstantenousMtbfs();
            $this->updatePredictedNumberOfFailure();
            $this->updateDataMtbf();
            $this->updateDataPredictedNumberOfFailure();
        }
        else{
            $this->endObservationTime = 0;
            $this->slope = 0;
        }
    }

    public function updatePredictedNumberOfFailure()
    {
        for ($index = 1; $index <= 16; $index++) {
            $time = 1000;
            $this->time[$index] = $index * $time;

            $valuePredictedNumberFailure = $this->lambda * pow($time * $index, $this->slope);
            $this->predictedNumberFailures[$index] = number_format($valuePredictedNumberFailure, 4, '.', '');
        }
    }

    public function updateInstantenousMtbfs()
    {
        for ($index = 1; $index <= 16; $index++) {
            $time = 1000;
            $this->time[$index] = $index * $time;

            $valueInstantenousMtbfs = 1 / ($this->lambda * $this->slope * pow(($time * $index), $this->slope - 1));
            $this->instantenousMtbfs[$index] = number_format($valueInstantenousMtbfs, 4, '.', '');

            $valueCumulativeMtbfs = 1 / ($this->lambda * pow(($time * $index), $this->slope - 1));
            $this->cumulativeMtbfs[$index] = number_format($valueCumulativeMtbfs, 4, '.', '');
        }
    }

    public function render()
    {
        return view('livewire.index');
    }
}
