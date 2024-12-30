<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                $user->token = $user->createToken($user->email ?? 'hirabook')->accessToken;

                return $this->sendResponse($user, 'User has been logged successfully.');
            } else {
                return $this->sendError('Unauthorized.', [], 401);
            }
        } catch (\Exception $e) {
            logError('AuthController', 'login', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $success['first_name'] = $user->first_name;
            $success['last_name'] = $user->last_name;
            $success['email'] = $user->email;
            $success['token'] = $user->createToken($user->email ?? 'hirabook')->accessToken;

            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Exception $e) {
            logError('AuthController', 'register', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->sendError('User not authenticated', [], 401);
            }

            $user->token()->revoke();

            return $this->sendResponse([], 'User logged out successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'logout', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = Auth::user();
            return $this->sendResponse($user, 'User fetched successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'user', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }
}
