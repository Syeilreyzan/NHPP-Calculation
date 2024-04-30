<?php

namespace App\Traits;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

trait HasCalculationTrait
{
    public $failureTimes = [], $endObservationTime = 0, $numberOfFailure = 0, $total, $slope, $lambda, $eta;

    public $inputFailureRate = 0, $valueFailureRate, $instantenousMtbf;
    public $inputCumulativeFailure = 0, $valueCumulativeFailure;
    public $inputCumFailureRate = 0, $valueCumFailureRate, $cumMtbf;
    public $inputExpectedNumberFailure1 = 0, $inputExpectedNumberFailure2 = 0, $valueExpectedNumberFailure;
    public $inputExpectedReliabilityBetween1 = 0, $inputExpectedReliabilityBetween2 = 0, $valueExpectedReliabilityBetween;
    public $inputMtbfBetween1, $inputMtbfBetween2, $valueMtbfBetween;
    public $currentAge = 0, $additionalMissionTime = 0, $valueSurviveToAge, $valueFailToAge;
    public $inputNextCumulativeFailure = 0, $valueNextCumulativeFailure;
    public $inputBoundPercent = 0, $bound1 = 0, $bound2 = 0, $bound3 = 0, $bound4 = 0;
    public $nextNumberOfFailure, $resultNextNumberOfFailure, $timeToNextFailure;
    public $costOverhaul = 0, $costUnplainedFailure = 0, $optimumTimeToOverhaul = 0, $resultOptimumTimeToOverhaul;
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
        // 'failureTimes' => 'required|array',
        'failureTimes.*.cumulative_failure_time' => 'numeric',// Define your validation rules here.
        'endObservationTime' => 'numeric',
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
        'failureTimes.*.cumulative_failure_time.numeric' => 'The cumulative failure time field is number.',
        'endObservationTime.numeric' => 'The end observation time field is number.',
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

    // WIP
    public function calculateBoundsPercent()
    {
        $convertToPercent = $this->inputBoundPercent / 100;
        // dd($convertToPercent);
        $this->bound1 = (chi_square_inverse((1 - $convertToPercent) / 2, 2 * $this->numberOfFailure) / (2 * $convertToPercent)) * $this->slope;
        $this->bound1 = (chi_square_inverse(1, 2)) * $this->slope;
        // $this->bound1  = (chi_square_inverse((1 - 0.95) / 2, 2 * 13) / (2 * 0.95)) * $this->slope;
        // $this->bound1 = $chisq_inv * $this->slope;
    }


    public function addRow($index)
    {
        $this->validateOnly('failureTimes.*.cumulative_failure_time');

        if (empty($this->failureTimes[$index]['cumulative_failure_time'])) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field.');
            return;
        }

        if ($index > 0 && $this->failureTimes[$index]['cumulative_failure_time'] <= $this->failureTimes[$index - 1]['cumulative_failure_time']) {
            $this->alert('error', 'Please enter the value greater than the previous value.');
            return;
        }

        if (isset($this->failureTimes[$index + 1]) && $this->failureTimes[$index + 1]['cumulative_failure_time'] > 0 && $this->failureTimes[$index] >= $this->failureTimes[$index + 1] ) {
            $this->alert('error', 'Please enter the value greater than the previous value and lower than next value.');
            return;
        }

        if ($index > 0 && $this->failureTimes[$index - 1]['cumulative_failure_time'] == 0) {
            $this->alert('error', 'Please enter The Cumulative Failure Time field');
            return;
        }

        $this->calculateRow();

