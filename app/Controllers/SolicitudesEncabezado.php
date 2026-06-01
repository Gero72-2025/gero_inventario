<?php

namespace App\Controllers;

use App\Models\SolicitudesEncabezadoModel;
use App\Models\DivisionesModel;
use App\Models\EtapaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class SolicitudesEncabezado extends BaseController
{
    protected $solicitudesModel;
    protected $divisionesModel;
    protected $etapasModel;
    protected $uploadPath;

    public function __construct()
    {
        $this->solicitudesModel = new SolicitudesEncabezadoModel();
        $this->divisionesModel = new DivisionesModel();
        $this->etapasModel = new EtapaModel();
        $this->uploadPath = WRITEPATH . 'uploads/solicitudes/';

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));
            $solicitudes = $this->solicitudesModel->getSolicitudesConRelaciones($searchTerm);

            return view('solicitudes-encabezado/index', [
                'solicitudes' => $solicitudes,
                'pager'       => $this->solicitudesModel->pager,
                'searchTerm'  => $searchTerm,
            ]);
        } catch (Throwable $e) {
            log_message('error', 'SolicitudesEncabezado::index - ' . $e->getMessage());
            throw $e;
        }
    }

    public function create()
    {
        try {
            $divisiones = $this->divisionesModel->where('status', 'activo')->where('deleted_at', null)->findAll();
            $etapas = $this->etapasModel->where('status', 'activo')->where('deleted_at', null)->findAll();

            return view('solicitudes-encabezado/create', [
                'title'      => 'Nueva Solicitud Encabezado',
                'heading'    => 'Crear Nueva Solicitud Encabezado',
                'divisiones' => $divisiones,
                'etapas'     => $etapas,
                'submitLabel' => 'Crear',
            ]);
        } catch (Throwable $e) {
            return redirect()->to('solicitudes-encabezado')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    public function store()
    {
        try {
            $validation = $this->validate([
                'id_division'          => 'required|integer|greater_than[0]',
                'id_etapa'             => 'required|integer|greater_than[0]',
                'folio_fisico'         => 'required|string|max_length[255]',
                'monto_estimado_total' => 'required|decimal|greater_than[0]',
                'pdf_firmado'          => 'uploaded[pdf_firmado]|mime_in[pdf_firmado,application/pdf]|max_size[pdf_firmado,5120]',
                'status'               => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $pdfPath = null;
            $pdfFile = $this->request->getFile('pdf_firmado');
            if ($pdfFile && $pdfFile->isValid()) {
                $newName = $pdfFile->getRandomName();
                $pdfFile->move($this->uploadPath, $newName);
                $pdfPath = 'solicitudes/' . $newName;
            }

            $idUsuario = session('user_id');

            $data = [
                'id_division'          => (int) $this->request->getPost('id_division'),
                'id_etapa'             => (int) $this->request->getPost('id_etapa'),
                'folio_fisico'         => $this->request->getPost('folio_fisico'),
                'monto_estimado_total' => (float) $this->request->getPost('monto_estimado_total'),
                'pdf_firmado_path'     => $pdfPath,
                'status'               => $this->request->getPost('status'),
                'id_usuario_solicita'  => $idUsuario,
                'id_usuario_creo'      => $idUsuario,
            ];

            $inserted = $this->solicitudesModel->insert($data);

            if ($inserted === false) {
                return redirect()->back()->withInput()
                    ->with('error', 'Error al crear el registro.');
            }

            return redirect()->to('solicitudes-encabezado')
                ->with('success', 'Solicitud encabezado creada exitosamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function ver($id)
    {
        try {
            $solicitud = $this->solicitudesModel->find($id);

            if (!$solicitud) {
                throw PageNotFoundException::forPageNotFound();
            }

            return view('solicitudes-encabezado/show', [
                'solicitud' => $solicitud,
                'title'     => 'Detalles de Solicitud Encabezado',
                'heading'   => 'Detalles de Solicitud Encabezado',
            ]);
        } catch (Throwable $e) {
            return redirect()->to('solicitudes-encabezado')
                ->with('error', 'Solicitud encabezado no encontrada.');
        }
    }

    public function editar($id)
    {
        try {
            $solicitud = $this->solicitudesModel->find($id);

            if (!$solicitud) {
                throw PageNotFoundException::forPageNotFound();
            }

            $divisiones = $this->divisionesModel->where('status', 'activo')->where('deleted_at', null)->findAll();
            $etapas = $this->etapasModel->where('status', 'activo')->where('deleted_at', null)->findAll();

            return view('solicitudes-encabezado/edit', [
                'solicitud'   => $solicitud,
                'divisiones'  => $divisiones,
                'etapas'      => $etapas,
                'title'       => 'Editar Solicitud Encabezado',
                'heading'     => 'Editar Solicitud Encabezado',
                'submitLabel' => 'Actualizar',
            ]);
        } catch (Throwable $e) {
            return redirect()->to('solicitudes-encabezado')
                ->with('error', 'Solicitud encabezado no encontrada.');
        }
    }

    public function actualizar($id)
    {
        try {
            $solicitud = $this->solicitudesModel->find($id);

            if (!$solicitud) {
                throw PageNotFoundException::forPageNotFound();
            }

            $validation = $this->validate([
                'id_division'          => 'required|integer|greater_than[0]',
                'id_etapa'             => 'required|integer|greater_than[0]',
                'folio_fisico'         => 'required|string|max_length[255]',
                'monto_estimado_total' => 'required|decimal|greater_than[0]',
                'pdf_firmado'          => 'permit_empty|uploaded[pdf_firmado]|mime_in[pdf_firmado,application/pdf]|max_size[pdf_firmado,5120]',
                'status'               => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $pdfPath = $solicitud['pdf_firmado_path'];
            $pdfFile = $this->request->getFile('pdf_firmado');

            if ($pdfFile && $pdfFile->isValid()) {
                if ($pdfPath && file_exists(WRITEPATH . 'uploads/' . $pdfPath)) {
                    unlink(WRITEPATH . 'uploads/' . $pdfPath);
                }
                $newName = $pdfFile->getRandomName();
                $pdfFile->move($this->uploadPath, $newName);
                $pdfPath = 'solicitudes/' . $newName;
            }

            $idUsuario = session('user_id');

            $data = [
                'id_division'          => (int) $this->request->getPost('id_division'),
                'id_etapa'             => (int) $this->request->getPost('id_etapa'),
                'folio_fisico'         => $this->request->getPost('folio_fisico'),
                'monto_estimado_total' => (float) $this->request->getPost('monto_estimado_total'),
                'pdf_firmado_path'     => $pdfPath,
                'status'               => $this->request->getPost('status'),
                'id_usuario_actualizo' => $idUsuario,
            ];

            $updated = $this->solicitudesModel->update($id, $data);

            if ($updated === false) {
                return redirect()->back()->withInput()
                    ->with('error', 'Error al actualizar el registro.');
            }

            return redirect()->to('solicitudes-encabezado')
                ->with('success', 'Solicitud encabezado actualizada exitosamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $solicitud = $this->solicitudesModel->find($id);

            if (!$solicitud) {
                throw PageNotFoundException::forPageNotFound();
            }

            $idUsuario = session('user_id');

            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->solicitudesModel->update($id, $data);

            if ($deleted === false) {
                return redirect()->back()
                    ->with('error', 'Error al eliminar el registro.');
            }

            return redirect()->to('solicitudes-encabezado')
                ->with('success', 'Solicitud encabezado eliminada exitosamente.');
        } catch (Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function descargarPdf($id)
    {
        try {
            $solicitud = $this->solicitudesModel->find($id);

            if (!$solicitud || !$solicitud['pdf_firmado_path']) {
                return redirect()->back()
                    ->with('error', 'Archivo PDF no encontrado.');
            }

            $filePath = WRITEPATH . 'uploads/' . $solicitud['pdf_firmado_path'];

            if (!file_exists($filePath)) {
                return redirect()->back()
                    ->with('error', 'El archivo PDF no existe en el servidor.');
            }

            return $this->response->download($filePath, null);
        } catch (Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error al descargar el PDF: ' . $e->getMessage());
        }
    }
}
