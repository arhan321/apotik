<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\Obat;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\ObatResource\Pages;
use App\Filament\Admin\Resources\ObatResource\RelationManagers;

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Manajemen Obat';
    protected static ?string $navigationLabel = 'Data Obat';
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

       public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis_id')
                    ->relationship('jenis', 'name')
                    ->required(),

                Select::make('golongan_id')
                    ->relationship('golongan', 'name')
                    ->required(),
                TextInput::make('kode_obat')
                    ->disabled()
                    ->dehydrated(false) 
                    ->label('Kode Otomatis'),
                Forms\Components\TextInput::make('nama_obat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('komposisi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('dosis')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('aturan_pakai')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nomor_izin_edaar')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('tanggal_kadaluarsa'),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric(),
                Select::make('status_label')
                    ->options([
                        'Dengan_Resep' => 'Dengan Resep',
                        'Tanpa_Resep' => 'Tanpa Resep',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Kosong' => 'Kosong',
                        'Kadaluarsa' => 'Kadaluarsa',
                    ])
                    ->required(),

                FileUpload::make('image')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->preserveFilenames()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('jenis.name')->label('Jenis'),
                TextColumn::make('golongan.name')->label('Golongan'),
                Tables\Columns\TextColumn::make('kode_obat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_obat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dosis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('aturan_pakai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_izin_edaar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_kadaluarsa')
                    ->date()
                    ->sortable(),
                TextColumn::make('harga')->money('IDR'),
                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_label'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListObats::route('/'),
            'create' => Pages\CreateObat::route('/create'),
            'edit' => Pages\EditObat::route('/{record}/edit'),
        ];
    }
}
