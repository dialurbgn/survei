/**
 * Dynamic Filters for DataTables
 * Enhanced version with better user experience
 */

class DynamicFilters {
    constructor(options = {}) {
		this.url = options.url || 'localhost' 
        this.filterColumns = options.columns || [];
		this.csrfHash = options.csrfHash || [];
        this.tableId = options.tableId || 'datatablesskp';
		this.table = options.table || 'master_option';
		this.tableRef = options.table || 'master_option';
        this.ajaxUrl = options.ajaxUrl || '';
        this.csrfTokenName = options.csrfTokenName || '';
        this.appliedFilters = {};

        this.init();
    }

    init() {
        this.generateFilters();
        this.setupEvents();
        this.loadSavedFilters();
    }

    generateFilters() {
        const filterContainer = $('#filter-row');
        filterContainer.empty();

        this.filterColumns.forEach((column, index) => {
            const filterHtml = this.createFilterElement(column);
            filterContainer.append(filterHtml);
			console.log(column)
            // Load options for select fields
            if (column.type === 'SELECT') {
                this.loadSelectOptions(column.tableid, column.tablename, column.table, column.name, column.columnname);
            }
        });
    }

    createFilterElement(column) {
        const colClass = 'col-lg-3 col-md-4 col-sm-6 mb-3';
        const fieldId = `filter_${column.name}`;
        
        switch(column.type) {
            case 'DATE':
            case 'DATETIME':
                return `
                    <div class="${colClass}">
                        <label class="form-label fs-7 fw-bold">${column.label}</label>
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control form-control-sm filter-input" 
                                   id="${fieldId}_start" 
                                   data-column="${column.name}" 
                                   data-type="date_start"
                                   placeholder="Dari">
                            <span class="input-group-text">s/d</span>
                            <input type="date" class="form-control form-control-sm filter-input" 
                                   id="${fieldId}_end" 
                                   data-column="${column.name}" 
                                   data-type="date_end"
                                   placeholder="Sampai">
                        </div>
                        <small class="text-muted">Rentang tanggal</small>
                    </div>
                `;
            
            case 'NUMBER':
            case 'CURRENCY':
                return `
                    <div class="${colClass}">
                        <label class="form-label fs-7 fw-bold">${column.label}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control form-control-sm filter-input" 
                                   id="${fieldId}_min" 
                                   data-column="${column.name}" 
                                   data-type="number_min"
                                   placeholder="Minimum">
                            <span class="input-group-text">-</span>
                            <input type="number" class="form-control form-control-sm filter-input" 
                                   id="${fieldId}_max" 
                                   data-column="${column.name}" 
                                   data-type="number_max"
                                   placeholder="Maksimum">
                        </div>
                        <small class="text-muted">Rentang nilai</small>
                    </div>
                `;
            
            case 'SELECT':
                return `
                    <div class="${colClass}">
                        <label class="form-label fs-7 fw-bold">${column.label}</label>
                        <select class="form-select form-select-sm filter-input" 
                                id="${fieldId}" 
                                data-column="${column.name}" 
                                data-type="select">
                            <option value="">-- Semua --</option>
                        </select>
                        <small class="text-muted">Pilih nilai</small>
                    </div>
                `;
            
            case 'TEXTAREA':
            case 'TEXTEDITOR':
                return `
                    <div class="${colClass}">
                        <label class="form-label fs-7 fw-bold">${column.label}</label>
                        <input type="text" class="form-control form-control-sm filter-input" 
                               id="${fieldId}" 
                               data-column="${column.name}" 
                               data-type="text"
                               placeholder="Cari dalam ${column.label}">
                        <small class="text-muted">Pencarian teks</small>
                    </div>
                `;
            
            default:
                return `
                    <div class="${colClass}">
                        <label class="form-label fs-7 fw-bold">${column.label}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control form-control-sm filter-input" 
                                   id="${fieldId}" 
                                   data-column="${column.name}" 
                                   data-type="text"
                                   placeholder="Cari ${column.label}">
                        </div>
                        <small class="text-muted">Pencarian teks</small>
                    </div>
                `;
        }
    }

	 loadSelectOptions(columnID, columnName, columnTable, column, name) {
		$.ajax({
			url: this.url,
			type: 'POST',
			data: {
				table: columnTable,
				columnid: columnID,
				columnname: columnName,
				[this.csrfTokenName]: this.csrfHash
			},
			dataType: 'json',
			success: (response) => {
				const select = $(`#filter_${column}`);

				// Kosongkan isi select
				select.empty().append('<option value="">-- Pilih --</option>');

				if (response.options) {
					response.options.forEach(option => {
						if (option.value && option.value.trim() !== '') {
							select.append(`<option value="${this.escapeHtml(option.value)}">${this.escapeHtml(option.text)}</option>`);
						}
					});

					// Inisialisasi ulang Select2 (destroy dulu biar bersih)
					if (select.hasClass('select2-hidden-accessible')) {
						select.select2('destroy');
					}
					select.select2({
						placeholder: 'Pilih ' + name,
						allowClear: true,
						width: '100%'
					});
				}

				// Perbarui CSRF token
				if (response.csrf_hash) {
					window.updateCsrfToken(response.csrf_hash);
				}
			},
			error: (xhr, status, error) => {
				console.error('Failed to load select options:', error);
			}
		});
	}


