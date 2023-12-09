<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EvaluatePlayerRequest;
use App\Http\Resources\PlayerResource;
use Exception;
use Illuminate\Http\Request;


final class PlayerController extends Controller
{
    public function index(EvaluatePlayerRequest $request)
    {
        // try {

        //     return new PlayerResource()
        // } catch (Exception $e) {

        // }
    }
}