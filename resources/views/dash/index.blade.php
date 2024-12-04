@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
    <br>
    <div class="row">

        <style>
            .small-box {
                margin: 15px;
            }
        </style>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $usuarioCount }}</h3>
                    <p>Usuarios</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-users"></i>
                </div>
                <a href="usuario" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $preveedoresCount }}</h3>
                    <p>Proveedores</p>
                </div>
                <div class="icon">
                    <i class="far fa-user-circle"></i>
                </div>
                <a href="proveedor" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $serviciosCount }}</h3>
                    <p>Áreas</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-medkit"></i>
                </div>
                <a href="servicio" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $categoriaCount }}</h3>
                    <p>Categorías</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-list"></i>
                </div>
                <a href="categoria" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $marcaCount }}</h3>
                    <p>Marcas</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-tags"></i>
                </div>
                <a href="marca" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <h3>{{ $presentacionCount }}</h3>
                    <p>Presentaciones</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-cubes"></i>
                </div>
                <a href="presentacion" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $insumoCount }}</h3>
                    <p>Insumos</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-stethoscope"></i>
                </div>
                <a href="insumo" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>

                    <p>New</p>
                </div>
                <div class="icon">
                    <i class="fa fa-signal"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Compras y Entregas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas de Compras y Entregas</h3>
                </div>
                <div class="card-body">
                    <canvas id="comprasEntregasChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Evolución del Valor del Inventario -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Evolucion Inventario</h3>
                </div>
                <div class="card-body">
                    <canvas id="evolucionInventarioChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Top Insumos por Mayor Cantidad Consumo</h3>
                </div>
                <div class="card-body">
                    <canvas id="top5CantidadEntregadaChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Top 5 Insumos por Mayor Valor de Consumo -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Top Insumos por Mayor Valor de Consumo</h3>
                </div>
                <div class="card-body">
                    <canvas id="top5ValorConsumoChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Distribución del Valor del Inventario por Categoría -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribución del Valor del Inventario por Categoría</h3>
                </div>
                <div class="card-body">
                    <canvas id="valorInventarioCategoriaChart"></canvas>
                </div>
            </div>
        </div>


    </div>

@stop

@section('css')
    <style></style>
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            
            //1
            const comprasYEntregas = @json($comprasYEntregasMensuales);
            const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre",
                "Octubre", "Noviembre", "Diciembre"
            ];
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth();
            const labels = [];

            // Calcular los últimos 6 meses
            for (let i = 5; i >= 0; i--) {
                const monthIndex = (currentMonth - i + 12) % 12;
                labels.push(months[monthIndex]);
            }

            const comprasData = comprasYEntregas.compras;
            const entregasData = comprasYEntregas.entregas;

            const ctx1 = document.getElementById('comprasEntregasChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels, // Los meses calculados dinámicamente
                    datasets: [{
                            label: 'Compras',
                            data: comprasData,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Entregas',
                            data: entregasData,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Valor de insumos'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Meses'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            //2
            const cantidadLabels = @json($topNombres); // Nombres de insumos
            const cantidadData = @json($topCantidades); // Cantidades entregadas
            const ctxCantidad = document.getElementById('top5CantidadEntregadaChart').getContext('2d');

            new Chart(ctxCantidad, {
                type: 'bar',
                data: {
                    labels: cantidadLabels,
                    datasets: [{
                        label: 'Cantidad Entregada',
                        data: cantidadData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Insumos'
                            }
                        }
                    }
                }
            });

            //3
            const ctxValor = document.getElementById('top5ValorConsumoChart').getContext('2d');
            const valorLabels = @json($top7ValorConsumo->pluck('nombre'));
            const valorData = @json($top7ValorConsumo->pluck('total_valor'));

            new Chart(ctxValor, {
                type: 'bar',
                data: {
                    labels: valorLabels,
                    datasets: [{
                        label: 'Valor de Consumo',
                        data: valorData,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString(); // Formato de moneda
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Insumos'
                            }
                        }
                    }
                }
            });

            //4
            const evolucionData = @json(array_column($evolucionInventario, 'valor'));
            const mesesLabels = @json(array_column($evolucionInventario, 'mes'));

            const ctxEvolucion = document.getElementById('evolucionInventarioChart').getContext('2d');
            new Chart(ctxEvolucion, {
                type: 'line',
                data: {
                    labels: mesesLabels, // Últimos 6 meses
                    datasets: [{
                        label: 'Valor del Inventario',
                        data: evolucionData, // Valores calculados
                        borderColor: 'rgba(34, 139, 34, 1)', // Verde intenso (forest green)
                        backgroundColor: 'rgba(34, 139, 34, 0.2)', // Verde claro con transparencia
                        tension: 0.4 // Curvatura de la línea
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top' // Posición de la leyenda
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString(); // Formato de moneda
                                }
                            }
                        }
                    }
                }
            });


            //5
            const categoriaLabels = @json(array_map(fn($categoria) => $categoria['nombre'], $categoriasValores));
            const categoriaValues = @json(array_map(fn($categoria) => $categoria['valor_total'], $categoriasValores));
            const totalInventario = categoriaValues.reduce((sum, value) => sum + parseFloat(value), 0);

            const ctxCategoria = document.getElementById('valorInventarioCategoriaChart').getContext('2d');
            new Chart(ctxCategoria, {
                type: 'doughnut',
                data: {
                    labels: categoriaLabels,
                    datasets: [{
                        data: categoriaValues,
                        backgroundColor: [
                            'rgba(250, 100, 150, 0.6)', 'rgba(255, 255, 0, 0.6)',
                            'rgba(255, 0, 0, 0.6)', 'rgba(144, 238, 144, 0.6)',
                            'rgba(0, 0, 255, 0.6)', 'rgba(0, 100, 0, 0.6)',
                            'rgba(255, 120, 0, 0.6)', 'rgba(128, 128, 128, 0.6)',
                            'rgba(128, 0, 128, 0.6)', 'rgba(139, 69, 19, 0.6)',
                            'rgba(255, 192, 203, 0.6)', 'rgba(0, 255, 255, 0.6)'
                        ]
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    const value = context.raw;
                                    // Calcular el porcentaje correctamente
                                    const percentage = ((value / totalInventario) * 100).toFixed(2);
                                    return `$${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        },
                        title: {
                            display: true,
                            // Ahora mostramos el total correctamente en el título
                            text: `Valor Inventario: $${totalInventario.toLocaleString()}`,
                            font: {
                                size: 14
                            },
                            color: '#000'
                        },
                        legend: {
                            labels: {
                                generateLabels: function(chart) {
                                    const dataset = chart.data.datasets[0];
                                    return chart.data.labels.map((label, index) => ({
                                        text: label,
                                        fillStyle: dataset.backgroundColor[index]
                                    }));
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
