<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\DatabaseRepository;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDatabaseRequest;
use App\Http\Requests\UpdateDatabaseRequest;

class DatabaseController extends Controller {

	/**
	 * The Database Repository
	 *
	 * @var DatabaseRepository
	 */
	protected $databases;

	/**
	 * Instantiate a new DatabaseController
	 *
	 * @param DatabaseRepository $databaseRepo
	 * @return void
	 */
	public function __construct(DatabaseRepository $databaseRepo)
	{
		$this->databases = $databaseRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->databases->all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(StoreDatabaseRequest $request)
	{
		return $this->databases->create($request->json('data'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return $this->databases->find($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(UpdateDatabaseRequest $request, $id)
	{
		return $this->databases->update($request->json('data'), $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($this->databases->delete($id)) {
			return response('', 204);
		}

		return response('', 500);
	}

}
