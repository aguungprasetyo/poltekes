<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Filament\Resources\LectureResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLecture extends EditRecord
{
    protected static string $resource = LectureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Model $record) {
                    $record->user->delete();
                    $record->delete();

                    return redirect(Filament::getUrl() . '/lectures');
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::find($data['user_id']);

        $data['name'] = $user->name;
        $data['email'] = $user->email;

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = User::find($record->user_id);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Jika password diisi, tambahkan ke array update
        if (!empty($data['password'])) {
            $userData['password'] = bcrypt($data['password']);
        }

        // Update data user
        $user->update($userData);

        $record->update($data);

        return $record;
    }
}
