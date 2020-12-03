<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //user create endpoint

    public function userCreate(Request $request)
    {
        // set new validation rule
        // Verify if the email entered is a lasu email
        Validator::extend('not_contains', function($attribute, $value, $parameters)
        {
            if (strpos($value, '@lasu.edu.ng') || strpos($value, '@st.lasu.edu.ng')) {
                $kkk = true;
            } else {
                $kkk = false;
            }

            return $kkk;
        });

        $messages = array(
            'not_contains' => 'The :attribute must be a LASU email address with @lasu.edu.ng',
        );

        $data = $request->only(['user_type', 'email', 'password']);
        $rules = [
            'user_type' => 'required|in:admin,lecturer,student',
            'email' => 'required|email|unique:users|not_contains',
            'password' => 'required'
        ];

        $validate_user = Validator::make($data, $rules, $messages);

        if ($validate_user->fails()) {
            return response()->json([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validate_user->errors()
                ]], 400);
        }

        $save_user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type' => $request->user_type
        ]);

        $accessToken = $save_user->createToken('authToken')->accessToken;

        if ($save_user) {
            return response()->json([
                'status' => 'success',
                'message' => 'user registered',
                'user_data' => $save_user,
                'user_access_token' => $accessToken
            ], 201);
        }

        return response()->json([
            'error' => [
                'type' => 'cannot register user',
                'message' => 'an error occurred, please try again'
            ]], 400);
    }

    public function userLogin(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $rules = [
            'email' => 'required|exists:users',
            'password' => 'required'
        ];

        $validate_user_data = Validator::make($data, $rules);

        if ($validate_user_data->fails()) {
            return response()->json([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validate_user_data->errors()
                ]], 400);
        }

        if (Auth::attempt($data)) {
            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            return response()->json([
                'status' => 'success',
                'message' => 'login successful',
                'user_data' => auth()->user(),
                'user_access_token' => $accessToken
            ], 200);
        }

        return response()->json([
            'error' => [
                'type' => 'cannot login user',
                'message' => 'invalid credentials, please try again'
            ]], 400);
    }

    public function userLogout() {
        auth()->logout();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'user logged out'
            ], 200);
    }

    public static function isAdmin() {
        if (auth()->user()->user_type !== 'admin') {
            return response([
                'error' => 'no access'
            ]);
        }
    }
}
