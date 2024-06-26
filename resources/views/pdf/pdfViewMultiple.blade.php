<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <title>{{ $title }}</title> --}}
    <style>
        body {
            font-family: 'Arial','sans-serif';
        }
        .container {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            row-gap: 20px;
        }
        .containerOther{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: start;
            width: 100%;
        }
        .graphTimetable{
            width: 100%;
        }
        .containerTable{
            display: grid;
            grid-template-columns: auto auto auto auto;
            gap: 10px;
        }
        .table{
            /* border: 1px solid gray; */
            width: 100%;
        }
        .table th {
            padding: 8px;
            font-size: 12px;
            text-align: center;
            background-color: #cecece;
            /* border-radius: 8px */
        }
        .table td {
            padding: 8px;
            font-size: 12px;
            text-align: center;
            background-color: #77767616;
            /* border-radius: 8px */
        }

        .table .cumulative{
            background-color: #99fa7e;
        }

        .table .focusColumn{
            background-color: #f8e467;
        }

        hr{
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid gray;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p>

    <div class="container">
        <div class="containerTable">
            @if (count($failureTimes1) > 1)
                <table class="table">
                    <tr>
                        <td colspan="4" style="font-style: bold; font-size: 16px; border-radius: 8px 8px 0px 0px">System 1</td>
                    </tr>
                    <tr>
                        <th class="rounded-tl-xl">No. of Failure</th>
                        <th class="py-4 w-2/12">Cum. Failure Time</th>
                        <th class="py-4">Time Between Failure</th>
                        <th class="py-4">ln (Cum. Failure time)</th>
                    </tr>
                    @foreach ($failureTimes1 as $key => $failureTime1)
                        @if ($loop->last && !isset($failureTime1['cumulative_failure_time']))
                            @continue
                        @endif
                        <tr>
                            <td style="border-radius: 0px 0px 0px 8px">{{ $key + 1 . '.' }}</td>
                            <td class="cumulative">{{ $failureTime1['cumulative_failure_time'] }}</td>
                            <td>{{ $failureTime1['time_between_failures'] }}</td>
                            <td style="border-radius: 0px 0px 8px 0px">{{ number_format($failureTime1['natural_log_cum_failure_time'],4) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
            @if (count($failureTimes2) > 1)
                <table class="table">
                    <tr>
                        <td colspan="4" style="font-style: bold; font-size: 16px; border-radius: 8px 8px 0px 0px">System 2</td>
                    </tr>
                    <tr>
                        <th class="rounded-tl-xl">No. of Failure</th>
                        <th class="py-4 w-2/12">Cum. Failure Time</th>
                        <th class="py-4">Time Between Failure</th>
                        <th class="py-4">ln (Cum. Failure time)</th>
                    </tr>
                    @foreach ($failureTimes2 as $key => $failureTime2)
                        @if ($loop->last && !isset($failureTime2['cumulative_failure_time']))
                            @continue
                        @endif
                        <tr>
                            <td style="border-radius: 0px 0px 0px 8px">{{ $key + 1 . '.' }}</td>
                            <td class="cumulative">{{ $failureTime2['cumulative_failure_time'] }}</td>
                            <td>{{ $failureTime2['time_between_failures'] }}</td>
                            <td style="border-radius: 0px 0px 8px 0px">{{ number_format($failureTime2['natural_log_cum_failure_time'],4) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
            @if (count($failureTimes3) > 1)
                <table class="table">
                    <tr>
                        <td colspan="4" style="font-style: bold; font-size: 16px; border-radius: 8px 8px 0px 0px">System 3</td>
                    </tr>
                    <tr>
                        <th class="rounded-tl-xl">No. of Failure</th>
                        <th class="py-4 w-2/12">Cum. Failure Time</th>
                        <th class="py-4">Time Between Failure</th>
                        <th class="py-4">ln (Cum. Failure time)</th>
                    </tr>
                    @foreach ($failureTimes3 as $key => $failureTime3)
                        @if ($loop->last && !isset($failureTime3['cumulative_failure_time']))
                            @continue
                        @endif
                        <tr>
                            <td style="border-radius: 0px 0px 0px 8px">{{ $key + 1 . '.' }}</td>
                            <td class="cumulative">{{ $failureTime3['cumulative_failure_time'] }}</td>
                            <td>{{ $failureTime3['time_between_failures'] }}</td>
                            <td style="border-radius: 0px 0px 8px 0px">{{ number_format($failureTime3['natural_log_cum_failure_time'],4) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
        <hr>
        <div class="containerOther">
            <table class="table">
                <tr>
                    <th>Number of System</th>
                    <th>Total No of failure for all system</th>
                    <th>End of Observation Time</th>
                    <th>Sum of In(Cum. TTF)</th>
                    <th>Slope(MLE)</th>
                    <th>Lambda</th>
                    <th>Eta</th>
                </tr>
                <tr>
                    <td>{{ $numberOfSystem }}</td>
                    <td>{{ $totalNumberOfFailureAllSystem }}</td>
                    <td class="focusColumn">{{ $endObservationTime }}</td>
                    <td>{{ number_format($total,2) }}</td>
                    <td>{{ number_format($slope,6) }}</td>
                    <td>{{ number_format($lambda,6) }}</td>
                    <td>{{ number_format($eta,6) }}</td>
                </tr>
                </tr>
            </table>
        </div>
        <hr>
        <div class="graphTimetable">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Instantaneous MTBF</th>
                        <th>Cumulative MTBF</th>
                        <th>Predicted Number of Failures</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($times as $index => $time)
                        <tr>
                            <td>{{ $time }}</td>
                            <td>{{ number_format($instantenousMtbfs[$index], 4) }}</td>
                            <td>{{ number_format($cumulativeMtbfs[$index], 4) }}</td>
                            <td>{{ number_format($predictedNumberFailures[$index], 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
