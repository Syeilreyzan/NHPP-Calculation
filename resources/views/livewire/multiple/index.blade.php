<div>
    <div class="w-full p-6 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                <h1 class="text-2xl">{{ __('NHPP Multiple System') }}</h1>
            </div>
        </div>
        <div x-data="{ tab: @entangle('tab') }" x-cloak class="relative">
            <div wire:ignore.self x-data="floatingButton()" x-init="init()" :style="'top: ' + top + 'px;'"
                class="fixed gap-2 ml-2 p-2 bg-white z-30">
                <button class="inline-block rounded-t-xl py-2 px-4 font-semibold border border-b-0 text-white hover:text-white hover:border hover:border-b-0"
                    :class="{ 'bg-blue-600 text-white border-blue-600 hover:text-white': tab == 'tab1'}"
                    @click.prevent="$wire.toggleTabMultiple('tab1')">
                    {{ __('System') }}
                </button>
                <button class="inline-block rounded-t-xl py-2 px-4 font-semibold border border-b-0 text-white hover:text-white hover:border hover:border-b-0"
                    :class="{ 'bg-blue-600 text-white border-blue-600 hover:text-white': tab == 'tab2'}"
                    @click.prevent="$wire.toggleTabMultiple('tab2')">
                    {{ __('Result') }}
                </button>
            </div>
            <div class="bg-white px-4 py-4 rounded-xl pt-4">
                <div x-show="tab == 'tab1'" class="">
                    <livewire:multiple.system />
                </div>
                <div x-show="tab == 'tab2'">
                    <livewire:multiple.result />
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        function floatingButton() {
            return {
                top: 145,

                init() {
                    window.addEventListener('scroll', () => {
                        this.top = Math.max(0, 145 - window.scrollY);
                    });
                }
            };
        }
    </script>
@endpush
</div>
