<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\GameRepository;

class GameSharedController extends Controller
{
    /**
     * The Game Repository.
     *
     * @var GameRepository
     */
    protected $games;

    /**
     * The name of the presenter to use for summary representation.
     *
     * @var string
     */
    protected $summaryPresenter = 'App\Presenters\GameSharedPresenter';

    /**
     * Instantiate a new GameSharedController.
     *
     * @param GameRepository $gameRepo
     *
     * @return void
     */
    public function __construct(GameRepository $gameRepo)
    {
        $this->games = $gameRepo;
    }

    public function index($gameId)
    {
        $game = $this->games->with(['sharedWith'])->skipPresenter()->find($gameId);
        if (!$game->public) {
            $this->authorize('show', $game);
        }
        return app($this->summaryPresenter)->present($game->sharedWith()->paginate());
    }

    public function store(Request $request, $gameId)
    {
        $game = $this->games->skipPresenter()->find($gameId);

        if ($game->public < 3) {
            $this->authorize('update', $game);
        }

        return $game->share((int) $request->json('data')['user_id'], $request->json('data')['access_level']);
    }
}
