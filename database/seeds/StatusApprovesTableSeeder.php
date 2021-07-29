<?php

use Illuminate\Database\Seeder;

use App\Models\StatusApprove;

class StatusApprovesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            [
                'code' => 'progress',
                'name' => 'On Progress',
                'description' => 'Sedang Dalam Proses'
            ],
            [
                'code' => 'waiting',
                'name' => 'Menunggu',
                'description' => 'Menunggu Persetujuan'
            ],
            [
                'code' => 'approved',
                'name' => 'Disetujui',
                'description' => 'Disetujui'
            ],
            [
                'code' => 'rejected',
                'name' => 'Ditolak',
                'description' => 'Ditolak'
            ],
        ];

        foreach ($status as $key => $item) {
            StatusApprove::create($item);
        }
    }
}
