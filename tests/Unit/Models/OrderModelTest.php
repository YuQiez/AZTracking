<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    public function test_table_fillable_and_relations()
    {
        $model = new Order();

        $this->assertEquals('orders', $model->getTable());
        $this->assertEquals(['name', 'address', 'customer_id', 'status_id'], $model->getFillable());

        $this->assertIsObject($model->customer());
        $this->assertIsObject($model->statuses());
    }
}
