<?php
namespace App\Http\Controllers;

use App\Http\Requests\ResetPassword\CheckCodeRequest;
use App\Http\Requests\ResetPassword\NewPasswordRequest;
use App\Http\Requests\ResetPassword\StoreRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{

    public function sendCode(StoreRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => "email doesn't exist", 'status' => false], 200);
            }
            $updated = $user->update([
                'reset_password_code' => $this->generateCode(),
                'email_verified_at' => now()->toDateString(),
            ]);
            if ($updated) {
                $user->notify(new ResetPasswordNotification());
                return response()->json(['message' => "successfully sent code to email", 'status' => true], 200);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
    public function checkCode(CheckCodeRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user->reset_password_code == $request->code) {
                return response()->json(['message' => "your code is correct", 'status' => true], 200);
            } else {
                return response()->json(['message' => "invalid code please resend", 'status' => false], 200);
            }

        } catch (\Throwable$th) {
            throw $th;
        }
    }
    public function newPassword(NewPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $record = $user->update(['password' => Hash::make($request->password)]);
            if ($record) {
                $user->update(['reset_password_code' => null]);
                return $this->response('password successfully update .', $record, true);
            } else {
                return $this->response('password cannot add.', null, false);
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
    public function generateCode()
    {
        return mt_rand(100000, 999999);
    }
}
