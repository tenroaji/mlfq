<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Api\Transformers\OrderTransformer;
use App\Filament\Resources\OrderResource\OrderDetailRelationManager;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ProductMaterialRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('number')
                    ->label('No. HP')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('alamat')
                    ->required(),
                Forms\Components\TextInput::make('down_payment')
                    ->label('DP')
                    ->default(0),
                Forms\Components\TextInput::make('sum_price')
                    ->label('Total Harga')
                    ->readOnly()
                    ->default(0),
                Forms\Components\Hidden::make('priority')
                    ->default(false),
//                    ->extraAttributes(['disabled' => d]),
                Forms\Components\TextInput::make('time')
                    ->label('Lama Pengerjaan (Hari)')
                    ->readOnly()
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->readOnly()
//                    ->options([
//                        'antrian'=>'Antrian',
//                        'designer'=>'Designer',
//                        'cutting'=>'Cutting',
//                    ])
//                    ->disableOptionWhen(fn (string $value): bool => $value === 'cutting' && auth()->user()->role !== 'super_admin')
//                    ->in(fn ($component): array => array_keys($component->getEnabledOptions()))
                ->default('antrian'),
                Forms\Components\Hidden::make('total_product')
                    ->default(0),
                Forms\Components\TextInput::make('antrian')
                    ->hidden()
                     ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('l, d F Y \J\a\m H:i'))
                   ->disabled(),
                Forms\Components\Toggle::make('finish')
                    ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->hasRole('designer')) {
                    $query->where('status',"designer")
                        ->where('finish',0)
                        ->orderBy('priority', 'desc')
                        ->orderBy('time')
                        ->orderBy('created_at');
                } elseif ($user->hasRole('manager')) {
                    $query->where('status',"manager")
                        ->orderBy('priority', 'desc')
                        ->orderBy('time')
                        ->orderBy('created_at');
                }else {
                    $query->where('finish', false)
                        ->orderBy('priority', 'desc')
                        ->orderBy('time', 'desc')
                        ->orderBy('created_at', 'asc')
                    ;
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('Antrian')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('No. Hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\IconColumn::make('priority')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Pesanan Masuk')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('antrian')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Lama Pengerjaan (Hari)')
                    ->sortable(),
//                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('l, d F Y \J\a\m H:i')),
                Tables\Columns\IconColumn::make('finish')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('finish'),
                TernaryFilter::make('priority')
                ->label('Prioritas'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                        ->label('Tanggal Masuk Orderan'),
                        DatePicker::make('created_until')
                        ->label('Tanggal Berakhir Orderan'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function() {
                            $user = Auth::user();
                            if ($user->hasRole('designer')) {
                                return false;
                            }else{
                                return true;
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderDetailRelationManager::class,
            ProductMaterialRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return OrderTransformer::class;
    }
}
