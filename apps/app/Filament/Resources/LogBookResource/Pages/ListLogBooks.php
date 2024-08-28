<?php

namespace App\Filament\Resources\LogBookResource\Pages;

use App\Filament\Resources\LogBookResource;
use App\Models\Group;
use App\Models\Lecture;
use App\Models\LogBook;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogBooks extends ListRecords
{
    protected static string $resource = LogBookResource::class;

    protected function getHeaderActions(): array
    {

        if (User::whereEmail('super_admin@mail.com')->where('id', auth()->id())->first()) {
            return [
                Actions\CreateAction::make(),
            ];
        } else {
            $role = auth()->user()->roles->pluck('name')->first();
            if ($role == 'lecturer') {
                return [];
            } elseif ($role == 'student') {
                $student = Student::where('user_id', auth()->id())->first();
                $studentGroup = StudentGroup::where('student_id', $student->id)->first();
                $leader = Group::where('leader_id', $student->id)->first();

                if ($studentGroup || $leader) {
                    return [
                        Actions\CreateAction::make(),
                    ];
                } else {
                    return [
                        Actions\CreateAction::make()
                            ->disabled()
                            ->label('Silahkan mendaftarkan grupmu terlebih dahulu')
                            ->color('warning'),
                    ];
                }
            } else {
                return [];
            }
        }
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {

        if (User::whereEmail('super_admin@mail.com')->where('id', auth()->id())->first()) {
            $logBook = LogBook::query();
        } else {
            $role = auth()->user()->roles->pluck('name')->first();

            if ($role == 'lecturer') {
                $lecture = Lecture::where('user_id', auth()->id())->first();

                $group = Group::where('lecturer_id', $lecture->id)->first();

                if ($group) {
                    $logBook = LogBook::query()->where('group_id', $group->id);
                } else {
                    $logBook = LogBook::query();
                }
            } elseif ($role == 'student') {
                $student = Student::where('user_id', auth()->id())->first();
                $studentGroup = StudentGroup::where('student_id', $student->id)->first();

                if ($studentGroup) {
                    $logBook = LogBook::query()->where('group_id', $studentGroup->group_id);
                } else {
                    $logBook = LogBook::query();
                }
            } else {
                $logBook = LogBook::query();
            }
        }

        return $logBook;
    }
}
