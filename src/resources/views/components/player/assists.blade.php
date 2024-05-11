@if($assists)
    <div class="flex justify-center space-x-[-10px]">
        @foreach(range(1, $assists) as $num)
            <div class="z-[{{ $loop->iteration }}] bg-white rounded-full p-0.5">
                <x-svg.assist-image :class="$attributes['class']" />
            </div>
        @endforeach
    </div>
@endif