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
    </style>
</head>
<body>
<div id="app" class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Dashboard de Configuración</h4>
            <p class="text-muted mb-0" style="font-size: 0.92rem;">Gestiona los JSON del sitio desde un panel limpio y rápido.</p>
        </div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" @click="applyToSite" :disabled="syncing">
            <span class="spinner-border spinner-border-sm" role="status" v-if="syncing"></span>
            <span v-else class="fw-semibold">⇆</span>
            Aplicar cambios al sitio
        </button>
    </div>

    <ul class="nav nav-pills mb-3 gap-2">
        <li class="nav-item" v-for="tab in tabs" :key="tab.id">
            <button class="nav-link" :class="{active: activeTab === tab.id}" @click="activeTab = tab.id">{{ tab.label }}</button>
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

    <div v-else class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card p-3 h-100">
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
                    <button class="btn btn-outline-primary btn-sm" @click="loadFile('staging')">Recargar borrador</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="loadFile('actual')">Ver original</button>
                    <button class="btn btn-outline-success btn-sm" @click="saveDraft" :disabled="saving">
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
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Editor JSON</h6>
                    <span class="badge text-bg-light" style="font-size: 0.8rem;">Fuente: {{ editorSource === 'actual' ? 'Producción' : 'Borrador' }}</span>
                </div>
                <textarea class="form-control" v-model="editorContent" spellcheck="false"></textarea>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Revisa que el JSON sea válido antes de guardar.</small>
                    <button class="btn btn-primary btn-sm" @click="saveDraft" :disabled="saving">
                        <span class="spinner-border spinner-border-sm" v-if="saving"></span>
                        <span v-else>Guardar borrador</span>
                    </button>
                </div>
            </div>
        </div>
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
                { id: 'editor', label: 'Editor' },
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
        };
    },
    methods: {
        async fetchSummary() {
            const res = await fetch('?api=summary');
            this.summary = await res.json();
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
    },
    mounted() {
        this.fetchSummary();
        this.loadFile();
    },
}).mount('#app');
</script>
</body>
</html>
