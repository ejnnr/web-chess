<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Repositories\GameRepository;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;

use App\Entities\Game;

class GameController extends Controller
{
	/**
	 * The Game Repository
	 *
	 * @var GameRepository
	 */
	protected $games;

	/**
	 * Instantiate a new GameController
	 *
	 * @param GameRepository $gameRepo
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
    public function index()
    {
		return $this->games->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $game = $this->games->skipPresenter()->find($id);
		$this->authorize($game);
		return $game->presenter();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGameRequest $request, $id)
    {
		$this->authorize($this->games->skipPresenter()->find($id));
		$data = $request->json('data');
		unset($data['owner_id']);
		return $this->games->update($request->json('data'), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$this->authorize($this->games->skipPresenter()->find($id));
		if ($this->games->delete($id)) {
			return response('', 204);
		}

		return response('', 500);
    }
}
