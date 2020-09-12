<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    public function index() {
        $users = User::orderBy('role_id')->orderBy('id')->paginate(50);
        $roles = Role::all();
        return view('admin.users.index', ['users' => $users, 'roles' => $roles]);
    }

    public function edit($user_id) {
    }

    public function update(Request $request, $user_id) {
        $user = User::find($user_id);
        if (!$user) {
            abort(404);
        }
        $request->validate([
            'name' => ['string', 'max:191'],
            'email' => ['string', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'password' => ['string', 'min:8', 'max:191'],
            'new_password' => ['string', 'min:8', 'max:191'],
            'role' => ['integer', 'between:1,4'],
        ]);
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->email) {
            $user->email = strtolower($request->email);
        }
        if (Hash::check($request->password, $user->password)) {
            $user->password = Hash::make($request->password);
        }
        if ($request->role && Auth::user()->hasPermission('admin') && Auth::user()->id !== $user->id && $user->id !== 1) {
            $user->role_id = $request->role;
        }
        $user->save();

        return redirect()->back()->with('success', 'Profile of ' . $user->name . ' updated');
    }

    public function destroy($user_id) {
        // Only admins can delete users, you can't delete yourself, you can't delete the main admin
        if (!Auth::user()->hasPermission('admin') || Auth::user()->id == $user_id || $user_id == 1) {
            abort(403);
        }
        User::destroy($user_id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
