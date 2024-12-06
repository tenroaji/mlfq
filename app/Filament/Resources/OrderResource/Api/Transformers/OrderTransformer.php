<?php
namespace App\Filament\Resources\OrderResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */



//    public function toArray($request)
//    {
//        $data['success'] = true;
////        $data['message'] = "Get data success";
////        $data['data']=$this->resource->toArray();
////        return $data;
//        return $this->resource->toArray();
//
//    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'alamat' => $this->alamat,
            'priority' => $this->priority,
            'time' => $this->time,
            'down_payment' => $this->down_payment,
            'sum_price' => $this->sum_price,
            'total_product' => $this->total_product,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'antrian' => $this->antrian,
            'finish' => $this->finish,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'order_detail' => $this->transformOrderDetails($this->orderDetail),
        ];
    }

    /**
     * Transform the order details.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|null  $orderDetails
     * @return array
     */
    private function transformOrderDetails($orderDetails)
    {
        if ($orderDetails) {
            return $orderDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'order_id' => $detail->order_id,
                    'product_id' => $detail->product_id,
                    'file_design' => $this->prependUrlToFileDesign($detail->file_design),
                    'status' => $detail->status,
                    'size_s' => $detail->size_s,
                    'quantity_size_s' => $detail->quantity_size_s,
                    'size_m' => $detail->size_m,
                    'quantity_size_m' => $detail->quantity_size_m,
                    'size_l' => $detail->size_l,
                    'quantity_size_l' => $detail->quantity_size_l,
                    'size_xl' => $detail->size_xl,
                    'quantity_size_xl' => $detail->quantity_size_xl,
                    'size_2xl' => $detail->size_2xl,
                    'quantity_size_2xl' => $detail->quantity_size_2xl,
                    'size_3xl' => $detail->size_3xl,
                    'quantity_size_3xl' => $detail->quantity_size_3xl,
                    'size_4xl' => $detail->size_4xl,
                    'quantity_size_4xl' => $detail->quantity_size_4xl,
                    'total_product' => $detail->total_product,
                    'quantity_total_product' => $detail->quantity_total_product,
                    'total_time' => $detail->total_time,
                    'sum_price' => $detail->sum_price,
                    'keterangan' => $detail->keterangan,
                    'created_at' => $detail->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $detail->updated_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
        }
        return []; // Return an empty array if order details are null
    }

    /**
     * Prepend the URL to the file design name.
     *
     * @param  string|null  $fileDesign
     * @return string|null
     */
    private function prependUrlToFileDesign($fileDesign)
    {
        if ($fileDesign) {
            $baseUrl = 'https://your-base-url.com/'; // Replace with your actual base URL
            return $baseUrl . $fileDesign;
        }
        return null; // Return null if no file design is present
    }
}
