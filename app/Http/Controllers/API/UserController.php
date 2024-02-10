<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
{
    $validation = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validation->fails()) {
        return response()->json(['errors' => $validation->errors()], 400); // إرجاع الأخطاء
    }

    $user = User::create([
        "name" => $request->name,
        "email" => $request->email,
        "password" => $request->password,
    ]);

    return response()->json(["msg" => 'success register', 'user' => $user]);
}
public function sendVerifyMail( $email)
{
   if(auth()->user()){
        $user=User::where('email',$email)->get();
        if(Count ($user)>0){
            $random=Str::random(40);
            $domain=URL::to("/verifymail/");
            $url=$domain.'/'.$random;
            $data['url']=$url;
            $data['email']=$email;
            $data['title']="email verify";
            $data['body']="pleas click here to verify your email";
            Mail::send('verifymail',['data'=>$data],function($message)use($data){
                $message->to ($data['email'])->subject($data['title']);
            });
                $user=User::find($user[0]['id']);
                $user->remember_token=$random;
                $user->save();
                return response()->json(['success'=>true,'msg'=>'check your email to verify your email']);

        }
   }
   else{
    return response()->json(['success' => false, "msg" => 'Unauthorized'], 401);

   }

}
// public function VerifyEmail($token){
//     $user=User:: where('remember_token',$token)->get();
//     if(Count($user)>0){
//             $datatime=Carbon::now()->format('Y-m-d H:i:s');
//             $user=User::find($user[0],['id']);
//             $user->remember_token='';
//             $user->	is_verify=1;
//             $user->email_verified_at= $datatime;
//             // $user->save();
//             return "<h1> succuess</h1>";
//     }
// else
// {
//     return view('404');
// }
// }
// public function VerifyEmail($token)
// {
//     $user = User::where('remember_token', $token)->first();

//     if ($user) {
//         $datetime = now(); // Carbon::now() is not required as now() provides the current date and time.
//         $user->remember_token = null; // Set remember_token to null instead of empty string.
//         $user->is_verify = true; // Use boolean true instead of integer 1.
//         $user->email_verified_at = $datetime;
//         $user->save();

//         return "<h1>Success</h1>"; // Corrected spelling of "success".
//     } else {
//         return view('404');
//     }
// }

public function VerifyEmail($token)
{
    $user = User::where('remember_token', $token)->first();

    if ($user) {
        $datetime = Carbon::now()->format('Y-m-d H:i:s');
        $user->remember_token = '';
        $user->is_verify = 1;
        $user->email_verified_at = $datetime;
        $user->save();

        return "<h1> success</h1>";
    } else {
        return view('404');
    }
}

public function login(Request $request){
    $validation = Validator::make($request->all(), [

        'email' => 'required|email',
        'password' => 'required',
    ]);
    if ($validation->fails()) {
        return response()->json(['errors' => $validation->errors()], 400); // إرجاع الأخطاء
    }
    if (!$token = auth()->attempt($validation->validated())) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return $this->respondWithToken($token);

}
protected function respondWithToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
    ]);
}
public function profile()
{
    try{
        return response()->json(['success' => true,'data'=>auth()->user()], 401);
    }
    catch(\Exception $e){
        return response()->json(['success' => false,"msg"=> $e->getMessage()], 401);

    }
}
public function profileUpdate(Request $request)
{
    if (auth()->user()) {
        $validation = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, "msg" => 'User not found'], 404);
        }

        $user->name = $request->name;
        if( $user->email !=$request->email){
            $user->is_verify =0;
        }
        $user->email = $request->email;
        $user->save();

        return response()->json(['success' => true, "msg" => 'Profile updated'], 200);
    } else {
        return response()->json(['success' => false, "msg" => 'Unauthorized'], 401);
    }
}
public function refresh()
{
    if (auth()->user()) {
    return $this->respondWithToken(auth()->refresh());
    }
    else{
        return response()->json(['success' => false, "msg" => 'Unauthorized'], 401);

    }
}
public function logout()
{
    auth()->logout();

    return response()->json(['message' => 'Successfully logged out']);
}
public function ForgetPassword(Request $request)
{
    try{
        $user=User::where('email',$request->email)->get();
        if(Count($user)>0){
            $token=Str::random(40);
            $domain=URL::to('/');
            $url=$domain.'/reset-password?token='.$token;
            $data['url']=$url;
            $data['email']=$request->email;
            $data['title']="reset password";
            $data['body']="pleas click here to reset password";
            Mail::send('ForgetPassword',['data'=>$data],function($message)use($data){
                $message->to ($data['email'])->subject($data['title']);
            });
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            PasswordReset::UpdateOrCreate(
                ['email'=>$request->email

            ],[
                'email'=>$request->email,
                'token'=>$token,
                'created_at'=> $datetime

            ]


            );
            return response()->json(['success'=>true,'msg'=>'please check your email']);



        }
        else{
            return response()->json(['success'=>false,'msg'=>'user not found']);
        }

    }
    catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
    }

}
public function ResetPasswordLoad(Request $request){

    $resetdata=PasswordReset::where('token',$request->token)->get();

    if(isset($request->token)&& count( $resetdata)>0){

            $user=User::where('email',  $resetdata[0]['email'])->get();
            return view('resetPassword',compact('user'));
    }
    else{
        return view('404');
    }
}

public function ResetPassword(Request $request){
    $request->validate
([
    'password'=>'required|string|confirmed'
]);
            $user=User::find( $request->id);
            $user->password= Hash::make($request->password);
                $user-> save();
                PasswordReset::where('email',$user->email)->delete();

            return "password reset successfully";
}
}
