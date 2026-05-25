<?php

namespace App\Controllers;

use App\Models\AgregadoEjerciciosFiscalesModel;
use App\Models\EjerciciosFiscalesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class AgregadoEjerciciosFiscales extends BaseController
{
    protected AgregadoEjerciciosFiscalesModel $agregadoModel;
    protected EjerciciosFiscalesModel $ejerciciosFiscalesModel;

    public function __construct()
    {
        $this->agregadoModel = new AgregadoEjerciciosFiscalesModel();
        $this->ejerciciosFiscalesModel = new EjerciciosFiscalesModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->agregadoModel
                ->select('agregado_ejercicios_fiscales.*, cat_ejercicios_fiscales.anio')
                ->join('cat_ejercicios_fiscales', 'cat_ejercicios_fiscales.id = agregado_ejercicios_fiscales.id_ejercicio_fiscal')
                ->where('agregado_ejercicios_fiscales.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_ejercicios_fiscales.anio', $searchTerm)
                    ->orLike('agregado_ejercicios_fiscales.justificacion', $searchTerm)
                    ->groupEnd();
            }

            $agregados = $builder
                ->orderBy('agregado_ejercicios_fiscales.id', 'DESC')
                ->paginate(10);

            return view('agregado_ejercicios_fiscales/index', [
                'agregados'  => $agregados,
                'pager'      => $this->agregadoModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('error', 'No fue posible cargar el listado de agregados.');
        }
    }

    public function create()
    {
        try {
            $ejerciciosFiscales = $this->ejerciciosFiscalesModel
                ->where('deleted_at', null)
                ->orderBy('anio', 'DESC')
                ->findAll();

            if (empty($ejerciciosFiscales)) {
                throw new RuntimeException('No hay ejercicios fiscales disponibles.');
            }

            return view('agregado_ejercicios_fiscales/create', [
                'ejerciciosFiscales' => $ejerciciosFiscales,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('error', 'No fue posible cargar el formulario de creación.');
        }
    }

    public function store()
    {
        $rules = [
            'id_ejercicio_fiscal' => 'required|integer|is_not_unique[cat_ejercicios_fiscales.id]',
            'monto'               => 'required|decimal',
            'justificacion'       => 'permit_empty|string',
            'status'              => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->agregadoModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el agregado.');
            }

            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('success', 'Agregado creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el agregado.');
        }
    }

    public function edit(int $id)
    {
        try {
            $agregado = $this->agregadoModel->find($id);

            if (! $agregado) {
                throw PageNotFoundException::forPageNotFound('El agregado no existe.');
            }

            $ejerciciosFiscales = $this->ejerciciosFiscalesModel
                ->where('deleted_at', null)
                ->orderBy('anio', 'DESC')
                ->findAll();

            return view('agregado_ejercicios_fiscales/edit', [
                'agregado'           => $agregado,
                'ejerciciosFiscales' => $ejerciciosFiscales,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('error', 'No fue posible cargar el agregado.');
        }
    }

    public function update(int $id)
    {
        try {
            $agregado = $this->agregadoModel->find($id);

            if (! $agregado) {
                throw PageNotFoundException::forPageNotFound('El agregado no existe.');
            }

            $rules = [
                'id_ejercicio_fiscal' => 'required|integer|is_not_unique[cat_ejercicios_fiscales.id]',
                'monto'               => 'required|decimal',
                'justificacion'       => 'permit_empty|string',
                'status'              => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->agregadoModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el agregado.');
            }

            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('success', 'Agregado actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el agregado.');
        }
    }

    public function delete(int $id)
    {
        try {
            $agregado = $this->agregadoModel->find($id);

            if (! $agregado) {
                throw PageNotFoundException::forPageNotFound('El agregado no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->agregadoModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->agregadoModel->delete($id);

            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('success', 'Agregado eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('agregado-ejercicios-fiscales'))
                ->with('error', 'No fue posible eliminar el agregado.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'id_ejercicio_fiscal'  => (int) $this->request->getPost('id_ejercicio_fiscal'),
            'monto'                => (float) $this->request->getPost('monto'),
            'justificacion'        => $this->request->getPost('justificacion') ?? '',
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
