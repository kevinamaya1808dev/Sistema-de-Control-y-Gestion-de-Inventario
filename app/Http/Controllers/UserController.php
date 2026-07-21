<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Cargamos los usuarios con sus roles, permisos y además sus movimientos de caja activos
        $users = User::with(['role', 'permissions', 'cajaMovimientos' => function ($query) {
            $query->where('estado', 'abierta');
        }])->get();

        $roles = Role::all();
        $permissions = Permission::all();

        // Métricas dinámicas para el dashboard
        $totalUsers = $users->count();
        $totalAdmins = $users->filter(fn ($u) => optional($u->role)->name === 'Administrador')->count();
        $totalOperators = $users->filter(fn ($u) => optional($u->role)->name === 'Operador')->count();
        $activeUsers = $users->where('is_active', true)->count();
        $cajasAbiertasHoy = $users->filter(fn ($u) => $u->cajaMovimientos->isNotEmpty())->count();

        return view('users.index', compact(
            'users', 'roles', 'permissions',
            'totalUsers', 'totalAdmins', 'totalOperators', 'activeUsers', 'cajasAbiertasHoy'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $isActive = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        // Pasamos la contraseña limpia porque el modelo User tiene 'password' => 'hashed' en $casts
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
            'is_active' => $isActive,
        ]);

        // Sincronizamos permisos (vacío por defecto si no hay selección)
        $user->permissions()->sync($request->input('permissions', []));

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, User $user)
    {
        // Protección explícita para el Super Administrador de la plataforma (ID 1)
        if ($user->id === 1) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$user->id,
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $user->role_id ?? 1,
                'is_active' => true, // El Super Admin jamás se desactiva
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $user->update($data);

            if (class_exists(Permission::class)) {
                $user->permissions()->sync(Permission::pluck('id')->toArray());
            }

            return redirect()->route('users.index')->with('success', 'Perfil del Super Administrador actualizado con éxito.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $isActive = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $isActive,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        // Sincronizamos correctamente los permisos editados
        $user->permissions()->sync($request->input('permissions', []));

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'Acción no permitida. El Super Administrador principal no puede ser eliminado.');
        }

        $tieneCajaAbierta = CajaMovimiento::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->exists();

        if ($tieneCajaAbierta) {
            return redirect()->route('users.index')->with('error', 'No se puede eliminar al usuario porque tiene una sesión de caja abierta activa.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado con éxito.');
    }
}
