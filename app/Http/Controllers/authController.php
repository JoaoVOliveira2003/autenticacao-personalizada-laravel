<?php

namespace App\Http\Controllers;

use App\Mail\NewUserConfirmation;
use Illuminate\Support\Facades\Auth;
use App\Models\User3;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\resetPassword;


class authController extends Controller
{

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $dados)
    {


        Auth::logout();

        //verifica dados
        $credenciais = $dados->validate(
            [
                'username' => 'required|string|min:3|max:30',
                'password' => 'required|string|min:3|max:32',
            ],
            [
                'username.required' => 'O nome de usuário é obrigatório.',
                'username.string'   => 'O nome de usuário deve ser um texto.',
                'username.min'      => 'O nome de usuário deve ter no mínimo 3 caracteres.',
                'username.max'      => 'O nome de usuário deve ter no máximo 30 caracteres.',

                'password.required' => 'A senha é obrigatória.',
                'password.string'   => 'A senha deve ser um texto.',
                'password.min'      => 'A senha deve ter no mínimo 8 caracteres.',
                'password.max'      => 'A senha deve ter no máximo 32 caracteres.',
            ]
        );

        // maneira simples de se fazer uma verificação

        // if(Auth::attempt($credenciais)){
        //     $dados->session()->regenerate();
        //     return redirect()->route('home');
        // }

        // verificar usuario

        $user = User3::where('username', $credenciais['username'])
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '<=', now());
            })
            ->whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->first();

        /*
            poderia ser isso
            $user = User::fromQuery(
                'SELECT * FROM users
                WHERE username = ?
                AND active = 1
                AND (blocked_ultil IS NULL OR blocked_ultil <= NOW())
                AND email_verified_at IS NOT NULL
                AND deleted_at IS NULL
                LIMIT 1',
                [$credenciais['username']]
            )->first();
        */

        if (!$user) {
            return back()->withInput()->with(['invalid_login' => 'login invalido1']);
        }

        // testa senha
        // if (!password_verify(password: $credenciais['password'], $user->password)) {
        if ($credenciais['password'] !== $user->password) {
            return back()->withInput()->with(['invalid_login' => 'login invalido2']);
        }


        // atualiza last_login
        $user->last_login = now();
        $user->save();

        // login propiamente dito
        Auth::login($user);
        $dados->session()->regenerate();



        // redirecionar
        return redirect()->intended(route('home'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function register()
    {
        return view('auth.register');
    }



    public function store_user(Request $dados)
    {
        $dados->validate(
            [
                'username' => 'required|min:3|max:30|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:32|string',
                'password_confirmation' => 'required|same:password',
            ],
            [
                'username.required' => 'O nome de usuário é obrigatório.',
                'username.min' => 'O nome de usuário deve ter no mínimo 3 caracteres.',
                'username.max' => 'O nome de usuário pode ter no máximo 30 caracteres.',
                'username.unique' => 'Este nome de usuário já está em uso.',

                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um endereço de e-mail válido.',
                'email.unique' => 'Este e-mail já está cadastrado.',

                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
                'password.max' => 'A senha pode ter no máximo 32 caracteres.',
                'password.string' => 'A senha deve ser um texto válido.',

                'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
                'password_confirmation.same' => 'A confirmação de senha não confere com a senha.',
            ]
        );



        $user = new User3();
        $user->username = $dados->username;
        $user->email    = $dados->email;
        $user->password = $dados->password;
        $user->token = Str::random(64);

        // gerar link
        $link = route('new_user_confirmation', ['token' => $user->token]);


        // enviar email
        $resultado = Mail::to($user->email)->send(new NewUserConfirmation($dados->username, $link));


        // gaurdar
        if (!$resultado) {
            return back()->withInput()->with([
                'server_error' => 'Ocorreu um b.o ao enviar email'
            ]);
        }

        $user->save();

        //    view de sucesso
        return view('auth.email_sent', ['email' => $user->email, 'link' => $user->token]);
    }

    public function new_user_confirmation($token)
    {
        $user = User3::where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        $user->email_verified_at = Carbon::now();
        $user->token  = null;
        $user->active = true;
        $user->save();

        Auth::login($user);
        return view('auth.new_user_confirmation');
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function charge_password(Request $dados)
    {

        $dados->validate(
            [
                'current_password' => 'required|string|min:3|max:32',
                'new_password' => 'required|string|min:3|max:32',
                'new_password_confirmation' => 'required|same:new_password',

            ],
            [
                'password.required' => 'A senha atual é obrigatória.',
                'password.string'   => 'A senha atual deve ser um texto válido.',
                'password.min'      => 'A senha atual deve ter no mínimo :min caracteres.',
                'password.max'      => 'A senha atual deve ter no máximo :max caracteres.',

                'new_password.required' => 'A nova senha é obrigatória.',
                'new_password.string'   => 'A nova senha deve ser um texto válido.',
                'new_password.min'      => 'A nova senha deve ter no mínimo :min caracteres.',
                'new_password.max'      => 'A nova senha deve ter no máximo :max caracteres.',

                'new_password_confirmation.required' => 'A confirmação da nova senha é obrigatória.',
                'new_password_confirmation.same'     => 'A confirmação da nova senha deve ser igual à nova senha.',
            ]
        );


        if ($dados->current_password != Auth::user()->password) {
            return back()->with(['server_error' => 'a senha n esta correta']);
        }

        // atualizar na base de dados

        $user = Auth::user();
        $user->password = $dados->new_password;
        $user->save();


        return redirect()->route('profile')->with([
            'success' => 'a senha foi atualizada.'
        ]);
    }

    public function forgot_password()
    {
        return view('auth.forget_password');
    }

    public function reset_password_update(Request $dados)
    {

        $dados->validate(
            [
                'email' => 'required|email'
            ],
            [
                'email.required' => 'O e-mail é obrigatório.',
                'email.email'    => 'Informe um e-mail válido.',
            ]
        );

        $mensagem = 'Verifique a sua caixa de correio caso tenha um email cadastrado';

        $user = User3::where('email', $dados->email)->first();

        if (!$user) {
            return back()->with(['server_message' => $mensagem . '1']);
        }

        $user->token = Str::random(65);

        $token_link = route('reset_password', ['token' => $user->token]);
        $result = Mail::to($user->email)->send(new ResetPassword($user->username, $token_link));


        if (!$result) {
            return back()->with(['server_message' => $mensagem . '2']);
        }

        $user->save();

        return back()->with(['server_message' => $mensagem . '3']);
    }

    public function reset_password($token)
    {
        $user = User3::where('token', $token)->first();


        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.reset_password', ['token' => $token]);
    }

    public function atualizacao_password(Request $dados)
    {
        $user = User3::where('token', operator: $dados->token)->first();

        // if(!$user){
        //     return redirect()->route('login');
        // }

        dd($dados->token);
        return;

        $user->password = $dados->new_password;
        $user->token = null;
        $user->save();

        return redirect()->route('login')->with(['sucess' => true]);
    }

    public function apagarConta(Request $dados)
    {

        $dados->validate(
            [
                'texto' => 'required|string|in:apagar',
            ],
            [
                'texto.required' => 'Texto obrigatório.',
                'texto.in'       => 'O texto deve ser exatamente "apagar".',
            ]
        );

        $id = Auth::id();

        if (User3::where('id', $id)->delete()) {
            return redirect()->route('login')->with(['sucess' => true]);
        } else {
            return back()->with(key: ['server_message' => 'erro ao pagar conta']);
        }
    }
}
