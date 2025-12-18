<?php
    $query_column = $this->ortyd->query_column($module, $exclude);
    if ($query_column) {
        foreach ($query_column as $rows_column) {
            if ($rows_column['name'] == 'active') {
                ${$rows_column['name']} = 1;
            } else {
                ${$rows_column['name']} = null;
            }
        }
        $tanggal = date('Y-m-d');

        if (isset($id)) {
            if ($id == '0') {
                $id = '';
                $iddata = 0;
                $typedata = 'Buat';
            } else {
                $iddata = $id;
                $typedata = 'Edit';
                if ($datarow) {
                    foreach ($query_column as $rows_column) {
                        foreach ($datarow as $rows) {
                            ${$rows_column['name']} = $rows->{$rows_column['name']};
                        }
                    }
                }
            }
        } else {
            $id = '';
            $iddata = 0;
            $typedata = 'Buat';
        }
    } else {
        $newURL = base_url($module);
        header('Location: ' . $newURL);
    }

    $created = $this->ortyd->select2_getname($iddata, 'vw_data_inbox', 'id', 'created');
    $created = date_create($created);
    $created = date_format($created, 'd F Y H:i:s');
    $ticket_no = $this->ortyd->select2_getname($iddata, 'vw_data_inbox', 'id', 'from_fullname');
    $pelapor = $this->ortyd->select2_getname($iddata, 'vw_data_inbox', 'id', 'from_fullname');
?>

<style>
.form-label-custom {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #3f4254;
    margin-bottom: 0.35rem;
    display: block;
}
.ticket-font-view-custom {
    background-color: #f9f9f9;
    border: 1px solid #e4e6ef;
    padding: 0.75rem 1rem;
    border-radius: 0.475rem;
    font-size: 1rem;
    color: #181c32;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}
</style>

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        <?php include(APPPATH . "views/navbar_header_form.php"); ?>

        <div class="row gx-6 gx-xl-9">
            <div class="col-lg-12">
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if ($query_column) {
                                foreach ($query_column as $rows_column) {
                                    $field = $rows_column['name'];
                                    $label_name = $this->ortyd->translate_column($module, $field);
                                    $width_column = $this->ortyd->width_column($module, $field);
                                    $tipe_data = $this->ortyd->getTipeData($module, $field);

                                    if ($tipe_data == 'SELECT') {
                                        $ref = $this->ortyd->get_table_reference($module, $field);
                                        if ($ref) {
                                            $ref_table = $ref[0];
                                            $ref_column = $ref[2];
                                            ${$field} = $this->ortyd->select2_getname(${$field}, $ref_table, 'id', $ref_column);
                                        }
                                    }

                                    if ($field == 'tanggal') {
                                        ?>
                                        <div class="col-lg-4 py-3">
                                            <div class="form-group">
                                                <label class="form-label-custom">Nomor Tiket</label>
                                                <div class="ticket-font-view-custom"><?= $ticket_no ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 py-3">
                                            <div class="form-group">
                                                <label class="form-label-custom">Pelapor</label>
                                                <div class="ticket-font-view-custom"><?= $pelapor ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-<?= $width_column ?> py-3">
                                            <div class="form-group">
                                                <label class="form-label-custom"><?= $label_name ?></label>
                                                <div class="ticket-font-view-custom"><?= $created ?></div>
                                            </div>
                                        </div>
                                        <?php
                                    } elseif ($tipe_data == 'FILE' || $field == 'file_id') {
                                        include(APPPATH . "views/common/uploadformside.php");
                                    } else {
                                        ?>
                                        <div class="col-lg-<?= $width_column ?> py-3">
                                            <div class="form-group">
                                                <label class="form-label-custom"><?= $label_name ?></label>
                                                <div class="ticket-font-view-custom"><?= ${$field} ?></div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="card-footer col-lg-12 py-3" id="btn-cancel-submit">
                            <!-- tombol bisa ditambahkan di sini -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    setTimeout(function() {
        $('.dropzone-panel').hide();
        $('.dropzone-toolbar').hide();
        $('.dropzone-text-muted').hide();
    }, 500);
});
</script>
