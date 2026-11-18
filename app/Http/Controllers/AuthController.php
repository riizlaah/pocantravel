<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index() {
        return view('index');
    }

    public function try_login(Request $req) {
        $creds = $req->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if(!Auth::attempt($creds)) {
            return redirect()->back()->withErrors(['email' => 'Kredensial tidak cocok']);
        }
        return redirect()->intended('/home');
    }

    public function register() {
        return view('register');
    }

    public function try_register(Request $req) {
        $newUser = $req->validate([
            'email' => ['required','email','unique:users,email'],
            'name' => ['required','string'],
            'password' => ['required', 'string', 'min:8'],
            'password2' => ['required', 'string', 'same:password'],
            'phone_number' => ['nullable', 'string', 'digits_between:9,16']
        ]);
        unset($newUser['password2']);
        $newUser['password'] = Hash::make($newUser['password']);
        User::create($newUser);
        // return redirect()->back()->withErrors(['email' => 'Gagal membuat akun']);
        // try {
        // } catch(Exception $e) {
        // }
        return redirect('/')->with('message', 'Akun berhasil dibuat');
    }

    public function logout() {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        return redirect('/');
    }
}
