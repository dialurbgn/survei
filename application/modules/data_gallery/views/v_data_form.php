
<?php
	
	$exclude = $exclude;
	$query_column = $this->ortyd->getviewlistform($module, $exclude, 2);
	if($query_column){
		foreach($query_column as $rows_column){
			if($rows_column['name'] == 'active'){
				${$rows_column['name']} = 1;
			}else{
				${$rows_column['name']} = null;
			}
		}
		$tanggal = date('Y-m-d');

		if(isset($id)){
			if($id == '0'){
				$id = '';
				$iddata = 0;
				$typedata = 'Buat';
			}else{
				$id = $id;
				$iddata = $id;
				$typedata = 'Edit';
				if($datarow && $datarow != null){
					foreach($query_column as $rows_column){
						foreach ($datarow as $rows) {
							${$rows_column['name']} = $rows->{$rows_column['name']};
						}
					}
				}
			}
		}else{
			$id = '';
			$iddata = 0;
			$typedata = 'Buat';
		}
	}else{
		$newURL = base_url($module);
		header('Location: '.$newURL);
	}
	
	$file_download = base_url($path);
?>
<div id="kt_content_container" class="app-container  container-fluid  d-flex flex-column-fluid align-items-start container-xxl" style="    margin-top: 30px;
    background: #ffffff;
    padding-top: 30px;
    padding-bottom: 30px;">

 <div class="content flex-row-fluid" id="kt_content" style="text-align: center;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    padding: 10px;
    border-radius: 10px;"> 
 
  <div class="row gx-6 gx-xl-9">
  <div class="col-lg-12">
  
	<?php if($file_store_format == 'xlsx' || $file_store_format == 'xls'){ ?>

	<style>
    body {
      background-color: #f8f9fa;
    }
    header {
      background-color: #0056cd;
      color: white;
      padding: 1rem 2rem;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    header h1 {
      margin: 0;
      font-size: 1.5rem;
    }
    .viewer-frame {
      width: 100%;
      height: 80vh;
      border: 2px solid #dee2e6;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      overflow: hidden;
    }
  </style>
  
  <header>
    <h1 style="color:#fff">📊 Excel Preview</h1>
    <a href="<?= $file_download ?>" class="btn btn-light btn-sm" download>
      ⬇️ Download Excel
    </a>
  </header>

  <div class="viewer-frame">
    <iframe 
      src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($file_download) ?>" 
      width="100%" 
      height="100%" 
      frameborder="0">
    </iframe>
  </div>

	<?php }elseif($file_store_format == 'pdf'){ ?>

  <style>
    body {
      overflow-x: hidden;
    }
    #sidebar {
      max-height: 90vh;
      overflow-y: auto;
      border-right: 1px solid #ddd;
    }
    .thumbnail {
      border: 2px solid transparent;
      cursor: pointer;
      margin-bottom: 10px;
    }
    .thumbnail.selected {
      border-color: #007bff;
    }
    .pdf-page {
      position: relative;
      margin-bottom: 2rem;
      text-align: center;
    }
    .textLayer span {
      color: transparent;
      //background-color: yellow;
      border-radius: 3px;
      padding: 1px;
    }
    canvas {
      border: 1px solid #dee2e6;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      margin: 0 auto;
    }
  </style>
  
	   <div class="row">
		<!-- Sidebar Thumbnail -->
		<div class="col-md-2" id="sidebar">
		  <h6>Preview</h6>
		  <div id="thumbnail-list"></div>
		</div>

		<!-- Main Viewer -->
		<div class="col-md-10">
		  <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
			<button class="btn btn-secondary" id="zoom-in">Zoom In 🔍</button>
			<button class="btn btn-secondary" id="zoom-out">Zoom Out 🔎</button>

			<input type="text" id="search-text" class="form-control w-auto" placeholder="Search text...">
			<button class="btn btn-primary" id="search-btn">Search</button>

			<select class="form-control w-auto" id="page-selector"></select>
			<button class="btn btn-success" id="go-to-page">Go</button>
			
			<a class="btn btn-success ms-auto" id="download-pdf" href="<?= $file_download ?>" download target="_blank">
			  📥 Download PDF
			</a>
		  </div>

		  <div id="pdf-container"></div>
		</div>
	  </div>

	  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
	 <!-- Tambahkan ini di bagian atas atau dekat tombol zoom -->


<script>
	const url = "<?= $file_download ?>";
	const container = document.getElementById('pdf-container');
	const thumbnails = document.getElementById('thumbnail-list');
	const pageSelector = document.getElementById('page-selector');

	let pdfDoc = null;
	let scale = 1.2;
	let pageCanvases = [];

	pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

	function renderAllPages(pdf) {
	  // Populate page selector
	  pageSelector.innerHTML = '';
	  for (let i = 1; i <= pdf.numPages; i++) {
		const option = document.createElement('option');
		option.value = i;
		option.textContent = 'Page ' + i;
		pageSelector.appendChild(option);
	  }

	  for (let i = 1; i <= pdf.numPages; i++) {
		pdf.getPage(i).then(page => {
		  const viewport = page.getViewport({ scale });
		  const pageDiv = document.createElement('div');
		  pageDiv.classList.add('pdf-page');
		  pageDiv.setAttribute('id', 'page-' + page.pageNumber);
		  pageDiv.setAttribute('data-page', page.pageNumber);

		  const canvas = document.createElement('canvas');
		  const ctx = canvas.getContext('2d');
		  canvas.height = viewport.height;
		  canvas.width = viewport.width;

		  pageDiv.appendChild(canvas);
		  container.appendChild(pageDiv);
		  pageCanvases.push({ canvas, pageNumber: page.pageNumber });

		  page.render({ canvasContext: ctx, viewport });

		  // Render thumbnail
		  renderThumbnail(page);

		  // Extract text for search
		  page.getTextContent().then(textContent => {
			const textLayerDiv = document.createElement('div');
			textLayerDiv.className = 'textLayer';
			textLayerDiv.style.position = 'absolute';
			textLayerDiv.style.top = '0';
			textLayerDiv.style.left = '0';
			textLayerDiv.style.width = canvas.width + 'px';
			textLayerDiv.style.height = canvas.height + 'px';
			textLayerDiv.style.pointerEvents = 'none';
			pageDiv.appendChild(textLayerDiv);

			textContent.items.forEach(item => {
			  const span = document.createElement('span');
			  span.textContent = item.str;
			  const [x, y] = item.transform.slice(4, 6);
			  span.style.transform = `translate(${x}px, ${y}px)`;
			  span.style.position = 'absolute';
			  span.style.fontSize = '12px';
			  textLayerDiv.appendChild(span);
			});

			pageDiv.setAttribute('data-text', textContent.items.map(i => i.str).join(' '));
		  });
		});
	  }
	}

	function renderThumbnail(page) {
	  const thumbScale = 0.3;
	  const viewport = page.getViewport({ scale: thumbScale });
	  const canvas = document.createElement('canvas');
	  canvas.width = viewport.width;
	  canvas.height = viewport.height;

	  page.render({ canvasContext: canvas.getContext('2d'), viewport });

	  const div = document.createElement('div');
	  div.classList.add('thumbnail');
	  div.appendChild(canvas);
	  div.setAttribute('data-page', page.pageNumber);

	  div.addEventListener('click', () => {
		document.getElementById('page-' + page.pageNumber).scrollIntoView({ behavior: 'smooth' });
		document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('selected'));
		div.classList.add('selected');
	  });

	  thumbnails.appendChild(div);
	}

	function searchText() {
	  const keyword = document.getElementById('search-text').value.trim().toLowerCase();
	  if (!keyword) return;

	  // Reset semua highlight sebelumnya
	  document.querySelectorAll('.textLayer span').forEach(span => {
		span.style.backgroundColor = 'transparent';
	  });

	  document.querySelectorAll('.pdf-page').forEach(page => {
		const spans = page.querySelectorAll('.textLayer span');
		spans.forEach(span => {
		  const text = span.textContent.toLowerCase();
		  if (text.includes(keyword)) {
			span.style.backgroundColor = 'yellow';
		  }
		});
	  });
	}

	function zoomAllPages(inOut) {
	  scale += inOut === 'in' ? 0.2 : -0.2;
	  container.innerHTML = '';
	  thumbnails.innerHTML = '';
	  pageCanvases = [];
	  renderAllPages(pdfDoc);
	}

	// Event pilih halaman
	document.getElementById('go-to-page').addEventListener('click', () => {
	  const pageNumber = parseInt(document.getElementById('page-selector').value);
	  const target = document.getElementById('page-' + pageNumber);
	  if (target) {
		target.scrollIntoView({ behavior: 'smooth' });

		// Highlight thumbnail
		document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('selected'));
		document.querySelector(`.thumbnail[data-page="${pageNumber}"]`)?.classList.add('selected');
	  }
	});

	// Load PDF
	pdfjsLib.getDocument(url).promise.then(pdf => {
	  pdfDoc = pdf;
	  renderAllPages(pdf);
	});

	document.getElementById('search-btn').addEventListener('click', searchText);
	document.getElementById('zoom-in').addEventListener('click', () => zoomAllPages('in'));
	document.getElementById('zoom-out').addEventListener('click', () => zoomAllPages('out'));
