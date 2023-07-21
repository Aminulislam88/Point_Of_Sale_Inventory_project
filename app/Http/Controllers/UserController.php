<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserController extends Controller
{
   function RegistrationPage():View{
       return view('pages.auth.registration-page');
   }

    function LoginPage():View{
        return view('pages.auth.login-page');
    }
    function SendOtpPage():View{
        return view('pages.auth.send-otp-page');
    }
    function VerifyOtpPage():View{
        return view('pages.auth.verify-otp-page');
    }
    function ResetPasswordPage():View{
        return view('pages.auth.reset-pass-page');
    }





    function UserRegistration(Request $request){
        try {


        User::create([
            'firstName'=>$request->input('firstName'),
            'lastName'=>$request->input('lastName'),
            'email'=>$request->input('email'),
            'mobile'=>$request->input('mobile'),
            'password'=>$request->input('password')

        ]);
        return response()->json([
            'status'=>'success',
            'message'=>'User Registration Successfully'
        ]);
        }catch (Exception $e){
        return response()->json([
            'status'=>'failed',
            'message'=>'User Registration Failed'
        ]);
        }
    }

    function UserLogin(Request $request){
        $count=User::where('email','=',$request->input('email'))
            ->where('password','=',$request->input('password'))
            ->count();
        if ($count==1){
            $token=JWTToken::CreateToken($request->input('email'));
            return response()->json([
               'status'=>'success',
               'message'=>'user Login Successful',
                'token'=>$token
            ]);
        }else{
            return response()->json(
                [
                    'status'=>'failed',
                    'message'=>'unauthorized'
                ]
            );
        }

    }

    function SendOTPCode(Request $request){
        $email=$request->input('email');
        $otp=rand(1000,9999);
        $count=User::where('email','=',$email)->count();

        if ($count==1){
            Mail::to($email)->send(new OTPMail($otp));
            User::where('email','=',$email)->update(['otp'=>$otp]);
            return response()->json([
                'status'=>'success',
                'message'=>'4 digit code sent on your gmail'
            ]);

        }else{
          return  response()->json(
                [
                    'status'=>'failed',
                    'message'=>'unauthorized'
                ]
            );
        }


    }

    function VerifyOTP(Request $request){
        $email=$request->input('email');
        $otp=$request->input('otp');
        $count=User::where('email','=',$email)
            ->where('otp','=',$otp)->count();

        if ($count==1){
            //dtabase otp update
            User::where('email','=',$email)->update(['otp'=>'0']);
            //pass reset token issue
            $token=JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
                'status'=>'success',
                'message'=>'otp verification successful',
                'token'=>$token
            ]);
        }else{
           return response()->json(
                [
                    'status'=>'failed',
                    'message'=>'unauthorized'
                ]
            );
        }
    }

    function ResetPassword(Request $request){
        try {
            $email=$request->header('email');
            $password=$request->input('password');
            User::where('email','=',$email)->update(['password'=>$password]);
            return response()->json([
                'status'=>'success',
                'message'=>'Request successful'
            ],200);

        }catch (Exception $exception){
            return response()->json([
                'status'=>'fail',
                'message'=>'Something went Wrong'
            ],200);
        }


    }
}
