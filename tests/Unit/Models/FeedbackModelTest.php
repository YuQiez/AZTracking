<?php

namespace Tests\Unit\Models;

use App\Models\Feedback;
use Tests\TestCase;

class FeedbackModelTest extends TestCase
{
    public function test_table_and_fillable()
    {
        $model = new Feedback();

        $this->assertEquals('feedback', $model->getTable());
        $this->assertEquals(['name', 'message'], $model->getFillable());
    }
}
