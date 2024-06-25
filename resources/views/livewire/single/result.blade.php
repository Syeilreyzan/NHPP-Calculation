<div>
    <div class="w-full flex flex-col gap-3 mt-14">
        <div class="flex bg-white rounded-lg shadow-md gap-2">
            {{-- Table Result --}}
            <div class="w-full flex flex-col gap-6 rounded-lg">
                <table class="rounded-xl w-full border  border-separate">
                    <thead>
                        <tr>
                            <th colspan="3" class="rounded-t-xl text-2xl p-2 bg-gray-table-header">{{ __('Results') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="1" class=""></th>
                            @endif
                        </tr>
                        <tr>
                            <th colspan="3" class="border text-xl p-2 bg-green-table-header">{{ __('Input Data') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="1" class=""></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]">{{ __('No. of failure') }}</td>
                            <td class="border py-1 px-2 text-center">n =</td>
                            <td class="border py-1 px-2 text-center">{{ $numberOfFailure }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2">{{ __('End of observation time (T*)') }}</td>
                            <td class="border py-1 px-2 text-center">T =</td>
                            <td class="border py-1 px-2 text-center">
                                <input
                                    wire:keydown.enter="inputEndObservationTime()"
                                    wire:model="endObservationTime"
                                    type="text"
                                    placeholder="0"
                                    class="border-[#ffe93f] p-0 rounded-lg w-24 text-center bg-[#fdf4b2] cursor-pointer"
                                    @if($endObservationTime == 0) onfocus="this.value=''" @endif
                                >
                            </td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2">{{ __('Sum of ln(cum TTF)') }}</td>
                            <td class="border py-1 px-2 text-center"></td>
                            <td class="border py-1 px-2 text-center">{{ number_format($total,6) }}</td>
                        </tr>
                        <tr>
                            <th colspan="3" class="border text-xl p-2 bg-green-table-header">{{ __('Estimated Parameters (Time Terminated)') }}</th>
                            @if ($checkBoxBounds)
                                <th class="border py-1 px-2 w-[10%] text-center">{{ __('Bounds') }}</th>
                                <td class="border py-1 px-2 w-[10%] text-center">
                                    <input wire:keydown.enter="calculateBoundsPercent()" wire:model="inputBoundPercent" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                    @if ($inputBoundPercent == 0) onfocus="this.value=''" placeholder="Enter Failure Rate" @endif>
                                    @error('inputBoundPercent')
                                        <span class="text-red-500">{{ $message }}</span>
                                    @enderror
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2">{{ __('Slope (MLE)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">&beta; =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ number_format($slope,6) }}</td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 text-center">{{ $bound1 ? number_format($bound1, 4) : 0 }}</td>
                                <td class="border py-1 px-2 text-center">{{ $bound2 }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2">{{ __('Lambda') }}</td>
                            <td class="border py-1 px-2 text-center">&lambda; =</td>
                            <td class="border py-1 px-2 text-center">{{ number_format($lambda,6) }}</td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 text-center">{{ $bound2 }}</td>
                                <td class="border py-1 px-2 text-center">{{ $bound2 }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 rounded-bl-xl">{{ __('Eta') }}</td>
                            <td class="border py-1 px-2 text-center">&eta; =</td>
                            <td class="border py-1 px-2 text-center rounded-br-xl">{{ number_format($eta,6) }}</td>
                        </tr>
                    </tbody>
                </table>
                {{-- Table Caculations --}}
                <table class="rounded-xl w-full border border-separate">
                    <thead>
                        <tr>
                            <th colspan="4" class="rounded-t-xl text-2xl p-2 bg-green-table-header">{{ __('Calculations') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="2" class=""></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]">{{ __('Failure Rate (Instantenous),') }}</td>
                            <td class="border py-1 px-2 w-[10%] text-center">t =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateFailureRate()" wire:model="inputFailureRate" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputFailureRate == 0) onfocus="this.value=''" placeholder="Enter Failure Rate" @endif>
                                @error('inputFailureRate')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $instantenousMtbf ? number_format($valueFailureRate, 6) : '' }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[90%]' : 'w-[80%]' }}">{{ __('Instantenous MTBF, 1/u(t)') }}</td>
                            {{-- <td class="{{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }}"></td> --}}
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $instantenousMtbf ? number_format($instantenousMtbf, 4) : '' }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]">{{ __('Cumulative failure N(t),') }}</td>
                            <td class="border py-1 px-2 w-[10%] text-center">t =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateCumulativeFailure()" wire:model="inputCumulativeFailure" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputCumulativeFailure == 0) onfocus="this.value=''" placeholder="Enter Cumulative Failure" @endif>
                                @error('inputCumulativeFailure')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $valueCumulativeFailure ? number_format($valueCumulativeFailure, 4) : ''  }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]">{{ __('Cum. Failure Rate,') }}</td>
                            <td class="border py-1 px-2 w-[10%] text-center">t =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateCumFailureRate()" wire:model="inputCumFailureRate" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputCumFailureRate == 0) onfocus="this.value=''" placeholder="Enter Cumulative Failure" @endif>
                                @error('inputCumFailureRate')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $valueCumFailureRate ? number_format($valueCumFailureRate, 4) : '' }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[90%]' : 'w-[80%]' }} rounded-bl-lg">{{ __('Cum MTBF') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center  rounded-br-lg">{{ $cumMtbf ? number_format($cumMtbf, 4) : '' }}</td>
                        </tr>

                        <tr>
                            <td class="p-1"></td>
                        </tr>

                        <tr>
                            <td colspan="4" class="border py-1 px-2 w-full bg-gray-300 rounded-t-lg">{{ __('Expected Number of Failure between') }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t1 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateExpectedNumberFailureBetween()" wire:model="inputExpectedNumberFailure1" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputExpectedNumberFailure1 == 0) onfocus="this.value=''" placeholder="t1" @endif>
                                @error('inputExpectedNumberFailure1')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%] rounded-bl-lg"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t2 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateExpectedNumberFailureBetween()" wire:model="inputExpectedNumberFailure2" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputExpectedNumberFailure2 == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('inputExpectedNumberFailure2')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">{{ $valueExpectedNumberFailure ? number_format($valueExpectedNumberFailure, 4) : '' }}</td>
                        </tr>

                        <tr>
                            <td class="p-1"></td>
                        </tr>

                        <tr>
                            <td colspan="4" class="border py-1 px-2 w-full bg-gray-300 rounded-t-lg">{{ __('Expected Reliability between') }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t1 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateExpectedReliabilityBetween()" wire:model="inputExpectedReliabilityBetween1" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputExpectedReliabilityBetween1 == 0) onfocus="this.value=''" placeholder="t1" @endif>
                                @error('inputExpectedReliabilityBetween1')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%] rounded-bl-lg"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t2 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateExpectedReliabilityBetween()" wire:model="inputExpectedReliabilityBetween2" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputExpectedReliabilityBetween2 == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('inputExpectedReliabilityBetween2')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">{{ $valueExpectedReliabilityBetween ? number_format($valueExpectedReliabilityBetween, 4) : '' }}</td>
                        </tr>

                        <tr>
                            <td class="p-1"></td>
                        </tr>

                        <tr>
                            <td colspan="4" class="border py-1 px-2 w-full bg-gray-300 rounded-t-lg">{{ __('MTBF between') }}</td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%]"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t1 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateMtbfBetween()" wire:model="inputMtbfBetween1" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputMtbfBetween1 == 0) onfocus="this.value=''" placeholder="t1" @endif>
                                @error('inputMtbfBetween1')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[50%] rounded-bl-lg"></td>
                            <td class="border py-1 px-2 w-[10%] text-center">t2 =</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateMtbfBetween()" wire:model="inputMtbfBetween2" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputMtbfBetween2 == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('inputMtbfBetween2')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">{{ $valueMtbfBetween ? number_format($valueMtbfBetween, 4) : '' }}</td>
                        </tr>

                        <tr>
                            <td class="p-1"></td>
                        </tr>

                        <tr>
                            <td colspan="4" class="border py-1 px-2 w-full bg-gray-300 rounded-t-lg">{{ __('Probability that the system will survive to age (𝑡+𝑑) ') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 w-[60%] text-right">{{ __('Current Age (t)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="probabilitySurviveToAge()" wire:model="currentAge" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($currentAge == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('currentAge')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 w-[60%] text-right">{{ __('Additional Mission Time (d)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="probabilitySurviveToAge()" wire:model="additionalMissionTime" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($additionalMissionTime == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('additionalMissionTime')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 w-[60%] text-right">{{ __('R(𝑡+𝑑)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $valueSurviveToAge ? number_format($valueSurviveToAge, 4) : '' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 w-[60%] text-right rounded-bl-lg">{{ __('Probability that the system will fail to age (𝑡+𝑑)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">{{ $valueFailToAge ? number_format($valueFailToAge, 4) : '' }}</td>
                        </tr>

                        <tr>
                            <td class="p-1"></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="border py-1 px-2 rounded-l-lg">{{ __('Cumulative failure N(t), for the next t=') }}</td>
                            <td class="border py-1 px-2 text-center">
                                <input wire:keydown.enter="calculateNextCumulativeFailure()" wire:model="inputNextCumulativeFailure" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputNextCumulativeFailure == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('inputNextCumulativeFailure')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="border py-1 px-2 text-center rounded-r-lg">{{ $valueNextCumulativeFailure ? number_format($valueNextCumulativeFailure, 4) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                    </tbody>
                </table>

                <table class="rounded-xl w-full border border-separate">
                    <thead>
                        <tr>
                            <th colspan="5" class="rounded-t-xl text-2xl p-2 bg-green-table-header">{{ __('Prediction of next failure (50% confidence)') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="2" class=""></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 w-[60%]">{{ __('Next failure occurance (time), N =') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $nextNumberOfFailure ? number_format($nextNumberOfFailure) : '' }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $resultNextNumberOfFailure ? number_format($resultNextNumberOfFailure, 3) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 w-[10%]"></td>
                                <td class="border py-1 px-2 text-center w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="4" class="border py-1 px-2 w-[60%] rounded-bl-lg">{{ __('Time to next Failure') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">{{ $timeToNextFailure ? number_format($timeToNextFailure, 3) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 w-[10%]"></td>
                                <td class="border py-1 px-2 text-center w-[10%]"></td>
                            @endif
                        </tr>
                    </tbody>
                </table>

                <table class="rounded-xl w-full border border-separate">
                    <thead>
                        <tr>
                            <th colspan="4" class="rounded-t-xl text-2xl p-2 bg-green-table-header">{{ __('Optimum time to overhaul') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="2" class=""></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}">{{ __('Cost of Unplanned failure (C1)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateTimeToOverhaul()" wire:model="costUnplainedFailure" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($costUnplainedFailure == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('costUnplainedFailure')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}">{{ __('Cost of Overhaul (C2)') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateTimeToOverhaul()" wire:model="costOverhaul" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($costOverhaul == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('costOverhaul')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}">{{ __('Optimum time to overhaul') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $optimumTimeToOverhaul ? number_format($optimumTimeToOverhaul, 3) : 0 }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="3" class="border py-1 px-2 rounded-bl-lg {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center  rounded-br-lg ">{{ $resultOptimumTimeToOverhaul ? number_format($resultOptimumTimeToOverhaul, 3) : 0 }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                    </tbody>
                </table>

                <table class="rounded-xl w-full border border-separate">
                    <thead>
                        <tr>
                            <th colspan="3" class="rounded-t-xl text-2xl p-2 bg-green-table-header">{{ __('Slope Change Analysis') }}</th>
                            @if ($checkBoxBounds)
                            <th colspan="2" class=""></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}">{{ __('Time of major change') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="calculateTimeMajorChange()" wire:model="inputTimeMajorChange" type="text" class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($inputTimeMajorChange == 0) onfocus="this.value=''" placeholder="t2" @endif>
                                @error('inputTimeMajorChange')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 w-[10%] text-center">{{ $countTimeMajorChange ?? '' }}</td>
                                <td class="border py-1 px-2 text-center w-[10%]">{{ $resulLnCumFailureTime ? number_format($resulLnCumFailureTime, 3) : '' }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center bg-yellow-100 font-semibold">Beta</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center bg-yellow-100 font-semibold">Lambda</td>
                            @if ($checkBoxBounds)
                                <td class="border py-1 px-2 w-[10%] text-center">{{ $value1 ?? ''}}</td>
                                <td class="border py-1 px-2 text-center w-[10%]">{{ $value2 ? number_format($value2, 3) : '' }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]">{{ __('Before change') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $betaBeforeChange ? number_format($betaBeforeChange, 5) : '' }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $lambdaBeforeChange ? number_format($lambdaBeforeChange, 5) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]">{{ __('After Change') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $betaAfterChange ? number_format($betaAfterChange, 5) : '' }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $lambdaAfterChange ? number_format($lambdaAfterChange, 5) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ __('End of observation') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="inputEndObservationTime()"
                                    wire:model="endObservationTime"
                                    type="text"
                                    class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                    @if ($endObservationTime == 0) onfocus="this.value=''" placeholder="t1" @endif
                                >
                                @error('endObservationTime')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ __('New failure rate') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $newFailureRate ? number_format($newFailureRate, 3) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td class="border py-1 px-2 w-[60%]"></td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ __('New MTBF') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">{{ $newMtbf ? number_format($newMtbf, 3) : '' }}</td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }}">{{ __('Time Start') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center">
                                <input wire:keydown.enter="generateTable()"
                                    wire:model="time"
                                    type="text"
                                    class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                    @if ($time == 0) onfocus="this.value=''" placeholder="Time Start" @endif
                                >
                                @error('time')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }} rounded-bl-lg">{{ __('Increment') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">
                                <input wire:keydown.enter="generateTable()"
                                wire:model="increment"
                                type="text"
                                class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"
                                @if ($increment == 0) onfocus="this.value=''" placeholder="Increment" @endif
                                >
                                @error('increment')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="2" class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[70%]' : 'w-[80%]' }} rounded-bl-lg">{{ __('Row Table') }}</td>
                            <td class="border py-1 px-2 {{ $checkBoxBounds ? 'w-[10%]' : 'w-[20%]' }} text-center rounded-br-lg">
                                <input wire:keydown.enter="generateTable()"
                                    wire:model="tableRows"
                                    type="text"
                                    class="px-4 py-0 text-center w-full rounded-lg border-yellow-500 bg-yellow-200 focus:ring-green-500 focus:border-green-500 placeholder:text-sm"

                                    @if ($tableRows == 0) onfocus="this.value=''" placeholder="Table Row" @endif>
                                    @error('tableRows')
                                        <span class="text-red-500">{{ $message }}</span>
                                    @enderror
                            </td>
                            @if ($checkBoxBounds)
                                <td class="w-[10%]"></td>
                                <td class="w-[10%]"></td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- <div class="grid gap-y-96 items-center"> --}}
            <div class="fixed right-10 flex flex-col w-auto gap-2" x-data="{ open: false, viewMore: 'View More!'}">
                <button x-tooltip="viewMore" x-on:click="open = ! open" x-on:click.outside="open = false"
                    class="disabled:bg-gray-200 disabled:text-gray-500 bg-blue-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500 sticky lg:fixed right-10 flex flex-col lg:flex-row w-full lg:w-auto gap-2">
                    <x-icons.hamburger />
                </button>
                <div x-show="open" class="flex flex-col gap-2"
                    x-data="{ refreshPage: 'Refresh!', checkBoxBounds: 'View Bounds', table: 'View Table', generatePdf: 'Export to PDF'}" x-cloak
                >
                    <button x-tooltip="refreshPage"
                        @if ($slope == 0 || $lambda == 0 || $eta == 0) disabled @endif
                        wire:click="isRefreshPage" class="disabled:bg-gray-200 disabled:text-gray-500 bg-red-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                        <x-icons.refresh />
                    </button>
                    <button x-tooltip="checkBoxBounds"
                        wire:click="checkBoxBounds"
                        value="0"
                        class="disabled:bg-gray-200 disabled:text-gray-500 bg-indigo-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                        <x-icons.view-column />
                    </button>
                    <button x-tooltip="table"
                        wire:click="openTable()" {{ $openTable ? '' : 'disabled' }}
                        class="disabled:bg-gray-200 disabled:text-gray-500 bg-green-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                        <x-icons.table />
                    </button>
                    <button x-tooltip="generatePdf"
                        @if ($times == null) disabled @endif
                        wire:click="isGeneratePdf"
                        class="disabled:bg-gray-200 disabled:text-gray-500 bg-yellow-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                        <x-icons.document />
                    </button>
                </div>
            </div>

            {{-- </div> --}}
        </div>
    </div>
    @push('js')
        <script>
            window.addEventListener('refresh-page', event => {
            window.location.reload(false);
            })
        </script>
    @endpush
</div>
