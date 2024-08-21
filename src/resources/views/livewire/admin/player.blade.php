<div class="flex items-center border-b border-gray-500 gap-x-5">
    <x-util.modal-button>
        <x-slot:icon>
            <x-player.player-image
                class="cursor-pointer w-14 h-14"
                :img="$playerInfo->img" />
        </x-slot:icon>

        <x-slot:disabled-icon></x-slot:disabled-icon>

        <div class="absolute top-0 left-0 z-10 flex items-center justify-center w-full h-full"
            style="background: rgba(0, 0, 0, 0.8);">
            <div class="flex justify-center w-3/6 p-5 bg-gray-500 rounded-lg h-3/6">
                <div class="relative flex justify-center w-full">                                
                    <div class="flex flex-col justify-center w-1/2 gap-y-3">
                        <p class="text-4xl font-black text-center text-gray-200">UpdatePlayerImage</p>
                        <input type="password" class="rounded-lg" wire:model="refreshKey">
                    </div>
                    
                    <div class="absolute bottom-0 right-0 flex justify-end p-3 gap-x-5">
                        <button type="button" @click="isOpen = false" class="px-5 py-1 bg-gray-600 border-2 border-gray-200 rounded-lg">
                            <p class="font-black text-white">Cancel</p>
                        </button>

                        <x-admin.register-button wire:click="updateImage">
                            Update
                        </x-admin.register-button>
                    </div>
                </div>
            </div>
        </div>
    </x-util.modal-button>
    
    <p class="w-5 text-xl text-gray-200">{{ $playerInfo->number ?? '-' }}</p>
    <p class="text-2xl text-gray-200">{{ $playerInfo->name }}</p>
</div>