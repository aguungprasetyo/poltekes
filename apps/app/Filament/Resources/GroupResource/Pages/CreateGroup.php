<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use App\Models\Group;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $data['leader_id'] = auth()->id();

        $group = static::getModel()::create($data);

        // $data['students'][] = $student->id;

        foreach ($data['students'] as $student) {
            $group->studentGroups()->create([
                'student_id' => $student,
            ]);
        }

        return $group;
    }
}
