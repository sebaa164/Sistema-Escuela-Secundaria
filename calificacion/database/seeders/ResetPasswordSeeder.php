<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class ResetPasswordSeeder extends Seeder
{
    public function run()
    {
        // Buscar el usuario por email o nombre
        $usuario = Usuario::where('email', 'like', '%facundo%')
            ->orWhere('nombre', 'like', '%facundo%')
            ->first();

        if ($usuario) {
            $usuario->update([
                'password' => Hash::make('password'),
                'estado' => 'activo'
            ]);
            
            $this->command->info('✅ Contraseña actualizada para: ' . $usuario->nombre_completo);
            $this->command->info('📧 Email: ' . $usuario->email);
            $this->command->info('🔑 Nueva contraseña: password');
        } else {
            $this->command->error('❌ No se encontró el usuario Facundo');
        }
    }
}
