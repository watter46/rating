@if($goals)
    <div class="flex justify-center -space-x-2">
        @foreach(range(1, $goals) as $num)
            <div class="{{ 'z-'.$loop->iteration * 10 }} bg-white rounded-full p-0.5">
                <x-svg.goal-image />
            </div>
        @endforeach
    </div>
@endif