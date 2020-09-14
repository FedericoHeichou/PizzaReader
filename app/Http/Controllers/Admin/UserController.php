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
        $user = User::find($user_id);
        if (!$user) {
            abort(404);
        }
        // If target user is master admin and who invoked update is not the master admin
        if ($user->id === 1 && Auth::user()->id !== 1) {
            return back()->with('warning', 'You are unauthorized to edit di user');
        }
        $action = Auth::user()->id === $user->id ? route('user.update') : route('admin.users.update', $user->id);
        return view('admin.users.edit')->with(['user' => $user, 'roles' => Role::all(), 'action' => $action]);
    }

    public function editYourself() {
        return $this->edit(Auth::user()->id);
    }

    // user_id can be set only from admins, for other users is used Auth::user()->id automatically
    public function update(Request $request, $user_id) {
        $user = User::find($user_id);
        if (!$user) {
            abort(404);
        }
        $request->validate([
            'name' => ['string', 'max:191', 'required'],
            'email' => ['string', 'email', 'max:191', 'required', Rule::unique('users')->ignore($user->id)],
            'password' => ['string', 'min:8', 'max:191', 'password:web', Rule::requiredIf(Auth::user()->id === $user->id)],
            'new_password' => ['string', 'min:8', 'max:191', 'nullable'],
            'role' => ['integer', 'between:1,4'],
        ]);

        // If target user is master admin and who invoked update is not the master admin
        if ($user->id === 1 && Auth::user()->id !== 1) {
            return back()->with('warning', 'You are unauthorized to edit this user');
        }

        $user->name = $request->name;
        $user->email = strtolower($request->email);

        if ($request->new_password) {
            $user->password = Hash::make($request->new_password);
        }
        // If you are admin and your are not editing yourself
        if ($request->role && Auth::user()->hasPermission('admin') && Auth::user()->id !== $user->id) {
            $user->role_id = $request->role;
        }
        $user->save();

        return back()->with('success', 'Profile of ' . $user->name . ' updated');
    }

    public function updateYourself(Request $request) {
        return $this->update($request, Auth::user()->id);
    }

    public function destroy($user_id) {
        if (Auth::user()->id == $user_id || $user_id == 1) {
            return back()->with('warning', 'You are unauthorized to delete this user');
        }
        User::destroy($user_id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
