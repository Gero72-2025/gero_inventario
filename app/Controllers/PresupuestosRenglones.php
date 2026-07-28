<?php

namespace App\Controllers;

use App\Models\PresupuestoRenglonesModel;
use App\Models\PresupuestosDivisionModel;
use App\Models\RenglonesModel;
use CodeIgniter\Controller;

class PresupuestosRenglones extends Controller
{
    protected $presupuestoRenglonesModel;
    protected $presupuestosDivisionModel;
    protected $renglonesModel;
    protected $session;

    public function __construct()
    {
        $this->presupuestoRenglonesModel = new PresupuestoRenglonesModel();
        $this->presupuestosDivisionModel = new PresupuestosDivisionModel();
        $this->renglonesModel = new RenglonesModel();
        $this->session = session();
    }

    private function checkLogin()
    {
        if (!session('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }
        return null;
    }

    public function index()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $presupuestos = $this->presupuestoRenglonesModel->getPresupuestoRenglonesConRelaciones($searchTerm);

            return view('presupuesto_renglones/index', [
                'presupuestos' => $presupuestos,
                'pager'        => $this->presupuestoRenglonesModel->pager,
                'searchTerm'   => $searchTerm,
            ]);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al cargar los presupuestos: ' . $e->getMessage());
            return redirect()->to('/dashboard');
        }
    }

    public function create()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglones = $this->renglonesModel->where('deleted_at', null)->findAll();

            $db = \Config\Database::connect();
            $subquery = $db->table('presupuestos_division pd2')
                ->select('MAX(pd2.id) as id')
                ->where('pd2.deleted_at', null)
                ->where('pd2.status', 'activo')
                ->groupBy('pd2.id_ejercicio, pd2.id_division')
                ->getCompiledSelect();

            $presupuestos = $db->query(
                "SELECT DISTINCT pd.id, COALESCE(cef.anio, 0) as anio, COALESCE(cd.nombre_division, 'Sin división') as nombre_division, pd.saldo_actual, pd.monto_asignado
                FROM presupuestos_division pd
                LEFT JOIN cat_ejercicios_fiscales cef ON pd.id_ejercicio = cef.id
                LEFT JOIN cat_divisiones cd ON pd.id_division = cd.id
                WHERE pd.id IN ($subquery)
                ORDER BY cef.anio DESC, pd.id DESC"
            )->getResultArray();

            $data['title']       = 'Nuevo Presupuesto Renglón';
            $data['heading']     = 'Crear Nuevo Presupuesto Renglón';
            $data['renglones']   = $renglones;
            $data['presupuestos'] = $presupuestos;

