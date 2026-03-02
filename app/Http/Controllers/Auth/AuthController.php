<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('hrms_logged_in')) {
            return $this->redirectByRole(session('hrms_role'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'hrms_logged_in' => true,
                'hrms_user_id' => $user->id,
                'hrms_user_name' => $user->name,
                'hrms_user_email' => $user->email,
                'hrms_role' => $user->role,
                'hrms_tenant_id' => $user->tenant_id,
                'hrms_tenant_name' => $user->tenant ? $user->tenant->name : 'Super Admin',
            ]);
            return $this->redirectByRole($user->role);
        }

        return back()->withErrors(['email' => 'Invalid email or password. Please try again.'])->withInput();
    }

    private function redirectByRole($role)
    {
        switch ($role) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
            case 'hr':
                return redirect()->route('admin.dashboard');
            case 'employee':
                return redirect()->route('employee.dashboard');
            default:
                return redirect()->route('login');
        }
    }

    public function logout()
    {
        session()->forget(['hrms_logged_in', 'hrms_user_id', 'hrms_user_name', 'hrms_user_email', 'hrms_role', 'hrms_tenant_id', 'hrms_tenant_name']);
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}