<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shortcode
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

   public function parse($content)
	{
		$content = preg_replace_callback('/\[berita_kemendag\s+limit="(\d+)"\]/', function ($matches) {
			$limit = isset($matches[1]) ? (int)$matches[1] : 4;
			return $this->render_berita_kemendag($limit);
		}, $content);

		// Your existing shortcode handlers
		$content = preg_replace_callback('/\[slider\s+ids="(.+?)"(?:\s+class="(.+?)")?(?:\s+id="(.+?)")?\]/', function ($matches) {
			$ids = $matches[1];
			$class = isset($matches[2]) ? $matches[2] : '';
			$id = isset($matches[3]) ? $matches[3] : '';
			return $this->render_slider($ids, $class, $id);
		}, $content);

		$content = preg_replace_callback('/\[gallery\s+ids="(.+?)"\]/', function ($matches) {
			return $this->render_gallery($matches[1]);
		}, $content);

		$content = preg_replace_callback('/\[video\s+id="(.+?)"\]/', function ($matches) {
			return $this->render_video($matches[1]);
		}, $content);
		
		// shortcode instagram_slider dengan atribut username dan limit
		$content = preg_replace_callback('/\[instagram_slider\s+ids="(.+?)"\]/', function ($matches) {
			$ids = explode(',', $matches[1]);
			return $this->render_instagram_slider_from_ids($ids);
		}, $content);
		
		// Tambah shortcode slider_produk dengan opsi limit
		$content = preg_replace_callback('/\[slider_produk(?:\s+limit="(\d+)")?\]/', function ($matches) {
			$limit = isset($matches[1]) ? (int)$matches[1] : 3; // default 3 produk
			return $this->render_slider_produk($limit);
		}, $content);

		$content = preg_replace('/\[html\](.*?)\[\/html\]/s', '$1', $content);

		return $content;
	}
