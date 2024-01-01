<div
    x-data="{
        messages: [],
        remove(message) {
            this.messages.splice(this.messages.indexOf(message), 1)
        },
        notify(message) {
            this.messages.push(message);
            setTimeout(() => { this.remove(message) }, 2500);
        }
    }"
    @notify.window="notify(event.detail.message)"
    class="fixed inset-0 z-50 flex flex-col items-end justify-center px-4 py-6 space-y-4 pointer-events-none sm:p-6 sm:justify-start">
    <template x-for="(message, messageIndex) in messages" :key="messageIndex" hidden>
        <div
            class="flex items-center w-full max-w-sm p-4 overflow-hidden bg-gray-800 border-4 border-gray-700 rounded-lg shadow-lg pointer-events-auto fadeInRight">
            <div class="flex-shrink-0">
                <svg class="w-7 h-7"
                    fill="none" viewBox="0 0 24 24" stroke="#5CB400">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <div class="flex-1 ml-3">
                <p x-text="message" class="text-lg font-black leading-5 text-gray-300"></p>
            </div>

            <button class="inline-flex text-gray-400 transition duration-150 ease-in-out focus:outline-none focus:text-gray-500"
            @click="remove(message)" >
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    @vite('resources/css/message.css')
</div>