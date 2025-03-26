<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'setting menu',
            'portal menu',
            'create portal',
            'edit portal',
            'delete portal',
            'qrCode menu',
            'create qrCode',
            'productCategory menu',
            'create productCategory',
            'edit productCategory',
            'delete productCategory',
            'product menu',
            'create product',
            'edit product',
            'delete product',
            'store menu',
            'create store',
            'edit store',
            'delete store',
            'influencer menu',
            'influencerVideo menu',
            'create influencerVideo',
            'edit influencerVideo',
            'delete influencerVideo',
            'tag menu',
            'create avdetisement menu',
            'likeVideo menu',
            'delete likeVideo',
            'avdetisement profile',
            'create avdetisement',
        ];

        // Insert unique permissions into the database
        foreach (array_unique($permissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
