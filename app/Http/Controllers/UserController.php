<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\User;
use App\Services\User\UserService;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor
     * @param UserService $userService
     */
    public function __construct (UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * UserController login
     * @param  Request $request
     * @return Response
     */
    public function login (Request $request): Response
    {
        return $this->userService->login($request);
    }

    /**
     * UserController checkToken
     * @param  Request $request
     * @return Response
     */
    public function checkToken (Request $request): Response
    {
        return $this->userService->checkToken($request);
    }

    /**
     * UserController logout
     * @param  Request $request
     * @return Response
     */
    public function logout (Request $request): Response
    {
        return $this->userService->logout($request);
    }

    /**
     * UserController index
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->userService->index($request);
    }

    /**
     * UserController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->userService->store($request);
    }

    /**
     * UserController show
     * @param  User $user
     * @param  Request $request
     * @return Response
     */
    public function show (User $user, Request $request): Response
    {
        return $this->userService->show($user, $request);
    }

    /**
     * UserController update
     * @param  User $user
     * @param  Request $request
     * @return Response
     */
    public function update (User $user, Request $request): Response
    {
        return $this->userService->update($user, $request);
    }

    /**
     * UserController destroy
     * @param  User $user
     * @param  Request $request
     * @return Response
     */
    public function destroy (User $user, Request $request): Response
    {
        return $this->userService->destroy($user, $request);
    }

    public function resetPassword (User $user, Request $request): Response
    {
        return $this->userService->resetPassword($user, $request);
    }
}
