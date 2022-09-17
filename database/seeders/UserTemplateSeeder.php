<?php

namespace Database\Seeders;

use App\Models\UserTemplate;
use Illuminate\Database\Seeder;

class UserTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserTemplate::create([
            'hp' => '1234567890987654321',
            'template_id' => 1,
            'reply_id' => 1,
        ]);

        UserTemplate::create([
            'hp' => '1234567890987654321',
            'template_id' => 2,
            'reply_id' => 2,
        ]);

        UserTemplate::create([
            'hp' => '1234567890987654321',
            'template_id' => 3,
            'reply_id' => 3,
        ]);

        UserTemplate::create([
            'hp' => '1234567890987654321',
            'template_id' => 11,
            'reply_id' => 4,
        ]);
    }
}
