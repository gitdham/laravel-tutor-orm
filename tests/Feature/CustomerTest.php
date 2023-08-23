<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Wallet;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\VirtualAccountSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CustomerTest extends TestCase {
    public function test_one_to_one() {
        $this->seed(CustomerSeeder::class);
        $this->seed(WalletSeeder::class);

        $customer = Customer::find('IDHAM');
        $this->assertNotNull($customer);

        $wallet = $customer->wallet;
        $this->assertNotNull($wallet);
        $this->assertEquals(1000000, $wallet->amount);

        Log::info(json_encode($customer));
    }

    public function test_one_to_one_query() {
        $customer = new Customer();
        $customer->id = 'JET';
        $customer->name = 'Jet';
        $customer->email = 'jet@mail.com';
        $customer->save();
        $this->assertNotNull($customer);

        $wallet = new Wallet();
        $wallet->amount = 1000000;
        $customer->wallet()->save($wallet);
        $this->assertNotNull($wallet->customer_id);
    }

    public function test_has_one_through() {
        $this->seed([CustomerSeeder::class, WalletSeeder::class, VirtualAccountSeeder::class]);

        $customer = Customer::find('IDHAM');
        $this->assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        $this->assertNotNull($virtualAccount);
        $this->assertEquals('BCA', $virtualAccount->bank);
    }
}