</script>

	  
	<?php }elseif ($file_store_format == 'docx' || $file_store_format == 'doc') { ?>

	<style>
	  .viewer-frame {
		width: 100%;
		height: 80vh;
		border: 2px solid #dee2e6;
		border-radius: 10px;
		box-shadow: 0 0 10px rgba(0,0,0,0.08);
		overflow: hidden;
		margin-bottom: 20px;
	  }
	  header {
		background-color: #0078D4;
		color: white;
		padding: 1rem 2rem;
		border-radius: 0.5rem;
		margin-bottom: 1.5rem;
		display: flex;
		align-items: center;
		justify-content: space-between;
	  }
	  header h1 {
		margin: 0;
		font-size: 1.5rem;
	  }
	</style>

	<header>
	  <h1 style="color:#fff">📄 Word Preview</h1>
	  <a href="<?= $file_download ?>" class="btn btn-light btn-sm" download>
		⬇️ Download Word
	  </a>
	</header>

	<div class="viewer-frame">
	  <iframe 
		src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($file_download) ?>" 
		width="100%" 
		height="100%" 
		frameborder="0">
	  </iframe>
	</div>


	<?php }elseif($file_store_format == 'png' || $file_store_format == 'jpg' ||$file_store_format == 'jpeg'){ ?>

	<img
	  src="<?php echo $file_download; ?>"
	  width="100%" height="100%" style="border: none; min-height: 90vh;" />

	<?php }else{ ?>

	<h1>File Tidak bisa di buka</h1>

	<?php } ?>
	</div>
	</div>
	</div>
</div>