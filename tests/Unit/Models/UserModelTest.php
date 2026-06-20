<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_fillable_and_relations()
    {
        $model = new User();

        $this->assertEquals(['name', 'email', 'password'], $model->getFillable());

        $this->assertIsObject($model->statuses());
    }
}
