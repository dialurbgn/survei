<?php
    // Initialize module untuk detail list
    $module_detail_list = 'data_survei_pm_detail_list';
    $exclude_detail_list = array(
        'id', 
        'survei_pm_detail_id', 
        'master_kelompok_id',
        'provinsi_name',
        'kabkota_name', 
        'kecamatan_name',
        'kelurahandes_name',
        'posisi_latitude',  // Exclude karena pakai map
        'posisi_longitude', // Exclude karena pakai map
        'slug',
        'status_id',
        'created',
        'modified',
        'createdid',
        'modifiedid',
        'active'
    );
    
    // Get column metadata from database
    $query_column_detail_list = $this->ortyd->getviewlistform($module_detail_list, $exclude_detail_list, 2);
    
    // Initialize variables
    if($query_column_detail_list){
        foreach($query_column_detail_list as $rows_column){
            ${$rows_column['name']} = ($rows_column['type'] == 'NUMBER' || $rows_column['type'] == 'CURRENCY') ? 0 : '';
        }
    }
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<!-- Modal Detail POK -->
<div class="modal fade" id="modalPOKDetail" tabindex="-1" aria-labelledby="modalPOKDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPOKDetailLabel">
                    <i class="fas fa-list-ul"></i> Detail Unit Kelompok - <span id="modalPOKName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Alert Info -->
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i> 
                    Silakan tambahkan detail unit kelompok untuk Jenis Kelompok ini. Total penerima manfaat akan otomatis dihitung.
                </div>
                
                <!-- Form Input Detail -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form id="formPOKDetail">
                            <input type="hidden" id="detail_id" name="detail_id" value="0">
                            <input type="hidden" id="survei_pm_detail_id" name="survei_pm_detail_id">
                            <input type="hidden" id="master_kelompok_id" name="master_kelompok_id">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token">
                            
                            <!-- Hidden inputs for lat/lng (filled by map) -->
                            <input type="hidden" id="posisi_latitude" name="posisi_latitude" value="0">
                            <input type="hidden" id="posisi_longitude" name="posisi_longitude" value="0">
                            
                            <!-- Display Jenis Kelompok (readonly) -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Jenis Kelompok Penerima Manfaat</label>
                                    <input type="text" class="form-control bg-light" id="display_jenis_kelompok" readonly>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <?php 
                            if($query_column_detail_list) {
                                // Group fields by section
                                $section_general = array('kode_unit', 'nama_unit', 'jenis_kepemilikan');
                                $section_location = array('provinsi_id', 'kabkota_id', 'kecamatan_id', 'kelurahandes_id', 'alamat', 'kode_pos');
                                $section_jumlah = array('jumlah_pria', 'jumlah_wanita', 'jumlah_total');
                                
                                $current_section = '';
                                $row_open = false;
                                
                                foreach($query_column_detail_list as $rows_column) {
                                    $field_name = $rows_column['name'];
                                    $field_label = $this->ortyd->translate_column($module_detail_list, $rows_column['name']);
                                    $field_type = $rows_column['type'];
                                    $is_nullable = $rows_column['is_nullable'] == 'YES';
                                    $required = !$is_nullable ? 'required' : '';
                                    
                                    // Determine section
                                    $new_section = '';
                                    if(in_array($field_name, $section_location)) {
                                        $new_section = 'location';
                                    } elseif(in_array($field_name, $section_jumlah)) {
                                        $new_section = 'jumlah';
                                    } else {
                                        $new_section = 'general';
                                    }
                                    
                                    // Section header
                                    if($new_section != $current_section) {
                                        // Close previous row if open
                                        if($row_open) {
                                            echo '</div>'; // Close row
                                            $row_open = false;
                                        }
                                        
                                        if($new_section == 'location') {
                                            echo '<hr class="my-4">';
                                            echo '<h6 class="mb-3 font-weight-bold"><i class="fas fa-map-marker-alt"></i> Lokasi Unit Kelompok</h6>';
                                        } elseif($new_section == 'jumlah') {
                                            echo '<hr class="my-4">';
                                            echo '<h6 class="mb-3 font-weight-bold"><i class="fas fa-users"></i> Jumlah Anggota Kelompok Utama</h6>';
                                        }
                                        
                                        $current_section = $new_section;
                                    }
                                    
                                    // Determine column width
                                    $col_width = 'col-md-12';
                                    if(in_array($field_name, array('kode_unit', 'jenis_kepemilikan', 'provinsi_id', 'kabkota_id', 'kecamatan_id', 'kelurahandes_id'))) {
                                        $col_width = 'col-md-6';
                                    } elseif(in_array($field_name, array('jumlah_pria', 'jumlah_wanita', 'jumlah_total'))) {
                                        $col_width = 'col-md-4';
                                    } elseif($field_name == 'nama_unit') {
                                        $col_width = 'col-md-12';
                                    }
                                    
                                    // Open row if needed
                                    if(!$row_open && in_array($col_width, array('col-md-6', 'col-md-4'))) {
                                        echo '<div class="row">';
                                        $row_open = true;
                                    }
                                    
                                    // Start field wrapper
                                    echo '<div class="' . $col_width . ' mb-3">';
                                    echo '<label class="form-label" for="' . $field_name . '">';
                                    echo $field_label;
                                    if(!$is_nullable) echo ' <span class="text-danger">*</span>';
                                    echo '</label>';
                                    
                                    // Render field based on type
                                    if($field_name == 'jenis_kepemilikan') {
                                        // Dropdown
                                        echo '<select class="form-control" id="' . $field_name . '" name="' . $field_name . '" ' . $required . '>';
                                        echo '<option value="">Pilih Satu</option>';
                                        echo '<option value="Negeri">Negeri</option>';
                                        echo '<option value="Swasta">Swasta</option>';
                                        echo '</select>';
                                        
                                    } elseif(in_array($field_name, array('provinsi_id', 'kabkota_id', 'kecamatan_id', 'kelurahandes_id', 'wil_id'))) {
                                        // Select2 for location
                                        $disabled = $field_name != 'wil_id' ? 'disabled' : '';
                                        $placeholder = 'Pilih ' . str_replace('_id', '', ucwords(str_replace('_', ' ', $field_name)));
                                        
                                        echo '<select class="form-control select2-modal" id="' . $field_name . '" name="' . $field_name . '" ';
                                        echo 'data-placeholder="' . $placeholder . '" ' . $disabled . '>';
                                        echo '<option value="">' . $placeholder . '</option>';
                                        echo '</select>'; ?>
										
										
										 <!-- ===== LEAFLET MAP SECTION ===== -->
                            <hr class="my-4">
                            <h6 class="mb-3 font-weight-bold">
                                <i class="fas fa-map-marked-alt"></i> Koordinat Lokasi (Peta Interaktif)
                            </h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> 
                                        Klik atau geser marker <span class="badge bg-danger">merah</span> pada peta untuk mengisi koordinat secara otomatis
                                    </p>
                                    
                                    <!-- Map Container -->
                                    <div id="mapContainer" style="height: 400px; width: 100%; border: 2px solid #dee2e6; border-radius: 8px; position: relative; z-index: 1;">
                                        <!-- Loading Overlay -->
                                        <div id="mapLoading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                                                                    background: rgba(255,255,255,0.9); z-index: 1000; 
                                                                    display: flex; align-items: center; justify-content: center;">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary mb-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mb-0 text-muted">Memuat peta...</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Map Info -->
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb"></i> 
                                            <strong>Tips:</strong> Gunakan zoom in/out untuk navigasi peta. 
                                            Geser marker merah ke lokasi yang diinginkan.
                                        </small>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="mt-2 d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnDetectLocation">
                                            <i class="fas fa-crosshairs"></i> Deteksi Lokasi Saya
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetMap">
                                            <i class="fas fa-redo"></i> Reset Peta
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="btnSearchAddress">
                                            <i class="fas fa-search"></i> Cari Alamat
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Koordinat Display (readonly, auto-filled from map) -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Latitude 
                                        <small class="text-muted">(otomatis dari peta)</small>
                                    </label>
                                    <input type="text" class="form-control bg-light" 
                                           id="display_latitude" 
                                           placeholder="-6.200000" readonly>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Longitude 
                                        <small class="text-muted">(otomatis dari peta)</small>
                                    </label>
                                    <input type="text" class="form-control bg-light" 
                                           id="display_longitude" 
                                           placeholder="106.816666" readonly>
                                </div>
                            </div>
                            <!-- ===== END LEAFLET MAP SECTION ===== -->
							
								    <?php
                                        
                                    } elseif($field_name == 'alamat') {
                                        // Textarea
                                        echo '<textarea class="form-control" id="' . $field_name . '" name="' . $field_name . '" rows="2" ';
                                        echo 'placeholder="Contoh: Jl. Raya Pendidikan No. 123" ' . $required . '></textarea>';
                                        
                                    } elseif($field_type == 'NUMBER' || $field_type == 'CURRENCY') {
                                        // Number input
                                        $readonly = $field_name == 'jumlah_total' ? 'readonly' : '';
                                        $bg_class = $field_name == 'jumlah_total' ? 'bg-light' : '';
                                        $min = $field_name == 'jumlah_total' ? '' : 'min="0"';
                                        
                                        echo '<input type="number" class="form-control ' . $bg_class . '" ';
                                        echo 'id="' . $field_name . '" name="' . $field_name . '" ';
                                        echo 'value="0" ' . $min . ' ' . $readonly . ' ' . $required . '>';
                                        
                                    } else {
                                        // Text input
                                        $maxlength = '';
                                        if(isset($rows_column['character_maximum_length']) && $rows_column['character_maximum_length']) {
                                            $maxlength = 'maxlength="' . $rows_column['character_maximum_length'] . '"';
                                        }
                                        
                                        $placeholder = 'Masukkan ' . strtolower($field_label);
                                        if($field_name == 'kode_unit') $placeholder = 'Contoh: TK-001';
                                        if($field_name == 'nama_unit') $placeholder = 'Contoh: TK Pertiwi 1';
                                        if($field_name == 'kode_pos') $placeholder = 'Contoh: 40000';
                                        
                                        echo '<input type="text" class="form-control" ';
                                        echo 'id="' . $field_name . '" name="' . $field_name . '" ';
                                        echo 'placeholder="' . $placeholder . '" ' . $maxlength . ' ' . $required . '>';
                                    }
                                    
                                    echo '</div>'; // Close field wrapper
                                    
                                    // Close row after last field in row
                                    if($row_open && ($field_name == 'jenis_kepemilikan' || $field_name == 'kelurahandes_id' || $field_name == 'jumlah_total')) {
                                        echo '</div>'; // Close row
                                        $row_open = false;
                                    }
                                }
                                
                                // Close any remaining open row
                                if($row_open) {
                                    echo '</div>';
                                }
                            }
                            ?>
                            
                           
                            
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary" id="btnResetForm">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnSaveDetail">
                                    <i class="fas fa-save"></i> <span id="btnSaveText">Simpan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- List Detail yang sudah ada -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-list"></i> Daftar Unit Kelompok (<span id="totalUnits">0</span> unit)
                        </h6>
                        <div>
                            <span class="badge bg-info">Total Penerima: <span id="totalPenerimaInModal">0</span> orang</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tableDetailList">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode/Nama Kelompok</th>
                                        <th width="12%">Kepemilikan</th>
                                        <th width="20%">Lokasi</th>
                                        <th width="8%" class="text-center">Pria</th>
                                        <th width="8%" class="text-center">Wanita</th>
                                        <th width="8%" class="text-center">Total</th>
                                        <th width="12%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyDetailList">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">Belum ada data unit kelompok</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styling */
