<?php

namespace App\Http\Controllers;

use Auth;
use App\Repositories\GameRepository;
use App\Criteria\VisibleGameCriterion;
use App\Criteria\GameHasTagRequestCriterion;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Entities\Game;

class GameController extends Controller
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
    protected $summaryPresenter = 'App\Presenters\GameSummaryPresenter';

    /**
     * Instantiate a new GameController.
     *
     * @param GameRepository $gameRepo
     *
     * @return void
     */
    public function __construct(GameRepository $gameRepo)
    {
        $this->games = $gameRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VisibleGameCriterion $visibleCriterion, GameHasTagRequestCriterion $hasTagCriterion)
    {
        $this->games->setPresenter($this->summaryPresenter);
        $this->games->pushCriteria($visibleCriterion);
        $this->games->pushCriteria($hasTagCriterion);

        return $this->games->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGameRequest $request)
    {
        $this->authorize('store', Game::class);

        return $this->games->create(array_merge($request->json('data'), ['owner_id' => Auth::user()->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $game = $this->games->skipPresenter()->find($id);
        if ($game->public < 1) {
            $this->authorize($game);
        }

        return $game->presenter();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGameRequest $request, $id)
    {
        $game = $this->games->skipPresenter()->find($id);
        if ($game->public < 3) {
            $this->authorize($game);
        }
        $data = $request->json('data');
        unset($data['owner_id']);

        return $this->games->skipPresenter(false)->update($data, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game = $this->games->skipPresenter()->find($id);
        if ($game->public < 3) {
            $this->authorize($game);
        }

        if ($this->games->delete($id)) {
            return response('', 204);
        }

        return response('', 500);
    }
}
