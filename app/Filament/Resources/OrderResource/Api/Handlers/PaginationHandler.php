<?php
namespace App\Filament\Resources\OrderResource\Api\Handlers;

use App\Filament\Resources\OrderResource\Api\Transformers\OrderTransformer;
use App\Utils\ResponseUtils;
use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\OrderResource;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = OrderResource::class;


    public function handler()
    {
        $query = static::getEloquentQuery()->with('orderDetail')
            ->orderBy('priority', 'desc')
            ->orderBy('time','desc')
            ->orderBy('created_at', 'asc');
        $model = static::getModel();

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

//        return ResponseUtils::success($query, 'Load data success');
        return ResponseUtils::success(OrderTransformer::collection($query), 'Load data success');

//        return static::getApiTransformer()::collection($query);
    }
}
