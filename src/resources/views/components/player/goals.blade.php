@if($goals)
    <div class="flex">
        @if($goals <= 3)
            @foreach(range(1, $goals) as $num)
                <div class="bg-white rounded-full" style="padding: 0.5px;">
                    <x-svg.goal-image />
                </div>
            @endforeach
        @else
            <div class="flex items-center justify-center px-0.5 bg-white rounded-full">
                <p class="pr-1 font-black text-sky-700">{{ $goals }}</p>
                <x-svg.goal-image />
            </div>
        @endif
    </div>
@endif