.modal-xl {
    max-width: 1200px;
}

#modalPOKDetail .card {
    border-radius: 8px;
}

#modalPOKDetail .form-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

#modalPOKDetail .table th {
    font-weight: 600;
    font-size: 0.85rem;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

#modalPOKDetail .table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

#modalPOKDetail .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

#modalPOKDetail .select2-modal {
    width: 100% !important;
}

/* Badge styling */
.badge {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
}

/* Make Select2 work in modal */
.select2-container {
    z-index: 9999 !important;
}

/* === LEAFLET MAP STYLES === */
#mapContainer {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.leaflet-container {
    font-family: inherit;
    border-radius: 8px;
    z-index: 1;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

.leaflet-popup-content {
    margin: 13px 19px;
    font-size: 13px;
}

/* Custom Marker Popup */
.marker-popup {
    text-align: center;
}

.marker-popup .coordinates {
    font-family: monospace;
    font-size: 12px;
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 4px;
    margin-top: 5px;
}

/* Loading State */
#mapLoading {
    transition: opacity 0.3s ease;
}

#mapLoading.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Map Controls Enhancement */
.leaflet-control-zoom a {
    width: 30px;
    height: 30px;
    line-height: 30px;
    font-size: 18px;
}

.leaflet-bar {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    #modalPOKDetail .table {
        font-size: 0.8rem;
    }
    
    #mapContainer {
        height: 300px;
    }
    
    .leaflet-control-zoom a {
        width: 26px;
        height: 26px;
        line-height: 26px;
        font-size: 16px;
    }
}
</style>

