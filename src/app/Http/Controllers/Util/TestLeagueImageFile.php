<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


class TestLeagueImageFile
{
    private const DIR_PATH = 'leagues';
    private const BACKUP_DIR = 'tests/leagues';
    private const LEAGUE_ID = 39;

    public function __construct()
    {
        if (!File::exists(self::BACKUP_DIR)) {
            File::makeDirectory(public_path(self::BACKUP_DIR), 0777, true, true);
        }
    }
    
    public function get()
    {
        return File::get($this->generateBackupPath(self::LEAGUE_ID));
    }

    private function generateBackupPath(): string
    {
        return public_path(self::BACKUP_DIR.'/'.Season::current().'_'.self::LEAGUE_ID);
    }

    private function generatePath()
    {
        return public_path(self::DIR_PATH.'/'.Season::current().'_'.self::LEAGUE_ID);
    }

    public function toBackup()
    {
        File::move($this->generatePath(self::LEAGUE_ID), $this->generateBackupPath(self::LEAGUE_ID));
    }

    public function deleteBackUp()
    {
        if (!File::exists($this->generatePath())) {
            dd($this->generatePath().': file has not been moved yet.');
        }

        File::delete($this->generateBackupPath());
    }
}