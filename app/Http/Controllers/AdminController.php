<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected function AgentApprove($id)
    {
        $customer = Agent::find($id);
        $customer->status = '1';
        $body = 'Hey Customer ! Congratulations - Your Request At Overwatch is Now Approved - You Can Now Log in  :)';
        $protocol = 'http';
        if ($customer->update()) {
            Mail::raw($body, function ($message) use ($customer) {
                $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
                $message->to($customer->email);
                $message->subject('Confirmation');
            });
        }
        return response()->json(['status' => 'success', 'message' => 'Email Has Been Send To The Customer !']);
    }

    protected function SalemanApprove($id)
    {
        $contractor = Salesman::find($id);
        $contractor->status = "1";
        $body = 'Hey Contractor ! Congratulations - Your Request At Overwatch is Now Approved - You Can Now Log in  :)';
        if ($contractor->update()) {
            Mail::raw($body, function ($message) use ($contractor) {
                $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
                $message->to($contractor->email);
                $message->subject('Confirmation');
            });
        }
        return response()->json(['status' => 'success', 'message' => 'Email Has Been Send To The Contractor !']);
    }

    protected function Agent()
    {
        $scustomers = Agent::all();
        return response()->json(['customers' => $scustomers], 200);
    }
    //
    protected function AdminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors(),
                ],
                201,
            );
        }
        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (auth('admin')->attempt($credentials)) {
            $user = auth('admin')->user();
            $token = $user->createToken('auth_token')->accessToken;
            return response()->json(
                [
                    'status' => 'success',
                    'user' => ['user' => $user->name, 'role' => 'admin', 'email' => $user->email],
                    'accessToken' => $token,
                ],
                200,
            );
        }
        return response()->json(['status' => 'failed', 'message' => 'Invalid Credentials']);
    }

    protected function AdminForgetPassword(Request $request)
    {
        // return $request->all();
        $user = Admin::whereEmail($request->email)->first();

        if ($user == null) {
            return response()->json(['status' => 'Failed', 'message' => 'Email Not Exists']);
        } else {
            $token = rand(123456, 999999);
            $user->reset_token = $token;
            $user->update();
            $protocol = 'http';

            Mail::raw('Hey Admin : Your Code For Password Resseting is :' . $token, function ($message) use ($request) {
                $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
                $message->to($request->email);
                $message->subject('Reset Password');
            });
            return response()->json(['status' => 'Success', 'message' => 'Email Has Been Send To You !']);
        }
    }

    protected function AdminResetPassword(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Failed', 'message' => $validator->errors()]);
        }
        $user = Admin::where(['reset_token' => $request->token])->first();
        if (!$user) {
            return response()->json(['status' => 'Failed', 'message' => 'Token Expired !']);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $user->reset_token = null;
        if ($user->save()) {
            return response()->json(['status' => 'Success', 'message' => 'Password Successfully Changed !']);
        }
    }
}
