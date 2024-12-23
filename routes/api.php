<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\MoleculeController;
use App\Http\Controllers\PermissionController;

Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/logout', [AuthController::class, 'logout']);

    // User management
    Route::post('/register', [AuthController::class, 'register']);
    Route::put('/users/{user}', [AuthController::class, 'updateUser']);
    Route::delete('/users/{user}', [AuthController::class, 'deleteUser']);
    Route::get('/users', [AuthController::class, 'users']);

    // Role management
    Route::get('/roles', [RoleController::class, 'index']);       
    Route::get('/roles/{role}', [RoleController::class, 'show']); 
    Route::post('/roles', [RoleController::class, 'store']);      
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    // Gestion des permissions par rôle
    Route::get('/roles/{roleId}/permissions', [PermissionController::class, 'getPermissionsByRole']);
    Route::post('/roles/{roleId}/permissions', [PermissionController::class, 'addPermissionToRole']);
    Route::delete('/roles/{roleId}/permissions/{permissionId}/remove', [PermissionController::class, 'removePermissionFromRole']);
    Route::post('/add/users/{userId}/permissions', [PermissionController::class, 'addPermissionToUser']);
    Route::delete('/users/{userId}/permissions/{permissionId}', [PermissionController::class, 'removePermissionFromUser']);
    Route::get('/users/{userId}/grouped-permissions', [PermissionController::class, 'groupedPermissions']);
    Route::get('/users/permissions-list', [PermissionController::class, 'permissionsList']);

    // Medicaments
    Route::get('/medicaments', [MedicamentController::class, 'index']);
    Route::get('/medicaments/{id}', [MedicamentController::class, 'show']);
    Route::post('/medicaments', [MedicamentController::class, 'store']);
    Route::put('/medicaments/{id}', [MedicamentController::class, 'update']);
    Route::delete('/medicaments/{id}', [MedicamentController::class, 'destroy']);
    Route::post('/medicaments/import', [MedicamentController::class, 'importCsv']);;


    Route::post('/molecules/search',[MoleculeController::class, 'searchMolecule']);
    Route::post('/molecules/interactions',[MoleculeController::class, 'checkInteractions']);
});

// Password reset
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
