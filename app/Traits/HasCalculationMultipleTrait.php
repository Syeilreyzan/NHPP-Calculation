<?php

namespace App\Traits;

use Exception;

trait HasCalculationMultipleTrait
{
    public $failureTimes1 = [], $failureTimes2 = [], $failureTimes3 = [];
    public $endObservationTime;
    public $totalNumberOfFailureAllSystem = 0;
    public $numberOfFailure1 = 0, $numberOfFailure2 = 0, $numberOfFailure3 = 0;
    public $numberOfSystem = 0;
    public $totalFailure1, $totalFailure2, $totalFailure3;
    public $total, $total1, $total2, $total3, $slope, $lambda, $eta;

    public $inputFailureRate = 0, $valueFailureRate, $instantenousMtbf;
    public $inputCumulativeFailure = 0, $valueCumulativeFailure;
    public $inputCumFailureRate = 0, $valueCumFailureRate, $cumMtbf;
    public $inputExpectedNumberFailure1 = 0, $inputExpectedNumberFailure2 = 0, $valueExpectedNumberFailure;
    public $inputExpectedReliabilityBetween1 = 0, $inputExpectedReliabilityBetween2 = 0, $valueExpectedReliabilityBetween;
    public $inputMtbfBetween1, $inputMtbfBetween2, $valueMtbfBetween;
    public $currentAge = 0, $additionalMissionTime = 0, $valueSurviveToAge, $valueFailToAge;
    public $inputNextCumulativeFailure = 0, $valueNextCumulativeFailure;
    public $inputBoundPercent = 0, $bound1 = 0, $bound2 = 0, $bound3 = 0, $bound4 = 0;
    public $nextNumberOfFailure = 0, $resultNextNumberOfFailure = 0, $timeToNextFailure = 0;
    public $costOverhaul = 0, $costUnplainedFailure = 0, $optimumTimeToOverhaul = 0, $resultOptimumTimeToOverhaul = 0;
    public $inputTimeMajorChange = 0, $countTimeMajorChange, $valueTimeMajorChange, $resulLnCumFailureTime;
    public $value1, $value2;
    public $betaBeforeChange, $lambdaBeforeChange, $betaAfterChange, $lambdaAfterChange;
    public $newFailureRate, $newMtbf;
    public $time = 0, $increment, $tableRows;

    public $instantenousMtbfs = [];
    public $cumulativeMtbfs = [];
    public $predictedNumberFailures = [];
    public $times = [];

    public function rules()
    {
        return [
        'failureTimes1.*.cumulative_failure_time' => 'numeric',// Define your validation rules here.
        'failureTimes2.*.cumulative_failure_time' => 'numeric',// Define your validation rules here.
        'endObservationTime' => 'numeric',
        'numberOfSystem' => 'numeric',
        'inputFailureRate' => 'numeric',
        'inputCumulativeFailure' => 'numeric',
        'inputCumFailureRate' => 'numeric',
        'inputExpectedNumberFailure1' => 'numeric',
        'inputExpectedNumberFailure2' => 'numeric',
        'inputExpectedReliabilityBetween1' => 'numeric',
        'inputExpectedReliabilityBetween2' => 'numeric',
        'inputMtbfBetween1' => 'numeric',
        'inputMtbfBetween2' => 'numeric',
        'currentAge' => 'numeric',
        'additionalMissionTime' => 'numeric',
        'inputNextCumulativeFailure' => 'numeric',
        'costOverhaul' => 'numeric',
        'costUnplainedFailure' => 'numeric',
        'inputTimeMajorChange' => 'numeric',
        'time' => 'numeric',
        'increment' => 'numeric',
        'tableRows' => 'numeric',

        ];
    }

