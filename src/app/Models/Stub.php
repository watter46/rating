<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Stub extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'fixture'
    ];

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($date) => Carbon::parse($date)
        );
    }

    public function scopeNext($query)
    {
        $query
            ->whereDate('date', '>=', now('UTC'))
            ->orderBy('date')
            ->whereNull('fixture');
    }
}
