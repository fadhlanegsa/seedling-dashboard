/**
 * DataTables Configuration
 * Dashboard Stok Bibit Persemaian Indonesia
 */

$(document).ready(function() {
    // Default DataTable configuration
    var defaultConfig = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        order: [[0, 'asc']]
    };

    // Initialize all tables with class 'datatable'
    $('.datatable').each(function() {
        $(this).DataTable(defaultConfig);
    });

    // Stock table with custom configuration
    if ($('#stockTable').length) {
        $('#stockTable').DataTable($.extend({}, defaultConfig, {
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting on last column (actions)
            ]
        }));
    }

    // BPDAS table
    if ($('#bpdasTable').length) {
        $('#bpdasTable').DataTable($.extend({}, defaultConfig, {
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        }));
    }

    // Requests table
    if ($('#requestsTable').length) {
        $('#requestsTable').DataTable($.extend({}, defaultConfig, {
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        }));
    }

    // Users table
    if ($('#usersTable').length) {
        $('#usersTable').DataTable($.extend({}, defaultConfig, {
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        }));
    }

    // Seedling types table
    if ($('#seedlingTypesTable').length) {
        $('#seedlingTypesTable').DataTable($.extend({}, defaultConfig, {
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        }));
    }

    // Export buttons
    $('.btn-export-excel').on('click', function() {
        var table = $(this).data('table');
        exportTableToExcel(table);
    });

    $('.btn-export-pdf').on('click', function() {
        var table = $(this).data('table');
        exportTableToPDF(table);
    });
});

/**
 * Export table to Excel
 */
function exportTableToExcel(tableId) {
    var table = $('#' + tableId).DataTable();
    var data = table.buttons.exportData();
    
    // Create workbook
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet([data.header].concat(data.body));
    
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, tableId + '_' + new Date().getTime() + '.xlsx');
}

/**
 * Export table to PDF
 */
function exportTableToPDF(tableId) {
    var table = $('#' + tableId).DataTable();
    var data = table.buttons.exportData();
    
    // This would require jsPDF library
    // Implementation depends on specific requirements
    alert('Export to PDF functionality - requires jsPDF library');
}

/**
 * Refresh DataTable
 */
function refreshDataTable(tableId) {
    var table = $('#' + tableId).DataTable();
    table.ajax.reload(null, false);
}

/**
 * Add row to DataTable
 */
function addRowToDataTable(tableId, rowData) {
    var table = $('#' + tableId).DataTable();
    table.row.add(rowData).draw(false);
}

/**
 * Update row in DataTable
 */
function updateRowInDataTable(tableId, rowIndex, rowData) {
    var table = $('#' + tableId).DataTable();
    table.row(rowIndex).data(rowData).draw(false);
}

/**
 * Delete row from DataTable
 */
function deleteRowFromDataTable(tableId, rowIndex) {
    var table = $('#' + tableId).DataTable();
    table.row(rowIndex).remove().draw(false);
}
