<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:2', 'max:10', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed']

        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thankyou for registering');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        
        if(auth()->attempt([
            'username' => $incomingFields['loginusername'],
            'password' => $incomingFields['loginpassword']
        ])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You are Logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid Login');
        }
    }

    public function showCorrectHomePage()
    {
        if(auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }   
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You are Logged out');
    }

    public function profile(User $user)
    {
        return view('profile-posts', ['avatar' => $user->avatar, 'username' => $user->username, 'posts' => $user->posts()->latest()->get(), 'postCount' => $user->posts()->count()]);
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:1000'
        ]);

        // Create an instance of ImageManager
        $manager = new ImageManager();

        // Read the uploaded file
        $image = $manager->make($request->file('avatar'));

        // Resize and crop the image to 120x120 and convert it to JPEG
        $imgData = $image->fit(120, 120)->encode('jpg');

        $user = auth()->user();

        $filename = $user->id . "_" . uniqid() . ".jpg";

        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != "/fallback-avatar.jpg")
        {
            Storage::delete(str_replace("/storage", "/public", $oldAvatar));
        }

        return back()->with('success', 'Avatar successfully uploaded');
    }
}
