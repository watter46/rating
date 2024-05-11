@if($goals)
    <div class="flex justify-center space-x-[-10px]">
        @foreach(range(1, $goals) as $num)
            <div class="z-[{{ $loop->iteration }}] bg-white rounded-full p-0.5">
                <x-svg.goal-image :class="$attributes['class']" />
            </div>
        @endforeach
    </div>
@endif