            return view('presupuesto_renglones/create', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->to('presupuestos-renglones');
        }
    }

    public function store()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $validation = $this->validate([
                'id_presupuesto_division' => 'required|integer|greater_than[0]',
                'id_renglon'              => 'required|integer|greater_than[0]',
                'monto_asignado'          => 'required|decimal|greater_than[0]',
                'status'                  => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idPresupuesto = (int) $this->request->getPost('id_presupuesto_division');
            $idRenglon = (int) $this->request->getPost('id_renglon');
            $montoAsignado = (float) $this->request->getPost('monto_asignado');

            $presupuesto = $this->presupuestosDivisionModel->find($idPresupuesto);
            if (!$presupuesto) {
                $this->session->setFlashdata('error', 'Presupuesto de división no encontrado.');
                return redirect()->back()->withInput();
            }

            $renglon = $this->renglonesModel->find($idRenglon);
            if (!$renglon) {
                $this->session->setFlashdata('error', 'Renglón no encontrado.');
                return redirect()->back()->withInput();
            }

            $disponible = $this->presupuestoRenglonesModel->obtenerDisponiblePorPresupuesto($idPresupuesto);

            if ($montoAsignado > $disponible) {
                $this->session->setFlashdata('error', "Monto asignado no puede superar el disponible: " . number_format($disponible, 2));
                return redirect()->back()->withInput();
            }

            $idUsuario = session('user_id');
            $saldoActual = $montoAsignado;

            $data = [
                'id_presupuesto_division' => $idPresupuesto,
                'id_renglon'              => $idRenglon,
                'monto_asignado'          => $montoAsignado,
                'saldo_actual'            => $saldoActual,
                'status'                  => $this->request->getPost('status'),
                'id_usuario_creo'         => $idUsuario,
            ];

            $inserted = $this->presupuestoRenglonesModel->insert($data);

            if ($inserted === false) {
                $this->session->setFlashdata('error', 'Error al crear el registro.');
                return redirect()->back()->withInput();
            }

            $this->presupuestosDivisionModel->recalcularSaldosPorGrupo($idPresupuesto, $idUsuario);

            $this->session->setFlashdata('success', 'Presupuesto renglón creado exitosamente.');
            return redirect()->to('presupuestos-renglones');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al guardar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function ver($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $presupuesto = $this->presupuestoRenglonesModel->find($id);

            if (!$presupuesto) {
                throw new \Exception('Presupuesto renglón no encontrado.');
            }

            $data['presupuesto'] = $presupuesto;
            $data['title']       = 'Detalles de Presupuesto Renglón';
            $data['heading']     = 'Detalles de Presupuesto Renglón';

            if ($this->request->isAJAX()) {
                return view('presupuesto_renglones/show_modal', $data);
            }

            return view('presupuesto_renglones/show', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('presupuestos-renglones');
        }
    }

    public function editar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $presupuesto = $this->presupuestoRenglonesModel->find($id);

            if (!$presupuesto) {
                throw new \Exception('Presupuesto renglón no encontrado.');
            }

            $renglones = $this->renglonesModel->where('deleted_at', null)->findAll();

            $db = \Config\Database::connect();
            $subquery = $db->table('presupuestos_division pd2')
                ->select('MAX(pd2.id) as id')
                ->where('pd2.deleted_at', null)
                ->where('pd2.status', 'activo')
                ->groupBy('pd2.id_ejercicio, pd2.id_division')
                ->getCompiledSelect();

            $presupuestos = $db->query(
                "SELECT DISTINCT pd.id, COALESCE(cef.anio, 0) as anio, COALESCE(cd.nombre_division, 'Sin división') as nombre_division, pd.saldo_actual, pd.monto_asignado
                FROM presupuestos_division pd
                LEFT JOIN cat_ejercicios_fiscales cef ON pd.id_ejercicio = cef.id
                LEFT JOIN cat_divisiones cd ON pd.id_division = cd.id
                WHERE pd.id IN ($subquery)
                ORDER BY cef.anio DESC, pd.id DESC"
            )->getResultArray();

            $data['presupuesto']  = $presupuesto;
            $data['renglones']    = $renglones;
            $data['presupuestos'] = $presupuestos;
            $data['title']        = 'Editar Presupuesto Renglón';
            $data['heading']      = 'Editar Presupuesto Renglón';

            if ($this->request->isAJAX()) {
                return view('presupuesto_renglones/edit_modal', $data);
            }

            return view('presupuesto_renglones/edit', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('presupuestos-renglones');
        }
    }

    public function actualizar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $presupuesto = $this->presupuestoRenglonesModel->find($id);

            if (!$presupuesto) {
                throw new \Exception('Presupuesto renglón no encontrado.');
            }

            $validation = $this->validate([
                'id_presupuesto_division' => 'required|integer|greater_than[0]',
                'id_renglon'              => 'required|integer|greater_than[0]',
                'monto_asignado'          => 'required|decimal|greater_than[0]',
                'status'                  => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idPresupuesto = (int) $this->request->getPost('id_presupuesto_division');
            $montoAsignado = (float) $this->request->getPost('monto_asignado');
            $idRenglon = (int) $this->request->getPost('id_renglon');

            $pd = $this->presupuestosDivisionModel->find($idPresupuesto);
            if (!$pd) {
                $this->session->setFlashdata('error', 'Presupuesto de división no encontrado.');
                return redirect()->back()->withInput();
            }

            $renglon = $this->renglonesModel->find($idRenglon);
            if (!$renglon) {
                $this->session->setFlashdata('error', 'Renglón no encontrado.');
                return redirect()->back()->withInput();
            }

            $disponible = $this->presupuestoRenglonesModel->obtenerDisponiblePorPresupuesto($idPresupuesto);
            $montoAnterior = (float) $presupuesto['monto_asignado'];
            $disponibleConAnterior = $disponible + $montoAnterior;

            if ($montoAsignado > $disponibleConAnterior) {
                $this->session->setFlashdata('error', "Monto asignado no puede superar el disponible: " . number_format($disponibleConAnterior, 2));
                return redirect()->back()->withInput();
            }

            $idUsuario = session('user_id');
            $saldoActual = $montoAsignado;

            $data = [
                'id_presupuesto_division' => $idPresupuesto,
                'id_renglon'              => $idRenglon,
                'monto_asignado'          => $montoAsignado,
                'saldo_actual'            => $saldoActual,
                'status'                  => $this->request->getPost('status'),
                'id_usuario_actualizo'    => $idUsuario,
            ];

            $updated = $this->presupuestoRenglonesModel->update($id, $data);

            if ($updated === false) {
                $this->session->setFlashdata('error', 'Error al actualizar el registro.');
                return redirect()->back()->withInput();
            }

            $idPresupuestoAnterior = (int) $presupuesto['id_presupuesto_division'];

            if ($idPresupuestoAnterior !== $idPresupuesto) {
                $this->presupuestosDivisionModel->recalcularSaldosPorGrupo($idPresupuestoAnterior, $idUsuario);
            }

            $this->presupuestosDivisionModel->recalcularSaldosPorGrupo($idPresupuesto, $idUsuario);

            $this->session->setFlashdata('success', 'Presupuesto renglón actualizado exitosamente.');
            return redirect()->to('presupuestos-renglones');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $presupuesto = $this->presupuestoRenglonesModel->find($id);

            if (!$presupuesto) {
                throw new \Exception('Presupuesto renglón no encontrado.');
            }

            $idUsuario = session('user_id');
            $idPresupuesto = (int) $presupuesto['id_presupuesto_division'];
            $montoAsignado = (float) $presupuesto['monto_asignado'];

            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->presupuestoRenglonesModel->update($id, $data);

            if ($deleted === false) {
                if ($this->request->isAJAX()) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['success' => false, 'message' => 'Error al eliminar el registro.']);
                }
                $this->session->setFlashdata('error', 'Error al eliminar el registro.');
                return redirect()->back();
            }

            $presupuestoDivision = $this->presupuestosDivisionModel->find($idPresupuesto);
            if ($presupuestoDivision) {
                $this->presupuestosDivisionModel->recalcularSaldosPorGrupo($idPresupuesto, $idUsuario);
            }

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON(['success' => true, 'message' => 'Presupuesto renglón eliminado exitosamente.']);
            }

            $this->session->setFlashdata('success', 'Presupuesto renglón eliminado exitosamente.');
            return redirect()->to('presupuestos-renglones');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function getById($id)
    {
        try {
            $presupuesto = $this->presupuestoRenglonesModel->find($id);

            if (!$presupuesto) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Presupuesto renglón no encontrado.']);
            }

            return $this->response
                ->setStatusCode(200)
                ->setJSON(['success' => true, 'data' => $presupuesto]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerDisponible($idPresupuestoDivision)
    {
        try {
            $disponible = $this->presupuestoRenglonesModel->obtenerDisponiblePorPresupuesto($idPresupuestoDivision);

            return $this->response
                ->setStatusCode(200)
                ->setJSON(['success' => true, 'disponible' => $disponible]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
