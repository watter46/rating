<?php declare(strict_types=1);

namespace App\Console\Commands\UseCase;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


readonly class ClassGenerator
{
    private const EXTENSION = '.php';
    
    public function __construct(
        private string  $arg,
        private string  $rootDirName,
        private string  $commandName,
        private string  $template,
        private ?string $subCommandName = null
    ) {}

    /**
     * Generate the use case class.
     *
     * @return void
     */
    public function execute(): void
    {
        $this->dirExistsOrMake();
        
        $template = File::get(app_path($this->template));

        $contents = Str::of($template)
            ->replace('{$nameSpace}', $this->nameSpace())
            ->replace('{$className}', $this->className())
            ->replace('{$subCommandClassName}', $this->subCommandClassName())
            ->value();
        
        File::put($this->filePath(), $contents);
    }
    
    /**
     * ファイルが存在するか判定する
     *
     * @return bool
     */
    public function fileExists(): bool
    {
        return File::exists($this->filePath());
    }
    
    /**
     * ディレクトリが存在しなければ作成する
     *
     * @return void
     */
    private function dirExistsOrMake(): void
    {
        $dirPath = $this->dirPath();

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath, 0755, true, true);
        }
    }

    private function className(): string
    {
        return Str::studly($this->fileName());
    }

    private function nameSpace(): string
    {
        return Str::of($this->dirName())
                ->replace('/', '\\')
                ->value();
    }
    
    private function dirName(): string
    {
        return Str::beforeLast($this->arg, '/');
    }

    private function dirPath(): string
    {
        return app_path($this->rootDirName)
                 . '/'
                 . $this->dirName();
    }

    private function fileName(): string
    {
        return Str::afterLast($this->arg, '/') . $this->commandName;
    }

    private function filePath(): string
    {
        return app_path($this->rootDirName)
                . '/'
                . $this->dirName()
                . '/'
                . $this->fileName()
                . self::EXTENSION;
    }

    private function subCommandClassName(): string
    {
        $subCommand = Str::afterLast($this->arg, '/');
        
        return Str::studly($subCommand) . $this->subCommandName;
    }
}