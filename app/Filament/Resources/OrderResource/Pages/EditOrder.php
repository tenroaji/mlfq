<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class EditOrder extends EditRecord
{

    public $antrian,$time,$status;


    protected static string $resource = OrderResource::class;


    public function mount($record): void
    {
        parent::mount($record);
    }

    #[On('refresh')]
    public function refresh(): void
    {
        $this->refreshFormData([
            'antrian','time','sum_price','status',
        ]);
    }

//    #[On('refreshForm')]
//    public function refreshForm(): void
//    {
//        parent::refreshFormData(array_keys($this->record->toArray()));
//    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(function() {
                    $user = Auth::user();
                    if ($user->hasRole('designer')) {
                        return false;
                    }else{
                        return true;
                    }
                }),
        ];
    }
}
