<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Image;
use Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isAuthor')) {
            return User::all();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'bio' => $request->bio,
            'photo' => $request->photo,
        ]);

        return 'User created successfully!';
    }

    public function show($id)
    {
        
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|string|min:6'
        ]);

        $currentPhoto = $user->photo;

        if ($request->photo != $currentPhoto) {

            if (file_exists(public_path('uploads/').$currentPhoto)) {
                unlink(public_path('uploads/').$currentPhoto);
            }

            $filename = 'user-'.time().'.png';
            Image::make($request->photo)->save(public_path('uploads/').$filename);
            User::where('id', $user->id)->update([
                'photo' => $filename,
            ]);
        }
        if ($request->password) {
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->password),
            ]);
        }
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio
        ]);

        return response('Profile updated');

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'sometimes|required|string'
        ]);
        if ($request->password) {
            User::where('id', $id)->update([
                'password' => Hash::make($request->password),
            ]);
        }
        User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'bio' => $request->bio,
            'photo' => $request->photo,
        ]);

        return 'User updated successfully';
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');

        User::where('id', $id)->delete();

        return 'User Deleted from Users table';
    }
}
