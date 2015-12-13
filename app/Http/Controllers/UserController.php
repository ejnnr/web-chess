<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepository;

use Illuminate\Http\Request;

class UserController extends Controller {

	/**
	 * The User Repository
	 *
	 * @var UserRepository
	 */
	protected $users;

	/**
	 * Instantiate a new UserController
	 *
	 * @param UserRepository $userRepo
	 * @return void
	 */
	public function __construct(UserRepository $userRepo)
	{
		$this->users = $userRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->users->all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		return $this->users->create($request->json('data'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return $this->users->find($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		return $this->users->update($request->json('data'), $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($this->users->delete($id)) {
			return response('', 204);
		}

		return response('', 500);
	}

}
