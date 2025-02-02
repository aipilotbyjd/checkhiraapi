<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
            $user->otp_expiry = now()->addMinutes(10); // OTP valid for 10 minutes
            $user->save();

            $apiKey = env('INTERAKT_API_KEY');

            if (!$apiKey) {
                throw new \Exception('Interakt API key not configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.interakt.ai/v1/public/message/', [
                        'countryCode' => '+91',
                        'phoneNumber' => $request->phone,
                        // 'fullPhoneNumber' => '+91' . $request->phone,
                        // 'campaignId' => env('INTERAKT_CAMPAIGN_ID'),
                        'callbackData' => 'hiraapi',
                        'type' => 'Template',
                        'template' => [
                            'name' => 'hiraapi',
                            'languageCode' => 'en_US',
                            "bodyValues" => [
                                $otp
                            ],
                            'buttonPayload' => [
                                "0" => [
                                    "Copy code"
                                ]
                            ]
                        ]
                    ]);

            Log::info($response->json());

            $result = $response->json()['result'];

            Log::info($result);

            if ($result) {
                return $this->sendResponse([
                    'message' => 'OTP sent successfully',
                    'expires_in' => 10 // minutes
                ], 'OTP sent successfully');
            } else {
                return $this->sendError('OTP not sent', [], 400);
            }

            // return $this->sendResponse([
            //     'message' => 'OTP sent successfully',
            //     'expires_in' => 10 // minutes
            // ], 'OTP sent successfully');
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

            if ($user->otp_expiry < now()) {
                return $this->sendError('OTP has expired', [], 400);
            }

            // Clear OTP after successful verification
            $user->otp = null;
            $user->otp_expiry = null;
            $user->save();

            $user->token = $user->createToken($user->email ?? 'hirabook')->accessToken;

            return $this->sendResponse($user, 'OTP verified successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'verifyOtp', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $resetToken = Str::random(60);
            $user->password_reset_token = $resetToken;
            $user->password_reset_expires_at = now()->addHours(24); // Token valid for 24 hours
            $user->save();

            // Send reset password email
            Mail::send('emails.forgot-password', [
                'user' => $user,
                'resetToken' => $resetToken,
                'resetUrl' => env('FRONTEND_URL') . '/reset-password?token=' . $resetToken
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset Your Password');
            });

            return $this->sendResponse([
                'message' => 'Password reset link has been sent to your email'
            ], 'Reset email sent successfully');

        } catch (\Exception $e) {
            logError('AuthController', 'forgotPassword', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::where('password_reset_token', $request->token)
                ->where('password_reset_expires_at', '>', now())
                ->first();

            if (!$user) {
                return $this->sendError('Invalid or expired reset token', [], 400);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->password_reset_token = null;
            $user->password_reset_expires_at = null;
            $user->save();

            return $this->sendResponse([
                'message' => 'Password has been reset successfully'
            ], 'Password reset successful');

        } catch (\Exception $e) {
            logError('AuthController', 'resetPassword', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . Auth::id(),
                'phone' => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,phone,' . Auth::id(),
                'address' => 'sometimes|required|string|max:500',
                'profile_image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::findOrFail(Auth::id());

            // Update only provided fields
            if ($request->has('first_name'))
                $user->first_name = $request->first_name;
            if ($request->has('last_name'))
                $user->last_name = $request->last_name;
            if ($request->has('email'))
                $user->email = $request->email;
            if ($request->has('phone'))
                $user->phone = $request->phone;
            if ($request->has('address'))
                $user->address = $request->address;

            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete('profile_images/' . $user->profile_image);
                }

                // Store new image
                $imageName = time() . '_' . Str::random(10) . '.' . $request->profile_image->extension();
                $request->profile_image->storeAs('profile_images', $imageName, 'public');
                $user->profile_image = $imageName;
            }

            $user->save();

            return $this->sendResponse($user, 'Profile updated successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'updateProfile', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = Auth::user();
            return $this->sendResponse($user, 'Profile fetched successfully');
        } catch (\Exception $e) {
            logError('AuthController', 'profile', $e->getMessage());
            return $this->sendError('Something went wrong', [], 500);
        }
    }
}
