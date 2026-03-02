<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || session('hrms_role') !== 'superadmin') {
            return redirect()->route('login');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenants = Tenant::withCount('employees')->orderBy('created_at', 'desc')->paginate(15);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:tenants,domain',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'plan' => 'required|in:starter,professional,enterprise',
            'status' => 'required|in:active,inactive,suspended',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8',
            'office_lat' => 'nullable|numeric',
            'office_lng' => 'nullable|numeric',
        ]);

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'domain' => $validated['domain'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'plan' => $validated['plan'],
            'status' => $validated['status'],
            'office_lat' => $validated['office_lat'] ?? null,
            'office_lng' => $validated['office_lng'] ?? null,
        ]);

        User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant and admin account created successfully.');
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenant = Tenant::findOrFail($id);
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenant = Tenant::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'plan' => 'required|in:starter,professional,enterprise',
            'status' => 'required|in:active,inactive,suspended',
            'office_lat' => 'nullable|numeric',
            'office_lng' => 'nullable|numeric',
        ]);
        $tenant->update($validated);
        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        Tenant::findOrFail($id)->delete();
        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant deleted.');
    }
}