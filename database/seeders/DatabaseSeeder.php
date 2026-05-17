<?php

namespace Database\Seeders;

use App\Models\AcademicBlock;
use App\Models\ProjectGroup;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'name' => 'Admin Teacher',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        foreach ([3, 4, 5] as $blockNumber) {
            $block = AcademicBlock::updateOrCreate(['name' => 'Block ' . $blockNumber]);

            for ($groupNumber = 1; $groupNumber <= 8; $groupNumber++) {
                ProjectGroup::updateOrCreate([
                    'academic_block_id' => $block->id,
                    'number' => $groupNumber,
                ]);
            }
        }
    }
}
