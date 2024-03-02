<div>
    <div class="w-full p-6 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <div class=" flex flex-col">
                <h1 class="text-2xl">{{ __('NHPP Single System') }}</h1>
            </div>
            {{-- Breadcrum --}}
            <div class="p-4">
                {{ __('Dashboard') }}
            </div>
        </div>

        <div class="flex bg-white rounded-t-xl shadow-md h-96">
            <div class="w-3/12 grid grid-cols-1 divide-y rounded-xl">
                <div class="w-full flex items-center divide-x px-2 py-6 bg-blue-900 text-white rounded-tl-xl">
                    <div class="w-[30%] text-center text-2xl">
                        &beta;
                    </div>
                    <div class="w-[70%] flex flex-col px-2 font-medium text-xl">
                        <div>{{ $slope ? number_format($slope,6) : 0 }}</div>
                        <div>{{ __('Slope (MLE)') }}</div>
                    </div>
                </div>

                <div class="w-full flex items-center divide-x px-2 py-6 bg-blue-900 text-white">
                    <div class="w-[30%] text-center text-2xl">
                        &lambda;
                    </div>
                    <div class="w-[70%] flex flex-col px-2 font-medium text-xl">
                        <div>{{ $lambda ? number_format($lambda,6) : 0 }}</div>
                        <div>{{ __('Lambda') }}</div>
                    </div>
                </div>

                <div class="w-full flex items-center divide-x px-2 py-6 bg-blue-900 text-white">
                    <div class="w-[30%] text-center text-2xl">
                        &eta;
                    </div>
                    <div class="w-[70%] flex flex-col px-2 font-medium text-xl">
                        <div>{{ $eta ? number_format($eta,6) : 0 }}</div>
                        <div>{{ __('Eta') }}</div>
                    </div>
                </div>
            </div>
            <div class="w-full flex gap-4 p-4">
                <div class="w-full rounded-lg shadow-md overflow-auto">
                    <table class="w-full ">
                        <thead class="sticky top-0 bg-white rounded-t-xl">
                            <tr class="bg-gray-200">
                                <th class="py-4 rounded-tl-xl">{{ __('No. of Failure') }}</th>
                                <th class="py-4 w-2/12">{{ __('Cum. Failure Time') }}</th>
                                <th class="py-4">{{ __('Time Between Failure') }}</th>
                                <th class="py-4">{{ __('Cum MTBF') }}</th>
                                <th class="py-4">{{ __('ln (Cum. Failure time)') }}</th>
                                <th class="py-4 rounded-tr-xl">{{ __('ln (T*/ti)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($failureTimes as $key => $failureTime)
                                <tr class="text-center border-b">
                                    <td>{{ $key + 1 . '.' }}</td>
                                    <td class="p-1">
                                        <div x-data="{ inputText: '' }" class="flex flex-col gap-2">
                                            <input
                                                wire:keydown.enter="addRow({{ $key }})"
                                                wire:model="failureTimes.{{ $key }}.cumulative_failure_time"
                                                type="text"
                                                x-ref="inputField{{ $key }}"
                                                x-model="inputText"
                                                placeholder="Enter the number"
                                                class="rounded-lg border-gray-400 text-center"
                                                value="{{ $failureTime['cumulative_failure_time'] }}"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        {{ $failureTime['time_between_failures'] }}
                                        <input type="text" class="sr-only" disabled wire:model="failureTimes.{{ $key }}.time_between_failures">
                                    </td>
                                    <td>
                                        {{ number_format($failureTime['cum_mtbf'], 2) }}
                                        <input type="text" class="sr-only" disabled wire:model="failureTimes.{{ $key }}.cum_mtbf">
                                    </td>
                                    <td>
                                        {{ number_format($failureTime['natural_log_cum_failure_time'], 3) }}
                                        <input type="text" class="sr-only" disabled wire:model="failureTimes.{{ $key }}.natural_log_cum_failure_time" placeholder="{{ $failureTime['natural_log_cum_failure_time'] }}">
                                    </td>
                                    <td>
                                        {{ number_format($failureTime['natural_log_tti'], 3) }}
                                        <input type="text" class="sr-only" disabled wire:model="failureTimes.{{ $key }}.natural_log_tti" placeholder="{{ $failureTime['natural_log_tti'] }}">
                                    </td>
                                </tr>
                                @error('failureTimes.'.$key.'.*')
                                    <tr>
                                        <td></td>
                                        @error('failureTimes.'.$key.'.cumulative_failure_time')
                                            <td>
                                                <span class="text-red-500 text-sm font-medium text-left">{{ $message }}</span>
                                            </td>
                                        @enderror
                                    </tr>
                                @enderror
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="w-5/12 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <h1 class="text-2xl">Result</h1>
                            <h6>Estimated Parameters</h6>
                        </div>
                        <div class="flex gap-2">
                            <div x-data="{ refreshPage: 'Refresh!' }">
                                <button x-tooltip="refreshPage"
                                    @if ($slope == 0 || $lambda == 0 || $eta == 0) disabled @endif
                                    wire:click="isRefreshPage" class="disabled:bg-gray-200 disabled:text-gray-500 bg-red-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                                    <x-icons.refresh />
                                </button>
                            </div>
                            <div x-data="{ generatePdf: 'Export to PDF' }">
                                <button x-tooltip="generatePdf"
                                    @if ($slope == 0 || $lambda == 0 || $eta == 0) disabled @endif
                                    wire:click="isGeneratePdf" class="disabled:bg-gray-200 disabled:text-gray-500 bg-green-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                                    <x-icons.document />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 h-auto">
                        <div class="flex flex-col shadow-md items-center py-4 rounded-lg bg-[#c8eceea5] text-[#22B8BE]">
                            <div class="text-lg">
                                {{ $numberOfFailure }}
                            </div>
                            {{ __('No. of failure (n)') }}
                        </div>

                        <div class="flex flex-col shadow-md items-center py-4 rounded-lg bg-[#fdf4b2] text-[#d1b200]">
                            <input
                                wire:keydown.enter="inputEndObservationTime()"
                                wire:model="endObservationTime"
                                type="text"
                                placeholder="0"
                                class="border-[#ffe93f] rounded-lg w-24 text-center bg-[#fdf4b2]"
                                @if($endObservationTime == 0) onfocus="this.value=''" @endif
                            >
                            @error('endObservationTime')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            {{ __('End of obervation time (T*)') }}
                        </div>

                        <div class="flex flex-col shadow-md items-center py-4 rounded-lg bg-[#e4e4e4] text-[#8F8F8F]">
                            <div class="text-lg">
                                {{ number_format($total , 2) }}
                            </div>
                            {{ __('Sum of In(Cum. TTF)') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 w-full">
            <div class="w-1/2 divide-y-2 p-4 shadow-md rounded-bl-xl flex flex-col gap-4 bg-white">
                <div class="font-inter font-medium text-lg text-gray-900">
                    {{ __('Time VS MTBF') }}
                </div>
                <div class="pt-4">
                    <canvas wire:ignore id="timeVsMtbfChart"></canvas>
                </div>
            </div>
            <div class="w-1/2 divide-y-2 p-4 shadow-md rounded-br-xl flex flex-col gap-4 bg-white">
                <div class="font-inter font-medium text-lg text-gray-900">
                    {{ __('Time VS Predicted Number of Failure') }}
                </div>
                <div class="pt-4">
                    <canvas wire:ignore id="timeVsPredictedNumberOfFailureChart"></canvas>
                </div>
        </div>
    </div>
    @push('js')

        <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

        <script type="text/javascript">
                var ctx = document.getElementById("timeVsMtbfChart");

                var myLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: "Instantenous MTBF",
                                fill: false,
                                borderColor: 'rgb(0, 142, 34)',
                                tension: 0.1,
                                data: [],
                            },
                            {
                                label: "Cumulative MTBF",
                                fill: false,
                                borderColor: 'rgb(30, 58, 138)',
                                tension: 0.1,
                                data: [],
                            },
                        ],
                    },
                });

                Livewire.on('updateChartMtbf', event => {
                    var labels = JSON.parse(event.labels);
                    var data = JSON.parse(event.data);
                    var data1 = JSON.parse(event.data1);
                    myLineChart.data.labels = labels;
                    myLineChart.data.datasets[0].data = data;
                    myLineChart.data.datasets[1].data = data1;
                    myLineChart.update();
                })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsPredictedNumberOfFailureChart");

            var myLineChart1 = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "Instantenous MTBF",
                            fill: false,
                            borderColor: 'rgb(229, 11, 11)',
                            tension: 0.1,
                            data: [],
                        },
                    ],
                },
            });

            Livewire.on('updateChartPredicted', event => {
                var times = JSON.parse(event.times);
                var data = JSON.parse(event.data);
                myLineChart1.data.labels = times;
                myLineChart1.data.datasets[0].data = data;
                myLineChart1.update();
            })
        </script>

        <script>
            window.addEventListener('refresh-page', event => {
            window.location.reload(false);
            })
        </script>
    @endpush
</div>
