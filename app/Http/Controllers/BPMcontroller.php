<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class BPMcontroller extends Controller
{
    function login(){
        return view('login');
    }

    function registration(){
         return view('registration');
    }

    function loginpost(Request $request){
        $request->validate([
            'login' => 'required',
            'password' => 'required'
            
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';
        $credentials = [
            $field => $login,
            'password' => $request->input('password'),
        ];

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();

            if (auth()->user()->role === 'admin') {
                return redirect()->intended(route(name:'admin.dashboard'));
            }

            if (auth()->user()->role === 'student') {
                return redirect()->intended(route(name:'student.dashboard'));
            }

            Auth::logout();
            return redirect(route(name:'login'))->with("error", "Your account is not assigned to a valid role.");
        }
        return redirect(route(name:'login'))->with("error", "Login Credentials are Invalid");

    }

    function registrationpost(Request $request){
         $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
            
        ]);
        $data['name'] = $request->name;
         $data['email'] = $request->email;
          $data['password'] = Hash::make($request->password);
          $data['role'] = 'admin';
          $user = User::create($data);
          if(!$user){
            return redirect(route(name:'registration'))->with("error", "Login Credentials are Invalid");
          }
           return redirect(route(name:'login'))->with("success", "Registration Successful");
         }

        function logout(){
            Session::flush();
            Auth::logout();
            return redirect(route(name:'login'));
        }
}
