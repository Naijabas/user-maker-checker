<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RequestResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Notifications\SendRequestNotification;

class RequestController extends BaseController
{
    /**
     * Display a listing of the requests that are approved and are not mine(i.e. the one I submitted).
     *
     * @return RequestResource
     */
    public function index(): RequestResource
    {
        return new RequestResource(\App\Models\Request::whereApproved(false)
            ->where('created_by', '!=', Auth::id())
            ->get());
    }

    /**
     * Store a newly created request in storage.
     * The necessary comments have been attached to the @store method
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            // Start Database transaction
            DB::beginTransaction();
            $data = $request->all();
            if(array_key_exists('approved', $data)){unset($data['approved']);};  // Remove the approved element from the array if it is part of the incoming array
            $user = User::create($data);  // Create the user
            $data = [
                'created_by' => Auth::id(),
                'details' => json_encode($user),    // I would suggest using serialization as it is roughly faster than json_encoding
                'type' => 'create'
            ];
            $createRequest = \App\Models\Request::create($data);
            if ($createRequest) {
//                Send to other admins (I used cursor to cater for a lot of admins :))
                foreach (User::role('Admin')->where('email', '!=', Auth::user()->email)->cursor() as $admin) {
                    $admin->notify(new SendRequestNotification($createRequest));
                }
            }
            DB::commit();  //Save to DB now
            return $this->sendResponse($user, 'User Successfully submitted for review');
        } catch (Exception $e) {
            DB::rollBack();  //Roll back the record if there is a problem
            return $this->sendError('Error', ['error' => $e->getMessage()]);  // Return the error
        }
    }

    /**
     * Update the specified request in storage.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $updateData = $request->all();
            if(array_key_exists('approved', $updateData)){unset($updateData['approved']);};
            $updateData['id'] = $id;
            $data = [
                'created_by' => Auth::id(),
                'details' => json_encode($updateData),  // I would suggest using serialization as it is roughly faster than json_encoding
                'type' => 'update'
            ];
            $request = \App\Models\Request::create($data);
            if ($request) {
                foreach (User::role('Admin')->where('email', '!=', Auth::user()->email)->cursor() as $admin) {
                    $admin->notify(new SendRequestNotification($request));
                }
            }

            DB::commit();
            return $this->sendResponse($updateData, 'Update Successfully submitted for review');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Error', ['error' => 'User can not be updated at the moment']);
        }
    }

    /**
     * Remove the specified request from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $updateData['id'] = $id;
            $data = [
                'created_by' => Auth::id(),
                'details' => json_encode($updateData),  // I would suggest using serialization as it is roughly faster than json_encoding
                'type' => 'delete'
            ];
            $request = \App\Models\Request::create($data);
            if ($request) {
                foreach (User::role('Admin')->where('email', '!=', Auth::user()->email)->cursor() as $admin) {
                    $admin->notify(new SendRequestNotification($request));
                }
            }

            DB::commit();
            return $this->sendResponse($updateData, 'Deletion Successfully submitted for review');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Error', ['error' => 'User can not be deleted at the moment']);
        }
    }


    /**
     * @param $id
     * @param $type
     * @return JsonResponse
     */
    public function review($id, $type): JsonResponse
    {
        try {
            DB::beginTransaction();
            $request = \App\Models\Request::whereId($id)->first();
            if ($request && $request->created_by != Auth::id()) {
                if ($type == 'approve') {
                    switch ($request->type) {
                        case 'create':
                            $this->createApproval($request);
                            break;
                        case 'update':
                            $this->updateApproval($request);
                            break;
                        case 'delete':
                            $this->deleteApproval($request);
                            break;
                    }
                } elseif ($type == 'decline') {
                    $request->delete();
                    DB::commit();
                    return $this->sendResponse($request, 'Request Successfully declined');
                } else {
                    return $this->sendError('Error', ['error' => 'Wrong method supplied for review type']);
                }
            } else {
                throw new Exception('Request has either been reviewed or approved or you can not approve your own request');
            }

            DB::commit();
            return $this->sendResponse($request, 'Request Successfully approved');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Important comment for @createApproval @updateApproval and @deleteApproval will be attached to this method
     * @param $request
     * @return bool
     */
    public function createApproval($request): bool
    {
        $user = json_decode($request->details);     //  Decode the object
        $realUser = User::findOrFail($user->id);    //  Find the User to be updated

        //  Update the User status here
        $updateUser = $realUser->update([
            'approved' => true
        ]);

        if (!$updateUser) {
            return false;
        }

        $request->update(['approved' => true]);
        return true;
    }

    /**
     * @param $request
     * @return bool
     */
    public function updateApproval($request): bool
    {
        $user = json_decode($request->details);
        $realUser = User::findOrFail($user->id);
        $updateUser = $realUser->update((array)$user);

        if (!$updateUser) {
            return false;
        }

        $request->update(['approved' => true]);
        return true;
    }

    /**
     * @param $request
     * @return bool
     */
    public function deleteApproval($request): bool
    {
        $user = json_decode($request->details);
        $realUser = User::findOrFail($user->id);

        if ($realUser->delete() && $request->delete()) {
            return true;
        }

        return false;
    }
}