<script>
/**
 * POK Detail Handler - Simplified Version
 * Wilayah: Single select (wil_id, wil_name) - Same as main form
 */

(function() {
    'use strict';
    
    // ===================================
    // GLOBAL VARIABLES
    // ===================================
    
    let currentPOKNumber = null;
    let currentPOKName = '';
    let currentMasterKelompokId = null;
    let currentSurveiPmDetailId = null;
    
    // Leaflet map
    let map = null;
    let marker = null;
    let isMapInitialized = false;
    const DEFAULT_CENTER = [-6.200000, 106.816666];
    const DEFAULT_ZOOM = 13;
    
    // ===================================
    // INITIALIZATION
    // ===================================
    
    $(document).ready(function() {
        console.log('POK Detail Handler - Initializing...');
        initPOKDetailHandlers();
        initFormHandlers();
    });
    
    /**
     * Initialize POK detail buttons
     */
    function initPOKDetailHandlers() {
        $('[id^="survei_pm_pok_"]').each(function() {
            const inputField = $(this);
            const fieldId = inputField.attr('id');
            const pokNumber = fieldId.replace('survei_pm_pok_', '');
            
            if (inputField.next('.btn-pok-detail').length) {
                return;
            }
            
            const btnDetail = $('<button type="button" class="btn btn-sm btn-info btn-pok-detail ms-2">')
                .html('<i class="fas fa-list"></i> Isi Detail Data')
                .attr('data-pok-number', pokNumber);
            
            inputField.after(btnDetail);
        });
        
        console.log('POK Detail buttons initialized');
    }
    
    // ===================================
    // MODAL OPEN
    // ===================================
    
    $(document).on('click', '.btn-pok-detail', function(e) {
        e.preventDefault();
        const pokNumber = $(this).data('pok-number');
        openPOKDetailModal(pokNumber);
    });
    
    function openPOKDetailModal(pokNumber) {
        currentPOKNumber = pokNumber;
        
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        $.ajax({
            url: baseurl + 'frontend/get_master_kelompok',
            type: 'POST',
            data: {
                pok_number: pokNumber,
                '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
            },
            dataType: 'json',
            success: function(response) {
				updateCsrfToken(response.csrf_hash);
                if (response.status === 'success') {
                    currentPOKName = response.data.nama_kelompok;
                    currentMasterKelompokId = response.data.id;
                    
                    $('#modalPOKName').text(currentPOKName);
                    $('#display_jenis_kelompok').val(currentPOKName);
                    $('#master_kelompok_id').val(currentMasterKelompokId);
                    
                    getSurveiPmDetailId(function() {
                        loadPOKDetailList();
                        Swal.close();
                        $('#modalPOKDetail').modal('show');
                    });
                } else {
                    Swal.fire('Error', response.message || 'Gagal memuat data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    }
    
    /**
     * Get survei_pm_detail_id
     */
    function getSurveiPmDetailId(callback) {
        const formId = $('form[id^="formSurvei"]').attr('id');
        const surveiId = formId ? formId.replace('formSurvei', '') : '0';
        
        if (!$('#form_survei_id').length) {
            $('#formPOKDetail').append('<input type="hidden" id="form_survei_id" name="form_survei_id" value="' + surveiId + '">');
        } else {
            $('#form_survei_id').val(surveiId);
        }
        
        if (surveiId && surveiId !== '0') {
            $.ajax({
                url: baseurl + 'frontend/get_survei_detail_id',
                type: 'POST',
                data: {
                    survei_pm_id: surveiId,
                    '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
                },
                dataType: 'json',
                success: function(response) {
					updateCsrfToken(response.csrf_hash);
                    if (response.status === 'success') {
                        currentSurveiPmDetailId = response.detail_id || null;
                        $('#survei_pm_detail_id').val(currentSurveiPmDetailId || '0');
                    }
                    if (response.csrf_hash) {
                        $('.csrf_token').val(response.csrf_hash);
                    }
                    if (callback) callback();
                },
                error: function() {
                    currentSurveiPmDetailId = null;
                    $('#survei_pm_detail_id').val('0');
                    if (callback) callback();
                }
            });
        } else {
            currentSurveiPmDetailId = null;
            $('#survei_pm_detail_id').val('0');
            if (callback) callback();
        }
    }
    
    /**
     * Load POK detail list
     */
    function loadPOKDetailList() {
        if (!currentMasterKelompokId) {
            showEmptyState();
            return;
        }
        
        $.ajax({
            url: baseurl + 'frontend/get_pok_detail_list',
            type: 'POST',
            data: {
                survei_pm_detail_id: currentSurveiPmDetailId || 0,
                master_kelompok_id: currentMasterKelompokId,
                '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
            },
            dataType: 'json',
            success: function(response) {
				updateCsrfToken(response.csrf_hash);
                if (response.status === 'success') {
                    renderDetailList(response.data);
                    updateTotalInModal(response.total);
                    updateMainFormValue(response.total);
                } else {
                    showEmptyState();
                }
                
                if (response.csrf_hash) {
                    $('.csrf_token').val(response.csrf_hash);
                }
            },
            error: function() {
                showEmptyState();
            }
        });
    }
    
    function showEmptyState() {
        $('#tbodyDetailList').html(
            '<tr><td colspan="8" class="text-center text-muted py-4">' +
            '<i class="fas fa-inbox fa-2x mb-2"></i>' +
            '<p class="mb-0">Belum ada data unit kelompok</p></td></tr>'
        );
        $('#totalUnits').text('0');
        updateTotalInModal(0);
    }
    
    function renderDetailList(data) {
        if (!data || data.length === 0) {
            showEmptyState();
            return;
        }
        
        let html = '';
        $.each(data, function(index, item) {
            html += '<tr>';
            html += '<td>' + (index + 1) + '</td>';
            html += '<td>';
            if (item.kode_unit) {
                html += '<strong>' + escapeHtml(item.kode_unit) + '</strong><br>';
            }
            html += '<small class="text-muted">' + escapeHtml(item.nama_unit) + '</small>';
            html += '</td>';
            html += '<td>' + (item.jenis_kepemilikan || '-') + '</td>';
            html += '<td><small>' + (item.wil_name || '-') + '</small></td>';
            html += '<td class="text-center">' + (item.jumlah_pria || 0) + '</td>';
            html += '<td class="text-center">' + (item.jumlah_wanita || 0) + '</td>';
            html += '<td class="text-center"><strong>' + (item.jumlah_total || 0) + '</strong></td>';
            html += '<td class="text-center">';
            html += '<button type="button" class="btn btn-sm btn-warning btn-edit-detail me-1" data-id="' + item.id + '">' +
                    '<i class="fas fa-edit"></i></button>';
            html += '<button type="button" class="btn btn-sm btn-danger btn-delete-detail" data-id="' + item.id + '">' +
                    '<i class="fas fa-trash"></i></button>';
            html += '</td>';
            html += '</tr>';
        });
        
        $('#tbodyDetailList').html(html);
        $('#totalUnits').text(data.length);
    }
    
    function updateTotalInModal(total) {
        $('#totalPenerimaInModal').text(total || 0);
    }
    
    function updateMainFormValue(total) {
        const inputField = $('#survei_pm_pok_' + currentPOKNumber);
        inputField.val(total || 0);
        inputField.trigger('change');
        
        if (typeof window.calculateTotalPenerima === 'function') {
            window.calculateTotalPenerima();
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // ===================================
    // WILAYAH SELECT2 - SIMPLE
    // ===================================
    
    $('#modalPOKDetail').on('shown.bs.modal', function() {
        initWilayahSelect();
        
        if (!isMapInitialized) {
            createMap();
        } else {
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 100);
        }
    });
    
    /**
     * Initialize simple wilayah select (same as main form)
     */
    function initWilayahSelect() {
        // Destroy if exists
        if ($('#wil_id').hasClass('select2-hidden-accessible')) {
            $('#wil_id').select2('destroy');
        }
        
        $('#wil_id').select2({
            dropdownParent: $('#modalPOKDetail'),
            placeholder: 'Pilih Wilayah',
            allowClear: true,
            width: '100%',
            ajax: {
                url: baseurl + 'frontend/select2_kecamatan',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    if (!params) params = {};
                    return {
                        q: params.term || '',
						table: 'm_set_wil_administratif',
						id:'wil_id',
						name:'wil_keyword',
                        page: params.page || 1,
                        '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
                    };
                },
                processResults: function(data, params) {
                    if (!params) params = {};
                    if (!params.page) params.page = 1;
                    
                    if (data.csrf_hash) {
                        $('.csrf_token').val(data.csrf_hash);
                    }
                    
                    const results = (data.items || []).map(function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    });
                    
                    return {
                        results: results,
                        pagination: {
                            more: (params.page * 30) < (data.total_count || 0)
                        }
                    };
                },
                cache: true
            }
        });
    }
    
    /**
     * Wilayah change - auto geocode
     */
    $(document).on('change', '#wil_id', function() {
        const wilText = $(this).find('option:selected').text();
        
        if (wilText && wilText !== 'Pilih Wilayah') {
            setTimeout(function() {
                performSearch(wilText + ', Indonesia');
            }, 500);
        }
    });
    
    // ===================================
    // FORM HANDLERS
    // ===================================
    
    function initFormHandlers() {
        $(document).on('input change', '#jumlah_pria, #jumlah_wanita', function() {
            const pria = parseInt($('#jumlah_pria').val()) || 0;
            const wanita = parseInt($('#jumlah_wanita').val()) || 0;
            $('#jumlah_total').val(pria + wanita);
        });
        
        $(document).on('submit', '#formPOKDetail', function(e) {
            e.preventDefault();
            savePOKDetail();
        });
        
        $(document).on('click', '#btnResetForm', resetForm);
        $(document).on('click', '.btn-edit-detail', function() {
            editPOKDetail($(this).data('id'));
        });
        $(document).on('click', '.btn-delete-detail', function() {
            deletePOKDetail($(this).data('id'));
        });
    }
    
    function savePOKDetail() {
        const namaUnit = $('#nama_unit').val();
        if (!namaUnit || namaUnit.trim() === '') {
            Swal.fire('Peringatan', 'Nama unit kelompok harus diisi', 'warning');
            return;
        }
        
        const formData = $('#formPOKDetail').serialize();
        
        $('#btnSaveDetail').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: baseurl + 'frontend/save_pok_detail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const icon = response.auto_created ? 'info' : 'success';
                    const title = response.auto_created ? 'Berhasil (Auto-Create)' : 'Berhasil';
                    
                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: response.message,
                        timer: response.auto_created ? 3000 : 1500,
                        showConfirmButton: response.auto_created
                    });
                    
                    if (response.survei_pm_detail_id) {
                        currentSurveiPmDetailId = response.survei_pm_detail_id;
                        $('#survei_pm_detail_id').val(currentSurveiPmDetailId);
                    }
                    
                    if (response.csrf_hash) {
                        $('.csrf_token').val(response.csrf_hash);
                    }
                    
                    loadPOKDetailList();
                    resetForm();
                } else {
                    Swal.fire('Error', response.message || 'Gagal menyimpan', 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $('#btnSaveDetail').prop('disabled', false).html('<i class="fas fa-save"></i> <span id="btnSaveText">Simpan</span>');
            }
        });
    }
    
    function editPOKDetail(detailId) {
        $.ajax({
            url: baseurl + 'frontend/get_pok_detail',
            type: 'POST',
            data: {
                id: detailId,
                '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    if (response.csrf_hash) {
                        $('.csrf_token').val(response.csrf_hash);
                    }
                    
                    $('#detail_id').val(data.id);
                    
                    for (let key in data) {
                        if ($('#' + key).length && key !== 'id') {
                            $('#' + key).val(data[key]);
                        }
                    }
                    
                    // Wilayah select
                    if (data.wil_id && data.wil_name) {
                        const option = new Option(data.wil_name, data.wil_id, true, true);
                        $('#wil_id').empty().append(option).trigger('change');
                    }
                    
                    // Load coordinates to map
                    if (data.posisi_latitude && data.posisi_longitude && 
                        data.posisi_latitude != 0 && data.posisi_longitude != 0) {
                        setTimeout(function() {
                            setMapCoordinates(
                                parseFloat(data.posisi_latitude), 
                                parseFloat(data.posisi_longitude), 
                                15
                            );
                        }, 500);
                    }
                    
                    $('#formTitle').text('Edit Unit Kelompok');
                    $('#btnSaveText').text('Update');
                    
                    $('.modal-body').animate({ scrollTop: 0 }, 300);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        });
    }
    
    function deletePOKDetail(detailId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseurl + 'frontend/delete_pok_detail',
                    type: 'POST',
                    data: {
                        id: detailId,
                        '<?= $this->security->get_csrf_token_name(); ?>': $('.csrf_token').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil dihapus',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            if (response.csrf_hash) {
                                $('.csrf_token').val(response.csrf_hash);
                            }
                            
                            loadPOKDetailList();
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    }
    
    function resetForm() {
        $('#formPOKDetail')[0].reset();
        $('#detail_id').val('0');
        $('#display_jenis_kelompok').val(currentPOKName);
        $('#master_kelompok_id').val(currentMasterKelompokId);
        $('#survei_pm_detail_id').val(currentSurveiPmDetailId || '0');
        $('#jumlah_total, #jumlah_pria, #jumlah_wanita').val('0');
        $('#posisi_latitude, #posisi_longitude').val('0');
        $('#display_latitude, #display_longitude').val('');
        $('#formTitle').text('Tambah Unit Kelompok Baru');
        $('#btnSaveText').text('Simpan');
        
        //$('#wil_id').val('').trigger('change');
        
        if (map && marker) {
            setMapCoordinates(DEFAULT_CENTER[0], DEFAULT_CENTER[1], DEFAULT_ZOOM);
        }
    }
    
    // ===================================
    // LEAFLET MAP
    // ===================================
    
    function createMap() {
        if (isMapInitialized) return;
        
        try {
            map = L.map('mapContainer', {
                center: DEFAULT_CENTER,
                zoom: DEFAULT_ZOOM,
                zoomControl: true,
                scrollWheelZoom: true
            });
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19,
                minZoom: 5
            }).addTo(map);
            
            const redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            marker = L.marker(DEFAULT_CENTER, {
                draggable: true,
                icon: redIcon
            }).addTo(map);
            
            marker.on('dragend', function() {
                const pos = marker.getLatLng();
                updateMapCoordinates(pos.lat, pos.lng);
                updateMarkerPopup(pos.lat, pos.lng);
            });
            
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateMapCoordinates(e.latlng.lat, e.latlng.lng);
                updateMarkerPopup(e.latlng.lat, e.latlng.lng);
            });
            
            updateMarkerPopup(DEFAULT_CENTER[0], DEFAULT_CENTER[1]);
            
            setTimeout(function() {
                $('#mapLoading').addClass('hidden');
                if (map) map.invalidateSize();
            }, 500);
            
            isMapInitialized = true;
            console.log('Map initialized');
            
        } catch (error) {
            console.error('Map error:', error);
            $('#mapLoading').html('<div class="alert alert-danger m-3">Gagal memuat peta</div>');
        }
    }
    
    function updateMapCoordinates(lat, lng) {
        const formattedLat = parseFloat(lat).toFixed(8);
        const formattedLng = parseFloat(lng).toFixed(8);
        
        $('#posisi_latitude').val(formattedLat);
        $('#posisi_longitude').val(formattedLng);
        $('#display_latitude').val(formattedLat);
        $('#display_longitude').val(formattedLng);
    }
    
    function updateMarkerPopup(lat, lng) {
        if (!marker) return;
        
        const content = `
            <div class="marker-popup">
                <strong>📍 Lokasi Unit</strong>
                <div class="coordinates">
                    <div><strong>Lat:</strong> ${parseFloat(lat).toFixed(8)}</div>
                    <div><strong>Lng:</strong> ${parseFloat(lng).toFixed(8)}</div>
                </div>
                <small class="text-muted d-block mt-2">Geser marker untuk mengubah</small>
            </div>
        `;
        
        marker.bindPopup(content).openPopup();
    }
    
    function setMapCoordinates(lat, lng, zoom) {
        if (!map || !marker) return;
        
        zoom = zoom || DEFAULT_ZOOM;
        const latlng = L.latLng(lat, lng);
        
        marker.setLatLng(latlng);
        map.setView(latlng, zoom);
        
        updateMapCoordinates(lat, lng);
        updateMarkerPopup(lat, lng);
    }
    
    function detectLocation() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Browser tidak mendukung geolocation', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Mendeteksi lokasi...',
            html: 'Mohon izinkan akses lokasi',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                Swal.close();
                setMapCoordinates(position.coords.latitude, position.coords.longitude, 15);
                Swal.fire({
                    icon: 'success',
                    title: 'Lokasi Terdeteksi',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            function(error) {
                Swal.close();
                let msg = 'Gagal mendeteksi lokasi';
                if (error.code === 1) msg = 'Izin lokasi ditolak';
                else if (error.code === 2) msg = 'Lokasi tidak tersedia';
                else if (error.code === 3) msg = 'Timeout';
                Swal.fire('Error', msg, 'error');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
    
    function resetMapPosition() {
        setMapCoordinates(DEFAULT_CENTER[0], DEFAULT_CENTER[1], DEFAULT_ZOOM);
        Swal.fire({
            icon: 'info',
            title: 'Peta Direset',
            timer: 1000,
            showConfirmButton: false
        });
    }
    
    function searchAddress() {
        Swal.fire({
            title: 'Cari Alamat',
            input: 'text',
            inputPlaceholder: 'Contoh: Monas Jakarta',
            showCancelButton: true,
            confirmButtonText: 'Cari',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) return 'Silakan masukkan alamat';
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                performSearch(result.value);
            }
        });
    }
    
    function performSearch(query) {
        Swal.fire({
            title: 'Mencari...',
            html: query,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;
        
        fetch(url)
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data && data.length > 0) {
                    const result = data[0];
                    setMapCoordinates(parseFloat(result.lat), parseFloat(result.lon), 15);
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditemukan',
                        html: `<small>${result.display_name}</small>`,
                        timer: 2000
                    });
                } else {
                    Swal.fire('Tidak Ditemukan', 'Coba kata kunci lain', 'warning');
                }
            })
            .catch(err => {
                Swal.close();
                Swal.fire('Error', 'Gagal mencari lokasi', 'error');
                console.error('Geocoding error:', err);
            });
    }
    
    $(document).on('click', '#btnDetectLocation', detectLocation);
    $(document).on('click', '#btnResetMap', resetMapPosition);
    $(document).on('click', '#btnSearchAddress', searchAddress);
    
    // ===================================
    // EXPOSE GLOBAL
    // ===================================
    
    window.POKDetailHandler = {
        openModal: openPOKDetailModal,
        setMapCoordinates: setMapCoordinates,
        resetMap: resetMapPosition
    };
    
    console.log('POK Detail Handler - Ready!');
    
})();
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>