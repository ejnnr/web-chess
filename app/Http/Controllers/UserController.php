<?php namespace App\Http\Controllers;

use Gate;
use App\Repositories\UserRepository;
use App\Entities\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * The User Repository.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * The name of the presenter to use for summary representation.
     *
     * @var string
     */
    protected $summaryPresenter = 'App\Presenters\UserSummaryPresenter';

    /**
     * Instantiate a new UserController.
     *
     * @param UserRepository $userRepo
     *
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
        $this->users->setPresenter($this->summaryPresenter);

        return $this->users->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreUserRequest $request)
    {
        return response($this->users->create($request->json('data')), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->users->skipPresenter()->find($id);
        if (Gate::allows('show', $user)) {
            return $user->presenter();
        } else {
            return $user->setPresenter(app($this->summaryPresenter))->presenter();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $this->authorize($this->users->skipPresenter()->find($id));
        $this->users->skipPresenter(false);

        return response($this->users->update($request->json('data'), $id), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize($this->users->skipPresenter()->find($id));
        if ($this->users->delete($id)) {
            return response('', 204);
        }

        return response('', 500);
    }
}
