<?php

namespace Tests\Unit\Models;

use App\Models\Status;
use Tests\TestCase;

class StatusModelTest extends TestCase
{
    public function test_table_fillable_and_relations()
    {
        $model = new Status();

        $this->assertEquals('statuses', $model->getTable());
        $this->assertEquals(['name', 'display_name', 'order', 'last_updated_by'], $model->getFillable());

        $this->assertIsObject($model->lastUpdatedBy());
        $this->assertIsObject($model->orders());
    }
}
