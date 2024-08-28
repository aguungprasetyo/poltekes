<?php

namespace App\Filament\Pages;

use App\Enums\UserStatus;
use App\Models\CarrierService;
use App\Models\Lecture;
use App\Models\Role;
use App\Models\Student;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as FilamentRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Register extends FilamentRegister
{
    public function register(): ?RegistrationResponse
    {

        $data = $this->form->getState();

        $user = DB::transaction(function () use ($data) {
            $user_data = collect($data)->toArray();

            return $this->getUserModel()::create($user_data);
        });

        $user->assignRole(Role::find($data['role']));

        if (Role::find($data['role'])->name == 'lecture') {
            Lecture::create([
                'user_id' => $user->id,
                'id_number' => $data['id_number'],
                'address' => '-',
                'phone' => $data['phone_number'],
            ]);
        } elseif (Role::find($data['role'])->name == 'student') {
            Student::create([
                'user_id' => $user->id,
                'id_number' => $data['id_number'],
                'address' => '-',
                'phone' => $data['phone_number'],
            ]);
        } else {
            return null;
        }

        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        $this->dispatch('registered');

        return app(RegistrationResponse::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        $this->getIdNumberFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),
                $this->getNameFormComponent(),
                $this->getPhoneFormComponent(),
                Grid::make()
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
                Grid::make()
                    ->schema([
                        // $this->getTextComponent(),
                        $this->getRoleFormComponent(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getIdNumberFormComponent(): Component
    {
        return TextInput::make('id_number')
            ->label('NIM / NIP')
            ->required()
            ->maxLength(255)
            ->helperText('NIM untuk Mahasiswa / NIP untuk Dosen')
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->rules(['regex:/^[a-zA-Z0-9._%+-]+@poltekesyogyakarta\.ac\.id$/']);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->label('Nomor HP')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama Lengkap')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getTextComponent()
    {
        return new HtmlString('<p>Simpan dan ingat password anda</p>');
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->label('Status')
            ->options(Role::whereNot('name', 'super_admin')->whereNot('name', 'admin')->pluck('name', 'id'))
            ->native(false)
            ->columnSpanFull()
            ->required();
    }
}
