<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Role;
use App\Services\Role\RoleService;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * RoleController constructor
     * @param RoleService $roleService
     */
    public function __construct (RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * RoleController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->roleService->index($request);
    }

    /**
     * RoleController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->roleService->store($request);
    }

    /**
     * RoleController show
     * @param  Role $role
     * @param  Request $request
     * @return Response
     */
    public function show (Role $role, Request $request): Response
    {
        return $this->roleService->show($role, $request);
    }

    /**
     * RoleController update
     * @param  Role $role
     * @param  Request $request
     * @return Response
     */
    public function update (Role $role, Request $request): Response
    {
        return $this->roleService->update($role, $request);
    }

    /**
     * RoleController destroy
     * @param  Role $role
     * @param  Request $request
     * @return Response
     */
    public function destroy (Role $role, Request $request): Response
    {
        return $this->roleService->destroy($role, $request);
    }
}
