<div class="flex flex-col divide-y">
    <div class="flex items-center justify-between p-5">
        <span class="text-lg font-semibold">{{ __('Table') }}</span>
        <div wire:click="closingModal" class=" cursor-pointer">
            <x-icons.xmark class="cursor-pointer"/>
        </div>
    </div>
    <div class="w-full flex flex-col gap-6 rounded-lg p-5 max-h-[70vh] overflow-y-auto">
        <table class="w-full rounded-xl border-separate text-center">
            <thead class="bg-green-table-header">
                <tr>
                    <th class="border rounded-tl-xl py-2">Time</th>
                    <th class="border py-2">{{ __('Instantenous MTBF') }}</th>
                    <th class="border py-2">{{ __('Cumulative MTBF') }}</th>
                    <th class="border rounded-tr-xl py-2">{{ __('Predicted number of failure') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($times as $index => $time)
                    @php
                        $evenNumber = $index % 2 === 0;
                    @endphp
                    <tr>
                        <td class="border {{ $evenNumber ? 'bg-gray-300' : 'bg-gray-200' }} {{ $loop->last ? 'rounded-bl-xl' : '' }} font-semibold">{{ $time }}</td>
                        <td class="border {{ $evenNumber ? 'bg-gray-300' : 'bg-gray-200' }} font-medium">{{ $instantenousMtbfs[$index] }}</td>
                        <td class="border {{ $evenNumber ? 'bg-gray-300' : 'bg-gray-200' }} font-medium">{{ $cumulativeMtbfs[$index] }}</td>
                        <td class="border {{ $evenNumber ? 'bg-gray-300' : 'bg-gray-200' }}  {{ $loop->last ? 'rounded-br-xl' : '' }} font-medium">{{ $predictedNumberFailures[$index] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
