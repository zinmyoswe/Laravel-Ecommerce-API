<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

   


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'session_id' => 'nullable|string', // 👈 include this
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 🛒 Merge guest cart with user's cart
        if ($request->session_id) {
            $guestCartItems = Cart::where('session_id', $request->session_id)->get();

            foreach ($guestCartItems as $item) {
                // Check if user already has same product/size in cart
                $existing = Cart::where('user_id', $user->id)
                    ->where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->first();

                if ($existing) {
                    $existing->quantity += $item->quantity;
                    $existing->save();
                    // remove guest row
                    // $item->delete(); 
                } else {
                    $item->user_id = $user->id;
                    $item->session_id = null;
                    $item->save();
                }
            }
        }

        // 🔐 Generate Sanctum token
        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
