<button class="px-5 py-1 border-2 border-gray-200 rounded-lg bg-sky-600"
    wire:loading.class="opacity-50"
    wire:loading.attr="disabled"
    {{ $attributes }}>
    <p class="font-black text-white" wire:loading.class.add="hidden">{{ $slot }}</p>
    <p class="hidden font-black text-white" wire:loading.class.remove="hidden">Saving...</p>
</button>