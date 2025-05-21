<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use App\Models\LogSistema;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Procesar intento de login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Buscar al usuario por username o email
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        // Si no existe el usuario
        if (!$user) {
            LogSistema::registrar('Intento de login fallido: usuario no existe', 'users', null, $request->username);
            return redirect()->back()
                ->withErrors(['username' => 'Credenciales incorrectas'])
                ->withInput($request->except('password'));
        }

        // Si el usuario no está activo
        if (!$user->activo) {
            LogSistema::registrar('Intento de login fallido: usuario inactivo', 'users', $user->id);
            return redirect()->back()
                ->withErrors(['username' => 'Esta cuenta está desactivada. Contacte al administrador.'])
                ->withInput($request->except('password'));
        }

        // Si el usuario está bloqueado
        if ($user->estaBloqueado()) {
            LogSistema::registrar('Intento de login fallido: usuario bloqueado', 'users', $user->id);
            return redirect()->back()
                ->withErrors(['username' => 'Esta cuenta está temporalmente bloqueada debido a múltiples intentos fallidos. Por favor, intente más tarde.'])
                ->withInput($request->except('password'));
        }

        // Intentar autenticar
        if (!Hash::check($request->password, $user->password)) {
            // Incrementar intentos fallidos
            $intentos = $user->incrementarIntentosFallidos();
            
            // Máximo de intentos permitidos
            $maxIntentos = ConfiguracionSistema::obtenerValor('max_intentos_login', 3);
            
            // Si superó el máximo de intentos, bloquear la cuenta
            if ($intentos >= $maxIntentos) {
                $tiempoBloqueo = ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30);
                $user->bloquear($tiempoBloqueo);
                
                LogSistema::registrar('Usuario bloqueado por múltiples intentos fallidos', 'users', $user->id);
                
                return redirect()->back()
                    ->withErrors(['username' => "Su cuenta ha sido bloqueada por {$tiempoBloqueo} minutos debido a múltiples intentos fallidos."])
                    ->withInput($request->except('password'));
            }
            
            LogSistema::registrar('Intento de login fallido: contraseña incorrecta', 'users', $user->id);
            
            return redirect()->back()
                ->withErrors(['username' => 'Credenciales incorrectas'])
                ->withInput($request->except('password'));
        }

        // Login exitoso, resetear intentos fallidos
        $user->desbloquear();
        Auth::login($user, $request->has('remember'));
        
        LogSistema::registrar('Login exitoso', 'users', $user->id);
        
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogSistema::registrar('Logout', 'users', Auth::id());
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesar solicitud de recuperación de contraseña
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
        }

        if (!$user->activo) {
            return back()->withErrors(['email' => 'Esta cuenta está desactivada. Contacte al administrador.']);
        }

        // Generar token de recuperación
        $token = $user->generarTokenRecuperacion();
        
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));
        
        // Enviar correo con token (simulado aquí - implementar con Mail)
        // Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));
        
        LogSistema::registrar('Solicitud de recuperación de contraseña', 'users', $user->id);
        
        return back()->with('status', 'Se ha enviado un enlace para restablecer su contraseña al correo electrónico proporcionado.');
    }

    /**
     * Mostrar formulario para restablecer contraseña
     */
    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Correo electrónico no proporcionado.']);
        }
        
        $user = User::where('email', $email)->first();
        
        if (!$user || !$user->validarTokenRecuperacion($token)) {
            return redirect()->route('password.request')->withErrors(['email' => 'El enlace para restablecer la contraseña es inválido o ha expirado.']);
        }
        
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !$user->validarTokenRecuperacion($request->token)) {
            return redirect()->route('password.request')->withErrors(['email' => 'El enlace para restablecer la contraseña es inválido o ha expirado.']);
        }
        
        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->limpiarTokenRecuperacion();
        $user->desbloquear(); // Por si estaba bloqueado por intentos fallidos
        $user->save();
        
        LogSistema::registrar('Restablecimiento de contraseña exitoso', 'users', $user->id);
        
        return redirect()->route('login')->with('status', 'Su contraseña ha sido restablecida correctamente. Puede iniciar sesión ahora.');
    }

    /**
     * Redireccionar basado en el rol del usuario
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->isPaciente()) {
            return redirect()->route('paciente.dashboard');
        } else {
            // Rol desconocido
            Auth::logout();
            return redirect()->route('login')->withErrors(['role' => 'Rol de usuario no válido.']);
        }
    }
}