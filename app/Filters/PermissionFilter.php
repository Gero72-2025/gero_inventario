<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = (bool) (session('is_logged_in') ?? false);
        if (! $isLoggedIn) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Debes iniciar sesion para acceder a esta pagina.');
        }

        $router = service('router');
        $controllerName = (string) ($router->controllerName() ?? '');
        $methodName = (string) ($router->methodName() ?? '');

        if ($controllerName === '' || $methodName === '') {
            return null;
        }

        $parts = explode('\\', $controllerName);
        $shortController = end($parts) ?: '';
        if ($shortController === '') {
            return null;
        }

        $nombrePermiso = $shortController . '::' . $methodName;
        $userId = (int) (session('user_id') ?? 0);

        if ($userId <= 0) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Sesion no valida.');
        }

        try {
            $db = db_connect();
            $tienePermiso = $db->table('usuarios u')
                ->select('p.id')
                ->join('rol_permisos rp', 'rp.id_rol = u.id_rol AND rp.deleted_at IS NULL', 'inner')
                ->join('permisos p', 'p.id = rp.id_permiso AND p.deleted_at IS NULL', 'inner')
                ->where('u.id', $userId)
                ->where('u.deleted_at', null)
                ->where('p.nombre_permiso', $nombrePermiso)
                ->limit(1)
                ->get()
                ->getFirstRow('array');

            if ($tienePermiso === null) {
                $body = view('errors/access_denied', [
                    'pageTitle'     => 'Acceso denegado',
                    'nombrePermiso' => $nombrePermiso,
                    'usuarioAlias'  => (string) (session('user_alias') ?? ''),
                ]);

                return service('response')
                    ->setStatusCode(403)
                    ->setBody($body);
            }

            return null;
        } catch (Throwable $e) {
            $body = view('errors/access_denied', [
                'pageTitle'     => 'Acceso denegado',
                'nombrePermiso' => $nombrePermiso,
                'usuarioAlias'  => (string) (session('user_alias') ?? ''),
            ]);

            return service('response')
                ->setStatusCode(500)
                ->setBody($body);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
