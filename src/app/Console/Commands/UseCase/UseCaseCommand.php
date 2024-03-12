<?php declare(strict_types=1);

namespace App\Console\Commands\UseCase;

use Illuminate\Console\Command;

use App\Console\Commands\UseCase\Generator;


final class UseCaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:uc {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make:uc {name  : DirName/FileName -> UseCases/DirName/FileNameUseCase.php}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $arg = $this->argument('name');

        $generator = Generator::setup($arg);
        
        if ($generator->fileExists()) {
            $this->error("Already Exist ($arg)");
            return;
        }

        $generator->execute();

        $this->info("UseCase Created!!");
    }
}