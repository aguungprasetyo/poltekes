<?php

namespace App\Filament\Resources\LogBookResource\Pages;

use App\Filament\Resources\LogBookResource;
use App\Models\LogBookAttachment;
use App\Models\Student;
use App\Models\StudentGroup;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLogBook extends EditRecord
{
    protected static string $resource = LogBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $attachment = LogBookAttachment::where('log_book_id', $data['id'])->get()->pluck('path')->toArray();

        $data['attachment'] = $attachment;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $student = Student::where('user_id', auth()->id())->first();
        $studentGroup = StudentGroup::where('student_id', $student->id)->first();

        $data['group_id'] = $studentGroup->group_id;
        $data['updated_by'] = auth()->id();

        $record->update($data);

        LogBookAttachment::where('log_book_id', $record->id)->delete();
        foreach ($data['attachment'] as $attach) {
            $record->attachments()->create([
                'path' => $attach,
            ]);
        }

        return $record;
    }
}
