<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carga de Ciudades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .preview-box {
            border: 2px dashed #007bff;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .data-card {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="main-container">
        <h1 class="text-center mb-4 text-primary">Gestión de Ciudades</h1>

        <!-- Formulario de carga -->
        <div class="preview-box">
            <?php echo form_open_multipart('ciudad_controller/upload', ['id' => 'uploadForm']); ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Seleccionar archivo CSV</label>
                    <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-upload"></i> Subir Archivo
                </button>
            <?php echo form_close(); ?>
        </div>

        <!-- Mensajes de estado -->
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3">
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Selector de ciudades -->
        <div class="mt-5">
            <h3 class="text-center mb-3">Ciudades Disponibles</h3>
            <select id="citySelect" class="form-select form-select-lg">
                <option value="" selected disabled>-- Seleccione una ciudad --</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city['name']); ?>">
                        <?php echo htmlspecialchars($city['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Información de la ciudad -->
        <div class="data-card">
            <h4 class="text-center mb-4">Información Detallada</h4>
            <div id="cityInfo" class="text-center">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tbody id="infoContent"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Manejar cambio en el select
            $('#citySelect').change(function() {
                const cityName = $(this).val();
                $('#infoContent').html('<tr><td colspan="2">Cargando...</td></tr>');

                if (cityName) {
                    $.ajax({
                        url: '<?php echo site_url('ciudad_controller/get_city'); ?>',
                        type: 'GET',
                        
                    dataType: 'json',
                    data: { name: cityName },
                    success: function(response) {
                        if (response.status === 'success') {
        const data = response.data;
        $('#infoContent').html(`
            <tr>
                <th>Departamento:</th>
                <td>${data.departamento}</td>
            </tr>
            <tr>
                <th>Nombre:</th>
                <td>${data.nombre}</td>
            </tr>
            <tr>
                <th>Código Principal:</th>
                <td>${data.codigo_principal}</td>
            </tr>
            <tr>
                <th>Código Adicional:</th>
                <td>${data.codigo_adicional}</td>
            </tr>
        `);
    } else {
                                $('#infoContent').html('<tr><td colspan="2">Ciudad no encontrada</td></tr>');
                            }
                        },
                        error: function(xhr) {
                            $('#infoContent').html('<tr><td colspan="2">Error al cargar datos</td></tr>');
                            console.error('Error:', xhr.responseText);
                        }
                    });
                }
            });

            // Actualizar select después de subir archivo
            $('#uploadForm').on('submit', function(e) {
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function() {
                        location.reload();
                    }
                });
                return false;
            });
        });
    </script>
</body>
</html>