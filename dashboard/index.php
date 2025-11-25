<?php
$baseDir = realpath(__DIR__ . '/..');
$stagingDir = __DIR__ . '/jsons';

$managedFiles = [
    'floorings' => [
        'label' => 'Floorings',
        'actual' => $baseDir . '/floorings.json',
        'staging' => $stagingDir . '/floorings.json',
    ],
    'moldings' => [
        'label' => 'Moldings',
        'actual' => $baseDir . '/moldings.json',
        'staging' => $stagingDir . '/moldings.json',
    ],
    'orders' => [
        'label' => 'Orders',
        'actual' => $baseDir . '/orders.json',
        'staging' => $stagingDir . '/orders.json',
    ],
    'store_config' => [
        'label' => 'Store Config',
        'actual' => $baseDir . '/store_config.json',
        'staging' => $stagingDir . '/store_config.json',
    ],
    'inventory' => [
        'label' => 'Inventory',
        'actual' => $baseDir . '/inventory.json',
        'staging' => $stagingDir . '/inventory.json',
    ],
    'zip_zones' => [
        'label' => 'Zip Zones',
        'actual' => $baseDir . '/store/zip_zones.json',
        'staging' => $stagingDir . '/zip_zones.json',
    ],
];

function ensureStaging(array $files, string $stagingDir): void
{
    if (!is_dir($stagingDir)) {
        mkdir($stagingDir, 0755, true);
    }

    foreach ($files as $info) {
        if (file_exists($info['actual']) && !file_exists($info['staging'])) {
            @copy($info['actual'], $info['staging']);
        }
    }
}

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function loadContent(string $path): string
{
    return file_exists($path) ? (string) file_get_contents($path) : '';
}

function normalizeFileKey(array $files, ?string $key): ?string
{
    if ($key === null || !isset($files[$key])) {
        return null;
    }

    return $key;
}

function buildSummary(string $path): array
{
    $summary = [
        'exists' => file_exists($path),
        'size' => null,
        'modified' => null,
        'validJson' => false,
        'structure' => 'unknown',
        'items' => 0,
    ];

    if (!file_exists($path)) {
        return $summary;
    }

    $summary['size'] = filesize($path);
    $summary['modified'] = filemtime($path);
    $content = file_get_contents($path);
    $decoded = json_decode($content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $summary['validJson'] = true;
        if (is_array($decoded)) {
            $summary['structure'] = array_keys($decoded) === range(0, count($decoded) - 1) ? 'array' : 'object';
            $summary['items'] = count($decoded);
        } else {
            $summary['structure'] = gettype($decoded);
        }
    }

    return $summary;
}

function handleApi(array $files, string $stagingDir, string $baseDir): void
{
    $action = $_GET['api'] ?? null;
    $fileKey = normalizeFileKey($files, $_GET['file'] ?? null);

    ensureStaging($files, $stagingDir);

    if ($action === 'summary') {
        $response = [];
        foreach ($files as $key => $info) {
            $response[] = [
                'key' => $key,
                'label' => $info['label'],
                'paths' => [
                    'actual' => str_replace($baseDir . '/', '', $info['actual']),
                    'staging' => str_replace($baseDir . '/', '', $info['staging']),
                ],
                'actual' => buildSummary($info['actual']),
                'staging' => buildSummary($info['staging']),
            ];
        }

        respond(['files' => $response]);
    }

    if ($action === 'load') {
        if ($fileKey === null) {
            respond(['error' => 'Archivo inválido'], 400);
        }

        $source = $_GET['source'] ?? 'staging';
        $path = $source === 'actual' ? $files[$fileKey]['actual'] : $files[$fileKey]['staging'];

        respond([
            'file' => $fileKey,
            'source' => $source,
            'content' => loadContent($path),
        ]);
    }

    if ($action === 'upload') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respond(['error' => 'Método no permitido'], 405);
        }

        if (!isset($_FILES['file'])) {
            respond(['error' => 'No se adjuntó ningún archivo'], 400);
        }

        $uploadDir = $baseDir . '/images/cdn/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = $_FILES['file']['name'] ?? 'image';
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeBase = preg_replace('/[^a-zA-Z0-9-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'image';
        $targetName = $safeBase . '-' . uniqid() . ($extension ? '.' . $extension : '');
        $targetPath = $uploadDir . $targetName;

        $moved = move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        if (!$moved) {
            respond(['error' => 'No se pudo guardar el archivo. Inténtalo de nuevo.'], 500);
        }

        $relativePath = 'images/cdn/' . $targetName;
        respond([
            'message' => 'Imagen subida correctamente.',
            'path' => $relativePath,
        ]);
    }

    if ($action === 'save') {
        $body = json_decode(file_get_contents('php://input'), true);
        $fileKey = normalizeFileKey($files, $body['file'] ?? null);
        $content = $body['content'] ?? '';

        if ($fileKey === null) {
            respond(['error' => 'Archivo inválido'], 400);
        }

        json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            respond(['error' => 'JSON inválido. Revise la estructura antes de guardar.'], 400);
        }

        file_put_contents($files[$fileKey]['staging'], $content);

        respond([
            'file' => $fileKey,
            'message' => 'Guardado en borrador correctamente.',
        ]);
    }

    if ($action === 'apply') {
        $results = [];
        foreach ($files as $key => $info) {
            if (!file_exists($info['staging'])) {
                $results[] = ['file' => $key, 'copied' => false, 'message' => 'No existe la copia en borrador.'];
                continue;
            }

            $copied = @copy($info['staging'], $info['actual']);
            $results[] = [
                'file' => $key,
                'copied' => (bool) $copied,
                'message' => $copied ? 'Sincronizado con el sitio.' : 'No se pudo sincronizar.',
            ];
        }

        respond(['results' => $results]);
    }

    respond(['error' => 'Acción no soportada'], 400);
}

