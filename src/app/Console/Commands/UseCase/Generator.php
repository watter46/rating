<?php declare(strict_types=1);

namespace App\Console\Commands\UseCase;

use App\Console\Commands\UseCase\ClassGenerator;


final readonly class Generator extends ClassGenerator
{
    public const ROOT_DIR_NAME = 'UseCases';
    public const COMMAND_NAME  = 'UseCase';
    public const TEMPLATE_PATH = 'Console/Commands/UseCase/Template.txt';

    public const SUB_COMMAND_NAME = 'Command';

    private function __construct(private string $arg)
    {
        Parent::__construct(
            arg           : $arg,
            rootDirName   : self::ROOT_DIR_NAME,
            commandName   : self::COMMAND_NAME,
            template      : self::TEMPLATE_PATH,
            subCommandName: self::SUB_COMMAND_NAME
        );
    }
    
    public static function setup(string $arg)
    {
        return new self($arg);
    }
}