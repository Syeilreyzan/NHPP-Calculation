<div>
    <div class="flex flex-col gap-4 w-full mt-12">
        <div class="flex bg-white rounded-t-xl shadow-md h-96">
            {{-- <div class="w-3/12 grid grid-cols-1 divide-y rounded-xl">
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
            </div> --}}
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
                                        <div class="flex flex-col gap-2">
                                            <input
                                                wire:keydown.enter="addRow({{ $key }})"
                                                wire:model="failureTimes.{{ $key }}.cumulative_failure_time"
                                                type="text"
                                                placeholder="Enter the number"
                                                class="rounded-lg border-gray-400 text-center"
                                                {{-- value="{{ $failureTime['cumulative_failure_time'] }}" --}}
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

                <div class="w-5/12 flex flex-col justify-center gap-4">
                    <div class="flex justify-end gap-2">
                        <div x-data="{ refreshPage: 'Refresh!' }">
                            <button x-tooltip="refreshPage"
                                @if ($slope == 0 || $lambda == 0 || $eta == 0) disabled @endif
                                wire:click="isRefreshPage" class="disabled:bg-gray-200 disabled:text-gray-500 bg-red-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                                <x-icons.refresh />
                            </button>
                        </div>
                        <div x-data="{ generatePdf: 'Export to PDF' }">
                            <button x-tooltip="generatePdf"
                                @if ($times == null) disabled @endif
                                wire:click="isGeneratePdf" class="disabled:bg-gray-200 disabled:text-gray-500 bg-green-200 p-2 rounded-lg shadow-md cursor-pointer hover:bg-blue-200 hover:text-blue-500">
                                <x-icons.document />
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 h-auto">
                        <div class="flex flex-col items-center py-4 rounded-t-xl bg-[#c8eceea5] text-[#22B8BE]">
                            <div class="text-lg">
                                {{ $numberOfFailure }}
                            </div>
                            {{ __('No. of failure (n)') }}
                        </div>

                        <div class="flex flex-col items-center py-4 bg-[#fdf4b2] text-[#d1b200]">
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

                        <div class="flex flex-col items-center py-4 rounded-b-xl bg-[#e4e4e4] text-[#8F8F8F]">
                            <div class="text-lg">
                                {{ number_format($total , 2) }}
                            </div>
                            {{ __('Sum of In(Cum. TTF)') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{
            dropdown: false,
            hover: false,
            openModalChart: false,
            openModalChart1: false,
            openModalBothChart: false,
            }"
            class="relative flex" x-cloak>
            <div>
                <button
                    @if($time == 0 ||$increment == 0 || $tableRows == 0) disabled @endif
                    @mouseleave="hover=false"
                    @mouseover="hover = {{ $time == 0 ||$increment == 0 || $tableRows == 0 ? false : true }} "
                    @click="dropdown = ! dropdown"
                    @click.outside="dropdown = false"
                    class="border border-blue-600 bg-white rounded-xl w-40 flex gap-2 justify-center items-center px-3 py-2 font-semibold text-sm text-blue-600 hover:border-gray-200 hover:bg-blue-600 hover:text-white disabled:border-gray-500 disabled:bg-gray-200 disabled:text-gray-500">
                    {{ __('Print Chart') }}
                    <div x-show="hover" >
                        <x-icons.download-arrow class="!w-4 !h-4"/>
                    </div>
                </button>
            </div>

            <div x-show="dropdown" class="absolute top-10 flex flex-col text-sm text-left bg-white rounded-lg border border-blue-600 divide-y divide-blue-600">
                <button @click="openModalBothChart = ! openModalBothChart"
                    class="rounded-t-md text-left px-3 py-2 text-blue-600 bg-white hover:bg-blue-600 hover:text-white">
                    {{ __('Print Both Chart') }}
                </button>
                <button @click="openModalChart = ! openModalChart"
                    class="text-left px-3 py-2 text-blue-600 bg-white hover:bg-blue-600 hover:text-white">
                    {{ __('Print Time vs MTBF only') }}
                </button>

                <button @click="openModalChart1 = ! openModalChart1"
                    class="rounded-b-md text-left px-3 py-2 text-blue-600 bg-white hover:bg-blue-600 hover:text-white">
                    {{ __('Print Time VS Predicted Number of Failure') }}
                </button>
            </div>

            <div x-show="openModalChart" class="fixed z-50 overflow-x-hidden overflow-y-scroll inset-0 h-screen bg-gray-600 bg-opacity-50 justify-center print:justify-start print:pt-10 items-center flex flex-col gap-2">
                <div @click.outside="openModalChart = false"
                    class="print:w-auto relative rounded-lg shadow w-[50%] flex flex-col gap-4 bg-white p-4 overflow-y-auto">
                    <div class="hidden print:block">
                        <span>{{ __('NHPP Single System') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <div class="font-inter font-medium text-lg text-gray-900">
                            {{ __('Time VS MTBF') }}
                        </div>
                        <button @click="openModalChart = false" class="print:hidden p-1 rounded-lg hover:bg-gray-300 hover:text-red-500">
                            <x-icons.xmark class="w-4 h-4 cursor-pointer "/>
                        </button>
                    </div>
                    <div class="pt-4 print:w-full border-t print:border-t-0">
                        <canvas wire:ignore id="timeVsMtbfChart1"></canvas>
                    </div>
                    <div class="flex justify-end p-3 border-t print:border-t-0">
                        <button
                            onclick="window.print()"
                            class="print:hidden w-auto px-4 py-2 border border-blue-600 text-blue-600 bg-white rounded-lg cursor-pointer text-sm hover:bg-blue-600 hover:text-white hover:border-white">
                            {{ __('Print Chart') }}
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="openModalChart1" class="fixed z-50 overflow-x-hidden overflow-y-scroll inset-0 h-screen bg-gray-600 bg-opacity-50 justify-center print:justify-start print:pt-10 items-center flex flex-col gap-2">
                <div @click.outside="openModalChart1 = false" class="print:w-auto relative rounded-lg shadow w-[40%] flex flex-col gap-4 bg-white p-4 overflow-y-auto">
                    <div class="hidden print:block">
                        <span>{{ __('NHPP Single System') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <div class="font-inter font-medium text-lg text-gray-900">
                            {{ __('Time VS Predicted Number of Failure') }}
                        </div>
                        <button @click="openModalChart1 = false" class="print:hidden p-1 rounded-lg hover:bg-gray-300 hover:text-red-500">
                            <x-icons.xmark class="w-4 h-4 cursor-pointer "/>
                        </button>
                    </div>
                    <div class="pt-4 print:w-full border-t">
                        <canvas wire:ignore id="timeVsPredictedNumberOfFailureChart1"></canvas>
                    </div>
                    <div class="flex justify-end p-3 border-t">
                        <button
                            onclick="window.print()"
                            class="print:hidden w-auto px-4 py-2 border border-blue-600 text-blue-600 bg-white rounded-lg cursor-pointer text-sm hover:bg-blue-600 hover:text-white hover:border-white">
                            {{ __('Print Chart') }}
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="openModalBothChart" class="fixed z-50 overflow-x-hidden overflow-y-hidden inset-0 h-screen bg-gray-600 bg-opacity-50 justify-center print:justify-start print:pt-10 items-center flex flex-col gap-2">
                <div @click.outside="openModalBothChart = false" class="print:w-auto relative overflow-y-scroll rounded-lg shadow w-[50%] min-h-[50vh] max-h-[90vh] flex flex-col gap-4 bg-white p-4">
                    <div class="hidden print:block">
                        <span>{{ __('NHPP Single System') }}</span>
                    </div>
                    <div class="print:hidden flex justify-between">
                        <div class="font-inter font-medium text-lg text-gray-900">
                            {{ __('Print Both Charts') }}
                        </div>
                        <button @click="openModalBothChart = false" class="p-1 rounded-lg hover:bg-gray-300 hover:text-red-500">
                            <x-icons.xmark class="w-4 h-4 cursor-pointer "/>
                        </button>
                    </div>
                    <div class="max-h-[80vh] min-h-[50vh] overflow-auto">
                        <div class="flex justify-between">
                            <div class="font-inter font-medium text-lg text-gray-900">
                                {{ __('Time VS MTBF') }}
                            </div>
                        </div>
                        <div class="pt-4 print:w-full border-t">
                            <canvas wire:ignore id="timeVsMtbfChart2"></canvas>
                        </div>
                        <div class="flex justify-between">
                            <div class="font-inter font-medium text-lg text-gray-900">
                                {{ __('Time VS Predicted Number of Failure') }}
                            </div>
                        </div>
                        <div class="pt-4 print:w-full border-t">
                            <canvas wire:ignore id="timeVsPredictedNumberOfFailureChart2"></canvas>
                        </div>
                    </div>
                    <div class="flex justify-end p-3 border-t">
                        <button
                            onclick="window.print()"
                            class="print:hidden w-auto px-4 py-2 border border-blue-600 text-blue-600 bg-white rounded-lg cursor-pointer text-sm hover:bg-blue-600 hover:text-white hover:border-white">
                            {{ __('Print Chart') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 w-full">
            {{-- <div class="flex flex-col gap-2 w-1/2 p-4 shadow-md rounded-bl-xl bg-white"> --}}
            <div id="printTimeVsMtbfChart" class="w-1/2 p-4 shadow-md rounded-bl-xl divide-y-2 flex flex-col gap-4 bg-white">
                <div class="font-inter font-medium text-lg text-gray-900">
                    {{ __('Time VS MTBF') }}
                </div>
                <div class="w-full pt-4">
                    <canvas wire:ignore id="timeVsMtbfChart"></canvas>
                </div>
            </div>
            {{-- </div> --}}
            <div id="printTimeVsPredictedNumberOfFailureChart" class="w-1/2 p-4 shadow-md rounded-br-xl divide-y-2 flex flex-col gap-4 bg-white">
                <div class="font-inter font-medium text-lg text-gray-900">
                    {{ __('Time VS Predicted Number of Failure') }}
                </div>
                <div class="w-full pt-4">
                    <canvas wire:ignore id="timeVsPredictedNumberOfFailureChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

        <script>
            window.addEventListener("DOMContentLoaded", function () {
                Livewire.emit("updateChartMtbf1");
                Livewire.emit("updateDataPredictedNumberOfFailure1");
            });
        </script>

        <script type="text/javascript">
                var ctx = document.getElementById("timeVsMtbfChart");

                var timeVsMtbfChart = new Chart(ctx, {
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
                    timeVsMtbfChart.data.labels = labels;
                    timeVsMtbfChart.data.datasets[0].data = data;
                    timeVsMtbfChart.data.datasets[1].data = data1;
                    timeVsMtbfChart.update();
                })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsMtbfChart1");

            var timeVsMtbfChart1 = new Chart(ctx, {
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
                timeVsMtbfChart1.data.labels = labels;
                timeVsMtbfChart1.data.datasets[0].data = data;
                timeVsMtbfChart1.data.datasets[1].data = data1;
                timeVsMtbfChart1.update();
            })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsMtbfChart2");

            var timeVsMtbfChart2 = new Chart(ctx, {
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
                timeVsMtbfChart2.data.labels = labels;
                timeVsMtbfChart2.data.datasets[0].data = data;
                timeVsMtbfChart2.data.datasets[1].data = data1;
                timeVsMtbfChart2.update();
            })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsPredictedNumberOfFailureChart");

            var timeVsPredictedNumberOfFailureChart = new Chart(ctx, {
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
                timeVsPredictedNumberOfFailureChart.data.labels = times;
                timeVsPredictedNumberOfFailureChart.data.datasets[0].data = data;
                timeVsPredictedNumberOfFailureChart.update();
            })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsPredictedNumberOfFailureChart1");

            var timeVsPredictedNumberOfFailureChart1 = new Chart(ctx, {
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
                timeVsPredictedNumberOfFailureChart1.data.labels = times;
                timeVsPredictedNumberOfFailureChart1.data.datasets[0].data = data;
                timeVsPredictedNumberOfFailureChart1.update();
            })
        </script>

        <script type="text/javascript">
            var ctx = document.getElementById("timeVsPredictedNumberOfFailureChart2");

            var timeVsPredictedNumberOfFailureChart2 = new Chart(ctx, {
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
                timeVsPredictedNumberOfFailureChart2.data.labels = times;
                timeVsPredictedNumberOfFailureChart2.data.datasets[0].data = data;
                timeVsPredictedNumberOfFailureChart2.update();
            })
        </script>

        <script>
            window.addEventListener('refresh-page', event => {
            window.location.reload(false);
            })
        </script>
    @endpush
</div>
