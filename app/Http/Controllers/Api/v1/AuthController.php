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

    public function phoneLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            // Check if user exists
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return $this->sendError('User not found. Please register first.', [], 404);
            }

            // Generate and store OTP
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(5); // OTP valid for 5 minutes
            $user->save();

            $message = "Your OTP is: " . $otp;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('TWILIO_AUTH_TOKEN'),
            ])->post('https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json', [
                        'To' => $request->phone,
                        'From' => env('TWILIO_PHONE_NUMBER'),
                        'Body' => $message,
                    ]);

            if ($response->successful()) {
                return $this->sendResponse([
                    'message' => 'OTP sent successfully',
                    'expires_in' => 5 // minutes
                ], 'OTP sent successfully');
            } else {
                return $this->sendError('Failed to send OTP', [], 500);
            }
        } catch (\Exception $e) {
            logError('AuthController', 'phoneLogin', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'otp' => 'required|numeric|digits:4'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return $this->sendError('User not found', [], 404);
            }

            if ($user->otp != $request->otp) {
                return $this->sendError('Invalid OTP', [], 400);
            }

            if ($user->otp_expires_at < now()) {
                return $this->sendError('OTP has expired', [], 400);
            }

            // Clear OTP after successful verification
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            $user->token = $user->createToken($user->email ?? 'hirabook')->accessToken;

            return $this->sendResponse($user, 'OTP verified successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'verifyOtp', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }
}
