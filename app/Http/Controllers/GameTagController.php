<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\TagRepository;
use App\Repositories\GameRepository;

class GameTagController extends Controller
{
	/**
	 * The Tag Repository
	 *
	 * @var TagRepository
	 */
	protected $tags;

	/**
	 * The Game Repository
	 *
	 * @var GameRepository
	 */
	protected $games;

	/**
	 * The name of the presenter to use for summary representation
	 *
	 * @var string
	 */
	protected $summaryPresenter = 'App\Presenters\GameTagPresenter';

	/**
	 * Instantiate a new TagController
	 *
	 * @param TagRepository $tagRepo
	 * @param GameRepository $gameRepo
	 * @return void
	 */
	public function __construct(TagRepository $tagRepo, GameRepository $gameRepo)
	{
		$this->tags = $tagRepo;
		$this->games = $gameRepo;
	}

	public function index($gameId)
	{
		$game = $this->games->with(['tags'])->skipPresenter()->find($gameId);
		if (!Auth::check()) {
			if (!$game->public) {
				abort(403);
			}
		} else {
			$this->authorize('show', $game);
		}
		return app($this->summaryPresenter)->present($game->tags);
	}

	public function store(Request $request, $gameId)
	{
		$game = $this->games->skipPresenter()->find($gameId);
		if ($game->public < 3) {
			$this->authorize('update', $game);
		}
		if (is_null($game->tags()->find((int)$request->json('data')))) {
			return response(422, 'A game cannot be tagged with the same tag twice.');
		}
		return $game->tags()->attach((int)$request->json('data'));
	}

	public function destroy($gameId, $tagId)
	{
		$game = $this->games->skipPresenter()->find($gameId);
		if ($game->public < 3) {
			$this->authorize('update', $game); // note that removing a tag from a game is an update of that game, not destroying it
		}
		$game->tags()->detach($tagId);
	}
}
