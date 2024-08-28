<?php

namespace App\Filament\Resources\LogBookResource\Pages;

use App\Filament\Resources\LogBookResource;
use App\Filament\Resources\LogBookResource\Actions\CommentAction;
use Filament\Actions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewLogBook extends ViewRecord
{
    protected static string $resource = LogBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            CommentAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make('Info Kegiatan')
                    ->schema([
                        TextEntry::make('group.name'),
                        TextEntry::make('title'),
                        TextEntry::make('date')
                    ])->inlineLabel()
                    ->columnSpanFull(),


                Section::make('Detail Kegiatan')
                    ->schema([
                        TextEntry::make('log')
                            ->html(),
                    ])->inlineLabel()
                    ->columnSpanFull(),

                Section::make('Lampiran Kegiatan')
                    ->schema([
                        ImageEntry::make('attachments.path')
                            ->label('Lampiran'),

                    ])->inlineLabel()
                    ->columnSpanFull(),


                Section::make('Komentar Kegiatan')
                    ->schema([
                        TextEntry::make('comment')
                            ->label('Komentar')
                            ->html()
                            ->default('Belum ada komentar'),
                    ])->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }
}
