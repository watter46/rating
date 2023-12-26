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
<body class="h-screen p-2 bg-slate-900">    
    <div class="flex w-full h-full">
        <div class="flex items-center justify-center w-full h-full">
            <div class="relative h-full">
                <x-field.field-svg />
                <div id="box" class="absolute top-0 flex items-end justify-center w-full h-full">
                    <div class="flex flex-col w-full h-full pt-10">                        
                        @foreach($lineups['startXI'] as $line => $players)
                            <div id="line-{{ $line + 1 }}" class="flex items-center h-full justify-evenly">
                                @foreach($players as $index => $player)
                                    <div class="flex justify-center w-full">
                                        <livewire:player :$player :key="$player['id']" />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center w-full h-full players">
            <x-score.score
                :fixture="$fixture"
                :teams="$teams"
                :league="$league"
                :score="$score" />
            
            <livewire:player-detail :$modelId :$lineups />
        </div>
    </div>
    
    @livewireScripts
</body>
</html>