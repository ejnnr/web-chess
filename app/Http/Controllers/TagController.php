<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\TagRepository;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;

class TagController extends Controller {

	/**
	 * The Tag Repository
	 *
	 * @var TagRepository
	 */
	protected $tags;

	/**
	 * Instantiate a new TagController
	 *
	 * @param TagRepository $tagRepo
	 * @return void
	 */
	public function __construct(TagRepository $tagRepo)
	{
		$this->tags = $tagRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->tags->all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(StoreTagRequest $request)
	{
		return $this->tags->create($request->json('data'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return $this->tags->find($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(UpdateTagRequest $request, $id)
	{
		return $this->tags->update($request->json('data'), $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($this->tags->delete($id)) {
			return response('', 204);
		}

		return response('', 500);
	}

}
