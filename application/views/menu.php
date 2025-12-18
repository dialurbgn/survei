<!--begin::Menu-->
<div class="menu-sidebar-off menu-atas menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-700 menu-state-primary menu-arrow-gray-400 fw-semibold my-5 my-lg-0 align-items-stretch px-2 px-lg-0" id="#kt_header_menu" data-kt-menu="true">

<?php 
$groupId = $this->session->userdata('group_id');

// Ambil semua menu induk (parent)
$this->db->select('master_menu.*');
$this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id', 'left');
$this->db->order_by('sort', 'asc');

// Grup kondisi utama
$this->db->group_start();
    $this->db->where('parent_id', null);
    $this->db->where('master_menu.show', 1);
    $this->db->where('users_groups_access.view', 1);
    $this->db->where('gid', $groupId);
$this->db->group_end();

// Tambahkan juga menu dengan module data_bantuan (jika dia bukan menu utama, akan tetap muncul)
$this->db->or_where('master_menu.module', 'data_bantuan');

$menus = $this->db->get('master_menu')->result();

foreach ($menus as $menu) {
    // Ambil anak-anak dari menu ini
    $this->db->select('master_menu.*');
    $this->db->where('parent_id', $menu->id);
    $this->db->where('master_menu.show', 1);
    $this->db->where('users_groups_access.view', 1);
    $this->db->where('gid', $groupId);
    $this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id');
    $this->db->order_by('sort', 'asc');
    $children = $this->db->get('master_menu')->result();

    // Jika module-nya data_bantuan, tambahkan User Manual
    if ($menu->module == 'data_bantuan') {
        $manual = new stdClass();
        $manual->url = 'uploads/user_manual.pdf';
        $manual->icon = 'fa fa-book';
        $manual->name = 'User Manual';
        $manual->target_blank = true;
        $children[] = $manual;
    }

    $hasChildren = count($children) > 0;
    $isActive = (isset($module) && $module == $menu->module) ? 'here' : '';
?>

    <!--begin:Menu item-->
    <div id="menu_id_<?php echo $menu->id; ?>" class="menu-item <?php echo $isActive; ?> <?php echo $hasChildren ? 'menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2' : ''; ?>"
        <?php if ($hasChildren): ?> data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" <?php endif; ?>>

        <?php if ($hasChildren): ?>
            <span class="menu-link py-3">
                <span class="menu-title">
                    <span class="menu-icon"><i class="text-white <?php echo $menu->icon; ?>"></i></span>
                    <?php echo $menu->name; ?><div class="notifnyamenu" id="menu_<?php echo $menu->module; ?>"></div>
                </span>
                <span class="menu-arrow d-lg-none"></span>
            </span>

            <!--begin:Menu sub-->
            <div class="menu-sub sub-menu-custome menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px" style="max-height:300px;overflow: auto !important;">
                <?php foreach ($children as $child): ?>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="<?php echo base_url($child->url ?? ''); ?>" <?php echo isset($child->target_blank) ? 'target="_blank"' : ''; ?>>
                            <span class="menu-icon"><i class="<?php echo $child->icon; ?>"></i></span>
                            <span class="menu-title"><?php echo $child->name; ?><div class="notifnyamenu" id="menu_child_<?php echo str_replace(' ','_',trim($child->name)); ?>"></div></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!--end:Menu sub-->

        <?php else: ?>
            <a class="menu-link py-3" href="<?php echo base_url($menu->url ?? ''); ?>">
                <span class="menu-title">
                    <span class="menu-icon"><i class="text-white <?php echo $menu->icon; ?>"></i></span>
                    <?php echo $menu->name; ?><div class="notifnyamenu" id="menu_<?php echo $menu->module; ?>"></div>
                </span>
                <span class="menu-arrow d-lg-none"></span>
            </a>
        <?php endif; ?>
    </div>
    <!--end:Menu item-->

<?php } ?>
</div>
<!--end::Menu-->
