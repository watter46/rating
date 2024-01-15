<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\FixturesResource;
use App\UseCases\Fixture\FetchFixtureListUseCase;
use App\UseCases\Fixture\RegisterFixtureListUseCase;
use Exception;
use Illuminate\Http\Request;

class AdminFixtureController
{
    public function index(FetchFixtureListUseCase $fetchFixtureList, FixturesResource $resource)
    {
        try {
            $fixtures = $fetchFixtureList->execute();
            
            return view('admin.auth.dashboard', ['fixtures' => $resource->format($fixtures)]);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function update(Request $request, RegisterFixtureListUseCase $registerFixtureList)
    {
        try {
            if ($request->input('refreshKey') !== config('refreshKey.key')) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('message', [
                        'type' => 'error',
                        'message' => 'Keyが一致しません。'
                    ]);
            }

            redirect()
                ->route('admin.dashboard')
                ->with('message', [
                    'type' => 'success',
                    'message' => 'Success!'
                ]);
            dd('good');
            
            $registerFixtureList->execute();

            return redirect()->route('admin.dashboard');

        } catch (Exception $e) {

        }
    }
}