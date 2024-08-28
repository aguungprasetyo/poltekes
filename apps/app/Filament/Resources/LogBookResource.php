<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogBookResource\Actions\DownloadAction;
use App\Filament\Resources\LogBookResource\Pages;
use App\Filament\Resources\LogBookResource\RelationManagers;
use App\Models\LogBook;
use App\Models\Student;
use App\Models\StudentGroup;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogBookResource extends Resource
{
    protected static ?string $model = LogBook::class;

    public static function canViewAny(): bool
    {
        $role = auth()->user()->roles->pluck('name')->first();
        return $role === 'super_admin' || $role === 'lecture' || $role === 'student';
    }

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel =  'Catatan Kegiatan';
    protected static ?string $label =  'Catatan Kegiatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('activity')
                    ->label('Kegiatan Ke')
                    ->required()
                    ->helperText('Contoh: Kegiatan Ke-1'),
                DatePicker::make('date')
                    ->label('Tanggal Kegiatan')
                    ->required(),
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('log')
                    ->label('Deskripsi Kegiatan')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('attachment')
                    ->disk('public')
                    ->directory('bulk_upload')
                    ->columns()
                    ->columnSpanFull()
                    ->multiple()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Kelompok')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->searchable()
                    ->sortable()
                    ->date('l, j F Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        return static::downloadPdf($record);
                    }),
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
            'index' => Pages\ListLogBooks::route('/'),
            'create' => Pages\CreateLogBook::route('/create'),
            'view' => Pages\ViewLogBook::route('/{record}'),
            'edit' => Pages\EditLogBook::route('/{record}/edit'),
        ];
    }

    protected static function downloadPdf($record)
    {
        $imagePath = public_path('logo-kemenker.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);

        $icon = 'data:image/' . $imageType . ';base64,' . $imageData;

        $studentGroup = StudentGroup::where('group_id', $record->group_id)->get()->pluck('student_id')->toArray();
        $sudents = Student::with('user')->whereIn('id', $studentGroup)->get()->pluck('user.name', 'id_number')->toArray();

        $attachment = $record->attachments->pluck('path')->toArray();
        $attachs = [];

        foreach ($attachment as $attach) {
            $imagePath = public_path("storage/" . $attach);
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);

            $image = 'data:image/' . $imageType . ';base64,' . $imageData;

            array_push($attachs, $image);
        }

        $data = [
            'student' => $sudents,
            'record' => $record,
            'icon' => $icon,
            'attachment' => $attachs
        ];

        $pdf = \PDF::loadView('pdf.template', ['data' => $data])->setPaper('a4');
        $filename = \Str::orderedUuid() . '.pdf';
        $pdf->save($filename, 'local');


        return \Storage::disk('local')->download($filename, 'kurirgo-cetak-label-pengiriman.pdf');
    }
}
