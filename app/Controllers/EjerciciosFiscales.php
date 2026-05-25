<?php

namespace App\Controllers;

use App\Models\EjerciciosFiscalesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class EjerciciosFiscales extends BaseController
{
    protected EjerciciosFiscalesModel $ejerciciosFiscalesModel;

    public function __construct()
    {
        $this->ejerciciosFiscalesModel = new EjerciciosFiscalesModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->ejerciciosFiscalesModel
                ->where('cat_ejercicios_fiscales.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_ejercicios_fiscales.anio', $searchTerm)
                    ->groupEnd();
            }

            $ejerciciosFiscales = $builder
                ->orderBy('cat_ejercicios_fiscales.anio', 'DESC')
                ->paginate(10);

            return view('ejercicios_fiscales/index', [
                'ejerciciosFiscales' => $ejerciciosFiscales,
                'pager'              => $this->ejerciciosFiscalesModel->pager,
                'searchTerm'         => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('error', 'No fue posible cargar el listado de ejercicios fiscales.');
        }
    }

    public function create()
    {
        return view('ejercicios_fiscales/create');
    }

    public function store()
    {
        $rules = [
            'anio'              => 'required|integer|greater_than[1900]|less_than_equal_to[2999]|is_unique[cat_ejercicios_fiscales.anio]',
            'presupuesto_total' => 'required|decimal',
            'estado_abierto'    => 'permit_empty|in_list[0,1]',
            'status'            => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->ejerciciosFiscalesModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el ejercicio fiscal.');
            }

            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('success', 'Ejercicio fiscal creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el ejercicio fiscal.');
        }
    }

    public function edit(int $id)
    {
        try {
            $ejercicioFiscal = $this->ejerciciosFiscalesModel->find($id);

            if (! $ejercicioFiscal) {
                throw PageNotFoundException::forPageNotFound('El ejercicio fiscal no existe.');
            }

            return view('ejercicios_fiscales/edit', [
                'ejercicioFiscal' => $ejercicioFiscal,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('error', 'No fue posible cargar el ejercicio fiscal.');
        }
    }

    public function update(int $id)
    {
        try {
            $ejercicioFiscal = $this->ejerciciosFiscalesModel->find($id);

            if (! $ejercicioFiscal) {
                throw PageNotFoundException::forPageNotFound('El ejercicio fiscal no existe.');
            }

            $rules = [
                'anio'              => 'required|integer|greater_than[1900]|less_than_equal_to[2999]|is_unique[cat_ejercicios_fiscales.anio,id,' . $id . ']',
                'presupuesto_total' => 'required|decimal',
                'estado_abierto'    => 'permit_empty|in_list[0,1]',
                'status'            => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->ejerciciosFiscalesModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el ejercicio fiscal.');
            }

            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('success', 'Ejercicio fiscal actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el ejercicio fiscal.');
        }
    }

    public function delete(int $id)
    {
        try {
            $ejercicioFiscal = $this->ejerciciosFiscalesModel->find($id);

            if (! $ejercicioFiscal) {
                throw PageNotFoundException::forPageNotFound('El ejercicio fiscal no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->ejerciciosFiscalesModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->ejerciciosFiscalesModel->delete($id);

            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('success', 'Ejercicio fiscal eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('ejercicios-fiscales'))
                ->with('error', 'No fue posible eliminar el ejercicio fiscal.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'anio'                 => (int) $this->request->getPost('anio'),
            'presupuesto_total'    => (float) $this->request->getPost('presupuesto_total'),
            'estado_abierto'       => (bool) $this->request->getPost('estado_abierto') ? 1 : 0,
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
