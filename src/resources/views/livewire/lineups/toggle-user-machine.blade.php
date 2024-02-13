<div class="flex flex-col items-center justify-center md:gap-y-3">
    <div class="flex items-center gap-x-1">
        <x-svg.machine-image class="w-4 h-4 md:w-6 md:h-6" />
        <p class="text-white md:text-2xl">/</p>
        <x-svg.user-image class="w-4 h-4 md:w-6 md:h-6" />
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" value="" class="sr-only peer" wire:model.live="isUser">
        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-0 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-700 md:scale-[1.6]">
        </div>
    </label>
</div>