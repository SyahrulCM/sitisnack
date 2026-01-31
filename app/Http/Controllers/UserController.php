<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin','pjpu','produksi','penjualan','distribusi','reseller'];
        return view('users.create', compact('roles'));
    }

    public function store(Request $r)
    {
        $roles = ['admin','pjpu','produksi','penjualan','distribusi','reseller'];

        $r->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:50|unique:users,username',
            'email'        => 'nullable|email|max:255|unique:users,email',
            'password'     => ['required','confirmed', Password::min(6)],
            'role'         => 'required|in:' . implode(',', $roles),
            'no_hp'        => 'nullable|string|max:30',
            'alamat'       => 'nullable|string|max:500',
            'status_aktif' => 'nullable|boolean',
        ]);

        User::create([
            'nama_lengkap' => $r->nama_lengkap,
            'username'     => $r->username,
            'email'        => $r->email,
            'password'     => Hash::make($r->password),
            'role'         => $r->role,
            'no_hp'        => $r->no_hp,
            'alamat'       => $r->alamat,
            'status_aktif' => $r->boolean('status_aktif'), // checkbox
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }
}