    setupEvents() {
        // Toggle filter visibility
        $('#toggle-filters').off('click').on('click', () => {
            this.toggleFilterContainer();
        });

        // Apply filters
        $('#apply-filters').off('click').on('click', () => {
            this.applyFilters();
        });

        // Clear filters
        $('#clear-filters').off('click').on('click', () => {
            this.clearFilters();
        });

        // Quick apply on Enter key
        $(document).off('keypress', '.filter-input').on('keypress', '.filter-input', (e) => {
            if (e.which === 13) {
                this.applyFilters();
            }
        });

        // Auto-apply for select changes
        $(document).off('change', '.filter-input[data-type="select"]').on('change', '.filter-input[data-type="select"]', () => {
            this.applyFilters();
        });

        // Save filters on change (debounced)
        let saveTimeout;
        $(document).off('input change', '.filter-input').on('input change', '.filter-input', () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                this.saveFilters();
            }, 1000);
        });
    }

    toggleFilterContainer() {
        const container = $('#filter-container');
        const icon = $('#toggle-filters i');
        
        container.slideToggle(300, () => {
            icon.toggleClass('fa-chevron-down fa-chevron-up');
            
            // Save state
            localStorage.setItem('filterContainerOpen', container.is(':visible'));
        });
    }

    applyFilters() {
        this.collectFilters();
        this.reloadTable();
        this.updateFilterBadge();
        this.saveFilters();
        
        // Show success message for significant filters
        const filterCount = Object.keys(this.appliedFilters).length;
        if (filterCount > 3) {
            this.showToast(`${filterCount} filter diterapkan`, 'success');
        }
    }

    collectFilters() {
        this.appliedFilters = {};

        $('.filter-input').each((index, element) => {
            const $element = $(element);
            const column = $element.data('column');
            const type = $element.data('type');
            const value = $element.val();

            if (value && value.trim() !== '') {
                if (!this.appliedFilters[column]) {
                    this.appliedFilters[column] = {};
                }
                this.appliedFilters[column][type] = value.trim();
            }
        });
    }

    clearFilters() {
        $('.filter-input').val('');
        this.appliedFilters = {};
        this.updateFilterBadge();
        this.reloadTable();
        this.saveFilters();
        
        this.showToast('Filter dibersihkan', 'info');
    }

    reloadTable() {
        if (this.table) {
            this.table.ajax.reload(null, false); // Keep current page
        } else if (window.table) {
            window.table.ajax.reload(null, false);
        }
    }

    updateFilterBadge() {
        const filterCount = Object.keys(this.appliedFilters).length;
        const toggleButton = $('#toggle-filters');
        
        if (filterCount > 0) {
            toggleButton.html(`<i class="fas fa-filter"></i> Filter (${filterCount})`);
            toggleButton.removeClass('btn-light').addClass('btn-primary');
        } else {
            toggleButton.html('<i class="fas fa-filter"></i> Filter');
            toggleButton.removeClass('btn-primary').addClass('btn-light');
        }
    }

    getFiltersForAjax() {
        return this.appliedFilters;
    }

    saveFilters() {
		
		console.log(this.tableRef);
		
        const filterData = {
            filters: this.appliedFilters,
            timestamp: Date.now()
        };
        localStorage.setItem(`tableFilters_${this.tableRef}`, JSON.stringify(filterData));
    }

    loadSavedFilters() {
        try {
            const saved = localStorage.getItem(`tableFilters_${this.tableRef}`);
            if (saved) {
                const filterData = JSON.parse(saved);
                
                // Check if filters are not too old (24 hours)
                const maxAge = 24 * 60 * 60 * 1000;
                if (Date.now() - filterData.timestamp < maxAge) {
                    this.appliedFilters = filterData.filters || {};
                    this.restoreFilterInputs();
                    this.updateFilterBadge();
                }
            }

            // Restore filter container state
            const containerOpen = localStorage.getItem('filterContainerOpen') === 'true';
            if (containerOpen) {
                $('#filter-container').show();
                $('#toggle-filters i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        } catch (error) {
            console.warn('Could not load saved filters:', error);
        }
    }

    restoreFilterInputs() {
        Object.keys(this.appliedFilters).forEach(column => {
            const columnFilters = this.appliedFilters[column];
            
            Object.keys(columnFilters).forEach(type => {
                const value = columnFilters[type];
                const selector = `.filter-input[data-column="${column}"][data-type="${type}"]`;
                $(selector).val(value);
            });
        });
    }

    showToast(message, type = 'info') {
        // Simple toast notification
        const toast = $(`
            <div class="toast-notification toast-${type}" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
                color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
                padding: 12px 20px;
                border-radius: 5px;
                border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
                z-index: 9999;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                max-width: 300px;
            ">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
                ${message}
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut(300, () => toast.remove());
        }, 3000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Export filters for external use
    exportFilters() {
        const filterSummary = {};
        Object.keys(this.appliedFilters).forEach(column => {
            const columnData = this.appliedFilters[column];
            const columnInfo = this.filterColumns.find(col => col.name === column);
            
            if (columnInfo) {
                filterSummary[columnInfo.label] = columnData;
            }
        });
        
        return {
            summary: filterSummary,
            raw: this.appliedFilters,
            count: Object.keys(this.appliedFilters).length
        };
    }

    // Reset specific column filter
    resetColumnFilter(columnName) {
        delete this.appliedFilters[columnName];
        
        // Clear inputs for this column
        $(`.filter-input[data-column="${columnName}"]`).val('');
        
        this.updateFilterBadge();
        this.reloadTable();
        this.saveFilters();
    }
}

// Global instance
window.DynamicFilters = DynamicFilters;