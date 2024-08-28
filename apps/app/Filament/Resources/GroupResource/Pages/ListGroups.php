<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        $leader = Group::where('leader_id', auth()->id())->first();
        $student = Student::where('user_id', auth()->id())->first();

        if (User::whereEmail('super_admin@mail.com')->where('id', auth()->id())->first()) {
            $studentGroupCount = 0;
        } else {
            if ($leader) {
                $studentGroupCount = 1;
            } else {
                $studentGroup = StudentGroup::where('student_id', $student->id)->get();
                $studentGroupCount = count($studentGroup);
            }
        }

        return [
            Actions\CreateAction::make()
                ->visible(fn() => $studentGroupCount == 0),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {

        if (User::whereEmail('super_admin@mail.com')->where('id', auth()->id())->first()) {
            $group = Group::query();
        } else {
            $student = Student::where('user_id', auth()->id())->first();

            $studentGroup = StudentGroup::where('student_id', $student->id)->get();

            $group = Group::query()->whereIn('id', $studentGroup->pluck('group_id'));
        }

        return $group;
    }
}
