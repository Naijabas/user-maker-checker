<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\SendRequestNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return UserResource
     */
    public function index(): UserResource
    {
        return new UserResource(User::whereApproved(true)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::create($request->all());
            $data = [
                'created_by' => Auth::id(),
                'details' => json_encode($user)
            ];
            $request = \App\Models\Request::create($data);
            if($request){
                $admins = User::role('Admin')->get();
                foreach (($admins)->cursor() as $admin) {
                    $admin->notify(new SendRequestNotification($request));
                }
            }

            DB::commit();
            return $this->sendResponse($user, 'User Successfully submitted for review');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error', ['error' => 'User can not be submitted at the moment']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