        if (isset($this->failureTimes[$index]['cumulative_failure_time']) &&
            $this->failureTimes[$index]['cumulative_failure_time'] > 0 &&
            $index == count($this->failureTimes) - 1) {
            $this->failureTimes[] = [
                'id' => 0,
                'cumulative_failure_time' => null,
                'time_between_failures' => 0,
                'cum_mtbf' => 0,
                'natural_log_cum_failure_time' => 0,
                'natural_log_tti' => 0,
            ];
        }
    }

    public function calculateRow()
    {
        foreach ($this->failureTimes as $index => $failureTime) {
            $check_cumulative_failure_time = $failureTime['cumulative_failure_time'];
            if (
                !is_numeric($check_cumulative_failure_time)
            ) {
                unset($this->failureTimes[$index]);
                $this->failureTimes = array_values($this->failureTimes);
            }
        }
        $this->validateOnly('failureTimes.*.cumulative_failure_time');
        $this->validateOnly('endObservationTime');
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
                    if (is_null($this->failureTimes[$index]['cumulative_failure_time']) || $this->failureTimes[$index]['cumulative_failure_time'] == 0 ) {
                        $this->failureTimes[$index]['time_between_failures'] = 0;
                    } else {
                        $this->failureTimes[$index]['time_between_failures'] = $this->failureTimes[$index]['cumulative_failure_time'] - $this->failureTimes[$index - 1]['cumulative_failure_time'];
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
                    }
                }
            }

            $this->calculateSlopeLambdaEta();
            // $this->calculatePredictionNextFailure();
            $this->setSession();
        }
    }

    public function updatedEndObservationTime()
    {
        $this->inputEndObservationTime();
    }

    public function inputEndObservationTime()
    {
        // $this->validate();

        if ($this->endObservationTime > 0) {
            if($this->numberOfFailure > 0) {
                $this->calculateRow();
            }else{
                $this->alert('error', 'Please enter The Cumulative Failure Time field.');
                return;
            }
        }
        // else{
        //     return;
        // }
    }

    public function calculateSlopeLambdaEta()
    {
        if($this->endObservationTime > 0 && $this->numberOfFailure > 0) {
            $this->slope = $this->numberOfFailure / ($this->numberOfFailure * log($this->endObservationTime) - $this->total);
            $this->lambda = $this->numberOfFailure / pow($this->endObservationTime, $this->slope);
            $this->eta = pow((1/$this->lambda), (1/$this->slope));

            $this->calculatePredictionNextFailure();
            // $this->nextNumberOfFailure = $this->numberOfFailure + 1;
            // $this->resultNextNumberOfFailure = pow($this->nextNumberOfFailure / $this->lambda, 1 / $this->slope);
            // $this->updateInstantenousMtbfs();
            // $this->updatePredictedNumberOfFailure();
            // $this->updateDataMtbf();
            // $this->updateDataPredictedNumberOfFailure();
        }
        else{
            $this->endObservationTime = 0;
            $this->slope = 0;
        }

        $this->setSession();
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
            // $this->valueFailureRate = 0.005188 * 1.300880 * pow($this->inputFailureRate, (1.300880 - 1));

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
        // $this->valueCumulativeFailure = 0.005188 * pow(610, 1.300880);
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
        // $this->valueCumFailureRate = (0.005188 * pow($this->inputCumFailureRate, 1.300880)) / $this->inputCumFailureRate;
        $this->cumMtbf = 1 / $this->valueCumFailureRate;
        $this->setSession();
    }

    public function calculateExpectedNumberFailureBetween()
    {
        $this->validateOnly('inputExpectedNumberFailure1');
        $this->validateOnly('inputExpectedNumberFailure2');
        $this->valueExpectedNumberFailure = $this->lambda * (pow($this->inputExpectedNumberFailure2, $this->slope) - pow($this->inputExpectedNumberFailure1, $this->slope));
        // $this->valueExpectedNumberFailure = 0.005188 * (pow(610, 1.300880) - pow(410, 1.300880));
        $this->setSession();
    }

    public function calculateExpectedReliabilityBetween()
    {
        $this->validateOnly('inputExpectedReliabilityBetween1');
        $this->validateOnly('inputExpectedReliabilityBetween2');
        $this->valueExpectedReliabilityBetween = exp(-$this->lambda * (pow($this->inputExpectedReliabilityBetween2, $this->slope) - pow($this->inputExpectedReliabilityBetween1, $this->slope)));
        // $this->valueExpectedReliabilityBetween = exp(-$this->lambda * (pow($this->inputExpectedReliabilityBetween1, $this->slope) - pow($this->inputExpectedReliabilityBetween2, $this->slope)));
        // $this->valueExpectedReliabilityBetween = exp(-0.005188 * (pow(610, 1.300880) - pow(410, 1.300880)));
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
        $this->valueSurviveToAge = exp(-$this->lambda * pow($this->currentAge + $this->additionalMissionTime, $this->slope));
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

    public function calculatePredictionNextFailure()
    {
        if (isset($this->endObservationTime) && $this->endObservationTime > 0 && isset($this->numberOfFailure) && $this->numberOfFailure > 0){
            # code...
            $this->nextNumberOfFailure = $this->numberOfFailure + 1;
            $this->resultNextNumberOfFailure = pow($this->nextNumberOfFailure / $this->lambda, 1 / $this->slope);
            $this->timeToNextFailure = $this->resultNextNumberOfFailure - $this->endObservationTime;
        }
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
            return "No possible solution";
        }
        // $this->resultOptimumTimeToOverhaul = $this->lambda * ($this->slope - 1) * $this->costUnplainedFailure;
        $this->setSession();
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
        if($this->failureTimes && count($this->failureTimes) > 0) {
            foreach ($this->failureTimes as $index => $value) {
                if ($this->failureTimes[$index]['cumulative_failure_time'] > 0 && $this->failureTimes[$index]['cumulative_failure_time'] <= $this->inputTimeMajorChange) {
                    $this->countTimeMajorChange++;
                    if(isset($this->failureTimes[$index]['natural_log_cum_failure_time'])){
                        $this->resulLnCumFailureTime += $this->failureTimes[$index]['natural_log_cum_failure_time'];
                    }
                }
                if (isset($this->failureTimes[$index]['natural_log_tti']) && $this->failureTimes[$index]['cumulative_failure_time'] > $this->inputTimeMajorChange) {
                    $this->value2 += $this->failureTimes[$index]['natural_log_tti'];
                }
                $this->value1 = $this->numberOfFailure - $this->countTimeMajorChange;
            }
        }
        $this->calculateColumnBeta();
        $this->calculateColumnLambda();
        $this->calculateNewFailureRate();
        $this->setSession();
    }

    public function calculateColumnBeta()
    {
        if ($this->countTimeMajorChange * log($this->inputTimeMajorChange) - $this->resulLnCumFailureTime != 0) {
            $this->betaBeforeChange = $this->countTimeMajorChange / ($this->countTimeMajorChange * log($this->inputTimeMajorChange) - $this->resulLnCumFailureTime);
            $this->betaAfterChange = $this->value1 / ($this->countTimeMajorChange * log($this->endObservationTime / $this->inputTimeMajorChange) + $this->value2);
        }
        else {
            $this->betaBeforeChange = 0;
            $this->betaAfterChange = 0;
        }
    }

    public function calculateColumnLambda()
    {
        $this->lambdaBeforeChange = $this->countTimeMajorChange / pow($this->inputTimeMajorChange, $this->betaBeforeChange);
        $this->lambdaAfterChange = $this->numberOfFailure / pow($this->endObservationTime, $this->betaAfterChange);
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
