<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'role' => 'admin',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@collaborbit.com',
            'password' => 'admincollab123',
        ]);
        $users = [
            [
                'role' => 'user',
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo@example.com',
                'password' => 'Demo12345',
                'department' => 'Product Management',
                'bio' => 'Демонстрационный аккаунт Collaborbit. Здесь можно изучить проекты, задачи, команду и переписку.',
            ],

            // Команда Nice Design
            [
                'role' => 'user',
                'first_name' => 'Анна',
                'last_name' => 'Волкова',
                'email' => 'anna.volkova@nice-design.test',
                'password' => 'NiceDesign123',
                'department' => 'UI/UX Design',
                'bio' => 'Lead UI/UX designer в Nice Design. Отвечает за исследования, прототипы и дизайн-систему.',
            ],
            [
                'role' => 'user',
                'first_name' => 'Максим',
                'last_name' => 'Орлов',
                'email' => 'maxim.orlov@nice-design.test',
                'password' => 'NiceDesign123',
                'department' => 'Frontend Development',
                'bio' => 'Frontend-разработчик Nice Design. Переносит макеты в адаптивные пользовательские интерфейсы.',
            ],
            [
                'role' => 'user',
                'first_name' => 'София',
                'last_name' => 'Лебедева',
                'email' => 'sofia.lebedeva@nice-design.test',
                'password' => 'NiceDesign123',
                'department' => 'Graphic Design',
                'bio' => 'Графический дизайнер. Работает с айдентикой, иллюстрациями и визуальными материалами.',
            ],
            [
                'role' => 'user',
                'first_name' => 'Илья',
                'last_name' => 'Морозов',
                'email' => 'ilya.morozov@nice-design.test',
                'password' => 'NiceDesign123',
                'department' => 'Product Management',
                'bio' => 'Project manager Nice Design. Координирует команду, сроки и коммуникацию с заказчиками.',
            ],
            [
                'role' => 'user',
                'first_name' => 'Елена',
                'last_name' => 'Соколова',
                'email' => 'elena.sokolova@nice-design.test',
                'password' => 'NiceDesign123',
                'department' => 'Marketing',
                'bio' => 'Маркетолог Nice Design. Занимается позиционированием проектов и коммуникационной стратегией.',
            ],

            [
                'role' => 'admin',
                'first_name' => 'Collaborbit',
                'last_name' => 'Admin',
                'email' => 'admin@collaborbit.com',
                'password' => 'AdminCollab123',
                'department' => 'Engineering',
                'bio' => 'Администратор платформы Collaborbit.',
            ],
        ];

        foreach ($users as $attributes) {
            $user = User::firstOrNew([
                'email' => $attributes['email'],
            ]);
            $user->forceFill([
                ...$attributes,
                'email_verified_at' => now(),
            ])->save();
        }
    }
}
