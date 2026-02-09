<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Password;

class OwnerController extends Controller
{
    /// Display all owners
    public function index()
    {
        // Logic to retrieve and return all owners
        $owners = User::orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $owners
        ], 200);
    }

    /// Save a new owner
    public function register(Request $request)
    {
        // Logic to validate and save a new owner
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 400);
        } else {
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('owners', 'public');
                $avatar = "/storage/" . $avatarPath;
            } else {
                $avatar = null;
            }
            $owner = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'avatar' => $avatar
            ]);
            $token = $owner->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'data' => $owner,
            ], 201);
        }
    }

    /// Login an owner
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|string|min:6"
        ]);
        if (!$validate->fails()) {
            $credencials = $request->only("email", "password");
            if (!auth()->attempt($credencials)) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid Credencials"
                ], 401);
            }
            $owner = auth()->user();
            $token = $owner->createToken("auth_token")->plainTextToken;
            return response()->json([
                "status" => true,
                "message" => "Owner Logged In Successfully",
                "token" => $token,
                "Token Type" => "Bearer",
                "user" => $owner,
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "error" => $validate->errors()
            ], 422);
        }
    }

    /// Edit an existing owner
    public function updateProfile(Request $request)
    {
        $owner = auth()->user();
        if (!$owner) {
            return response()->json([
                "status" => false,
                "message" => "Owner unauthenticated"
            ], 404);
        } else {
            $validate = Validator::make($request->all(), [
                "name" => "sometimes|string|max:255",
                "email" => "sometimes|email|unique:users,email," . $owner->id,
            ]);
            if (!$validate->fails()) {
                if ($request->hasFile('avatar')) {
                    $oldImage = substr($owner->avatar, 9);
                    Storage::disk("public")->delete($oldImage);
                    $avatarPath = $request->file("avatar")->store("owners", "public");
                    $avatar = "/storage/" . $avatarPath;
                    $owner->avatar = $avatar;
                }
                if ($request->has('name')) {
                    $owner->name = $request->name;
                }
                if ($request->has('email')) {
                    $owner->email = $request->email;
                }
                if ($request->has('password')) {
                    $owner->password = $request->password;
                }
                $owner->save();
                return response()->json([
                    "status" => true,
                    "message" => "Owner Profile Updated Successfully",
                    "owner" => $owner
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "errors" => $validate->errors()
                ], 422);
            }
        }
    }

    // Logout user currently authenticated
    public function logoutCurrentDevice(Request $request)
    {
        $owner = $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => true,
            "message" => "Owner Logged Out (Currently) Successfully"
        ], 200);
    }

    // Logout user currently authenticated
    public function logoutAllDevices(Request $request)
    {
        $owner = $request->user()->tokens()->delete();
        return response()->json([
            "status" => true,
            "message" => "Owner Logged Out (All) Successfully"
        ], 200);
    }

    // Get user profile
    public function profile()
    {
        $user = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Owner Profile Retrieved Successfully",
            "user" => $user
        ], 200);
    }

    /// Get Rooms of the authenticated owner
    public function rooms(){
        $owner = auth()->user();
        $rooms = $owner->rooms()->orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $rooms
        ], 200);
    }
}
