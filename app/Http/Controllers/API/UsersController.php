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

}
