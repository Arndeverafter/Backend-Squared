<?php

namespace App\Http\Controllers;

use App\Helpers\UtilsHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        // When viewing all authors we are choosing no to return the admin deets in the results
        $user = User::select('name', 'email', 'phone', 'id')->where('id', '!=', 1)->paginate(30);
        return UtilsHelper::apiResponseConstruct("payload", $user, 201);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        return UtilsHelper::apiResponseConstruct('payload', User::findOrFail($id), 201);
    }
}