protected function render_slider_produk(int $limit = 3)
{
    // Ambil data produk dari database dengan limit dinamis
    $this->CI->db->select('
        product_code AS kode,
        laporan_no as nomor_publikasi,
        laporan_no as nomor_laporan,
        product_name as nama_produk,
        resiko_deskripsi as risiko,
        lokasi,
        tindak_lanjut,
        gambar as image_product
    ');
    $this->CI->db->from('vw_data_laporan');
    $this->CI->db->where('status_id', 4);
    $this->CI->db->limit($limit);
    $query = $this->CI->db->get();

    $produk = $query->result_array();
    if (empty($produk)) {
        return null;
    }

    // CSS sama seperti sebelumnya (dipotong di sini supaya tidak terlalu panjang di jawaban)
    $css = <<<CSS
<style>
.owl-slider-product {background-color:#fff;}
.owl-slider-product .header-title {
    margin:0 auto 10px auto;
    padding:20px;
    background:#dc3545;
    color:#fff;
    font-weight:700;
    font-size:1.4rem;
    text-align:center;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(220,53,69,0.5);
    letter-spacing:0.03em;
    text-transform:uppercase;
}
.owl-slider-product .item {display:flex !important;align-items:center;padding:20px 0;}
.owl-slider-product .col-md-6.text-center {
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
.owl-slider-product .col-md-6.text-center a {
    cursor:pointer;
    text-decoration:none;
    color:inherit;
    transition:transform 0.3s ease;
}
.owl-slider-product .col-md-6.text-center a:hover {transform:scale(1.05);}
.owl-slider-product .col-md-6.text-center img {
    max-width:100%;
    max-height:400px;
    object-fit:contain;
    border-radius:15px;
    box-shadow:0 8px 20px rgba(220,53,69,0.5);
    transition:box-shadow 0.3s ease;
    filter:drop-shadow(0 0 8px #dc3545);
}
.owl-slider-product .col-md-6.text-center a:hover img {box-shadow:0 12px 30px rgba(220,53,69,0.7);}
.owl-slider-product .col-md-6 .product-detail {
    background:#fff3f3;
    padding:40px 30px;
    border-radius:15px;
    box-shadow:0 8px 30px rgba(220,53,69,0.2);
    transition:box-shadow 0.3s ease;
    border:2px solid #dc3545;
}
.owl-slider-product .col-md-6 .product-detail:hover {box-shadow:0 12px 45px rgba(220,53,69,0.4);}
.owl-slider-product .product-detail h4 {
    font-weight:700;
    color:#b02a37;
    margin-bottom:25px;
    font-size:1.8rem;
    letter-spacing:0.05em;
    text-transform:uppercase;
    border-bottom:3px solid #dc3545;
    padding-bottom:10px;
}
.owl-slider-product .product-detail p {
    font-size:1rem;
    line-height:1.6;
    color:#5a2127;
    margin-bottom:15px;
    font-weight:600;
}
.owl-slider-product .product-detail p strong {color:#dc3545;font-weight:700;}
.owl-slider-product .owl-nav {
    position:absolute;
    top:50%;
    width:100%;
    display:flex;
    justify-content:space-between;
    transform:translateY(-50%);
    padding:0 20px;
    pointer-events:none;
}
.owl-slider-product .owl-nav button {
    pointer-events:all;
    background:#fff3f3;
    border-radius:50%;
    border:2px solid #dc3545;
    padding:10px 15px;
    box-shadow:0 2px 6px rgba(220,53,69,0.4);
    transition:background-color 0.3s ease;
}
.owl-slider-product .owl-nav button:hover {background:#dc3545;}
.owl-slider-product .owl-nav button span {
    font-size:2rem;
    color:#dc3545;
}
.owl-slider-product .owl-dots {
    text-align:center;
    margin-top:20px;
}
.owl-slider-product .owl-dot span {
    width:12px;
    height:12px;
    background:#dc3545;
    display:inline-block;
    margin:5px;
    border-radius:50%;
    opacity:0.4;
}
.owl-slider-product .owl-dot.active span {opacity:1;}
@media (max-width: 767.98px) {
  .owl-slider-product .item {min-height:auto;padding:20px 0;}
  .owl-slider-product .col-md-6.text-center img {max-height:250px;}
  .owl-slider-product .product-detail {padding:30px 20px;}
  .owl-slider-product .product-detail h4 {font-size:1.5rem;}
}
.owl-slider-product .owl-stage-outer {padding-bottom:40px;}
.owl-slider-product .owl-carousel .item {margin:0 15px;box-sizing:border-box;}
.owl-slider-product .owl-carousel {margin-left:-15px;margin-right:-15px;}
.owl-slider-product .owl-dots {margin-top:30px;}
.owl-slider-product .owl-dot {display:inline-block;margin:0 8px;}
.owl-slider-product .owl-dot span {
    width:16px;
    height:16px;
    background:#dc3545;
    border-radius:50%;
    opacity:0.3;
    transition:opacity 0.3s ease, transform 0.3s ease;
    cursor:pointer;
    box-shadow:0 0 6px rgba(220,53,69,0.5);
}
.owl-slider-product .owl-dot.active span,
.owl-slider-product .owl-dot:hover span {
    opacity:1;
    transform:scale(1.3);
    box-shadow:0 0 15px rgba(220,53,69,0.9);
}

/* Styling tombol Lihat Semua Produk */
.owl-slider-product .lihat-semua-container {
    text-align: center;
    margin-top: 30px;
}
.owl-slider-product .btn-lihat-semua {
    display: inline-block;
    background-color: #dc3545;
    color: #fff;
    font-weight: 700;
    padding: 12px 30px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(220,53,69,0.5);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.05em;
	margin-top:20px;
}
.owl-slider-product .btn-lihat-semua:hover {
    background-color: #b02a37;
    box-shadow: 0 8px 30px rgba(176,42,55,0.7);
}
</style>
CSS;

    $html = '<div class="owl-slider-product">';
    $html .= '<div class="header-title">Produk/Barang Beredar di Pasar yang Tidak Sesuai Standar Nasional Indonesia<br>atau Standar Lainnya dan Dapat Merugikan Konsumen Bila Digunakan</div>';
    $html .= '<div id="unix">';

    foreach ($produk as $p) {
        // Proses image
        if (!empty($p['image_product'])) {
            $arrayImages = explode(',', $p['image_product']);
            $firstImage = trim($arrayImages[0]);
            $imageUrl = base_url() . $firstImage;
        } else {
            $imageUrl = base_url() . 'themes/ortyd/assets/media/avatars/no_image.jpg';
        }

        // Escape data untuk HTML
        $kode = htmlspecialchars($p['kode']);
        $nomorPublikasi = htmlspecialchars($p['nomor_publikasi']);
        $nomorLaporan = htmlspecialchars($p['nomor_laporan']);
        $namaProduk = htmlspecialchars($p['nama_produk']);
        $risiko = htmlspecialchars($p['risiko']);
        $lokasi = htmlspecialchars($p['lokasi']);
        $tindakLanjut = htmlspecialchars($p['tindak_lanjut']);
        $imageUrlHtml = htmlspecialchars($imageUrl);

        // Render item dengan data-* attributes untuk digunakan JS
        $html .= '<div class="item">';
        $html .= '<div class="container-fluid py-4">';
        $html .= '<div class="row align-items-center">';
        $html .= '<div class="col-md-6 text-center">';
        $html .= "<a href=\"#\" 
            onclick=\"openproduk(this); return false;\" 
            data-kode=\"$kode\" 
            data-nomor-publikasi=\"$nomorPublikasi\" 
            data-nomor-laporan=\"$nomorLaporan\" 
            data-nama-produk=\"$namaProduk\" 
            data-risiko=\"$risiko\" 
            data-lokasi=\"$lokasi\" 
            data-tindak-lanjut=\"$tindakLanjut\" 
            data-image=\"$imageUrlHtml\"
        >";
        $html .= "<img data-lazy=\"$imageUrlHtml\" alt=\"$namaProduk\" class=\"img-fluid rounded shadow\">";
        $html .= '<p class="mt-2">Klik Gambar &gt; Informasi Rinci</p>';
        $html .= '</a></div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="product-detail">';
        $html .= '<h4>Informasi Produk</h4>';
        $html .= "<p><strong>Nomor Publikasi:</strong> $nomorPublikasi</p>";
        $html .= "<p><strong>Nomor Laporan:</strong> $nomorLaporan</p>";
        $html .= "<p><strong>Nama Produk:</strong> $namaProduk</p>";
        $html .= "<p><strong>Risiko:</strong> $risiko</p>";
        $html .= "<p><strong>Lokasi Inspeksi:</strong> $lokasi</p>";
        $html .= "<p><strong>Tindak Lanjut:</strong> $tindakLanjut</p>";
        $html .= '</div></div></div></div></div>';
    }

    $html .= '</div>'; // tutup #unix

    // Tombol Lihat Semua Produk
    $html .= '<div class="lihat-semua-container">';
    $html .= '<a href="'.base_url('publikasi').'" class="btn-lihat-semua" target="_blank" rel="noopener">Lihat Semua Produk Publikasi</a>';
    $html .= '</div>';

    $html .= '</div>'; // tutup .owl-slider-product

    // JS dengan SweetAlert2 dan inisialisasi Slick slider
    $js = <<<JS
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function(){
    $('#unix').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      dots: true,
      infinite: true,
      speed: 800,
      autoplay: true,
      adaptiveHeight: true,
      prevArrow: '<button type="button" class="slick-prev">&#10094;</button>',
      nextArrow: '<button type="button" class="slick-next">&#10095;</button>'
    });
  });

  // Fungsi openproduk sekarang membaca data dari elemen yang di klik
  function openproduk(el) {
    const \$el = $(el);
    const nama = \$el.data('nama-produk');
    const publikasi = \$el.data('nomor-publikasi');
    const laporan = \$el.data('nomor-laporan');
    const risiko = \$el.data('risiko');
    const lokasi = \$el.data('lokasi');
    const tindak = \$el.data('tindak-lanjut');
    const img = \$el.data('image');

    Swal.fire({
      title: '<strong>' + nama + '</strong>',
      html:
        '<img src="' + img + '" style="max-width:100%; max-height:250px; border-radius:10px; margin-bottom:15px;">' +
        '<p><b>Nomor Publikasi:</b> ' + publikasi + '</p>' +
        '<p><b>Nomor Laporan:</b> ' + laporan + '</p>' +
        '<p><b>Risiko:</b> ' + risiko + '</p>' +
        '<p><b>Lokasi Inspeksi:</b> ' + lokasi + '</p>' +
        '<p><b>Tindak Lanjut:</b> ' + tindak + '</p>',
      showCloseButton: true,
      focusConfirm: false,
      confirmButtonText: 'Tutup',
      width: '600px',
      customClass: {
        popup: 'swal2-border-radius'
      }
    });
  }
</script>
JS;

    return $css . $html . $js;
}


	protected function render_instagram_slider_from_ids(array $ids)
	{
		if (empty($ids)) {
			return '<p>Tidak ada ID postingan Instagram.</p>';
		}

		$html_output = '<div class="instagram-slider" style="display:flex; gap:10px; overflow-x:auto;">';

		foreach ($ids as $shortcode) {
			$shortcode = trim($shortcode);
			$post_url = "https://www.instagram.com/p/{$shortcode}/";

			// Gunakan embed Instagram official iframe
			$embed_code = '<iframe src="https://www.instagram.com/p/' . $shortcode . '/embed" width="320" height="400" frameborder="0" scrolling="no" allowtransparency="true" allowfullscreen="true"></iframe>';

			$html_output .= '<div style="flex:0 0 auto; margin-right:10px;">' . $embed_code . '</div>';
		}

		$html_output .= '</div>';

		return $html_output;
	}




	private function render_berita_kemendag($limit = 4)
	{
		$kodeHTML = $this->CI->ortyd->bacaHTML('https://ditjenpktn.kemendag.go.id/');
		$pecah = explode('<article class="col-lg-6 col-md-6 mb-20 d-flex">', $kodeHTML);
		$message = array();
		
		if(count($pecah) > 1) {
			$output = '<div class="row">';
			$output .= '<div class="col text-center">';
			$output .= '<div class="appear-animation" data-appear-animation="blurIn" data-appear-animation-delay="0">';
			$output .= '<h2 class="mb-0 font-weight-bold">Berita</h2>';
			$output .= '<div class="divider divider-primary divider-small mt-2 mb-4 text-center">';
			$output .= '<hr class="my-0 mx-auto">';
			$output .= '</div></div></div></div>';
			$output .= '<div class="row">';
			$output .= '<div class="col-lg-12 pt-3 mt-1 appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="300">';
			$output .= '<div class="row">';
			for($x = 1; $x <= min($limit, count($pecah) - 1); $x++) {
				$image_product = explode('<div class=" post-card-1="" border-radius-10="" hover-up="" card-news="" shadow-border"="">', $pecah[$x]);
				$image_product = explode(' <div class=" post-card-1="" border-radius-10="" hover-up="" card-news="" shadow-border"="">', $image_product[0]);
				$image_product = explode('<div class="post-thumb thumb-overlay img-hover-slide position-relative"', $image_product[0]);
				$image_product = explode("background-image:", $image_product[1],2);
				$image_productnya = (string)$image_product[1];
				$image_productnya = explode("'", $image_productnya);
				$image = strip_tags($image_productnya[1]);
				
				$title = explode('<div class="post-content pt-15 pb-15 pl-1 pr-1">', $pecah[$x]);
				$title = explode('href="https://', $title[1]);
				$title = explode('">', $title[1]);
				$link = strip_tags($title[0]);
				$text = strip_tags($title[1]);
				
				$title = explode('<div class="post-content pt-15 pb-15 pl-1 pr-1">', $pecah[$x]);
				$title = explode('<div class="entry-meta meta-1 float-left font-x-small text-uppercase">', $title[1]);
				$title = explode('<span class="has-dot timeago text-primary">', $title[1]);
				$date = strip_tags($title[0]);
				$sumber = strip_tags($title[1]);    

				$data_date = explode(',', $date);
				$data_date = explode(" ", $data_date[1]);
				
				$bulan = array(
					'Januari' => '01',
					'Februari' => '02',
					'Maret' => '03',
					'April' => '04',
					'Mei' => '05',
					'Juni' => '06',
					'Juli' => '07',
					'Agustus' => '08',
					'September' => '09',
					'Oktober' => '10',
					'November' => '11',
					'Desember' => '12',
				);
				
				$day = $data_date[1];
				$month = $bulan[$data_date[2]];
				$year = trim($data_date[3]);
				$date = $year.'-'.$month.'-'.$day;
				
				$output .= '<div class="col-md-6" style="padding-bottom: 30px;">';
				$output .= '<div class="row">';
				$output .= '<div class="col-md-6 p-relative">';
				$output .= '<a target="_blank" href="https://'.$link.'" class="text-decoration-none text-light">';
				$output .= '<span class="position-absolute right-0 d-flex justify-content-end w-100 py-3 px-4 z-index-3">';
				$output .= '<span class="text-center bg-primary border-radius text-color-light font-weight-semibold line-height-2 px-3 py-2">';
				$output .= '<span class="position-relative z-index-2">';
				$output .= '<span class="text-8">'.date('d', strtotime($date)).'</span>';
				$output .= '<span class="custom-font-secondary d-block text-1 positive-ls-2 px-1">'.date('M', strtotime($date)).'</span>';
				$output .= '<span class="custom-font-secondary d-block text-1 positive-ls-2 px-1">'.date('Y', strtotime($date)).'</span>';
				$output .= '</span></span></span>';
				$output .= '<img src="'.$image.'" class="img-fluid" alt="'.$text.'" />';
				$output .= '</a></div>';
				$output .= '<div class="col-md-6" style="padding:10px">';
				$output .= '<span class="d-block text-color-grey font-weight-semibold positive-ls-2 text-2">Berita</span>';
				$output .= '<h4 class="custom-font-primary mb-2">';
				$output .= '<a target="_blank" href="https://'.$link.'" class="text-dark text-transform-none font-weight-bold text-1 line-height-3 text-color-hover-primary text-decoration-none">'.$text.'</a>';
				$output .= '</h4>';
				$output .= '<a target="_blank" href="https://'.$link.'" class="custom-view-more d-inline-flex font-weight-medium text-color-primary">Selengkapnya</a>';
				$output .= '</div></div></div>';
			}
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<div class="col-lg-12 text-center" style="margin-top:30px">';
			$output .= '<a target="_blank" class="btn btn-dark btn-modern text-uppercase font-weight-bold text-2 py-3 btn-px-4" href="https://ditjenpktn.kemendag.go.id/"><i class="fa fa-eye text-white mt-1"></i> Lihat semua</a>';
			$output .= '</div></div></section>';
			
			return $output;
		}
		
		return '';
	}


  private function render_slider($ids, $customClass = '')
	{
		$ids = explode(',', $ids);
		$uniqueClass = 'carousel-' . uniqid();
		$classAttr = $uniqueClass . ' ' . htmlspecialchars($customClass);

		$sliders = [];

		foreach ($ids as $slider_id) {
			$slider_id = (int)trim($slider_id);

			$query = $this->CI->db->select('data_slider.*, data_gallery.path')
				->from('data_slider')
				->join('data_gallery', 'data_slider.cover = data_gallery.id')
				->where('data_slider.id', $slider_id)
				->get();

			$result = $query->row_array();

			if ($result) {
				$sliders[] = $result;
			}
		}

		if (str_contains($customClass, 'slider-fullpage')) {
			// ==== VERSI OWL CAROUSEL ====
			ob_start();
			?>
			<div class="owl-carousel owl-carousel-light owl-carousel-light-init-fadeIn owl-theme manual dots-inside dots-modern dots-modern-lg dots-horizontal-center show-dots-hover show-dots-xs nav-style-1 nav-inside nav-inside-plus nav-dark nav-lg nav-font-size-lg show-nav-hover mb-0 <?= $classAttr ?>" data-plugin-options="{'autoplayTimeout': 5000}" data-dynamic-height="['700px','700px','700px','550px','500px']" style="height: 450px;">
				<div class="owl-stage-outer">
					<div class="owl-stage">
						<?php foreach ($sliders as $slider): ?>
							<?php if ($slider['type_id'] == 1): ?>
								<div class="owl-item position-relative" data-lazy-bg="<?= base_url($slider['path']); ?>" style="background-size: cover; background-position: center; backdrop-filter: blur(10px);">
									<div class="container h-100">
										<div class="row h-100">
											<div class="col-lg-6" style="background: #ffffffb5;">
												<div class="d-flex flex-column justify-content-center h-100">
													<p class="custom-font-slider-2 text-dark" data-plugin-animated-letters data-plugin-options="{'startDelay': 750, 'minWindowWidth': 0, 'animationSpeed': 30}"><?= $slider['sub_title']; ?></p>
													<h2 class="custom-font-slider-1 mb-0 font-weight-bold appear-animation" data-appear-animation="blurIn" data-appear-animation-delay="500"><?= $slider['title']; ?></h2>
													<div class="divider divider-primary divider-small text-start mt-2 mb-4 mx-0 appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="750"><hr class="my-0"></div>
													<p class="text-3-5 line-height-9 appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="1000" style="color: #11213e;"><?= $slider['description']; ?></p>
													<div class="appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="1250">
														<div class="d-flex align-items-center mt-2">
															<a target="_blank" href="<?= $slider['link']; ?>" class="btn btn-dark btn-modern text-uppercase font-weight-bold text-2 py-3 btn-px-4">Selengkapnya</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php else: ?>
								<div class="owl-item position-relative" data-lazy-bg="<?= base_url($slider['path']); ?>" style="background-size: cover; background-position: center;">
									<div class="container h-100" style="background: #11213ea6;">
										<div class="row h-100">
											<div class="col text-center">
												<div class="d-flex flex-column justify-content-center h-100">
													<p class="custom-font-slider-2 text-light" data-plugin-animated-letters data-plugin-options="{'startDelay': 750, 'minWindowWidth': 0, 'animationSpeed': 30}"><?= $slider['sub_title']; ?></p>
													<h2 class="custom-font-slider-1 mb-0 font-weight-bold text-light appear-animation" data-appear-animation="blurIn" data-appear-animation-delay="500"><?= $slider['title']; ?></h2>
													<div class="divider divider-primary divider-small mt-2 mb-4 appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="750"><hr class="my-0 me-auto"></div>
													<p class="text-3-5 line-height-9 appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="1000" style="color:#fff"><?= $slider['description']; ?></p>
													<div class="text-center appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="1250">
														<a target="_blank" href="<?= $slider['link']; ?>" class="btn btn-light text-dark btn-modern text-uppercase font-weight-bold text-2 py-3 btn-px-4">Selengkapnya</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="owl-nav">
					<button type="button" role="presentation" class="owl-prev"></button>
					<button type="button" role="presentation" class="owl-next"></button>
				</div>
				<div class="owl-dots mb-5">
					<?php for ($i = 0; $i < count($sliders); $i++): ?>
						<button role="button" class="owl-dot <?= $i === 0 ? 'active' : '' ?>"><span></span></button>
					<?php endfor; ?>
				</div>
			</div>
			<?php
			return ob_get_clean();
		} else {
			// ==== VERSI SLICK DEFAULT ====
			$html = '<div class="carousel ' . $classAttr . '">';
			foreach ($sliders as $slider) {
				$html .= '<div class="carousel-item">
							<img src="' . base_url($slider['path']) . '" alt="Slider ' . $slider['id'] . '" style="width:300px;">
						  </div>';
			}
			$html .= '</div>';
			$html .= <<<SCRIPT
	<script>
	$(window).on('load', function() {
		$('.$uniqueClass.carousel').not('.slick-initialized').slick({
			autoplay: true,
			autoplaySpeed: 4000,
			arrows: true,
			dots: true,
			infinite: true,
			speed: 600,
			fade: false,
			cssEase: 'ease-in-out'
		});
	});
	</script>
	SCRIPT;
			return $html;
		}
	}





    private function render_gallery($ids)
    {
        $ids = explode(',', $ids);
        $html = '<div class="gallery" style="display: flex; gap: 10px; flex-wrap: wrap;">';
        foreach ($ids as $id) {
            $html .= $this->CI->load->view('shortcodes/gallery_item', ['id' => trim($id)], true);
        }
        $html .= '</div>';
        return $html;
    }

    private function render_video($youtube_id)
    {
        return <<<HTML
<div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
    <iframe src="https://www.youtube.com/embed/{$youtube_id}" frameborder="0" allowfullscreen 
        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
    </iframe>
</div>
HTML;
    }
}
