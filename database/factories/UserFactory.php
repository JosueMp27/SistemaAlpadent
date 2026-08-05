<?php

/**
 * Documentacion de archivo:
 * Factory de pruebas; genera datos falsos consistentes para tests o seeders.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
/**
 * Documentacion de clase:
 * Factory de pruebas; genera datos falsos consistentes para tests o seeders.
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /**
     * Documentacion: Define datos falsos de factory.
     * Como lo hace: Devuelve valores generados por Faker para crear modelos durante pruebas o seeders.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    /**
     * Documentacion: Ejecuta la operacion unverified.
     * Como lo hace: Factory de pruebas; genera datos falsos consistentes para tests o seeders.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
