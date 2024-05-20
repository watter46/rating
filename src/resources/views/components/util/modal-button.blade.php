<div x-data="{
        isOpen: false,
        open() {
            this.isOpen = true;
            this.disabledScroll();
        },
        close() {
            this.isOpen = false;
            this.enableScroll();
        },
        enableScroll() {
            document.body.style.overflow = 'auto';
        },
        disabledScroll() {
            document.body.style.overflow = 'hidden';
        }
    }"
    x-cloak
    @close.window="close">

    <!-- Icon -->
    <div class="flex flex-col justify-center">
        <!-- Clickable Icon -->
        <div class="grid place-items-center" @click="open, $dispatch(`modal-opened-${@js($dispatchName)}`)">
            {{ $icon }} 
        </div>
        
        <!-- NonClickable Icon -->
        <div class="grid place-items-center">
            {{ $disabledIcon }} 
        </div>
    </div>

    <!-- Component -->
    <div x-show="isOpen"
        class="fixed top-0 left-0 w-screen h-full z-[99] p-2 overflow-y-auto grid"
        style="background: rgba(31, 41, 55, 1);"
        @click.outside="close">
        <div {{ $attributes->merge(['class' => 'rounded-lg justify-self-center self-center flex flex-col']) }}>
            <!-- CloseButton -->
            <div class="relative flex justify-end w-full">
                <div class="rounded-full cursor-pointer hover:bg-gray-600"
                    @click="close">
                    <x-svg.cross-image class="w-10 h-10 fill-gray-400" />
                </div>
            </div>
            
            <!-- Component -->
            <div class="flex-1">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>