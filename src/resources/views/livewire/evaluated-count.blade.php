<div class="absolute px-2 font-black text-center text-gray-300 rounded-xl right-5 bottom-10
    {{ $allEvaluated ? 'bg-amber-600' : 'bg-sky-900' }}">
    <p class="text-xs">Evaluated</p>
    <p class="text-sm">{{ $evaluatedCount }} / {{ $playerCount }}</p>
</div>