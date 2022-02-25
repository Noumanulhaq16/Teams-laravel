<?php

namespace App\Http\Controllers;

use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SalesmanController extends Controller
{
    protected function SalesmanRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:salemans',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => null,
                    'error' => $validator->errors()->first('email'),
                ],
                201,
            );
        }

        $user = new Salesman();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->role = 'salesman';
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Your Request Has Been Successfully Recieved',
                'error' => null,
            ],
            201,
        );
    }
    protected function AgentLogin(Request $request)
    {
        $controlls = $request->all();
        $rules =  [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($controlls, $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' =>  $validator->errors(),
            ], 200);
        }
        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (auth('agent')->attempt($credentials)) {


            $user = auth('agent')->user();
            $token = $user->createToken('auth_token')->accessToken;
            return response()->json([
                'accessToken' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }
    }
    protected function AgentForgotPassword(Request $request)
    {
        $user = Salesman::whereEmail($request->email)->first();
        if ($user == null) {
            return response()->json(['status' => 'Failed', 'message' => 'Email Not Exists']);
        } else {
            $token = rand(123456, 999999);
            $user->reset_token = $token;
            $user->update();
            $protocol = 'http';

            Mail::raw('Hey agent : Your Code For Password Resseting is :' . $token, function ($message) use ($request) {
                $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
                $message->to($request->email);
                $message->subject('Reset Password');
            });
            return response()->json([
                'status' => 'Success',
                'message' => 'Email Has Been Send To You !',
                'error' => null
            ]);
        }
    }

    protected function AgentResetPassword(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'token' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => null,
                'error' => $validator->errors()
            ]);
        }
        $user = Salesman::where(['reset_token' => $request->token])->first();
        if (!$user) {
            return response()->json([
                'status' => 'Failed',
                'message' => null,
                'error' => 'Token Expired !'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $user->reset_token = null;
        if ($user->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password Successfully Changed !',
                'error' => null
            ]);
        }
    }
}
