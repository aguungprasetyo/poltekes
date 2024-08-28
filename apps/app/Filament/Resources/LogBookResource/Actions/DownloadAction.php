<?php

namespace App\Filament\Resources\LogBookResource\Actions;

use App\Enums\TicketStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class DownloadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'downloadAction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Download');
        $this->icon('heroicon-o-document-arrow-down');
        $this->modalWidth('xl');

        $this->color('secondary');

        $this->form([
            RichEditor::make('comment')
                ->label('Komentar')
                ->required()
                ->columnSpanFull(),
        ]);

        $this->action(function (array $data) {
            $this->record->update([
                'comment' => $data['comment']
            ]);

            Notification::make()
                ->title('Komentar berhasil disimpan.')
                ->success()
                ->send();
        });
    }
}
