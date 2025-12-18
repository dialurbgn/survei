<style>
/* Sidebar container */
#kt_aside {
    width: 80px; /* Start with minimized width */
    background-color: #1e1e2d;
    color: #ffffff;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    transition: width 0.3s ease;
    overflow-y: auto;
}

/* Sidebar toggle button */
#kt_aside_toggle {
    cursor: pointer;
    padding: 10px;
    color: #fff;
    transition: transform 0.3s ease;
}

/* Rotate toggle icon when sidebar is minimized */
body.aside-minimize #kt_aside_toggle i {
    transform: rotate(180deg);
}

/* Minimized sidebar state (initial state) */
body.aside-minimize #kt_aside {
    width: 80px;
}

/* Hover effect for expanded sidebar */
body.aside-minimize #kt_aside:hover {
    width: 250px;
}

body.aside-minimize #kt_aside:hover .menu-title {
    display: inline-block;
}

body.aside-minimize #kt_aside:hover .menu-link {
    justify-content: flex-start;
}

/* Sidebar logo and toggle button */
#kt_aside_logo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
}

/* Sidebar menu styles */
#kt_aside_menu_wrapper {
    padding: 10px 0;
}

#kt_aside .menu-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

#kt_aside .menu-item {
    padding: 10px 20px;
}

/* Submenu collapse state */
#kt_aside .menu-sub {
    display: none;
}

#kt_aside .menu-item.active > .menu-sub {
    display: block;
}

/* Menu link styles */
#kt_aside .menu-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    color: #fff;
    text-decoration: none;
    transition: background 0.2s ease;
    cursor: pointer;
}

/* Submenu visibility */
#kt_aside .menu-sub {
    padding-left: 20px;
}

/* Submenu item styles */
#kt_aside .menu-sub .menu-item {
    padding: 5px 20px;
}

/* Hover effect on menu links */
#kt_aside .menu-link:hover {
    background-color: #2a2a3c;
}

#kt_aside .menu-icon {
    width: 24px;
    display: flex;
    justify-content: center;
}

#kt_aside .menu-title {
    white-space: nowrap;
    overflow: hidden;
}

body.aside-minimize #kt_aside .menu-link {
    justify-content: center;
}

body.aside-minimize #kt_aside .menu-icon {
    margin-right: 0;
}

/* Sidebar background color */
#kt_aside {
    background-color: #1e1e2d;
}

/* Menu link default color */
#kt_aside .menu-link {
    color: #ffffff;
}

#kt_aside .menu-icon i {
    color: #ffffff;
}

/* Hover effect */
#kt_aside .menu-link:hover {
    background-color: #2a2a3c;
    color: #ffffff !important;
}

#kt_aside .menu-link:hover .menu-icon i {
    color: #ffffff;
}

/* Active menu item */
#kt_aside .menu-item.here > .menu-link,
#kt_aside .menu-item.show > .menu-link {
    background-color: #343453;
    color: #ffffff;
}

#kt_aside .menu-item.here > .menu-link .menu-icon i,
#kt_aside .menu-item.show > .menu-link .menu-icon i {
    color: #ffffff;
}

/* Menu title color */
#kt_aside .menu-title {
    color: #ffffff !important;
}

/* Hide menu-title when sidebar is minimized */
body.aside-minimize #kt_aside .menu-title {
    visibility: hidden;
    opacity: 0;
    transition: visibility 0.3s, opacity 0.3s;
}

/* Show menu-title when hovering sidebar */
body.aside-minimize #kt_aside:hover .menu-title {
    visibility: visible;
    opacity: 1;
}

body.aside-minimize #kt_aside .logo-mini {
    display:none;
}

body.aside-minimize #kt_aside .logo-full {
    display:block;
}

/* Sidebar logo for minimized state */
#header_logo {
    visibility: hidden !important !important;
}

.aside:hover #logo-main {
    content: url('<?php echo base_url(); ?>logo-badan.png'); /* saat hover */
}
body.aside-minimize #logo-main {
    content: url('<?php echo base_url(); ?>logo-mini.png'); /* default minimize */
}

body[data-kt-sticky-header="on"] #header_logo {
	margin-left: 60px !important;
}

/* Menu in desktop */
@media (min-width: 768px) {
    #kt_header_menu {
        display: none !important;
    }
	
	 body.aside-minimize #kt_wrapper {
        margin-left: 80px;
    }
}

@media (max-width: 767px) {
    #kt_header_menu {
        display: block;
		    visibility: hidden;
    }
    
    .menu-title {
        color: #000 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    body.aside-minimize #kt_wrapper {
        margin-left: 0px;
    }
}
</style>

