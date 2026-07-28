<?php

namespace App\Controllers;

use App\Models\ContratosEmpleadosModel;
use App\Models\EmpleadosModel;
use App\Models\RenglonesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class ContratosEmpleados extends BaseController
{
    protected ContratosEmpleadosModel $contratosModel;
    protected EmpleadosModel $empleadosModel;
    protected RenglonesModel $renglonesModel;

    private string $uploadPath = WRITEPATH . 'uploads/contratos/';

    public function __construct()
    {
        $this->contratosModel = new ContratosEmpleadosModel();
        $this->empleadosModel = new EmpleadosModel();
        $this->renglonesModel = new RenglonesModel();

        // Crear directorio de subida si no existe
        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function index()
    {
        $searchTerm = trim((string) $this->request->getGet('q'));

        $builder = $this->contratosModel
            ->where('contratos_empleados.deleted_at', null)
            ->select('contratos_empleados.*, cat_empleados.nombre_completo')
            ->join('cat_empleados', 'contratos_empleados.id_empleado = cat_empleados.id', 'left');

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('contratos_empleados.numero_contrato', $searchTerm)
                ->orLike('cat_empleados.nombre_completo', $searchTerm)
                ->orLike('contratos_empleados.codigo_contrato', $searchTerm)
                ->orLike('contratos_empleados.expediente', $searchTerm)
                ->orLike('contratos_empleados.codigo_renglon', $searchTerm)
                ->groupEnd();
        }

        $contratos = $builder
            ->orderBy('contratos_empleados.id', 'DESC')
            ->paginate(10);

        return view('contratos-empleados/index', [
            'contratos' => $contratos,
            'pager'     => $this->contratosModel->pager,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function create()
    {
        $empleados = $this->empleadosModel
            ->where('status', 'activo')
            ->where('deleted_at', null)
            ->orderBy('nombre_completo', 'ASC')
            ->findAll();

        $renglones = $this->renglonesModel
            ->where('status', 'activo')
            ->where('deleted_at', null)
            ->orderBy('codigo_renglon', 'ASC')
            ->findAll();

        return view('contratos-empleados/create', [
            'empleados'  => $empleados,
            'renglones'  => $renglones,
        ]);
    }

    public function store()
    {
        $rules = [
            'numero_contrato'             => 'required|max_length[100]|is_unique[contratos_empleados.numero_contrato]',
            'expediente'                  => 'permit_empty|max_length[100]',
            'codigo_contrato'             => 'required|max_length[100]',
            'fecha_aceptacion_contrato'   => 'required|valid_date',
            'fecha_inicio'                => 'required|valid_date',
            'fecha_fin'                   => 'required|valid_date',
            'monto_contrato'              => 'required|decimal',
            'monto_texto'                 => 'permit_empty|max_length[255]',
            'cantidad_pagos'              => 'required|integer|greater_than[0]',
            'estado_contrato'             => 'required|in_list[vigente,vencido,rescindido]',
            'puente_financiamiento'       => 'permit_empty|max_length[255]',
            'renglon'                     => 'required|integer|is_not_unique[renglones.id]',
            'codigo_renglon'              => 'permit_empty|max_length[100]',
            'id_empleado'                 => 'required|integer|is_not_unique[cat_empleados.id]',
            'status'                      => 'permit_empty|in_list[activo,inactivo]',
            'pdf_contrato'                => 'uploaded[pdf_contrato]|mime_in[pdf_contrato,application/pdf]|max_size[pdf_contrato,5120]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();
        $pdfFile = $this->request->getFile('pdf_contrato');

        if ($pdfFile && $pdfFile->isValid()) {
            $newName = $pdfFile->getRandomName();
            if ($pdfFile->move($this->uploadPath, $newName)) {
                $data['pdf_contrato_path'] = 'contratos/' . $newName;
            }
        }

        try {
            if (! $this->contratosModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el contrato.');
            }

            return redirect()->to(base_url('contratos-empleados'))
                ->with('success', 'Contrato creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el contrato.');
        }
    }

    public function edit(int $id)
    {
        $contrato = $this->contratosModel->find($id);

        if (! $contrato) {
            throw PageNotFoundException::forPageNotFound('El contrato no existe.');
        }

        $empleados = $this->empleadosModel
            ->where('status', 'activo')
            ->where('deleted_at', null)
            ->orderBy('nombre_completo', 'ASC')
            ->findAll();

        $renglones = $this->renglonesModel
            ->where('status', 'activo')
            ->where('deleted_at', null)
            ->orderBy('codigo_renglon', 'ASC')
            ->findAll();

        return view('contratos-empleados/edit', [
            'contrato'  => $contrato,
            'empleados' => $empleados,
            'renglones' => $renglones,
        ]);
    }

    public function update(int $id)
    {
        try {
            $contrato = $this->contratosModel->find($id);

            if (! $contrato) {
                throw PageNotFoundException::forPageNotFound('El contrato no existe.');
            }

            $rules = [
                'numero_contrato'             => 'required|max_length[100]|is_unique[contratos_empleados.numero_contrato,id,' . $id . ']',
                'expediente'                  => 'permit_empty|max_length[100]',
                'codigo_contrato'             => 'required|max_length[100]',
                'fecha_aceptacion_contrato'   => 'required|valid_date',
                'fecha_inicio'                => 'required|valid_date',
                'fecha_fin'                   => 'required|valid_date',
                'monto_contrato'              => 'required|decimal',
                'monto_texto'                 => 'permit_empty|max_length[255]',
                'cantidad_pagos'              => 'required|integer|greater_than[0]',
                'estado_contrato'             => 'required|in_list[vigente,vencido,rescindido]',
                'puente_financiamiento'       => 'permit_empty|max_length[255]',
                'renglon'                     => 'required|integer|is_not_unique[renglones.id]',
                'codigo_renglon'              => 'permit_empty|max_length[100]',
                'id_empleado'                 => 'required|integer|is_not_unique[cat_empleados.id]',
                'status'                      => 'permit_empty|in_list[activo,inactivo]',
                'pdf_contrato'                => 'permit_empty|uploaded[pdf_contrato]|mime_in[pdf_contrato,application/pdf]|max_size[pdf_contrato,5120]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            $pdfFile = $this->request->getFile('pdf_contrato');

            if ($pdfFile && $pdfFile->isValid()) {
                // Eliminar archivo anterior si existe
                if (! empty($contrato['pdf_contrato_path'])) {
                    $oldPath = WRITEPATH . 'uploads/' . $contrato['pdf_contrato_path'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $newName = $pdfFile->getRandomName();
                if ($pdfFile->move($this->uploadPath, $newName)) {
                    $data['pdf_contrato_path'] = 'contratos/' . $newName;
                }
            }

            unset($data['id_usuario_creo']);

            if (! $this->contratosModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el contrato.');
            }

            return redirect()->to(base_url('contratos-empleados'))
                ->with('success', 'Contrato actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el contrato.');
        }
    }

    public function delete(int $id)
    {
        try {
            $contrato = $this->contratosModel->find($id);

            if (! $contrato) {
                throw PageNotFoundException::forPageNotFound('El contrato no existe.');
            }

            // Eliminar archivo PDF si existe
            if (! empty($contrato['pdf_contrato_path'])) {
                $filePath = WRITEPATH . 'uploads/' . $contrato['pdf_contrato_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->contratosModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->contratosModel->delete($id);

            return redirect()->to(base_url('contratos-empleados'))
                ->with('success', 'Contrato eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('contratos-empleados'))
                ->with('error', 'No fue posible eliminar el contrato.');
        }
    }

    public function descargarPdf(int $id)
    {
        try {
            $contrato = $this->contratosModel->find($id);

            if (! $contrato || empty($contrato['pdf_contrato_path'])) {
                throw PageNotFoundException::forPageNotFound('El archivo no existe.');
            }

            $filePath = WRITEPATH . 'uploads/' . $contrato['pdf_contrato_path'];

            if (! file_exists($filePath)) {
                throw PageNotFoundException::forPageNotFound('El archivo no se encontró en el servidor.');
            }

            return $this->response->download($filePath, null);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('contratos-empleados'))
                ->with('error', 'No fue posible descargar el archivo.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'numero_contrato'            => trim((string) $this->request->getPost('numero_contrato')),
            'expediente'                 => trim((string) $this->request->getPost('expediente')),
            'codigo_contrato'            => trim((string) $this->request->getPost('codigo_contrato')),
            'fecha_aceptacion_contrato'  => $this->request->getPost('fecha_aceptacion_contrato'),
            'fecha_inicio'               => $this->request->getPost('fecha_inicio'),
            'fecha_fin'                  => $this->request->getPost('fecha_fin'),
            'monto_contrato'             => (float) str_replace(',', '.', $this->request->getPost('monto_contrato')),
            'monto_texto'                => trim((string) $this->request->getPost('monto_texto')),
            'cantidad_pagos'             => (int) $this->request->getPost('cantidad_pagos'),
            'estado_contrato'            => $this->request->getPost('estado_contrato'),
            'puente_financiamiento'      => trim((string) $this->request->getPost('puente_financiamiento')),
            'renglon'                    => (int) $this->request->getPost('renglon'),
            'codigo_renglon'             => trim((string) $this->request->getPost('codigo_renglon')),
            'id_empleado'                => (int) $this->request->getPost('id_empleado'),
            'status'                     => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'            => $idUsuario,
            'id_usuario_actualizo'       => $idUsuario,
        ];
    }
}
