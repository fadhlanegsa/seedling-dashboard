/**
 * Main JavaScript File
 * Dashboard Stok Bibit Persemaian Indonesia
 */

$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            e.preventDefault();
            return false;
        }
    });

    // Form validation
    $('form').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
    });

    // Number formatting
    $('.format-number').each(function() {
        var num = parseInt($(this).text());
        $(this).text(num.toLocaleString('id-ID'));
    });

    // Tooltip initialization (if using Bootstrap tooltips)
    $('[data-toggle="tooltip"]').tooltip();

    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Print functionality
    $('.btn-print').on('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // Export to CSV
    $('.btn-export-csv').on('click', function(e) {
        e.preventDefault();
        var table = $(this).data('table');
        exportTableToCSV(table);
    });

    // Search with debounce
    var searchTimeout;
    $('.search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val();
        var searchUrl = $(this).data('url');
        
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 3) {
                performSearch(searchUrl, searchTerm);
            }
        }, 500);
    });
});

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId) {
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + ' tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), tableId + '.csv');
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    
    csvFile = new Blob([csv], {type: 'text/csv'});
    downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

/**
 * Perform AJAX search
 */
function performSearch(url, term) {
    $.ajax({
        url: url,
        method: 'GET',
        data: { term: term },
        success: function(response) {
            displaySearchResults(response);
        },
        error: function() {
            console.error('Search failed');
        }
    });
}

/**
 * Display search results
 */
function displaySearchResults(results) {
    var resultsContainer = $('#search-results');
    resultsContainer.empty();
    
    if (results.length === 0) {
        resultsContainer.html('<p>Tidak ada hasil ditemukan</p>');
        return;
    }
    
    results.forEach(function(item) {
        var html = '<div class="search-result-item">' +
                   '<h4>' + item.name + '</h4>' +
                   '<p>' + item.description + '</p>' +
                   '</div>';
        resultsContainer.append(html);
    });
}

/**
 * Show loading spinner
 */
function showLoading() {
    $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    $('.loading-overlay').remove();
}

/**
 * Show notification
 */
function showNotification(message, type) {
    var alertClass = 'alert-' + type;
    var html = '<div class="alert ' + alertClass + ' alert-dismissible">' +
               '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
               message +
               '</div>';
    
    $('.container').first().prepend(html);
    
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

/**
 * Format number to Indonesian format
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Format date to Indonesian format
 */
function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

/**
 * Validate form before submit
 */
function validateForm(formId) {
    var form = $('#' + formId);
    var isValid = true;
    
    form.find('[required]').each(function() {
        if ($(this).val() === '') {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    var temp = $('<input>');
    $('body').append(temp);
    temp.val(text).select();
    document.execCommand('copy');
    temp.remove();
    showNotification('Berhasil disalin ke clipboard', 'success');
}
