<?php

namespace App\Controllers;

use App\Models\RolModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Usuarios extends BaseController
{
    protected RolModel $rolModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->rolModel = new RolModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function login()
    {
        return view('usuarios/login');
    }

    public function authenticate()
    {
        $rules = [
            'usuario'  => 'required|max_length[100]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioInput = trim((string) $this->request->getPost('usuario'));
        $password = (string) $this->request->getPost('password');

        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->groupStart()
                    ->where('email', $usuarioInput)
                    ->orWhere('alias', $usuarioInput)
                ->groupEnd()
                ->first();

            if (! $usuario || ! password_verify($password, (string) $usuario['password'])) {
                return redirect()->back()->withInput()->with('error', 'Credenciales invalidas.');
            }

            $this->usuarioModel->update((int) $usuario['id'], [
                'esta_conectado'       => 1,
                'ultimo_login'         => date('Y-m-d H:i:s'),
                'id_usuario_actualizo' => (int) $usuario['id'],
            ]);

            session()->regenerate(true);

            session()->set([
                'user_id'      => (int) $usuario['id'],
                'user_alias'   => (string) $usuario['alias'],
                'user_email'   => (string) $usuario['email'],
                'is_logged_in' => true,
            ]);

            return redirect()->to(base_url('usuarios'));
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'No fue posible iniciar sesion.');
        }
    }

    public function forgotPassword()
    {
        return view('usuarios/forgot_password');
    }

    public function sendRecovery()
    {
        $rules = [
            'email' => 'required|valid_email|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email'));

        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->where('email', $email)
                ->first();

            if (! $usuario) {
                return redirect()->back()->with('success', 'Si el correo existe, recibiras instrucciones de recuperacion.');
            }

            $token = bin2hex(random_bytes(32));

            $this->usuarioModel->update((int) $usuario['id'], [
                'token_recuperacion'   => $token,
                'id_usuario_actualizo' => (int) $usuario['id'],
            ]);

            $resetUrl = base_url('reset-password?token=' . urlencode($token));

            $emailService = service('email');
            $emailService->setTo($email);
            $emailService->setSubject('Recuperacion de contrasena');
            $emailService->setMessage(
                "Hola {$usuario['alias']},\n\n" .
                "Para restablecer tu contrasena usa el siguiente enlace:\n" .
                $resetUrl . "\n\n" .
                'Si no solicitaste este cambio, ignora este mensaje.'
            );

            if (! $emailService->send()) {
                return redirect()->back()->withInput()->with('error', 'No fue posible enviar el correo de recuperacion.');
            }

            return redirect()->to(base_url('login'))
                ->with('success', 'Se envio un correo de recuperacion.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible procesar la recuperacion de contrasena.');
        }
    }

    public function resetPassword()
    {
        $token = trim((string) $this->request->getGet('token'));

        if ($token === '' || strlen($token) > 255) {
            return redirect()->to(base_url('login'))->with('error', 'Token de recuperacion invalido.');
        }

        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->where('token_recuperacion', $token)
                ->first();

            if (! $usuario) {
                return redirect()->to(base_url('login'))->with('error', 'Token de recuperacion invalido o expirado.');
            }

            return view('usuarios/reset_password', [
                'token' => $token,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('login'))->with('error', 'No fue posible validar el token de recuperacion.');
        }
    }

    public function updatePassword()
    {
        $rules = [
            'token'            => 'required|max_length[255]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = trim((string) $this->request->getPost('token'));
        $password = (string) $this->request->getPost('password');

        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->where('token_recuperacion', $token)
                ->first();

            if (! $usuario) {
                return redirect()->to(base_url('login'))->with('error', 'Token de recuperacion invalido o expirado.');
            }

            $this->usuarioModel->update((int) $usuario['id'], [
                'password'             => password_hash($password, PASSWORD_DEFAULT),
                'token_recuperacion'   => null,
                'id_usuario_actualizo' => (int) $usuario['id'],
            ]);

            return redirect()->to(base_url('login'))->with('success', 'Contrasena actualizada correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'No fue posible actualizar la contrasena.');
        }
    }

    public function logout()
    {
        $userId = (int) (session('user_id') ?? 0);

        try {
            if ($userId > 0) {
                $this->usuarioModel->update($userId, [
                    'esta_conectado'       => 0,
                    'id_usuario_actualizo' => $userId,
                ]);
            }

            session()->remove(['user_id', 'user_alias', 'user_email', 'is_logged_in']);
            session()->destroy();

            return redirect()->to(base_url('login'))->with('success', 'Sesion cerrada correctamente.');
        } catch (Throwable $e) {
            session()->destroy();
            return redirect()->to(base_url('login'))->with('error', 'Sesion cerrada con advertencias.');
        }
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->usuarioModel
                ->select('usuarios.*, cat_roles.nombre_rol AS tipo_rol')
                ->join('cat_roles', 'cat_roles.id = usuarios.id_rol AND cat_roles.deleted_at IS NULL', 'left')
                ->where('usuarios.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('usuarios.alias', $searchTerm)
                    ->orLike('usuarios.email', $searchTerm)
                    ->groupEnd();
            }

            $usuarios = $builder
                ->orderBy('usuarios.id', 'DESC')
                ->paginate(10);

            return view('usuarios/index', [
                'usuarios'   => $usuarios,
                'pager'      => $this->usuarioModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('login'))
                ->with('error', 'No fue posible cargar el listado de usuarios.');
        }
    }

    public function create()
    {
        return view('usuarios/create', [
            'roles' => $this->getRolesOptions(),
        ]);
    }

    public function store()
    {
        $rules = [
            'alias'      => 'required|max_length[50]',
            'email'      => 'required|valid_email|max_length[100]|is_unique[usuarios.email]',
            'password'   => 'required|min_length[8]|max_length[255]',
            'id_rol'     => 'required|integer|is_not_unique[cat_roles.id]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        $data = [
            'alias'             => trim((string) $this->request->getPost('alias')),
            'email'             => trim((string) $this->request->getPost('email')),
            'password'          => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'token_recuperacion'=> $this->nullableText($this->request->getPost('token_recuperacion')),
            'esta_conectado'    => (int) ($this->request->getPost('esta_conectado') ?? 0),
            'ultimo_login'      => $this->nullableDateTime($this->request->getPost('ultimo_login')),
            'id_rol'               => $this->nullableInt($this->request->getPost('id_rol')),
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];

        try {
            $this->usuarioModel->insert($data);

            return redirect()->to(base_url('usuarios'))
                ->with('success', 'Usuario creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el usuario.');
        }
    }

    public function edit(int $id)
    {
        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->find($id);

            if (! $usuario) {
                throw PageNotFoundException::forPageNotFound('El usuario no existe.');
            }

            return view('usuarios/edit', [
                'usuario' => $usuario,
                'roles'   => $this->getRolesOptions(),
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('usuarios'))
                ->with('error', 'No fue posible cargar el usuario.');
        }
    }

    public function update(int $id)
    {
        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->find($id);

            if (! $usuario) {
                throw PageNotFoundException::forPageNotFound('El usuario no existe.');
            }

            $rules = [
                'email'      => 'required|valid_email|max_length[100]|is_unique[usuarios.email,id,' . $id . ']',
                'password'   => 'permit_empty|min_length[8]|max_length[255]',
                'id_rol'     => 'required|integer|is_not_unique[cat_roles.id]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuarioActual = (int) (session('user_id') ?? 0);
            $idUsuarioActual = $idUsuarioActual > 0 ? $idUsuarioActual : null;

            $data = [
                'email'                => trim((string) $this->request->getPost('email')),
                'id_rol'               => $this->nullableInt($this->request->getPost('id_rol')),
                'id_usuario_actualizo' => $idUsuarioActual,
            ];

            $password = (string) $this->request->getPost('password');
            if ($password !== '') {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->usuarioModel->update($id, $data);

            return redirect()->to(base_url('usuarios'))
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el usuario.');
        }
    }

    public function delete(int $id)
    {
        try {
            $usuario = $this->usuarioModel
                ->where('deleted_at', null)
                ->find($id);

            if (! $usuario) {
                throw PageNotFoundException::forPageNotFound('El usuario no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->usuarioModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->usuarioModel->delete($id);

            return redirect()->to(base_url('usuarios'))
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('usuarios'))
                ->with('error', 'No fue posible eliminar el usuario.');
        }
    }

    private function nullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private function nullableDateTime($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $normalized = str_replace('T', ' ', $text);
        if (strlen($normalized) === 16) {
            $normalized .= ':00';
        }

        return $normalized;
    }

    private function getRolesOptions(): array
    {
        try {
            return $this->rolModel
                ->where('deleted_at', null)
                ->orderBy('nombre_rol', 'ASC')
                ->findAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}