if (isset($_GET['api'])) {
    handleApi($managedFiles, $stagingDir, $baseDir);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fb;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .nav-pills .nav-link {
            border-radius: 10px;
            font-size: 0.92rem;
            color: #5a6072;
        }
        .nav-pills .nav-link.active {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.16);
        }
        .nav-pills .nav-link.editor-tab-link {
            color: #b02a37;
            border: 1px solid transparent;
        }
        .nav-pills .nav-link.editor-tab-link.active {
            background: #f8d7da;
            color: #842029;
            border-color: #f1aeb5;
            box-shadow: 0 8px 20px rgba(176, 42, 55, 0.16);
        }
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
        }
        .card-title {
            font-size: 0.9rem;
            color: #1f2933;
        }
        .card-text, label, small, .form-control, .form-select {
            font-size: 0.9rem;
        }
        .badge-light {
            background: #eef2f7;
            color: #5a6072;
        }
        pre {
            background: #0b1220;
            color: #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            max-height: 320px;
            overflow: auto;
            font-size: 0.85rem;
        }
        textarea.form-control {
            font-family: 'JetBrains Mono', 'SFMono-Regular', Menlo, Consolas, monospace;
            background: #0b1220;
            color: #d7dde5;
            min-height: 340px;
            border: 1px solid #1f2937;
            border-radius: 12px;
        }
        textarea.form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .btn-sm {
            font-size: 0.85rem;
        }
        .status-pill {
            height: 10px;
            width: 10px;
            display: inline-block;
            border-radius: 50%;
            margin-right: 6px;
        }
        .editor-card .card-title,
        .editor-card .btn,
        .editor-card .badge {
            color: #b02a37;
        }
        .editor-card .btn,
        .editor-card .badge {
            border-color: #f8d7da;
        }
        .editor-card textarea.form-control {
            border-color: #f1aeb5;
            background-color: #fff8f8;
            color: #4a1c1c;
        }
    </style>
