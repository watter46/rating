<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use App\UseCases\Admin\Fixture\ValidatorInterface;


interface DataInterface
{
    public function validated(): ValidatorInterface;
}