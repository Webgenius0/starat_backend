<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'influencer']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'retailer']);
        Role::create(['name' => 'store']);
        Role::create(['name' => 'advertiser']);

        // Assigning all permissions
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->syncPermissions(Permission::all());
        // Assigning influencer permissions
        $influencerRole = Role::where('name', 'influencer')->first();
        $influencerRole->syncPermissions([
            'setting menu',
            'influencerVideo menu',
            'create influencerVideo',
            'edit influencerVideo',
            'delete influencerVideo',
            'setting menu',
            'tag menu',
        ]);
        // Assigning user permissions
        $userRole = Role::where('name', 'user')->first();
        $userRole->syncPermissions([
            'likeVideo menu',
            'delete likeVideo',
            'setting menu',
        ]);

        // Assigning retailer permissions
        $influencerRole = Role::where('name', 'retailer')->first();
        $influencerRole->syncPermissions([
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
            'setting menu',
            'tag menu',
            'create avdetisement menu',
        ]);

        // Assigning store permissions
        $storeRole = Role::where('name', 'store')->first();
        $storeRole->syncPermissions([
            'portal menu',
            'setting menu',
            'productCategory menu',
            'product menu',
            'influencer menu',
            'store menu',
            'edit store',
            'influencerVideo menu',
            'create influencerVideo',
            'edit influencerVideo',
            'delete influencerVideo',
            'setting menu',
            'qrCode menu',
            'create qrCode',
            'tag menu',
        ]);

        // Assigning Advertaiser permissions
        $storeRole = Role::where('name', 'advertiser')->first();
        $storeRole->syncPermissions([
            'avdetisement profile',
            'create avdetisement menu',
            'create avdetisement',
        ]);

        //User Assign admin role
        $admin_user = User::where('email', 'admin@admin.com')->first();
        if ($admin_user) {
            $admin_user->assignRole('admin');
        } else {
            $admin_user = User::create([
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('12345678'),
            ]);
            $admin_user->assignRole('admin');
        }
        //User Assign user role
        $user = User::where('email', 'user@user.com')->first();
        if ($user) {
            $user->assignRole('user');
        } else {
            $admin_user = User::create([
                'name' => 'user',
                'username' => 'user',
                'email' => 'user@user.com',
                'password' => bcrypt('12345678'),
            ]);
            $admin_user->assignRole('user');
        }
    }
}
