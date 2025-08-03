/**
 * Simple Table Enhancement - Local Alternative to DataTables
 * Provides basic search, pagination, and sorting functionality
 */

$(document).ready(function() {
    // Initialize enhanced tables
    $('.table-enhanced').each(function() {
        enhanceTable($(this));
    });
});

function enhanceTable(table) {
    const tableId = table.attr('id') || 'table-' + Math.random().toString(36).substr(2, 9);
    table.attr('id', tableId);
    
    // Add search functionality
    const searchHtml = `
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">प्रविष्टी दाखवा:</label>
                <select class="form-select form-select-sm d-inline-block w-auto" id="${tableId}-length">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">शोध:</label>
                <input type="text" class="form-control form-control-sm" id="${tableId}-search" placeholder="शोधा...">
            </div>
        </div>
    `;
    
    table.before(searchHtml);
    
    // Add pagination
    const paginationHtml = `
        <div class="row mt-3">
            <div class="col-md-6">
                <div id="${tableId}-info" class="dataTables_info"></div>
            </div>
            <div class="col-md-6">
                <nav>
                    <ul class="pagination pagination-sm justify-content-end" id="${tableId}-pagination">
                    </ul>
                </nav>
            </div>
        </div>
    `;
    
    table.after(paginationHtml);
    
    // Initialize table functionality
    initTableFeatures(tableId);
}

function initTableFeatures(tableId) {
    const table = $('#' + tableId);
    const tbody = table.find('tbody');
    const rows = tbody.find('tr').toArray();
    let filteredRows = rows;
    let currentPage = 1;
    let pageSize = 10;
    
    // Search functionality
    $('#' + tableId + '-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        filteredRows = rows.filter(row => {
            const text = $(row).text().toLowerCase();
            return text.includes(searchTerm);
        });
        currentPage = 1;
        updateTable();
    });
    
    // Page size change
    $('#' + tableId + '-length').on('change', function() {
        pageSize = parseInt($(this).val());
        currentPage = 1;
        updateTable();
    });
    
    function updateTable() {
        // Hide all rows
        tbody.find('tr').hide();
        
        // Calculate pagination
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / pageSize);
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, totalRows);
        
        // Show current page rows
        for (let i = startIndex; i < endIndex; i++) {
            $(filteredRows[i]).show();
        }
        
        // Update info
        const infoText = totalRows === 0 ? 'कोणत्याही प्रविष्टी सापडल्या नाहीत' : 
            `${totalRows} पैकी ${startIndex + 1} ते ${endIndex} प्रविष्टी दाखवत आहे`;
        $('#' + tableId + '-info').text(infoText);
        
        // Update pagination
        updatePagination(tableId, currentPage, totalPages);
    }
    
    function updatePagination(tableId, current, total) {
        const pagination = $('#' + tableId + '-pagination');
        pagination.empty();
        
        if (total <= 1) return;
        
        // Previous button
        const prevDisabled = current === 1 ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${current - 1}">मागील</a>
            </li>
        `);
        
        // Page numbers
        for (let i = 1; i <= total; i++) {
            const active = i === current ? 'active' : '';
            pagination.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        // Next button
        const nextDisabled = current === total ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${current + 1}">पुढील</a>
            </li>
        `);
        
        // Page click handlers
        pagination.find('a').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page >= 1 && page <= total) {
                currentPage = page;
                updateTable();
            }
        });
    }
    
    // Initial table update
    updateTable();
}
