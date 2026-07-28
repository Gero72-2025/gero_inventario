<?php

namespace App\Controllers;

use App\Models\PresupuestosDivisionModel;
use App\Models\EjerciciosFiscalesModel;
use App\Models\DivisionesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class PresupuestosDivisiones extends BaseController
{
    protected PresupuestosDivisionModel $presupuestosDivisionModel;
    protected EjerciciosFiscalesModel $ejerciciosFiscalesModel;
    protected DivisionesModel $divisionesModel;

    public function __construct()
    {
        $this->presupuestosDivisionModel = new PresupuestosDivisionModel();
        $this->ejerciciosFiscalesModel = new EjerciciosFiscalesModel();
        $this->divisionesModel = new DivisionesModel();
    }

    public function index()
    {
        $searchTerm = trim((string) $this->request->getGet('q'));
        $presupuestosDivisiones = $this->presupuestosDivisionModel->getPresupuestosDivisionesConRelaciones($searchTerm);

        return view('presupuestos_divisiones/index', [
            'presupuestosDivisiones' => $presupuestosDivisiones,
            'pager'                  => $this->presupuestosDivisionModel->pager,
            'searchTerm'             => $searchTerm,
        ]);
    }

    public function create()
    {
        try {
            $ejerciciosFiscales = $this->ejerciciosFiscalesModel
                ->where('deleted_at', null)
                ->where('status', 'activo')
                ->orderBy('anio', 'DESC')
                ->findAll();

            $ejerciciosFiscalesConDisponible = [];
            foreach ($ejerciciosFiscales as $ejercicio) {
                $sumaAgregado = (float) db_connect()
                    ->table('agregado_ejercicios_fiscales')
                    ->selectSum('monto')
                    ->where('id_ejercicio_fiscal', $ejercicio['id'])
                    ->where('deleted_at', null)
                    ->get()
                    ->getRow()
                    ->monto ?? 0;

                $sumaUsada = (float) db_connect()
                    ->table('presupuestos_division')
                    ->selectSum('monto_asignado')
                    ->where('id_ejercicio', $ejercicio['id'])
                    ->where('deleted_at', null)
                    ->get()
                    ->getRow()
                    ->monto_asignado ?? 0;

                $ejercicio['disponible'] = (float) $ejercicio['presupuesto_total'] + $sumaAgregado - $sumaUsada;
                $ejerciciosFiscalesConDisponible[] = $ejercicio;
            }

            $divisiones = $this->divisionesModel
                ->where('deleted_at', null)
                ->where('status', 'activo')
                ->orderBy('nombre_division', 'ASC')
                ->findAll();

            return view('presupuestos_divisiones/create', [
                'ejerciciosFiscales' => $ejerciciosFiscalesConDisponible,
                'divisiones'         => $divisiones,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('error', 'No fue posible cargar el formulario de creación.');
        }
    }

    public function store()
    {
        $rules = [
            'id_ejercicio'   => 'required|integer|greater_than[0]',
            'id_division'    => 'required|integer|greater_than[0]',
            'monto_asignado' => 'required|decimal',
            'status'         => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $idEjercicio = (int) $this->request->getPost('id_ejercicio');
            $idDivision = (int) $this->request->getPost('id_division');
            $montoAsignado = (float) $this->request->getPost('monto_asignado');

            $ejercicio = $this->ejerciciosFiscalesModel->find($idEjercicio);
            if (! $ejercicio) {
                throw new RuntimeException('El ejercicio fiscal no existe.');
            }

            $presupuestoTotalEjercicio = (float) $ejercicio['presupuesto_total'];

            $sumaAgregado = (float) db_connect()
                ->table('agregado_ejercicios_fiscales')
                ->selectSum('monto')
                ->where('id_ejercicio_fiscal', $idEjercicio)
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->monto ?? 0;

            $sumaActualEjercicio = (float) db_connect()
                ->table('presupuestos_division')
                ->selectSum('monto_asignado')
                ->where('id_ejercicio', $idEjercicio)
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->monto_asignado ?? 0;

            $presupuestoDisponible = $presupuestoTotalEjercicio + $sumaAgregado - $sumaActualEjercicio;

            if ($montoAsignado > $presupuestoDisponible) {
                return redirect()->back()->withInput()
                    ->with('error', 'Monto excede el presupuesto disponible. Disponible: Q ' . number_format($presupuestoDisponible, 2));
            }

            $data = $this->extractRequestData($montoAsignado);

            if (! $this->presupuestosDivisionModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el presupuesto de división.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;
            $idPresupuestoCreado = (int) $this->presupuestosDivisionModel->insertID();
            $this->presupuestosDivisionModel->recalcularSaldosPorGrupo($idPresupuestoCreado, $idUsuario);

            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('success', 'Presupuesto de división creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el presupuesto de división.');
        }
    }

    public function edit(int $id)
    {
        try {
            $presupuestoDivision = $this->presupuestosDivisionModel->find($id);

            if (! $presupuestoDivision) {
                throw PageNotFoundException::forPageNotFound('El presupuesto de división no existe.');
            }

            $ejerciciosFiscales = $this->ejerciciosFiscalesModel
                ->where('deleted_at', null)
                ->where('status', 'activo')
                ->orderBy('anio', 'DESC')
                ->findAll();

            $ejerciciosFiscalesConDisponible = [];
            foreach ($ejerciciosFiscales as $ejercicio) {
                $sumaAgregado = (float) db_connect()
                    ->table('agregado_ejercicios_fiscales')
                    ->selectSum('monto')
                    ->where('id_ejercicio_fiscal', $ejercicio['id'])
                    ->where('deleted_at', null)
                    ->get()
                    ->getRow()
                    ->monto ?? 0;

                $sumaUsada = (float) db_connect()
                    ->table('presupuestos_division')
                    ->selectSum('monto_asignado')
                    ->where('id_ejercicio', $ejercicio['id'])
                    ->where('id !=', $id)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRow()
                    ->monto_asignado ?? 0;

                $ejercicio['disponible'] = (float) $ejercicio['presupuesto_total'] + $sumaAgregado - $sumaUsada;
                $ejerciciosFiscalesConDisponible[] = $ejercicio;
            }

            $divisiones = $this->divisionesModel
                ->where('deleted_at', null)
                ->where('status', 'activo')
                ->orderBy('nombre_division', 'ASC')
                ->findAll();

            return view('presupuestos_divisiones/edit', [
                'presupuestoDivision' => $presupuestoDivision,
                'ejerciciosFiscales'  => $ejerciciosFiscalesConDisponible,
                'divisiones'          => $divisiones,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('error', 'No fue posible cargar el presupuesto de división.');
        }
    }

    public function update(int $id)
    {
        try {
            $presupuestoDivision = $this->presupuestosDivisionModel->find($id);

            if (! $presupuestoDivision) {
                throw PageNotFoundException::forPageNotFound('El presupuesto de división no existe.');
            }

            $idEjercicioAnterior = (int) $presupuestoDivision['id_ejercicio'];
            $idDivisionAnterior = (int) $presupuestoDivision['id_division'];

            $rules = [
                'id_ejercicio'   => 'required|integer|greater_than[0]',
                'id_division'    => 'required|integer|greater_than[0]',
                'monto_asignado' => 'required|decimal',
                'status'         => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $montoAsignado = (float) $this->request->getPost('monto_asignado');
            $idEjercicio = (int) $this->request->getPost('id_ejercicio');
            $idDivision = (int) $this->request->getPost('id_division');

            $ejercicio = $this->ejerciciosFiscalesModel->find($idEjercicio);
            if (! $ejercicio) {
                throw new RuntimeException('El ejercicio fiscal no existe.');
            }

            $presupuestoTotalEjercicio = (float) $ejercicio['presupuesto_total'];

            $sumaAgregado = (float) db_connect()
                ->table('agregado_ejercicios_fiscales')
                ->selectSum('monto')
                ->where('id_ejercicio_fiscal', $idEjercicio)
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->monto ?? 0;

            $sumaActualEjercicio = (float) db_connect()
                ->table('presupuestos_division')
                ->selectSum('monto_asignado')
                ->where('id_ejercicio', $idEjercicio)
                ->where('id !=', $id)
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->monto_asignado ?? 0;

            $presupuestoDisponible = $presupuestoTotalEjercicio + $sumaAgregado - $sumaActualEjercicio;

            if ($montoAsignado > $presupuestoDisponible) {
                return redirect()->back()->withInput()
                    ->with('error', 'Monto excede el presupuesto disponible. Disponible: Q ' . number_format($presupuestoDisponible, 2));
            }

            $data = $this->extractRequestData($montoAsignado);
            unset($data['id_usuario_creo']);

            if (! $this->presupuestosDivisionModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el presupuesto de división.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            if ($idEjercicioAnterior !== $idEjercicio || $idDivisionAnterior !== $idDivision) {
                $this->presupuestosDivisionModel->recalcularGrupo($idEjercicioAnterior, $idDivisionAnterior, $idUsuario);
            }

            $this->presupuestosDivisionModel->recalcularGrupo($idEjercicio, $idDivision, $idUsuario);

            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('success', 'Presupuesto de división actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el presupuesto de división.');
        }
    }

    public function delete(int $id)
    {
        try {
            $presupuestoDivision = $this->presupuestosDivisionModel->find($id);

            if (! $presupuestoDivision) {
                throw PageNotFoundException::forPageNotFound('El presupuesto de división no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->presupuestosDivisionModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->presupuestosDivisionModel->delete($id);
            $this->presupuestosDivisionModel->recalcularGrupo((int) $presupuestoDivision['id_ejercicio'], (int) $presupuestoDivision['id_division'], $idUsuario);

            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('success', 'Presupuesto de división eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('presupuestos-divisiones'))
                ->with('error', 'No fue posible eliminar el presupuesto de división.');
        }
    }

    private function extractRequestData(float $montoAsignado): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'id_ejercicio'         => (int) $this->request->getPost('id_ejercicio'),
            'id_division'          => (int) $this->request->getPost('id_division'),
            'monto_asignado'       => $montoAsignado,
            'saldo_actual'         => $montoAsignado,
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
