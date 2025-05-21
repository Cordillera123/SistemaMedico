<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Obtener roles
        $adminRole = Role::where('nombre', 'administrador')->first();
        $doctorRole = Role::where('nombre', 'doctor')->first();
        $pacienteRole = Role::where('nombre', 'paciente')->first();
        
        // Crear el administrador
        $admin = User::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'email' => 'admin@hospital.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'activo' => true,
            'email_verified_at' => now()
        ]);
        
        // Crear doctores
        $doctor1 = User::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'doctor@hospital.com',
            'username' => 'drperez',
            'password' => Hash::make('password'),
            'role_id' => $doctorRole->id,
            'activo' => true,
            'email_verified_at' => now()
        ]);
        
        Doctor::create([
            'user_id' => $doctor1->id,
            'especialidad' => 'Cardiología',
            'licencia_medica' => 'MED-12345',
            'biografia' => 'Doctor especializado en cardiología con más de 10 años de experiencia.',
            'horario_consulta' => 'Lunes a Viernes: 8:00 - 15:00'
        ]);
        
        $doctor2 = User::create([
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'email' => 'doctora@hospital.com',
            'username' => 'dragomez',
            'password' => Hash::make('password'),
            'role_id' => $doctorRole->id,
            'activo' => true,
            'email_verified_at' => now()
        ]);
        
        Doctor::create([
            'user_id' => $doctor2->id,
            'especialidad' => 'Pediatría',
            'licencia_medica' => 'MED-67890',
            'biografia' => 'Doctora especializada en pediatría con amplia experiencia en atención infantil.',
            'horario_consulta' => 'Lunes a Jueves: 9:00 - 17:00'
        ]);
        
        // Crear pacientes
        $paciente1 = User::create([
            'nombre' => 'Pedro',
            'apellido' => 'Sánchez',
            'email' => 'paciente@hospital.com',
            'username' => 'pedro',
            'password' => Hash::make('password'),
            'role_id' => $pacienteRole->id,
            'activo' => true,
            'email_verified_at' => now()
        ]);
        
        Paciente::create([
            'user_id' => $paciente1->id,
            'doctor_id' => 1, // Asignado al primer doctor
            'fecha_nacimiento' => '1985-05-15',
            'genero' => 'Masculino',
            'tipo_sangre' => 'O+',
            'alergias' => 'Penicilina',
            'antecedentes_medicos' => 'Hipertensión controlada'
        ]);
        
        $paciente2 = User::create([
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'email' => 'paciente2@hospital.com',
            'username' => 'ana',
            'password' => Hash::make('password'),
            'role_id' => $pacienteRole->id,
            'activo' => true,
            'email_verified_at' => now()
        ]);
        
        Paciente::create([
            'user_id' => $paciente2->id,
            'doctor_id' => 2, // Asignado al segundo doctor
            'fecha_nacimiento' => '1990-10-20',
            'genero' => 'Femenino',
            'tipo_sangre' => 'A+',
            'alergias' => 'Ninguna',
            'antecedentes_medicos' => 'Sin antecedentes relevantes'
        ]);
    }
}