</head>
<body>
<div id="app" class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Dashboard de Configuración</h4>
            <p class="text-muted mb-0" style="font-size: 0.92rem;">Gestiona los JSON del sitio desde un panel limpio y rápido.</p>
        </div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" @click="confirmApply" :disabled="syncing">
            <span class="spinner-border spinner-border-sm" role="status" v-if="syncing"></span>
            <span v-else class="fw-semibold">⇆</span>
            Aplicar cambios al sitio
        </button>
    </div>

    <div class="position-fixed" style="top: 16px; right: 16px; z-index: 1080; min-width: 320px;">
        <div v-for="alert in alerts" :key="alert.id" class="alert" :class="[`alert-${alert.type}`,'shadow-sm','border-0','d-flex','align-items-center','justify-content-between','mb-2']">
            <div class="me-3">{{ alert.message }}</div>
            <button type="button" class="btn-close" aria-label="Close" @click="dismissAlert(alert.id)"></button>
        </div>
    </div>

    <ul class="nav nav-pills mb-3 gap-2">
        <li class="nav-item" v-for="tab in tabs" :key="tab.id">
            <button class="nav-link" :class="[{active: activeTab === tab.id}, tab.id === 'editor' ? 'editor-tab-link' : '']" @click="activeTab = tab.id">{{ tab.label }}</button>
        </li>
    </ul>

    <div v-if="activeTab === 'home'">
        <div class="row g-3">
            <div class="col-12 col-md-4" v-for="item in summary.files" :key="item.key">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="status-pill" :style="{background: item.actual.validJson ? '#2ecc71' : '#e74c3c'}"></span>
                                <h6 class="card-title mb-0">{{ item.label }}</h6>
                            </div>
                            <small class="text-muted">{{ item.actual.structure }} • {{ item.actual.items }} elementos</small>
                        </div>
                        <span class="badge rounded-pill text-bg-light" style="font-size: 0.8rem;">{{ formatBytes(item.actual.size) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Última mod: {{ formatDate(item.actual.modified) }}</small>
                        <span class="badge rounded-pill bg-light text-dark">Prod</span>
                    </div>
                    <div class="mt-2 p-2 bg-light rounded" style="font-size: 0.8rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Borrador</span>
                            <span class="text-muted">{{ formatDate(item.staging.modified) }}</span>
                        </div>
                        <div class="text-muted">{{ formatBytes(item.staging.size) }} • {{ item.staging.items }} items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else-if="activeTab === 'manager'" class="row g-3">
        <div class="col-12">
            <div class="card p-3 h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Gestor de elementos</h6>
                        <div class="small text-muted">Edita elementos sin tocar el JSON. El gestor trabaja sobre el borrador en <code>dashboard/jsons</code>.</div>
                    </div>
                    <div class="d-flex flex-wrap align-items-end gap-2" style="min-width: 260px;">
                        <div class="w-100">
                            <label class="form-label text-muted mb-1">Archivo</label>
                            <select class="form-select form-select-sm" v-model="managerFile" @change="loadManagement()">
                                <option v-for="file in files" :value="file.key" :key="file.key">{{ file.label }}</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" @click="loadManagement()" :disabled="managerLoading">Recargar lista</button>
                            <button class="btn btn-outline-success btn-sm" @click="startNewItem" :disabled="managerLoading">Agregar nuevo</button>
                        </div>
                    </div>
                </div>
                <div class="mt-2 small" v-if="managerError">
                    <span class="text-danger">{{ managerError }}</span>
                </div>
                <div class="mt-2 small text-muted">Mantén el Editor JSON para cambios avanzados; cualquier guardado desde aquí también actualiza el borrador.</div>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-3 h-100">
                <template v-if="!showEditor">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="card-title mb-0">{{ managerTitle }}</h6>
                            <small class="text-muted">Se muestran todos los elementos disponibles.</small>
                        </div>
                        <span class="badge text-bg-light" style="font-size: 0.85rem;">{{ managerItems.length }} elemento(s)</span>
                    </div>
                    <div class="list-group small" style="max-height: 360px; overflow: auto;">
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" v-for="(item, idx) in managerItems" :key="idx" @click="startEditItem(item, idx)">
                            <div class="ms-0 me-auto">
                                <div class="fw-semibold">{{ renderItemTitle(item) }}</div>
                                <small class="text-muted">{{ renderItemSubtitle(item) }}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">Editar</span>
                        </button>
                        <div class="text-muted text-center py-3" v-if="!managerItems.length && !managerLoading">No hay elementos en este archivo.</div>
                        <div class="text-muted text-center py-3" v-if="managerLoading">Cargando...</div>
                    </div>
                </template>

                <template v-else>
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="mb-0">{{ editIndex === -1 ? 'Nuevo elemento' : 'Editar elemento' }}</h6>
                            <small class="text-muted">Completa los campos y guarda para actualizar el borrador.</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" @click="cancelEdit">
                            ✕ <span>Volver al listado</span>
                        </button>
                    </div>

                    <div class="mb-2" v-if="currentPrimaryKey">
                        <label class="form-label">Identificador ({{ currentPrimaryKey }})</label>
                        <input type="text" class="form-control form-control-sm" v-model="draftId" placeholder="Ingresa un identificador único" />
                    </div>

                    <div class="row g-2 mb-2" v-if="managerFile === 'orders'">
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" v-model="draftStatus">
                                <option value="pending">Pending</option>
                                <option value="complete">Complete</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control form-control-sm" v-model="draftDescription" placeholder="Notas del estado" />
                        </div>
                    </div>

                    <div class="mb-2" v-for="(field, i) in draftFields" :key="i">
                        <div class="row g-2 align-items-start">
                            <div class="col-4">
                                <input type="text" class="form-control form-control-sm" v-model="field.key" :readonly="!!field.config && field.key" placeholder="Campo" />
                                <div class="small text-muted" v-if="field.config">{{ field.config.type }} • {{ field.config.input_type }}</div>
                            </div>
                            <div class="col-7">
                                <template v-if="field.config?.input_type === 'textarea'">
                                    <textarea class="form-control form-control-sm" rows="4" v-model="field.value" :placeholder="fieldPlaceholder(field)"></textarea>
                                    <div class="small text-muted">Soporta texto enriquecido.</div>
                                </template>
                                <template v-else-if="field.config?.input_type === 'image'">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">URL</span>
                                            <input type="text" class="form-control form-control-sm" v-model="field.value" :placeholder="fieldPlaceholder(field)" />
                                        </div>
                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                            <input type="file" class="form-control form-control-sm" accept="image/*" @change="onImageSelected($event, field)" :disabled="field.uploading" />
                                            <span class="spinner-border spinner-border-sm" role="status" v-if="field.uploading"></span>
                                        </div>
                                        <div class="small text-muted">Las imágenes se guardan en <code>/images/cdn/</code>.</div>
                                        <div class="small text-danger" v-if="field.error">{{ field.error }}</div>
                                    </div>
                                </template>
                                <template v-else>
                                    <input :type="field.config?.input_type || 'text'" class="form-control form-control-sm" v-model="field.value" :placeholder="fieldPlaceholder(field)" />
                                </template>
                            </div>
                            <div class="col-1 text-end">
                                <button class="btn btn-link text-danger p-0" @click="removeField(i)" title="Eliminar campo">×</button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mb-3" @click="addField">Agregar campo</button>

                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-danger btn-sm" v-if="editIndex >= 0" @click="confirmDelete" :disabled="managerLoading">Eliminar</button>
                        <button class="btn btn-outline-secondary btn-sm" @click="cancelEdit">Cancelar</button>
                        <button class="btn btn-primary btn-sm" @click="confirmSaveManagedItem" :disabled="managerLoading">
                            {{ editIndex === -1 ? 'Crear' : 'Guardar cambios' }}
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div v-else-if="activeTab === 'editor'" class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card p-3 h-100 editor-card">
                <h6 class="card-title mb-3">Selector de archivo</h6>
                <div class="mb-3">
                    <label class="form-label text-muted">Archivo</label>
                    <select class="form-select form-select-sm" v-model="selectedFile" @change="loadFile()">
                        <option v-for="file in files" :value="file.key" :key="file.key">{{ file.label }}</option>
                    </select>
                </div>
                <div class="small text-muted mb-3">
                    Se edita siempre sobre la copia en <code>dashboard/jsons</code>. Usa "Aplicar cambios al sitio" cuando estés listo.
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-danger btn-sm" @click="loadFile('staging')">Recargar borrador</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="loadFile('actual')">Ver original</button>
                    <button class="btn btn-danger btn-sm" @click="saveDraft" :disabled="saving">
                        <span class="spinner-border spinner-border-sm" v-if="saving"></span>
                        <span v-else>Guardar borrador</span>
                    </button>
                </div>
                <div class="mt-3 small" v-if="statusMessage">
                    <span class="text-success" v-if="statusOk">{{ statusMessage }}</span>
                    <span class="text-danger" v-else>{{ statusMessage }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card p-3 editor-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Editor JSON</h6>
                    <span class="badge bg-danger-subtle text-danger" style="font-size: 0.8rem;">Fuente: {{ editorSource === 'actual' ? 'Producción' : 'Borrador' }}</span>
                </div>
                <textarea class="form-control border-danger-subtle" v-model="editorContent" spellcheck="false"></textarea>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Revisa que el JSON sea válido antes de guardar.</small>
                    <button class="btn btn-danger btn-sm" @click="saveDraft" :disabled="saving">
                        <span class="spinner-border spinner-border-sm" v-if="saving"></span>
                        <span v-else>Guardar borrador</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" :class="{show: confirmModal.show}" style="display: block;" v-if="confirmModal.show" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ confirmModal.title }}</h5>
                    <button type="button" class="btn-close" @click="closeConfirm"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ confirmModal.message }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeConfirm">{{ confirmModal.cancelText || 'Cancelar' }}</button>
                    <button type="button" class="btn" :class="confirmModal.danger ? 'btn-danger' : 'btn-primary'" @click="confirmProceed">{{ confirmModal.confirmText || 'Confirmar' }}</button>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            tabs: [
                { id: 'home', label: 'Home' },
                { id: 'manager', label: 'Gestor' },
                { id: 'editor', label: 'Editor avanzado JSON' },
            ],
            activeTab: 'home',
            files: [
                { key: 'floorings', label: 'Floorings' },
                { key: 'moldings', label: 'Moldings' },
                { key: 'orders', label: 'Orders' },
                { key: 'store_config', label: 'Store Config' },
                { key: 'inventory', label: 'Inventory' },
                { key: 'zip_zones', label: 'Zip Zones' },
            ],
            selectedFile: 'floorings',
            editorContent: '',
            editorSource: 'staging',
            statusMessage: '',
            statusOk: true,
            saving: false,
            syncing: false,
            summary: { files: [] },
            managerFile: 'floorings',
            managerItems: [],
            managerRaw: {},
            managerLoading: false,
            managerError: '',
            editIndex: -1,
            draftId: '',
            draftStatus: 'pending',
            draftDescription: '',
            draftFields: [],
            showEditor: false,
            templates: {},
            alerts: [],
            confirmModal: {
                show: false,
                title: '',
                message: '',
                confirmText: 'Confirmar',
                cancelText: 'Cancelar',
                danger: false,
                onConfirm: null,
                payload: null,
            },
            fileMeta: {
                floorings: { primaryKey: 'sku', titleField: 'name', type: 'array' },
                moldings: { primaryKey: 'sku', titleField: 'name', type: 'array' },
                inventory: { primaryKey: '__key', titleField: 'mode', type: 'inventory' },
                zip_zones: { primaryKey: 'zip', titleField: 'city', type: 'array' },
                orders: { primaryKey: 'id', titleField: 'status', type: 'orders' },
                store_config: { primaryKey: '__key', titleField: 'name', type: 'object' },
            },
        };
    },
    methods: {
        async fetchSummary() {
            const res = await fetch('?api=summary');
            this.summary = await res.json();
        },
        async fetchTemplates() {
            try {
                const res = await fetch('jsons/templates.json');
                if (res.ok) {
                    this.templates = await res.json();
                    if (!this.showEditor) {
                        this.resetDraftState(false);
                    }
                }
            } catch (e) {
                this.templates = {};
            }
        },
        formatBytes(bytes) {
            if (!bytes) return '0 B';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`;
        },
        formatDate(ts) {
            if (!ts) return 's/f';
            const d = new Date(ts * 1000);
            return d.toLocaleString('es-ES', { hour12: false });
        },
        async loadFile(source = 'staging') {
            const res = await fetch(`?api=load&file=${this.selectedFile}&source=${source}`);
            const data = await res.json();
            this.editorContent = data.content || '';
            this.editorSource = source;
            this.statusMessage = '';
        },
        async saveDraft() {
            this.saving = true;
            this.statusMessage = '';
            try {
                const res = await fetch('?api=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ file: this.selectedFile, content: this.editorContent }),
                });
                const data = await res.json();
                this.statusOk = !data.error;
                this.statusMessage = data.error || data.message;
                if (!data.error) {
                    this.editorSource = 'staging';
                    this.fetchSummary();
                }
            } catch (e) {
                this.statusOk = false;
                this.statusMessage = 'No se pudo guardar el borrador.';
            } finally {
                this.saving = false;
            }
        },
        async applyToSite() {
            this.syncing = true;
            this.statusMessage = '';
            try {
                const res = await fetch('?api=apply', { method: 'POST' });
                const data = await res.json();
                const ok = data.results.every(r => r.copied);
                this.statusOk = ok;
                this.statusMessage = ok ? 'Archivos sincronizados con el sitio.' : 'Algunos archivos no se pudieron copiar.';
                this.fetchSummary();
            } catch (e) {
                this.statusOk = false;
                this.statusMessage = 'Error al aplicar cambios al sitio.';
            } finally {
                this.syncing = false;
            }
        },
        confirmApply() {
            this.openConfirm({
                title: 'Aplicar cambios',
                message: '¿Confirmas aplicar todos los borradores al sitio?',
                confirmText: 'Aplicar',
                onConfirm: () => this.executeApply(),
            });
        },
        async executeApply() {
            await this.applyToSite();
            if (this.statusOk) {
                this.pushAlert('success', 'Cambios aplicados correctamente.');
            } else {
                this.pushAlert('danger', this.statusMessage || 'No se pudo aplicar los cambios.');
            }
        },
        currentMeta() {
            return this.fileMeta[this.managerFile] || { primaryKey: 'id', titleField: 'name', type: 'array' };
        },
        fieldConfig(key) {
            const template = this.templates[this.managerFile] || {};
            return template?.[key] || null;
        },
        createField(key, value, config = null) {
            return {
                key,
                value: this.stringifyValue(value),
                config: config || this.fieldConfig(key),
                uploading: false,
                error: '',
            };
        },
        fieldPlaceholder(field) {
            if (field?.config?.input_type === 'textarea') {
                return 'Ingresa una descripción (se permite HTML)';
            }
            if (field?.config?.input_type === 'image') {
                return '/images/cdn/archivo.png';
            }
            if (field?.config) {
                return `Valor (${field.config.type})`;
            }
            return 'Valor (se acepta JSON)';
        },
        buildFieldsFromTemplate(template) {
            const entries = Object.entries(template || {});
            if (!entries.length) return [this.createField('', '', null)];
            return entries.map(([key, meta]) => this.createField(key, meta.default_value, meta));
        },
        buildFieldsFromObject(obj) {
            const template = this.templates[this.managerFile] || {};
            const entries = Object.entries(obj || {});
            if (!entries.length) return [this.createField('', '', null)];
            return entries.map(([key, value]) => this.createField(key, value, template[key] || null));
        },
        resetManagerState() {
            this.managerItems = [];
            this.managerRaw = {};
            this.resetDraftState(false);
        },
        async loadManagement(fileKey = null) {
            if (fileKey) {
                this.managerFile = fileKey;
                this.selectedFile = fileKey;
            }
            this.managerLoading = true;
            this.managerError = '';
            this.resetManagerState();
            try {
                const res = await fetch(`?api=load&file=${this.managerFile}&source=staging`);
                const data = await res.json();
                const parsed = data.content ? JSON.parse(data.content) : (this.currentMeta().type === 'array' ? [] : {});
                const { items, raw } = this.normalizeManagedData(parsed);
                this.managerItems = items;
                this.managerRaw = raw;
                this.resetDraftState(false);
            } catch (e) {
                this.managerError = 'No se pudo cargar el archivo seleccionado.';
            } finally {
                this.managerLoading = false;
            }
        },
        normalizeManagedData(parsed) {
            const meta = this.currentMeta();
            if (meta.type === 'orders') {
                const orders = Array.isArray(parsed?.orders) ? parsed.orders : [];
                return { items: orders, raw: { orders } };
            }
            if (meta.type === 'inventory') {
                const entries = [];
                Object.entries(parsed || {}).forEach(([key, value]) => {
                    if (key === '__defaults') return;
                    entries.push({ __key: key, ...(value || {}) });
                });
                return { items: entries, raw: parsed || {} };
            }
            if (Array.isArray(parsed)) {
                return { items: parsed, raw: parsed };
            }
            if (typeof parsed === 'object') {
                const items = Object.entries(parsed || {}).map(([k, v]) => {
                    if (v && typeof v === 'object' && !Array.isArray(v)) {
                        return { __key: k, ...v };
                    }
                    return { __key: k, __value: v };
                });
                return { items, raw: parsed || {} };
            }
            return { items: [], raw: {} };
        },
        resetDraftState(showEditor = false) {
            this.editIndex = -1;
            this.draftId = '';
            this.draftStatus = 'pending';
            this.draftDescription = '';
            const meta = this.currentMeta();
            const template = this.templates[this.managerFile];
            if (template && meta.primaryKey && template[meta.primaryKey]?.default_value !== undefined) {
                this.draftId = template[meta.primaryKey].default_value;
            }
            if (template) {
                this.draftFields = this.buildFieldsFromTemplate(template);
            } else if (meta.type === 'object') {
                this.draftFields = [this.createField('__value', '', null)];
            } else {
                this.draftFields = [this.createField('', '', null)];
            }
            this.showEditor = showEditor;
        },
        startNewItem() {
            this.resetDraftState(true);
        },
        startEditItem(item, idx) {
            this.editIndex = idx;
            this.showEditor = true;
            const meta = this.currentMeta();
            const cleanItem = { ...item };
            if (meta.type === 'orders') {
                this.draftStatus = item.status || 'pending';
                this.draftDescription = item.description || '';
                delete cleanItem.status;
                delete cleanItem.description;
            }
            if (meta.type === 'inventory') {
                this.draftId = item.__key || '';
                delete cleanItem.__key;
            } else if (meta.type === 'object' && item.__key !== undefined) {
                this.draftId = item.__key;
                delete cleanItem.__key;
                if (Object.prototype.hasOwnProperty.call(cleanItem, '__value')) {
                    this.draftFields = [{ key: '__value', value: this.stringifyValue(cleanItem.__value) }];
                    delete cleanItem.__value;
                    return;
                }
            } else if (meta.primaryKey && item[meta.primaryKey] !== undefined) {
                this.draftId = item[meta.primaryKey];
                delete cleanItem[meta.primaryKey];
            } else {
                this.draftId = '';
            }
            this.draftFields = this.buildFieldsFromObject(cleanItem);
        },
        cancelEdit() {
            this.resetDraftState(false);
        },
        stringifyValue(value) {
            if (value === undefined) return '';
            if (value === null) return 'null';
            if (typeof value === 'string') return value;
            try {
                return JSON.stringify(value);
            } catch (e) {
                return String(value);
            }
        },
        addField() {
            this.draftFields.push(this.createField('', '', null));
        },
        removeField(index) {
            this.draftFields.splice(index, 1);
            if (!this.draftFields.length) {
                this.addField();
            }
        },
        renderItemTitle(item) {
            const meta = this.currentMeta();
            const primary = item[meta.primaryKey] || item.__key || '(sin id)';
            return primary;
        },
        renderItemSubtitle(item) {
            const meta = this.currentMeta();
            if (meta.type === 'orders') {
                return `Status: ${item.status || 'pending'}`;
            }
            const title = item[meta.titleField] || '';
            return title || 'Sin detalles';
        },
        parseFieldValue(value, config = null) {
            if (config?.type === 'text' || config?.type === 'image') {
                return typeof value === 'string' ? value : String(value ?? '');
            }
            if (config?.type === 'number') {
                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed : value;
            }
            if (config?.type === 'boolean') {
                if (typeof value === 'boolean') return value;
                const normalized = String(value).trim().toLowerCase();
                if (['true', '1', 'yes', 'on'].includes(normalized)) return true;
                if (['false', '0', 'no', 'off', ''].includes(normalized)) return false;
                return !!value;
            }
            if (config?.type === 'array' || config?.type === 'object') {
                try {
                    return JSON.parse(value);
                } catch (e) {
                    return value;
                }
            }
            try {
                return JSON.parse(value);
            } catch (e) {
                return value;
            }
        },
        createObjectFromFields() {
            const obj = {};
            this.draftFields.forEach(field => {
                if (!field.key) return;
                obj[field.key] = this.parseFieldValue(field.value, field.config);
            });
            if (this.currentMeta().type === 'object') {
                if (obj.__value !== undefined && Object.keys(obj).length === 1) {
                    return { __value: obj.__value };
                }
                delete obj.__value;
            }
            if (this.currentMeta().type === 'orders') {
                obj.status = this.draftStatus || 'pending';
                obj.description = this.draftDescription;
            }
            return obj;
        },
        async onImageSelected(event, field) {
            const file = event.target.files?.[0];
            field.error = '';
            if (!file) return;
            field.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            try {
                const res = await fetch('?api=upload', { method: 'POST', body: formData });
                const data = await res.json();
                if (!res.ok || data.error) {
                    field.error = data.error || 'No se pudo subir la imagen.';
                } else if (data.path) {
                    field.value = data.path;
                }
            } catch (e) {
                field.error = 'Error al conectar con el servidor.';
            } finally {
                field.uploading = false;
                event.target.value = '';
            }
        },
        confirmSaveManagedItem() {
            const isEdit = this.editIndex >= 0;
            this.openConfirm({
                title: isEdit ? 'Guardar cambios' : 'Crear nuevo elemento',
                message: isEdit ? '¿Deseas guardar las modificaciones en este elemento?' : '¿Deseas crear este nuevo elemento?',
                confirmText: isEdit ? 'Guardar' : 'Crear',
                onConfirm: () => this.saveManagedItem(),
            });
        },
        saveManagedItem() {
            const meta = this.currentMeta();
            const baseObj = this.createObjectFromFields();
            this.managerError = '';
            if (meta.primaryKey) {
                const idValue = this.draftId || baseObj[meta.primaryKey] || '';
                if (!idValue) {
                    this.managerError = 'El identificador es obligatorio.';
                    this.pushAlert('danger', this.managerError);
                    return;
                }
                baseObj[meta.primaryKey] = idValue;
            }
            const items = [...this.managerItems];
            if (this.editIndex >= 0) {
                items.splice(this.editIndex, 1, baseObj);
            } else {
                items.push(baseObj);
            }
            this.persistManagerItems(items);
            this.pushAlert('success', this.editIndex >= 0 ? 'Elemento actualizado correctamente.' : 'Elemento creado correctamente.');
        },
        confirmDelete() {
            if (this.editIndex < 0) return;
            this.openConfirm({
                title: 'Eliminar elemento',
                message: 'Esta acción eliminará el elemento del borrador. ¿Deseas continuar?',
                confirmText: 'Eliminar',
                danger: true,
                onConfirm: () => this.deleteManagedItem(),
            });
        },
        deleteManagedItem() {
            if (this.editIndex < 0) return;
            const items = [...this.managerItems];
            items.splice(this.editIndex, 1);
            this.persistManagerItems(items);
            this.pushAlert('success', 'Elemento eliminado correctamente.');
        },
        persistManagerItems(items) {
            const meta = this.currentMeta();
            let payload = items;
            if (meta.type === 'orders') {
                payload = { orders: items.map(order => ({ description: '', status: 'pending', ...order })) };
            } else if (meta.type === 'inventory') {
                const assembled = { ...(this.managerRaw.__defaults ? { __defaults: this.managerRaw.__defaults } : {}) };
                items.forEach(item => {
                    const key = item.__key || item[meta.primaryKey] || `item_${Date.now()}`;
                    const clone = { ...item };
                    delete clone.__key;
                    delete clone[meta.primaryKey];
                    assembled[key] = clone;
                });
                payload = assembled;
            } else if (meta.type === 'object') {
                const assembled = {};
                items.forEach(item => {
                    const key = item.__key || item[meta.primaryKey] || item.id || `item_${Date.now()}`;
                    const clone = { ...item };
                    delete clone.__key;
                    if (Object.prototype.hasOwnProperty.call(clone, '__value')) {
                        assembled[key] = clone.__value;
                    } else {
                        assembled[key] = clone;
                    }
                });
                payload = assembled;
            }
            this.managerItems = items;
            this.editorContent = JSON.stringify(payload, null, 2);
            this.saveDraft();
            this.resetDraftState(false);
        },
        pushAlert(type, message) {
            const id = Date.now() + Math.random();
            this.alerts.push({ id, type, message });
            setTimeout(() => this.dismissAlert(id), 5000);
        },
        dismissAlert(id) {
            this.alerts = this.alerts.filter(alert => alert.id !== id);
        },
        openConfirm(options) {
            this.confirmModal = {
                show: true,
                title: options.title || 'Confirmar',
                message: options.message || '¿Deseas continuar?',
                confirmText: options.confirmText || 'Confirmar',
                cancelText: options.cancelText || 'Cancelar',
                danger: !!options.danger,
                onConfirm: options.onConfirm || null,
                payload: options.payload || null,
            };
        },
        closeConfirm() {
            this.confirmModal.show = false;
        },
        confirmProceed() {
            const callback = this.confirmModal.onConfirm;
            const payload = this.confirmModal.payload;
            this.closeConfirm();
            if (typeof callback === 'function') {
                callback(payload);
            }
        },
    },
    computed: {
        managerTitle() {
            const label = this.files.find(f => f.key === this.managerFile)?.label || this.managerFile;
            return `Gestión de ${label}`;
        },
        currentPrimaryKey() {
            return this.currentMeta().primaryKey;
        },
    },
    mounted() {
        this.fetchTemplates();
        this.fetchSummary();
        this.loadFile();
        this.loadManagement();
    },
}).mount('#app');
</script>
</body>
</html>
