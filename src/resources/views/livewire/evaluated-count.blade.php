<div class="absolute px-2 font-black text-center text-gray-300 rounded-xl left-5 bottom-10
    {{ $allEvaluated ? 'bg-amber-600' : 'bg-gray-700' }}">
    <p class="text-xs">Evaluated</p>
    <p class="text-sm">{{ $evaluatedCount }} / {{ $playerCount }}</p>
</div>