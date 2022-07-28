<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public $customer;
    public $ordered_product;
    public $order;

    public function __construct($order, $customer, $ordered_product)
    {
        //parent::__construct($resource);
        $this->customer = $customer;
        $this->ordered_product = $ordered_product;
        $this->order = $order;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'order' => $this->order,
            'customer' => $this->customer,
            'ordered_product' => $this->ordered_product,
        ];
    }
}
