<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;


class AdminPlayerController extends Controller
{
    public function index()
    {
        try {
            return view('admin.auth.players');

        } catch (Exception $e) {
            dd($e);
        }
    }
}
