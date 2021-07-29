<?php

use Illuminate\Database\Seeder;

use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'code' => 'superadmin',
                'name' => 'Super Admin',
                'description' => 'Akun untuk mengelola pengguna'
            ],
            [
                'code' => 'level_1',
                'name' => 'Level 1',
                'description' => 'Level 1'
            ],
            [
                'code' => 'level_2',
                'name' => 'Level 2',
                'description' => 'Level 2'
            ],
            [
                'code' => 'guest',
                'name' => 'Tamu',
                'description' => 'Pengguna Luar'
            ],
        ];

        foreach ($roles as $key => $role) {
            Role::create($role);
        }
    }
}
