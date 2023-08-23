<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoucherTest extends TestCase {
    public function test_create_voucher() {
        $voucher = new Voucher();
        $voucher->name = 'Sample Voucher';
        $voucher->voucher_code = '123123123';
        $voucher->save();

        $this->assertNotNull($voucher->id);
    }

    public function test_create_voucher_uuid() {
        $voucher = new Voucher();
        $voucher->name = 'Sample Voucher';
        $voucher->save();

        $this->assertNotNull($voucher->id);
        $this->assertNotNull($voucher->voucher_code);
    }

    public function test_soft_delete_voucher() {
        $this->seed(VoucherSeeder::class);

        $voucher = Voucher::where('name', 'Sample Voucher')->first();
        $voucher->delete();

        // get voucher after delete
        $voucher = Voucher::where('name', 'Sample Voucher')->first();
        $this->assertNull($voucher);

        // get voucher with trashed
        $voucher = Voucher::withTrashed()->where('name', 'Sample Voucher')->first();
        $this->assertNotNull($voucher);
    }

    public function test_local_scope() {
        $voucher = new Voucher();
        $voucher->name = 'Sample Voucher';
        $voucher->is_active = true;
        $voucher->save();

        $total = Voucher::active()->count();
        $this->assertEquals(1, $total);
        

        $total = Voucher::nonActive()->count();
        $this->assertEquals(0, $total);
    }
}
