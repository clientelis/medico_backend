<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddRemovePermission;

class PermissionController extends Controller
{
    // Permissions par rôle
    public function getPermissionsByRole($roleId)
    {
        // Récupérer toutes les permissions
        $allPermissions = Permission::all();

        // Récupérer les permissions associées au rôle
        $rolePermissions = RolePermission::where('role_id', $roleId)->pluck('permission_id')->toArray();

        // Organiser les permissions par catégories
        $permissionsByCategory = [];
        foreach ($allPermissions as $permission) {
            $category = $permission->category;
            if (!isset($permissionsByCategory[$category])) {
                $permissionsByCategory[$category] = [];
            }
            $permissionsByCategory[$category][] = [
                'id' => $permission->id,
                'title' => $permission->title,
                'permission' => $permission->name,
                'is_active' => in_array($permission->id, $rolePermissions),
            ];
        }

        return response()->json([
            'categories' => array_map(function ($category, $permissions) {
                return ['name' => $category, 'permissions' => $permissions];
            }, array_keys($permissionsByCategory), $permissionsByCategory),
        ]);
    }

    // Ajouter une permission à un rôle
    public function addPermissionToRole(Request $request, $roleId)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::find($roleId);
        if (!$role) {
            return response()->json(['error' => 'Rôle non trouvé'], 404);
        }

        $permission = Permission::find($request->permission_id);
        if (!$permission) {
            return response()->json(['error' => 'Permission non trouvée'], 404);
        }

        RolePermission::create([
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        return response()->json(['message' => 'Permission ajoutée au rôle']);
    }

    // Supprimer une permission d'un rôle
    public function removePermissionFromRole($roleId, $permissionId)
    {
        // Rechercher l'association entre le rôle et la permission
        $rolePermission = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->first();

        // Vérifier si l'association existe
        if (!$rolePermission) {
            return response()->json(['error' => 'Permission non trouvée pour ce rôle'], 404);
        }

        // Supprimer l'association
        $rolePermission->delete();

        return response()->json(['message' => 'Permission supprimée du rôle avec succès']);
    }

    public function addPermissionToUser(Request $request, $userId)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        // Vérifier si l'utilisateur existe
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $permissionId = $request->permission_id;

        // Vérifier si l'utilisateur a déjà cette permission via son rôle
        $rolePermission = RolePermission::where('role_id', $user->role_id)
            ->where('permission_id', $permissionId)
            ->first();

        if ($rolePermission) {
            // Vérifier si la permission a été retirée via une entrée "Remove"
            $removedPermission = UserAddRemovePermission::where('user_id', $user->id)
                ->where('permission_id', $permissionId)
                ->where('type', 'Remove')
                ->first();

            if ($removedPermission) {
                // Si la permission a été retirée, supprimer l'entrée "Remove" pour la restaurer
                $removedPermission->delete();

                return response()->json(['message' => 'Permission restaurée pour l\'utilisateur']);
            }

            // Si la permission existe via le rôle et n'a pas été retirée, renvoyer une erreur
            return response()->json(['error' => 'L\'utilisateur a déjà cette permission via son rôle'], 400);
        }

        // Vérifier si l'utilisateur a déjà cette permission en type "Add"
        $existingPermission = UserAddRemovePermission::where('user_id', $user->id)
            ->where('permission_id', $permissionId)
            ->where('type', 'Add')
            ->first();

        if ($existingPermission) {
            return response()->json(['error' => 'L\'utilisateur a déjà cette permission'], 400);
        }

        // Ajouter la permission directement à l'utilisateur
        UserAddRemovePermission::create([
            'user_id' => $user->id,
            'permission_id' => $permissionId,
            'type' => 'Add',
        ]);

        return response()->json(['message' => 'Permission ajoutée à l\'utilisateur']);
    }

