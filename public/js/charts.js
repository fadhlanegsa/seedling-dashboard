/**
 * Charts Configuration (Chart.js)
 * Dashboard Stok Bibit Persemaian Indonesia
 */

// Chart.js default configuration
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.color = '#666';

/**
 * Stock by Province Pie Chart
 */
function createStockByProvinceChart(canvasId, data) {
    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    var labels = data.map(item => item.province_name);
    var values = data.map(item => parseInt(item.total_stock));
    var colors = generateColors(data.length);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribusi Stok Bibit per Provinsi',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.parsed || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Top Seedling Types Bar Chart
 */
function createTopSeedlingTypesChart(canvasId, data) {
    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    var labels = data.map(item => item.seedling_name);
    var values = data.map(item => parseInt(item.total_stock));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Stok',
                data: values,
                backgroundColor: '#2d5016',
                borderColor: '#1a3009',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Top 10 Jenis Bibit Terbanyak',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Stok: ' + context.parsed.x.toLocaleString('id-ID') + ' batang';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

/**
 * Stock Update Trend Line Chart
 */
function createStockUpdateTrendChart(canvasId, data) {
    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    var labels = data.map(item => item.date);
    var values = data.map(item => parseInt(item.total_quantity));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Stok',
                data: values,
                borderColor: '#2d5016',
                backgroundColor: 'rgba(45, 80, 22, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Tren Update Stok Mingguan',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Stok: ' + context.parsed.y.toLocaleString('id-ID') + ' batang';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

/**
 * Request Status Doughnut Chart
 */
function createRequestStatusChart(canvasId, data) {
    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak', 'Selesai'],
            datasets: [{
                data: [
                    data.pending || 0,
                    data.approved || 0,
                    data.rejected || 0,
                    data.completed || 0
                ],
                backgroundColor: [
                    '#ffc107',
                    '#28a745',
                    '#dc3545',
                    '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15
                    }
                },
                title: {
                    display: true,
                    text: 'Status Permintaan Bibit',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.parsed || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Generate random colors for charts
 */
function generateColors(count) {
    var colors = [
        '#2d5016', '#3d6b1f', '#4d8628', '#5da131', '#6dbc3a',
        '#7dd743', '#8df24c', '#9dff55', '#adff5e', '#bdff67',
        '#cdff70', '#ddff79', '#edff82', '#fdff8b', '#ffff94'
    ];
    
    if (count <= colors.length) {
        return colors.slice(0, count);
    }
    
    // Generate more colors if needed
    var generatedColors = [];
    for (var i = 0; i < count; i++) {
        var hue = (i * 360 / count) % 360;
        generatedColors.push('hsl(' + hue + ', 70%, 50%)');
    }
    
    return generatedColors;
}

/**
 * Update chart data
 */
function updateChartData(chart, newData) {
    chart.data.datasets[0].data = newData;
    chart.update();
}

/**
 * Destroy chart
 */
function destroyChart(chart) {
    if (chart) {
        chart.destroy();
    }
}
