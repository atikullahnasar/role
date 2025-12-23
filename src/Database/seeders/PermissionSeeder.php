<?php

namespace atikullahnasar\role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            //User Permissions
            ['group_name' => 'User', 'name' => 'user.manage'],
            ['group_name' => 'User', 'name' => 'user.create'],
            ['group_name' => 'User', 'name' => 'user.edit'],
            ['group_name' => 'User', 'name' => 'user.delete'],

            //Blog Permissions
            ['group_name' => 'Blog', 'name' => 'blog.manage'],
            ['group_name' => 'Blog', 'name' => 'blog.create'],
            ['group_name' => 'Blog', 'name' => 'blog.edit'],
            ['group_name' => 'Blog', 'name' => 'blog.delete'],

            //Faq Permissions
            ['group_name' => 'Faq', 'name' => 'faq.manage'],
            ['group_name' => 'Faq', 'name' => 'faq.create'],
            ['group_name' => 'Faq', 'name' => 'faq.edit'],
            ['group_name' => 'Faq', 'name' => 'faq.delete'],

            //Testimonials Permissions
            ['group_name' => 'Testimonial', 'name' => 'testimonial.manage'],
            ['group_name' => 'Testimonial', 'name' => 'testimonial.create'],
            ['group_name' => 'Testimonial', 'name' => 'testimonial.edit'],
            ['group_name' => 'Testimonial', 'name' => 'testimonial.delete'],

            //Settings Permissions
            ['group_name' => 'Setting', 'name' => 'setting.create'],
            ['group_name' => 'Setting', 'name' => 'setting.manage'],
            ['group_name' => 'Setting', 'name' => 'setting.edit'],
            ['group_name' => 'Setting', 'name' => 'setting.delete'],
        ];

        DB::table('beft_permissions')->insert($permissions);
    }
}