    protected $messages = [
        'failureTimes1.*.cumulative_failure_time.numeric' => 'The cumulative failure time field is number.',
        'failureTimes2.*.cumulative_failure_time.numeric' => 'The cumulative failure time field is number.',
        'endObservationTime.numeric' => 'The end observation time field is number.',
        'numberOfSystem.numeric' => 'The end observation time field is number.',
        'inputFailureRate.numeric' => 'The input failure rate field is number.',
        'inputCumulativeFailure.numeric' => 'The input cumulative failure field is number.',
        'inputCumFailureRate.numeric' => 'The input cumulative failure rate field is number.',
        'inputExpectedNumberFailure1.numeric' => 'The input expected number of failure field is number.',
        'inputExpectedNumberFailure2.numeric' => 'The input expected number of failure field is number.',
        'inputExpectedReliabilityBetween1.numeric' => 'The input expected reliability between field is number.',
        'inputExpectedReliabilityBetween2.numeric' => 'The input expected reliability between field is number.',
        'inputMtbfBetween1.numeric' => 'The input mtbf between field is number.',
        'inputMtbfBetween2.numeric' => 'The input mtbf between field is number.',
        'currentAge.numeric' => 'The current age field is number.',
        'additionalMissionTime.numeric' => 'The additional mission time field is number.',
        'inputNextCumulativeFailure.numeric' => 'The input next cumulative failure field is number.',
        'costOverhaul.numeric' => 'The cost overhaul field is number.',
        'costUnplainedFailure.numeric' => 'The cost unplained failure field is number.',
        'inputTimeMajorChange.numeric' => 'The input time major change field is number.',
        'time.numeric' => 'The time start field is number.',
        'increment.numeric' => 'The increment field is number.',
        'tableRows.numeric' => 'The table rows field is number.',

    ];

    public function addRow1($index)
    {
        $this->validateOnly('failureTimes1.*.cumulative_failure_time');

        if (empty($this->failureTimes1[$index]['cumulative_failure_time'])) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field.');
            return;
        }

        if ($index > 0 && $this->failureTimes1[$index]['cumulative_failure_time'] <= $this->failureTimes1[$index - 1]['cumulative_failure_time']) {
            $this->alert('error', 'Please enter the value greater than the previous value.');
            return;
        }

        if (isset($this->failureTimes1[$index + 1]) && $this->failureTimes1[$index + 1]['cumulative_failure_time'] > 0 && $this->failureTimes1[$index] >= $this->failureTimes1[$index + 1] ) {
            $this->alert('error', 'Please enter the value greater than the previous value and lower than next value.');
            return;
        }

