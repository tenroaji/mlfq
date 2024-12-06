<?php
namespace App\Filament\Resources\OrderResource\Api\Handlers;

use App\Utils\ResponseUtils;
use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\OrderResource;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = OrderResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return ResponseUtils::error("Error");

        $model->fill($request->all());

        $model->save();

        return ResponseUtils::success($model, 'Successfully Update Resource');
    }
}
