<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Filament\Resources\LectureResource;
use App\Models\Lecture;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLecture extends CreateRecord
{
    protected static string $resource = LectureResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        $user->assignRole(Role::where('name', 'lecture')->first());

        $data['user_id'] = $user->id;

        return Lecture::create($data);
    }
}
