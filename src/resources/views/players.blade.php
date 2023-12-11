<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/app.css', 'resources/js/field.js'])
    @livewireStyles
    
    <title>Rating</title>
</head>
<body style="background-color: #2a437c;">    
    <x-field.field-svg />

    <livewire:player-detail :$players />

    @props(['lineClass' => 'absolute flex flex-row-reverse gap-10 justify-evenly'])
    
    <div class="flex justify-center">
        @foreach($players as $line => $position)
            @if($line === 'FW')
                <div id="offense-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <livewire:player :$player :key="$position->pluck('id')->join('-')" />
                    @endforeach
                </div>
            @endif

            @if($line === 'MID')
                @if(count($position['line']) === 1)
                    <div id="mid-line" class="{{ $lineClass }}">
                        @foreach($position['line'][0] as $player)
                            <livewire:player :$player :key="$position->pluck('id')->join('-')" />
                        @endforeach
                    </div>
                @else
                    <div class="absolute flex flex-col z-index bg-violet-500">
                        @foreach($position as $index => $midPlayers)
                            <div id="mid-{{ $index + 1 }}" class="{{ $lineClass }}">
                                @foreach($midPlayers as $player)
                                    <livewire:player :$player :key="$position->pluck('id')->join('-')" />
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($line === 'DF')
                <div id="defense-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <livewire:player :$player :key="$position->pluck('id')->join('-')" />
                    @endforeach
                </div>
            @endif

            @if($line === 'GK')
                <div id="keeper-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <livewire:player :$player :key="$position->pluck('id')->join('-')" />
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>

    @livewireScripts
</body>
</html>