<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\TagRepository;

class TagSharedController extends Controller
{
    /**
     * The Tag Repository.
     *
     * @var TagRepository
     */
    protected $tags;

    /**
     * The name of the presenter to use for summary representation.
     *
     * @var string
     */
    protected $summaryPresenter = 'App\Presenters\TagSharedPresenter';

    /**
     * Instantiate a new TagSharedController.
     *
     * @param TagRepository $tagRepo
     *
     * @return void
     */
    public function __construct(TagRepository $tagRepo)
    {
        $this->tags = $tagRepo;
    }

    public function index($tagId)
    {
        $tag = $this->tags->with(['sharedWith'])->skipPresenter()->find($tagId);
        if (!$tag->public) {
            $this->authorize('show', $tag);
        }
        return app($this->summaryPresenter)->present($tag->sharedWith()->paginate());
    }

    public function store(Request $request, $tagId)
    {
        $tag = $this->tags->skipPresenter()->find($tagId);

        if ($tag->public < 3) {
            $this->authorize('update', $tag);
        }

        return $tag->share((int) $request->json('data')['user_id'], $request->json('data')['access_level']);
    }

    public function destroy($tagId, $userId)
    {
        $tag = $this->tags->skipPresenter()->find($tagId);
        if ($tag->public < 3) {
            $this->authorize('update', $tag); // note that unsharing a tag is an update of that tag, not destroying it
        }
        $tag->sharedWith()->detach($userId);
    }
}
