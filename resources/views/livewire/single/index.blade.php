<div>
    {{-- Be like water. --}}
    <div class="w-full p-6 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <div class=" flex flex-col">
                <h1 class="text-2xl">{{ __('NHPP Single System') }}</h1>
            </div>
        </div>

        <div x-data="{ tab: @entangle('tab') }" x-cloak class="relative">
            <div wire:ignore.self x-data="floatingButton()" x-init="init()" :style="'top: ' + top + 'px;'"
                class="fixed gap-2 ml-2 p-2 bg-white z-30">
                <button class="inline-block rounded-t py-2 px-4 font-semibold hover:text-blue-800"
                    :class="{ 'bg-white text-blue-700 border-l border-t border-r': tab == 'tab1'}"
                    @click.prevent="$wire.toggleTab('tab1')">
                    {{ __('System') }}
                </button>
                <button class="inline-block rounded-t py-2 px-4 font-semibold hover:text-blue-800"
                    :class="{ 'bg-white text-blue-700 border-l border-t border-r': tab == 'tab2'}"
                    @click.prevent="$wire.toggleTab('tab2')">
                    {{ __('Result') }}
                </button>
            </div>
            <div class="bg-white px-4 py-4 rounded-xl pt-4">
                <div x-show="tab == 'tab1'" class="">
                    <livewire:index />
                </div>
                <div x-show="tab == 'tab2'">
                    <livewire:single.result />
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
