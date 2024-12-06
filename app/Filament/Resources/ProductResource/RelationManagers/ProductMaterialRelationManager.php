<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductMaterialRelationManager extends RelationManager
{
    protected static string $relationship = 'productMaterial';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('material_id')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('material','name')
                    ,
                Forms\Components\TextInput::make('quantity')
                ->required()
                ->integer()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('material.name'),
                Tables\Columns\TextColumn::make('quantity'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record,$livewire) {
                        $id  =$record->order_id;
                        $material_id  =$record->material_id;
                        $total = ProductMaterial::where('order_id', $id)->sum('quantity');
                        $order = Material::find($material_id);
                        if ($order) {
                            // Decrease the quantity by the total quantity
                            $order->update([
                                'quantity' => $order->quantity - $total,
                            ]);
                        }

                        $livewire->dispatch('refresh');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
