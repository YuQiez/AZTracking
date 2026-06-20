<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use Tests\TestCase;

class CustomerModelTest extends TestCase
{
    public function test_table_and_fillable_and_relations()
    {
        $model = new Customer();

        $this->assertEquals('customers', $model->getTable());
        $this->assertEquals(['name', 'email', 'phone'], $model->getFillable());

        $relation = $model->orders();
        $this->assertIsObject($relation);
    }
}
