<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status) {
            Status::query()->firstOrCreate(['name' => $status]);
        }
    }
}
