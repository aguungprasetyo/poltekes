<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use App\Models\Student;
use App\Models\StudentGroup;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Model $record) {
                    $record->studentGroups()->delete();

                    $record->delete();

                    return redirect(Filament::getUrl() . '/groups');
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil daftar student_id yang terkait dengan group_id
        $studentIds = StudentGroup::where('group_id', $data['id'])->pluck('student_id');

        // Ambil nama-nama student dari student_id tersebut
        $data['students'] = Student::whereIn('id', $studentIds)
            ->with('user')
            ->get()
            ->map(function ($student) {
                return $student->user->name; // Ambil nama dari relasi user
            })
            ->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // $data['leader_id'] = auth()->id();

        $group = static::getModel()::find($record->id);

        $deleted = StudentGroup::where('group_id', $record->id)->delete();

        foreach ($data['students'] as $student) {
            $group->studentGroups()->create([
                'student_id' => $student,
            ]);
        }

        $record->update($data);

        return $record;
    }
}