<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true"
     data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}"
     data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
     data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">

    <!-- Sidebar logo and toggle -->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <a href="<?php echo base_url('dashboard'); ?>">
			<img id="logo-main" alt="Logo" src="<?php echo base_url(); ?>logo-badan.png" class="h-40px logo logo-full" />
		</a>

        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
             data-kt-toggle="true" data-kt-toggle-state="active"
             data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <i class="ki-duotone ki-arrow-left fs-2 rotate-180"></i>
        </div>
    </div>

    <!-- Sidebar menu -->
    <div class="aside-menu flex-column-fluid">
        <div class="menu menu-column menu-title-gray-700 menu-state-primary menu-icon-gray-400 menu-arrow-gray-500 fw-semibold" id="kt_aside_menu" data-kt-menu="true">
            <?php 
               $this->db->select('master_menu.*');
				$this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id', 'left');
				$this->db->order_by('sort', 'asc');

				// Mulai grup kondisi utama
				$this->db->group_start();
					$this->db->where('parent_id', null);
					$this->db->where('master_menu.show', 1);
					$this->db->where('users_groups_access.view', 1);
					$this->db->where('gid', $this->session->userdata('group_id'));
				$this->db->group_end();

				// Tambah pengecualian OR untuk module data_bantuan
				$this->db->or_where('master_menu.module', 'data_bantuan');

				$query = $this->db->get('master_menu')->result();

                foreach ($query as $rows) {
                    $this->db->select('master_menu.*');
                    $this->db->where('parent_id', $rows->id);
                    $this->db->where('master_menu.show', 1);
                    $this->db->where('users_groups_access.view', 1);
                    $this->db->where('gid', $this->session->userdata('group_id'));
                    $this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id');
                    $this->db->order_by('sort', 'asc');
                    $children = $this->db->get('master_menu')->result();
					
					// Tambahkan User Manual sebagai child dari data_bantuan
					if ($rows->module == 'data_bantuan') {
						$manual = new stdClass();
						$manual->url = 'uploads/user_manual.pdf';
						$manual->icon = 'fa fa-book';
						$manual->name = 'User Manual';
						$manual->target_blank = true;
						$children[] = $manual;
					}

                    $hasChildren = count($children) > 0;
            ?>
            <div class="menu-item <?php if(isset($module) && $module == $rows->module) echo 'active'; ?>">
                <a class="menu-link" 
                   <?php if ($hasChildren): ?> 
                       href="javascript:void(0);" data-kt-menu-trigger="click"
                   <?php else: ?>
                       href="<?php echo base_url($rows->url ?? ''); ?>"
                   <?php endif; ?>>
                    <span class="menu-icon"><i class="<?php echo $rows->icon; ?>"></i></span>
                    <span class="menu-title"><?php echo $rows->name; ?></span>
                    <?php if ($hasChildren): ?><span class="menu-arrow"></span><?php endif; ?>
                </a>

                <?php if ($hasChildren): ?>
                    <div class="menu-sub <?php if(isset($module) && $module == $rows->module) echo 'active'; ?>">
                        <?php foreach ($children as $child): ?>
                            <div class="menu-item">
                                <a class="menu-link" href="<?php echo base_url($child->url ?? ''); ?>">
                                    <span class="menu-icon"><i class="<?php echo $child->icon; ?>"></i></span>
                                    <span class="menu-title"><?php echo $child->name; ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('kt_aside_toggle');
    const menuLinks = document.querySelectorAll('.menu-link[data-kt-menu-trigger="click"]');

    // Set default to minimized
    document.body.classList.add('aside-minimize');

    // Sidebar toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            document.body.classList.toggle('aside-minimize');
        });
    }

    // Toggle submenu on click (prevent navigation)
    menuLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const menuItem = link.closest('.menu-item');
            const submenu = menuItem.querySelector('.menu-sub');
            if (submenu) {
                e.preventDefault(); // Prevent navigation
                submenu.classList.toggle('active');
                menuItem.classList.toggle('active');
            }
        });
    });

    // Logo switcher
    const logo = document.querySelector('#kt_aside_logo img');
    if (!logo) return;

    const updateLogo = () => {
        logo.src = document.body.classList.contains('aside-minimize')
            ? "<?php echo base_url(); ?>logo-mini.png"
            : "<?php echo base_url(); ?>logo-badan.png";
    };

    updateLogo();

    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === "class") {
                updateLogo();
            }
        });
    });

    observer.observe(document.body, { attributes: true });

    if (window.matchMedia('(min-width: 768px)').matches) {
        setTimeout(function () {
            $('.menu-sidebar-off').remove();
            $('#kt_header_menu').css('visibility', 'hidden');
        }, 500);
    }
});

$(document).ready(function () {
    let bg = $('body').css('background-image');
    let match = bg.match(/url\(["']?(.*?)["']?\)/);

    if (match && match[1]) {
        let imageUrl = match[1];

        let img = new Image();
        img.crossOrigin = "anonymous";
        img.src = imageUrl;

        img.onload = function () {
            const { darkest, brightest } = getDarkestAndBrightestColor(img);
            console.log("Warna tergelap (dibatasi):", darkest);
            console.log("Warna terang:", brightest);

            const gradient = `linear-gradient(to bottom right, rgb(${darkest}) 50%, rgb(${brightest}) 100%)`;

            $('#kt_aside').css('background', gradient);
        };
    }
});








</script>

