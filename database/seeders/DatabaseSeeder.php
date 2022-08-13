<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(CustomerStatusSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(OrderStatusSeeder::class);
    }
}
