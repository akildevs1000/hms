<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->user_type = $user->company_id > 0 ? ($user->employee_role_id > 0 ? "employee" : "company") : ($user->role_id > 0 ? "user" : "master");

        return response()->json([
            'token' => $user->createToken('myApp')->plainTextToken,
            'user' => $user,
        ], 200);
    }
    public function CompanyLogin(Request $request)
    {
        $model = User::query();
        $user = $model->whereEmail($request->email)->with('company', 'employee')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // if (!$user || $user->company->expiry <= now()) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Your account Expired'],
        //     ]);
        // }

        return response()->json([
            'token' => $user->createToken('myApp')->plainTextToken,
            'user' => $model->first(),
        ], 200);
    }
    public function me(Request $request)
    {
        $user = User::where('email', $request->user()->email)->first();
        $model = User::where('email', $user->email);
        if ($user && $user->assigned_permissions) {
            $user->permissions = $user->assigned_permissions->permission_names;
        } else {
            $user->permissions = [];
        }


        $user->user_type = $user->company_id > 0 ? ($user->employee_role_id > 0 ? "employee" : "company") : ($user->role_id > 0 ? "user" : "master");
        $model = $model->with('company', 'employee')->first();
        $obj = (($user->is_master == 1) && $user->role_id == 0 && ($user->employee_role_id == 0)) ? $user : $model;
        $obj->user_type =  $user->user_type;
        $obj->permissions =  $user->assigned_employee_permissions->permission_names ?? [];
        return response()->json(['user' => $obj], 200);

        // return response()->json(['user' => $user], 200);

        // $user = Auth::user();
        // return response()->json([
        //     'user' => $user,
        //     'permissions' => [],
        // ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }
}