    // Supprimer une permission d'un utilisateur
    public function removePermissionFromUser($userId, $permissionId)
    {
        // Vérifier si l'utilisateur existe
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Vérifier si la permission provient du rôle de l'utilisateur
        $rolePermission = RolePermission::where('role_id', $user->role_id)
            ->where('permission_id', $permissionId)
            ->first();

        if ($rolePermission) {
            // Si la permission provient du rôle, ajouter une entrée avec `type='Remove'` dans `user_permissions`
            $removedPermission = UserAddRemovePermission::where('user_id', $user->id)
                ->where('permission_id', $permissionId)
                ->where('type', 'Remove')
                ->first();

            if (!$removedPermission) {
                UserAddRemovePermission::create([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'type' => 'Remove',
                ]);

                return response()->json(['message' => 'Permission marquée comme supprimée (type="Remove") pour l\'utilisateur']);
            } else {
                return response()->json(['message' => 'La permission est déjà marquée comme supprimée (type="Remove") pour cet utilisateur']);
            }
        }

        // Si la permission ne provient pas du rôle, rechercher et supprimer une permission ajoutée directement
        $userPermission = UserAddRemovePermission::where('user_id', $user->id)
            ->where('permission_id', $permissionId)
            ->where('type', 'Add') // On ne supprime que les permissions directement ajoutées
            ->first();

        if (!$userPermission) {
            return response()->json(['error' => 'Permission non trouvée dans les permissions spécifiques de cet utilisateur'], 404);
        }

        // Supprimer la permission directement ajoutée
        $userPermission->delete();

        return response()->json(['message' => 'Permission supprimée des permissions spécifiques de l\'utilisateur']);
    }

    // Liste des permissions groupées d'un utilisateur
    public function groupedPermissions($userId)
    {
        // Vérifier si l'utilisateur existe
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Récupérer toutes les permissions disponibles
        $allPermissions = Permission::all();

        // Récupérer les permissions du rôle de l'utilisateur
        $rolePermissions = RolePermission::where('role_id', $user->role_id)
            ->pluck('permission_id')
            ->toArray();

        // Filtrer les permissions supprimées (type "Remove")
        $removedPermissions = UserAddRemovePermission::where('user_id', $user->id)
            ->where('type', 'Remove')
            ->pluck('permission_id')
            ->toArray();

        // Récupérer les permissions explicitement ajoutées (type "Add")
        $addedPermissions = UserAddRemovePermission::where('user_id', $user->id)
            ->where('type', 'Add')
            ->pluck('permission_id')
            ->toArray();

        // Organiser les permissions par catégorie
        $permissionsByCategory = [];

        foreach ($allPermissions as $permission) {
            $category = $permission->category;
            if (!isset($permissionsByCategory[$category])) {
                $permissionsByCategory[$category] = [];
            }

            // Vérifier si la permission est active (ajoutée manuellement ou via le rôle sans suppression)
            $isActive = in_array($permission->id, $addedPermissions) || (
                in_array($permission->id, $rolePermissions) &&
                !in_array($permission->id, $removedPermissions)
            );

            $permissionsByCategory[$category][] = [
                'id' => $permission->id,
                'title' => $permission->title,
                'permission' => $permission->name,
                'is_active' => $isActive,
            ];
        }

        // Formatage final de la réponse
        $response = [
            'categories' => array_map(function ($category, $permissions) {
                return [
                    'name' => $category,
                    'permissions' => $permissions,
                ];
            }, array_keys($permissionsByCategory), $permissionsByCategory),
        ];

        return response()->json($response);
    }

    // Liste simple des permissions effectives d'un utilisateur
    public function permissionsList()
    {
        $currentUser = Auth::user();

        // Vérifier si l'utilisateur existe
        $user = User::find($currentUser->id);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        // Récupérer les permissions via rôle (sans celles marquées comme "Remove")
        $rolePermissions = RolePermission::where('role_id', $user->role_id)
            ->pluck('permission_id')
            ->toArray();

        $removedPermissions = UserAddRemovePermission::where('user_id', $user->id)
            ->where('type', 'Remove')
            ->pluck('permission_id')
            ->toArray();

        // Permissions via rôle, exclure celles marquées comme "Remove"
        $permissionList = Permission::whereIn('id', $rolePermissions)
            ->whereNotIn('id', $removedPermissions)
            ->pluck('name')
            ->toArray();

        // Ajouter les permissions de type "Add"
        $addedPermissions = Permission::whereIn('id', UserAddRemovePermission::where('user_id', $user->id)
            ->where('type', 'Add')
            ->pluck('permission_id')
            ->toArray()
        )->pluck('name')
        ->toArray();

        $permissionList = array_merge($permissionList, $addedPermissions);

        return response()->json($permissionList);
    }

}
