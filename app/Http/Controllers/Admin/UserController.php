<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('id', '!=', auth()->id());

        // ── BÚSQUEDA ──
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ── FILTRO POR ROL ──
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // ── ORDENAMIENTO ──
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest'    => $query->orderBy('created_at', 'asc'),
            'az'        => $query->orderBy('name', 'asc'),
            'za'        => $query->orderBy('name', 'desc'),
            'role-admin' => $query->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END ASC")->orderBy('created_at', 'desc'),
            'role-user'  => $query->orderByRaw("CASE WHEN role = 'usuario' THEN 0 ELSE 1 END ASC")->orderBy('created_at', 'desc'),
            default     => $query->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END ASC")->orderBy('created_at', 'desc'),
        };

        $users = $query->paginate(15)->withQueryString();

        return view('admin.usuarios', compact('users'));
    }

    public function create()
    {
        return view('admin.usuarios-crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,usuario',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name'     => $request->name,
            'avatar'   => $avatarPath,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.usuarios')->withErrors(['error' => 'No puedes editar tu propia cuenta desde aquí.']);
        }

        return view('admin.usuarios-editar', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:admin,usuario',
            'avatar'   => 'nullable|max:10240',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}