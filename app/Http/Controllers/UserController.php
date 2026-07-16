<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'permissions'])->get();
        $roles = Role::all();
        $permissionsByModule = Permission::all()->groupBy('module');

        // Tarjetas KPI
        $totalUsers = $users->count();
        $totalAdmins = $users->where('role.name', 'Administrador')->count();
        $totalOperators = $users->where('role.name', 'Operador')->count();
        $activeUsers = $users->where('is_active', true)->count();

        return view('users.index', compact(
            'users', 'roles', 'permissionsByModule',
            'totalUsers', 'totalAdmins', 'totalOperators', 'activeUsers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('permissions')) {
            $user->permissions()->sync($request->input('permissions'));
        }

        return redirect()->back()->with('success', 'Usuario creado exitosamente.');
    }

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role_id' => 'required|exists:roles,id',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'is_active' => $user->id === 1 ? true : $request->has('is_active'), 
    ];

    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }

    $user->update($userData);

    
    if ($user->id === 1) {
        $user->permissions()->sync(\App\Models\Permission::pluck('id'));
    } else {
        $user->permissions()->sync($request->input('permissions', []));
    }

    return redirect()->back()->with('success', 'Usuario actualizado con éxito.');
}
}