        if ($index > 0 && $this->failureTimes1[$index - 1]['cumulative_failure_time'] == 0) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field');
            return;
        }

        $this->calculateRowSystem1();

        if (isset($this->failureTimes1[$index]['cumulative_failure_time']) &&
            $this->failureTimes1[$index]['cumulative_failure_time'] > 0 &&
            $index == count($this->failureTimes1) - 1) {
            $this->failureTimes1[] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'natural_log_cum_failure_time' => 0,
            ];
        }
        $newIndex = count($this->failureTimes1) - 1;
        $this->emit('rowAdded', $newIndex);
    }

    public function addRow2($index)
    {
        $this->validateOnly('failureTimes2.*.cumulative_failure_time');

        if (empty($this->failureTimes2[$index]['cumulative_failure_time'])) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field.');
            return;
        }

        if ($index > 0 && $this->failureTimes2[$index]['cumulative_failure_time'] <= $this->failureTimes2[$index - 1]['cumulative_failure_time']) {
            $this->alert('error', 'Please enter the value greater than the previous value.');
            return;
        }

        if (isset($this->failureTimes2[$index + 1]) && $this->failureTimes2[$index + 1]['cumulative_failure_time'] > 0 && $this->failureTimes2[$index] >= $this->failureTimes2[$index + 1] ) {
            $this->alert('error', 'Please enter the value greater than the previous value and lower than next value.');
            return;
        }

        if ($index > 0 && $this->failureTimes2[$index - 1]['cumulative_failure_time'] == 0) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field');
            return;
        }

        $this->calculateRowSystem2();

        if (isset($this->failureTimes2[$index]['cumulative_failure_time']) &&
            $this->failureTimes2[$index]['cumulative_failure_time'] > 0 &&
            $index == count($this->failureTimes2) - 1) {
            $this->failureTimes2[] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'natural_log_cum_failure_time' => 0,
            ];
        }
        $newIndex = count($this->failureTimes2) - 1;
        $this->emit('rowAdded2', $newIndex);
    }

    public function addRow3($index)
    {
        $this->validateOnly('failureTimes3.*.cumulative_failure_time');

        if (empty($this->failureTimes3[$index]['cumulative_failure_time'])) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field.');
            return;
        }

        if ($index > 0 && $this->failureTimes3[$index]['cumulative_failure_time'] <= $this->failureTimes3[$index - 1]['cumulative_failure_time']) {
            $this->alert('error', 'Please enter the value greater than the previous value.');
            return;
        }

        if (isset($this->failureTimes3[$index + 1]) && $this->failureTimes3[$index + 1]['cumulative_failure_time'] > 0 && $this->failureTimes3[$index] >= $this->failureTimes3[$index + 1] ) {
            $this->alert('error', 'Please enter the value greater than the previous value and lower than next value.');
            return;
        }

        if ($index > 0 && $this->failureTimes3[$index - 1]['cumulative_failure_time'] == 0) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field');
            return;
        }

        $this->calculateRowSystem3();

        if (isset($this->failureTimes3[$index]['cumulative_failure_time']) &&
            $this->failureTimes3[$index]['cumulative_failure_time'] > 0 &&
            $index == count($this->failureTimes3) - 1) {
            $this->failureTimes3[] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'natural_log_cum_failure_time' => 0,
            ];
        }
        $newIndex = count($this->failureTimes3) - 1;
        $this->emit('rowAdded3', $newIndex);
    }

    public function calculateRowSystem1()
    {
        foreach ($this->failureTimes1 as $index => $failureTime1) {
            $check_cumulative_failure_time = $failureTime1['cumulative_failure_time'];
            if (
                !is_numeric($check_cumulative_failure_time)
            ) {
                unset($this->failureTimes1[$index]);
                $this->failureTimes1 = array_values($this->failureTimes1);
            }
        }
        $this->validateOnly('failureTimes1.*.cumulative_failure_time');
        $this->numberOfFailure1 = 0;
        $this->total1 = 0;
        $this->totalFailure1 = 0;

        if ($this->failureTimes1 > 0) {
            foreach ($this->failureTimes1 as $index => $failureTime1) {
                if ($index == 0) {
                    $this->failureTimes1[$index]['time_between_failures'] = $this->failureTimes1[$index]['cumulative_failure_time'] - 0;
                    if ($this->endObservationTime > 0) {
                        $this->failureTimes1[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes1[$index]['cumulative_failure_time']);
                    }
                    $this->numberOfFailure1 = $index + 1;
                    $this->total1 += $this->failureTimes1[$index]['natural_log_cum_failure_time'];
                } else {
                    if (is_null($this->failureTimes1[$index]['cumulative_failure_time']) || $this->failureTimes1[$index]['cumulative_failure_time'] == 0 ) {
                        $this->failureTimes1[$index]['time_between_failures'] = 0;
                    } else {
                        $this->failureTimes1[$index]['time_between_failures'] = $this->failureTimes1[$index]['cumulative_failure_time'] - $this->failureTimes1[$index - 1]['cumulative_failure_time'];
                        if ($this->endObservationTime > 0) {
                            $this->failureTimes1[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes1[$index]['cumulative_failure_time']);
                        }
                        $this->numberOfFailure1 = $index + 1;
                        $this->total1 += $this->failureTimes1[$index]['natural_log_cum_failure_time'];
                        $this->totalFailure1 = $this->total1;
                    }
                }
                $this->totalFailure1 = $this->total1;
            }

            $this->calculateTotalNaturalLogCumFailureTime();
            $this->calculateTotalNumberOfFailures();
            $this->calculateSlopeLambdaEta();
            $this->updateInstantenousMtbfs();
            $this->updateDataMtbf();
            $this->updateDataPredictedNumberOfFailure();
            $this->setSession();
        }
    }

    public function calculateRowSystem2()
    {
        foreach ($this->failureTimes2 as $index => $failureTime2) {
            $check_cumulative_failure_time = $failureTime2['cumulative_failure_time'];
            if (
                !is_numeric($check_cumulative_failure_time)
            ) {
                unset($this->failureTimes2[$index]);
                $this->failureTimes2 = array_values($this->failureTimes2);
            }
        }
        $this->validateOnly('failureTimes2.*.cumulative_failure_time');
        $this->numberOfFailure2 = 0;
        $this->total2 = 0;
        $this->totalFailure2 = 0;

        if ($this->failureTimes2 > 0) {
            foreach ($this->failureTimes2 as $index => $failureTime2) {
                if ($index == 0) {
                    $this->failureTimes2[$index]['time_between_failures'] = $this->failureTimes2[$index]['cumulative_failure_time'] - 0;
                    if ($this->endObservationTime > 0) {
                        $this->failureTimes2[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes2[$index]['cumulative_failure_time']);
                    }
                    $this->numberOfFailure2 = $index + 1;
                    $this->total2 += $this->failureTimes2[$index]['natural_log_cum_failure_time'];
                } else {
                    if (is_null($this->failureTimes2[$index]['cumulative_failure_time']) || $this->failureTimes2[$index]['cumulative_failure_time'] == 0 ) {
                        $this->failureTimes2[$index]['time_between_failures'] = 0;
                    } else {
                        $this->failureTimes2[$index]['time_between_failures'] = $this->failureTimes2[$index]['cumulative_failure_time'] - $this->failureTimes2[$index - 1]['cumulative_failure_time'];
                        if ($this->endObservationTime > 0) {
                            $this->failureTimes2[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes2[$index]['cumulative_failure_time']);
                        }
                        $this->numberOfFailure2 = $index + 1;
                        $this->total2 += $this->failureTimes2[$index]['natural_log_cum_failure_time'];

                    }
                }
                $this->totalFailure2 = $this->total2;
            }

            $this->calculateTotalNaturalLogCumFailureTime();
            $this->calculateTotalNumberOfFailures();
            $this->calculateSlopeLambdaEta();
            $this->updateInstantenousMtbfs();
            $this->updateDataMtbf();
            $this->updateDataPredictedNumberOfFailure();
            $this->setSession();
        }
    }

    public function calculateRowSystem3()
    {
        foreach ($this->failureTimes3 as $index => $failureTime3) {
            $check_cumulative_failure_time = $failureTime3['cumulative_failure_time'];
            if (
                !is_numeric($check_cumulative_failure_time)
            ) {
                unset($this->failureTimes3[$index]);
                $this->failureTimes3 = array_values($this->failureTimes3);
            }
        }
        $this->validateOnly('failureTimes3.*.cumulative_failure_time');
        $this->numberOfFailure3 = 0;
        $this->total3 = 0;
        $this->totalFailure3 = 0;

        if ($this->failureTimes3 > 0) {
            foreach ($this->failureTimes3 as $index => $failureTime3) {
                if ($index == 0) {
                    $this->failureTimes3[$index]['time_between_failures'] = $this->failureTimes3[$index]['cumulative_failure_time'] - 0;
                    if ($this->endObservationTime > 0) {
                        $this->failureTimes3[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes3[$index]['cumulative_failure_time']);
                    }

                    $this->numberOfFailure3 = $index + 1;
                    $this->total3 += $this->failureTimes3[$index]['natural_log_cum_failure_time'];
                } else {
                    if (is_null($this->failureTimes3[$index]['cumulative_failure_time']) || $this->failureTimes3[$index]['cumulative_failure_time'] == 0 ) {
                        $this->failureTimes3[$index]['time_between_failures'] = 0;
                    } else {
                        $this->failureTimes3[$index]['time_between_failures'] = $this->failureTimes3[$index]['cumulative_failure_time'] - $this->failureTimes3[$index - 1]['cumulative_failure_time'];
                        if ($this->endObservationTime > 0) {
                            $this->failureTimes3[$index]['natural_log_cum_failure_time'] = log($this->endObservationTime / $this->failureTimes3[$index]['cumulative_failure_time']);
                        }

                        $this->numberOfFailure3 = $index + 1;
                        $this->total3 += $this->failureTimes3[$index]['natural_log_cum_failure_time'];

                    }
                }
                $this->totalFailure3 = $this->total3;
            }

            $this->calculateTotalNaturalLogCumFailureTime();
            $this->calculateTotalNumberOfFailures();
            $this->calculateSlopeLambdaEta();
            $this->updateInstantenousMtbfs();
            $this->updateDataMtbf();
            $this->updateDataPredictedNumberOfFailure();
            $this->setSession();
        }
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

    public function calculateTotalNumberOfFailures()
    {
        $this->totalNumberOfFailureAllSystem = $this->numberOfFailure1 + $this->numberOfFailure2 + $this->numberOfFailure3;
    }

    public function calculateTotalNaturalLogCumFailureTime()
    {
        if($this->total > 0) {
            $this->total = $this->totalFailure3 + $this->totalFailure2 + $this->totalFailure1;
        } else {
            $this->total = 0;
            $this->total = $this->totalFailure1 + $this->totalFailure2 + $this->totalFailure3;
        }
    }

    public function calculateSlopeLambdaEta()
    {
        if ($this->endObservationTime == null) {
            return;
        }
        if ($this->totalNumberOfFailureAllSystem > 0 && $this->total > 0 && $this->numberOfSystem > 0) {
            $this->validateOnly('endObservationTime');
            $this->slope = $this->totalNumberOfFailureAllSystem / $this->total;
            $this->lambda = $this->totalNumberOfFailureAllSystem / ($this->numberOfSystem * pow($this->endObservationTime, $this->slope));
            $this->eta = pow((1/$this->lambda), (1/$this->slope));
        }
        $this->setSession();
    }

    public function calculatePredictionNextFailureMultiple()
    {
        if ($this->numberOfSystem != 0 && $this->endObservationTime != 0){
            $this->nextNumberOfFailure = $this->totalNumberOfFailureAllSystem + 1;
            $this->resultNextNumberOfFailure = pow($this->nextNumberOfFailure / $this->lambda, 1 / $this->slope);
            $this->timeToNextFailure = $this->resultNextNumberOfFailure - $this->endObservationTime;
        }
        $this->setSession();
    }

    public function calculateSlope($slope, $totalNumberOfFailureAllSystem, $total)
    {
        $slope = $totalNumberOfFailureAllSystem / $total;
    }

    public function calculateTimeMajorChange()
    {
        if($this->inputTimeMajorChange == 0 || empty($this->inputTimeMajorChange)){
            $this->alert('error', 'Please enter the Time Major Change field.');
            return;
        }
        $this->validateOnly('inputTimeMajorChange');
        $this->countTimeMajorChange = 0;
        $this->resulLnCumFailureTime = 0;
        $this->value1 = 0;
        $this->value2 = 0;
        if($this->numberOfFailure1 <= $this->inputTimeMajorChange){
            $this->countTimeMajorChange = $this->numberOfFailure1;
            $this->resulLnCumFailureTime += $this->total1;
            if ($this->totalFailure2 > $this->inputTimeMajorChange) {
                $this->value2 = $this->totalFailure2;
            }
            $this->value1 = $this->totalNumberOfFailureAllSystem - $this->countTimeMajorChange;
        }
        $this->calculateColumnBeta();
        $this->calculateColumnLambda();
        $this->setSession();
    }

    public function calculateColumnBeta()
    {
        if ($this->countTimeMajorChange * log($this->inputTimeMajorChange) - $this->resulLnCumFailureTime != 0) {
            $this->betaBeforeChange = $this->countTimeMajorChange / ($this->countTimeMajorChange * log($this->inputTimeMajorChange) - $this->resulLnCumFailureTime);
            $denominator = $this->countTimeMajorChange * log($this->endObservationTime / $this->inputTimeMajorChange) + $this->value2;
            if ($denominator != 0) {
                $this->betaAfterChange = number_format($this->value1 / $denominator, 5);
            } else {
                $this->betaAfterChange = 'Error: Division by zero';
            }
        }
        else {
            $this->betaBeforeChange = 0;
            $this->betaAfterChange = 0;
        }
    }

    public function calculateColumnLambda()
    {
        $this->lambdaBeforeChange = $this->countTimeMajorChange / pow($this->inputTimeMajorChange, $this->betaBeforeChange);
        $this->lambdaAfterChange = $this->totalNumberOfFailureAllSystem / pow($this->endObservationTime, $this->betaAfterChange);
    }

    public function calculateNewFailureRate()
    {
        if ($this->betaBeforeChange > 0 && $this->lambdaBeforeChange > 0) {
            $this->newFailureRate = $this->betaAfterChange * $this->lambdaAfterChange * pow($this->endObservationTime, ($this->betaAfterChange - 1));
            if($this->newFailureRate != 0){
                $this->newMtbf = 1 / $this->newFailureRate;
            }else{
                $this->newMtbf = 0;
            }
        }
        else{
            $this->newFailureRate = 0;
            $this->newMtbf = 0;
        }
    }

    public function inputEndObservationTime()
    {
        if ($this->endObservationTime == null) {
            return;
        }
        if($this->totalNumberOfFailureAllSystem > 0) {
            if (isset($this->endObservationTime) && $this->endObservationTime != 0) {
                $this->calculateRowSystem1();
                $this->calculateRowSystem2();
                $this->calculateRowSystem3();
                $this->calculateSlopeLambdaEta();
                $this->calculateTotalNaturalLogCumFailureTime();
            }else{
                $this->alert('error', 'Please enter The Cumulative Failure Time field.');
                return;
            }
        }
        else{
            $this->alert('error', 'Please enter The Cumulative Failure Time field.');
            return;
        }
    }

    public function inputNumberOfFailure()
    {
        $this->validateOnly('numberOfSystem');
        $this->calculateSlopeLambdaEta();
    }

    public function calculateFailureRate()
    {
        if($this->inputFailureRate == 0 || empty($this->inputFailureRate)){
            $this->alert('error', 'Please enter the Cost of Overhaul field.');
            return;
        }
        $this->validateOnly('inputFailureRate');
        if(empty($this->endObservationTime)){
            $this->alert('error', 'Please enter End observation time.');
            return;
        }else{
            $this->valueFailureRate = $this->lambda * $this->slope * pow($this->inputFailureRate, ($this->slope - 1));
            $this->instantenousMtbf = 1 / $this->valueFailureRate;
        }
        $this->setSession();
    }

    public function calculateCumulativeFailure()
    {
        if($this->inputCumulativeFailure == 0 || empty($this->inputCumulativeFailure)){
            $this->alert('error', 'Please enter the Cost of Overhaul field.');
            return;
        }
        $this->validateOnly('inputCumulativeFailure');
        $this->valueCumulativeFailure = $this->lambda * pow($this->inputCumulativeFailure, $this->slope);
        $this->setSession();
    }

    public function calculateCumFailureRate()
    {
        if($this->inputCumFailureRate == 0 || empty($this->inputCumFailureRate)){
            $this->alert('error', 'Please enter the Cost of Overhaul field.');
            return;
        }
        $this->validateOnly('inputCumFailureRate');
        $this->valueCumFailureRate = ($this->lambda * pow($this->inputCumFailureRate, $this->slope)) / $this->inputCumFailureRate;
        $this->cumMtbf = 1 / $this->valueCumFailureRate;
        $this->setSession();
    }

    public function calculateExpectedNumberFailureBetween()
    {
            $this->validateOnly('inputExpectedNumberFailure1');
            $this->validateOnly('inputExpectedNumberFailure2');
            $this->valueExpectedNumberFailure = $this->lambda * (pow($this->inputExpectedNumberFailure2, $this->slope) - pow($this->inputExpectedNumberFailure1, $this->slope));
    }

    public function calculateExpectedReliabilityBetween()
    {
        $this->validateOnly('inputExpectedReliabilityBetween1');
        $this->validateOnly('inputExpectedReliabilityBetween2');
        $this->valueExpectedReliabilityBetween = exp(-$this->lambda * (pow($this->inputExpectedReliabilityBetween2, $this->slope) - pow($this->inputExpectedReliabilityBetween1, $this->slope)));
        $this->setSession();
    }

    public function calculateMtbfBetween()
    {
        $this->validateOnly('inputMtbfBetween1');
        $this->validateOnly('inputMtbfBetween2');
        $numerator = $this->inputMtbfBetween2 - $this->inputMtbfBetween1;
        $denominator = $this->lambda * (pow($this->inputMtbfBetween2, $this->slope) - pow($this->inputMtbfBetween1, $this->slope));
        $this->valueMtbfBetween = $numerator / $denominator;
        $this->setSession();
    }

    public function probabilitySurviveToAge()
    {
        if(empty($this->currentAge)){
            $this->alert('error', 'Please enter the Cost of Current Age field.');
            return;
        }
        elseif(empty($this->additionalMissionTime)){
            $this->alert('error', 'Please enter the Cost of Additional Mission Time field.');
            return;
        }
        $this->validateOnly('currentAge');
        $this->validateOnly('additionalMissionTime');
        $term1 = $this->lambda * pow(($this->currentAge + $this->additionalMissionTime), $this->slope);
        $term2 = $this->lambda * pow($this->currentAge, $this->slope);

        $exponentValue = -($term1 - $term2);

        $this->valueSurviveToAge = exp($exponentValue);

        $this->valueFailToAge = 1 - $this->valueSurviveToAge;
        $this->setSession();
    }

    public function calculateNextCumulativeFailure()
    {
        if($this->inputNextCumulativeFailure == 0 || empty($this->inputNextCumulativeFailure)){
            $this->alert('error', 'Please enter the Cost of Cumulative failure field.');
            return;
        }
        $this->validateOnly('inputNextCumulativeFailure');
        $this->valueNextCumulativeFailure = ($this->lambda) * (pow($this->inputNextCumulativeFailure + $this->endObservationTime, $this->slope)) - $this->lambda * pow($this->endObservationTime, $this->slope);
        $this->setSession();
    }

    public function calculateTimeToOverhaul()
    {
        if($this->costOverhaul == 0 || empty($this->costOverhaul)){
            $this->alert('error', 'Please enter the Cost of Unplanned failure field.');
            return;
        }
        if($this->costUnplainedFailure == 0 || empty($this->costUnplainedFailure)){
            $this->alert('error', 'Please enter the Cost of Overhaul field.');
            return;
        }
        $this->validateOnly('costOverhaul');
        $this->validateOnly('costUnplainedFailure');
        try {
            $this->optimumTimeToOverhaul = pow(($this->costOverhaul / ($this->lambda * ($this->slope - 1) * $this->costUnplainedFailure)), (1 / $this->slope));
            $this->resultOptimumTimeToOverhaul = $this->lambda * ($this->slope - 1) * $this->costUnplainedFailure;
        } catch (Exception $e) {
            return $this->alert('error', 'No possible solution');
        }
        $this->setSession();
    }

    public function openTable()
    {
        if (empty($this->time) || empty($this->increment) || empty($this->tableRows)) {
            return;
        }

        if(empty($this->lambda) || empty($this->slope)){
            $this->alert('error', 'Value Slope or Lambda cannot be null.');
            return;
        }
        $this->updateInstantenousMtbfs();
        $this->emit('openModal', 'modal.calculation-table', [
            'slope' => $this->slope,
            'lambda' => $this->lambda,
            'time' => $this->time,
            'increment' => $this->increment,
            'tableRows' => $this->tableRows,
            'instantenousMtbfs' => $this->instantenousMtbfs,
            'cumulativeMtbfs' => $this->cumulativeMtbfs,
            'predictedNumberFailures' => $this->predictedNumberFailures,
            'times' => $this->times,
        ]);
    }

    // Calculation for table
    public function updateInstantenousMtbfs()
    {
        if (isset($this->time) || isset($this->increment) || isset($this->tableRows)){
            session()->forget('nhpp.result.instantenousMtbfs');
            session()->forget('nhpp.result.cumulativeMtbfs');
            session()->forget('nhpp.result.predictedNumberFailures');
            session()->forget('nhpp.result.times');

            $this->instantenousMtbfs = [];
            $this->cumulativeMtbfs = [];
            $this->predictedNumberFailures = [];
            $this->times = [];

            for ($index = 0; $index < $this->tableRows; $index++) {
                $division = $index + 1;
                if ($index == 0) {
                    $this->times[$index] = intval($this->time);
                } else {
                    $this->times[$index] = $this->times[$index - 1] + $this->increment;
                }
                $valueInstantenousMtbfs = 1 / ($this->lambda * $this->slope * pow(($this->time * $division), $this->slope - 1));
                $this->instantenousMtbfs[$index] = number_format($valueInstantenousMtbfs, 3, '.', '');

                $valueCumulativeMtbfs = 1 / ($this->lambda * pow(($this->time * $division), $this->slope - 1));
                $this->cumulativeMtbfs[$index] = number_format($valueCumulativeMtbfs, 3, '.', '');

                $valuePredictedNumberFailure = $this->lambda * pow($this->time * $division, $this->slope);
                $this->predictedNumberFailures[$index] = number_format($valuePredictedNumberFailure, 3, '.', '');
            }
            $this->setSession();
        }else{
            return;
        }
    }
}
?>
