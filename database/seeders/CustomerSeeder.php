<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $customer = new Customer();
        $customer->id = 'IDHAM';
        $customer->name = 'Idham';
        $customer->email = 'adzani234@gmail.com';
        $customer->save();
    }
}
