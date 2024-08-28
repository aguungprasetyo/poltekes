<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use App\Models\Lecture;
use App\Models\Student;
use App\Models\StudentGroup;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    public static function canViewAny(): bool
    {
        $role = auth()->user()->roles->pluck('name')->first();
        return $role === 'super_admin' || $role === 'student';
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Kelompok';
    protected static ?string $label = 'Kelompok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Kelompok')
                    ->required(),
                Select::make('lecturer_id')
                    ->label('Dosen')
                    ->required()
                    ->options(
                        Lecture::with('user')
                            ->get()
                            ->pluck('user.name', 'id')
                    )
                    ->native(false),
                Select::make('students')
                    ->label('Anggota')
                    ->required()
                    ->options(function (callable $get) {
                        // Ambil semua student_id dari studentGroups
                        $studentGroup = StudentGroup::all()->pluck('student_id');

                        // Dapatkan semua students yang tidak ada dalam studentGroup
                        $students = Student::with('user')
                            ->whereNotIn('id', $studentGroup->toArray())
                            ->get()
                            ->mapWithKeys(function ($student) {
                                return [$student->id => $student->user->name];
                            });

                        return $students;
                    })
                    ->multiple()
                    ->columnSpanFull()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelompok')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lecture.user.name')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leader.name')
                    ->label('Ketua')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
