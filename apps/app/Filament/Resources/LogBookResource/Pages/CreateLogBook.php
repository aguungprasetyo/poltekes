<?php

namespace App\Filament\Resources\LogBookResource\Pages;

use App\Filament\Resources\LogBookResource;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentGroup;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLogBook extends CreateRecord
{
    protected static string $resource = LogBookResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $student = Student::where('user_id', auth()->id())->first();
        $studentGroup = StudentGroup::where('student_id', $student->id)->first();

        $data['group_id'] = $studentGroup->group_id;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $logBook = static::getModel()::create($data);

        foreach ($data['attachment'] as $attach) {
            $logBook->attachments()->create([
                'path' => $attach,
            ]);
        }

        return $logBook;
    }
}
