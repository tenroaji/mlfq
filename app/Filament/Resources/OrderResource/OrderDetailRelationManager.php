<?php

namespace App\Filament\Resources\OrderResource;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrderDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetail';


    public static function updateTotal(Set $set, Get $get, ?string $state)
    {
        $total = (int) $get('size_s') + (int) $get('size_m') + (int) $get('size_l') + (int) $get('size_xl') +
            (int) $get('size_2xl') + (int) $get('size_3xl') + (int) $get('size_4xl');

        $product_time = $get('product_time') ?? 1;
        $quantity = $get('quantity') ?? 1;
        $price = $get('price') ?? 1;
        if ($total !== null) {

                $count = ceil($total/$quantity);

            $set('total_time', $product_time*$count);
            $set('sum_price', $price*$total);
            $set('total_product', $total);
        } else {
            $set('sum_price', 0);
            $set('total_time', 0);
            $set('total_product', 0);
        }
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->allowHtml()
                    ->relationship('product','name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => '
                    <div style="display: flex; align-items: center;">
                        <img src="' . asset('storage/' . $record->image) . '" alt="Product Image" style="width: 100px; height: 100px; margin-right: 10px;"/>
                        ' . $record->name . '
                    </div>')
                    ->afterStateUpdated(function (Set $set, Get $get,$state) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('product_time', $product->time);
                            $set('quantity', $product->quantity);
                            $set('price', $product->price);
//                            self::updateTotal($set, $get, $state);
                        }
                    })
                    ->reactive(),
                Fieldset::make('Ukuran')

                    ->visible(fn (Get $get) => $get('product_id') !== null)
                    ->schema([
                        Forms\Components\TextInput::make('size_s')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_m')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_l')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_xl')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_2xl')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_3xl')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),

                        Forms\Components\TextInput::make('size_4xl')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => self::updateTotal($set, $get, $state))
                            ->integer()
                            ->required(),
                    ])
                    ->columns(7),

                Forms\Components\FileUpload::make('file_design')
                    ->visible(function(){
                        $user = Auth::user();
                        if ($user->hasRole('designer')) {
                            return true;
                        }else{
                            return false;
                        }
                    })
                    ->disk('public')
                    ->image(),
                Forms\Components\TextInput::make('total_product')->default(0),
                Forms\Components\TextInput::make('sum_price')->default(0)->readOnly()->label('Total Harga'),
                Forms\Components\TextInput::make('total_time')->default(0)->label('Total Hari Pengerjaan'),
                Forms\Components\Textarea::make('keterangan'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sum_price')
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')->disk('public'),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\ImageColumn::make('file_design')->disk('public'),
                Tables\Columns\TextColumn::make('total_product'),
                Tables\Columns\TextColumn::make('product.price')->label('Harga Product'),
                Tables\Columns\TextColumn::make('sum_price')->label('Total Harga'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function() {
                        $user = Auth::user();
                        if ($user->hasRole('designer')) {
                            return false;
                        }else{
                            return true;
                        }
                    })
                ->after(function ($record,$livewire) {
                    $id  =$record->order_id;
                    $total_time = OrderDetail::where('order_id', $id)->sum('total_time');
                    $total_price = OrderDetail::where('order_id', $id)->sum('sum_price');
                    $order = Order::find($id);
                    $prioritas = false;
                    if ($total_time >= 5){
                        $prioritas = true;
                    }
                    // Update Transaksi record
                    $order->update([
                        'time' => $total_time,
                        'sum_price'=>$total_price,
                        'status' => 'designer',
                        'priority' => $prioritas
                    ]);

                    $livewire->dispatch('refresh');
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record,$livewire) {
                        $id  =$record->order_id;
                        $total_time = OrderDetail::where('order_id', $id)->sum('total_time');

                        $total_price = OrderDetail::where('order_id', $id)->sum('sum_price');
                        $total_product = OrderDetail::where('order_id', $id)->sum('total_product');
                        $order = Order::find($id);
//                        $now = $order->created_at;
//                        $end = Carbon::parse($now)->addDays((int)$total_time);

                        $user = Auth::user();
                        if ($user->hasRole('designer')) {
                            $order->update([
                                'time' => $total_time,
//                                'antrian' => $end,
                                'sum_price'=>$total_price,
                                'total_product'=>$total_product,
                                'status' => 'cutting'
                            ]);
                        }else{
                            $prioritas = false;
                            if ($total_time >= 5){
                                $prioritas = true;
                            }
                            $order->update([
                                'time' => $total_time,
//                                'antrian' => $end,
                                'sum_price'=>$total_price,
                                'priority' => $prioritas,
                                'total_product'=>$total_product,
                                'status' => 'designer'
                            ]);
                        }
                        $livewire->dispatch('refresh');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function() {
                        $user = Auth::user();
                        if ($user->hasRole('designer')) {
                            return false;
                        }else{
                            return true;
                        }
                    })
                    ->after(function ($record,$livewire) {
                        $id  =$record->order_id;
                        $total_time = OrderDetail::where('order_id', $id)->sum('total_time');
                        $total_product = OrderDetail::where('order_id', $id)->sum('total_product');
                        $total_price = OrderDetail::where('order_id', $id)->sum('sum_price');
                        $order = Order::find($id);
                        // Update Transaksi record
                        $prioritas = false;
                        if ($total_time >= 5){
                            $prioritas = true;
                        }
                        $order->update([
                            'time' => $total_time,
                            'sum_price'=>$total_price,
                            'total_product' => $total_product,
                            'priority' => $prioritas
                        ]);
                        $livewire->dispatch('refresh');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
