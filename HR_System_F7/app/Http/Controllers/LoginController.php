<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginForm(){
        // DB::table('admin')->where('email', 'admin@gmail.com')->update([
        //     'password' => Hash::make('admin123')
        // ]);
        return View('adminLogin');
    }



    public function login(Request $request){
        $validate = $request->validate(
            [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]
            );

           
                $user = DB::table('admin')->where('email', $request->email)->first();
                if($user && Hash::check($request->password, $user->password)){
                    Auth::loginUsingId($user->id);
                    return redirect()->route('employees.index'); 
                }
                return back()->withErrors(['email' =>'Invalid Credentials']);
    }
}
