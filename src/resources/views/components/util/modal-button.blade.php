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
        <div class="flex justify-center" @click="open">
            {{ $img }}
        </div>
        
        <p class="text-xs font-black text-center text-gray-400 md:text-lg lg:text-base">
            {{ $name }}
        </p>
    </div>

    <!-- Modal -->
    <div x-show="isOpen"
        class="fixed top-0 left-0 w-full h-full z-[99] overflow-y-auto"
        style="background: rgba(31, 41, 55, 1);"
        @click.outside="close">
        <!-- CloseButton -->
        <div class="relative top-0 flex justify-end w-full">
            <div class="rounded-full cursor-pointer hover:bg-gray-600"
                @click="close">
                <x-svg.cross-image class="w-14 h-14 fill-gray-400" />
            </div>
        </div>
        
        <!-- Component -->
        <div class="flex items-center justify-center">
            {{ $slot }}
        </div>
    </div>
</div>