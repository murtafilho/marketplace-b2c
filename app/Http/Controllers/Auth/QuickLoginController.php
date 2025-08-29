<?php
/**
 * Arquivo: app/Http/Controllers/Auth/QuickLoginController.php
 * Descrição: Controller para login rápido via URL para testes
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class QuickLoginController extends Controller
{
    /**
     * Realiza logout e login rápido com credenciais da URL
     */
    public function quickLogin(Request $request)
    {
        // Se já estiver logado, fazer logout primeiro
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Verificar se tem email e senha na URL
        $email = $request->get('email');
        $password = $request->get('password');

        if (!$email || !$password) {
            return redirect('/login');
        }

        // Tentar fazer login
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirecionar baseado no role
            switch($user->role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'seller':
                    return redirect()->intended('/seller/dashboard');
                case 'customer':
                default:
                    return redirect()->intended('/');
            }
        }

        // Se falhar, redirecionar para login com erro
        return redirect('/login')
            ->withErrors(['email' => 'Credenciais inválidas'])
            ->withInput(['email' => $email]);
    }

    /**
     * Realiza logout e redireciona para login
     */
    public function forceLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Logout realizado com sucesso');
    }
}