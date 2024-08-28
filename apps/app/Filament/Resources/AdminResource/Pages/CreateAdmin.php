<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Assign the role 'admin' to the user
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            throw new \Exception('Role "admin" not found. Please create it first.');
        }

        $user->assignRole($adminRole);

        // Add user_id to $data before creating Admin record
        $data['user_id'] = $user->id;

        // Create the Admin record
        return Admin::create($data);
    }
}
