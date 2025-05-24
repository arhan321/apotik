<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pengajuan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PesananResource\Pages;
use App\Filament\Admin\Resources\PesananResource\RelationManagers;
use Filament\Forms\Components\{Repeater, Select, TextInput, DatePicker, Hidden};

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Apotek';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?int $navigationSort = 7;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('profile_id')
                    ->relationship('profile', 'nama_lengkap')
                    ->required()
                    ->label('Pemesan')
                    ->reactive(),

                Select::make('pengajuan_id')
                    ->relationship(
                        'pengajuan',
                        'nomor_pengajuan',
                        // di sinilah kita filter query:
                        fn (Builder $query, $get) => $query
                            ->where('profile_id', $get('profile_id'))
                            ->where('status', 'disetujui')
                    )
                    ->nullable()
                    ->reactive()
                    ->label('Berdasarkan Pengajuan')
                    ->hidden(fn (callable $get) => ! $get('profile_id')),
                Placeholder::make('catatan_pengajuan')
                    ->label('Catatan Pengajuan')
                    ->reactive()
                    ->content(fn (callable $get) => optional(Pengajuan::find($get('pengajuan_id')))->catatan ?? '-'),

                Placeholder::make('alamat_pengajuan')
                    ->label('Alamat Pengajuan')
                    ->reactive()
                    ->content(fn (callable $get) => optional(Pengajuan::find($get('pengajuan_id')))->alamat ?? '-'),

                Placeholder::make('jarak_pengajuan')
                    ->label('Jarak Pengajuan (km)')
                    ->reactive()
                    ->content(fn (callable $get) => optional(Pengajuan::find($get('pengajuan_id')))->jarak ?? 0),
                TextInput::make('nomor_pesanan')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('Nomor Otomatis'),
                Forms\Components\DatePicker::make('tanggal'),
                Repeater::make('items')
                    ->label('Produk yang Dipesan')
                    ->relationship()
                    ->schema([
                        Select::make('obat_id')
                            ->label('Produk')
                            ->relationship('obat', 'nama_obat')
                            ->required(),

                        TextInput::make('qty')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->reactive()
                            ->required(),

                        TextInput::make('total')
                            ->label('Subtotal')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0)
                            ->afterStateHydrated(function ($state, callable $set, $get) {
                                $qty = $get('qty') ?? 1;
                                $obat = \App\Models\Obat::find($get('obat_id'));
                                if ($obat) {
                                    $set('total', $qty * $obat->harga);
                                }
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $qty = $get('qty') ?? 1;
                                $obat = \App\Models\Obat::find($get('obat_id'));
                                if ($obat) {
                                    $set('total', $qty * $obat->harga);
                                }
                            }),
                    ])
                    ->columns(3),
                Repeater::make('pengiriman')
                    ->label('Detail Pengiriman')
                    ->relationship()
                    ->maxItems(1)
                    ->schema([
                        Select::make('pengirim_id')
                            ->label('Kurir')
                            ->relationship('pengirim', 'name')
                            ->required(),

                        TextInput::make('alamat')
                            ->label('Alamat Pengiriman')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('jarak')
                            ->label('Jarak (km)')
                            ->numeric()
                            ->reactive()
                            ->required(),

                        TextInput::make('total')
                            ->label('Ongkir')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, $get) {
                                $jarak = $get('jarak') ?? 0;
                                $chargeable = max($jarak - 5, 0);
                                $set('total', $chargeable * 3000);
                            })
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $jarak = $get('jarak') ?? 0;
                                $chargeable = max($jarak - 5, 0);
                                $set('total', $chargeable * 3000);
                            }),

                        Select::make('status')
                            ->label('Status Pengiriman')
                            ->options([
                                'menunggu'     => 'Menunggu Konfirmasi',
                                'dikirim' => 'Di Anttar',
                                'selesai'   => 'Selesai',
                                'dibatalkan'    => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2),
                TextInput::make('total')
                    ->label('Total Bayar')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, $get) {
                        $itemsTotal     = collect($get('items'))->sum('total');
                        $shippingTotal  = optional(collect($get('pengiriman'))->first())['total'] ?? 0;
                        $set('total', $itemsTotal + $shippingTotal);
                    })
                    ->afterStateUpdated(function (callable $set, $get) {
                        $itemsTotal     = collect($get('items'))->sum('total');
                        $shippingTotal  = optional(collect($get('pengiriman'))->first())['total'] ?? 0;
                        $set('total', $itemsTotal + $shippingTotal);
                    }),
                Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'diproses' => 'Diproses',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('menunggu')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pesanan')
                    ->searchable(),
                TextColumn::make('profile.nama_lengkap')->label('Pemesan'),
                TextColumn::make('pengajuan.nomor_pengajuan')->label('Pengajuan'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('items_summary')
                ->label('Produk')
                ->html() // karena kita tampilkan <br>
                ->getStateUsing(function ($record) {
                    return $record->items->map(function ($item) {
                        return '<b>' . $item->obat->nama_obat . '</b> x ' . $item->qty;
                    })->implode('<br>');
                }),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'warning' => 'diproses',
                    'primary' => 'dikirim',
                    'success' => 'selesai',
                    'danger' => 'dibatalkan',
                ]),
                Tables\Columns\TextColumn::make('pengajuan_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}