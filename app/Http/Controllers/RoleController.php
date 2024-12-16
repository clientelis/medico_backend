<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // List all roles
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    // Get a single role
    public function show(Role $role)
    {
        return response()->json(['role' => $role]);
    }

    // Create a new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        $role = Role::create([
            'id' => Str::uuid(),
            'name' => $request->name,
        ]);

        return response()->json(['role' => $role], 201);
    }

    // Update an existing role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json(['role' => $role]);
    }

    // Delete a role
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Role supprimé avec succès']);
    }
}
