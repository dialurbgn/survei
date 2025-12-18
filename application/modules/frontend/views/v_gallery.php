<?php
  $exclude = $exclude;
  $query_column = $this->ortyd->getviewlistform('data_gallery', $exclude);
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
        $id = $id;
        $iddata = $id;
        $typedata = 'Edit';
        if ($datarow && $datarow != null) {
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
    //$newURL = base_url($module);
    //header('Location: '.$newURL);
  }

  $file_download = base_url($path);
?>
<section id="contact" class="py-100" style="background-color: #f2f6fa;">
  <div class="container">
    <div class="row gx-6 gx-xl-9">
      <div class="col-lg-12">
        <?php if ($file_store_format == 'xlsx' || $file_store_format == 'xls') { ?>
          <style>
            /* Global Styles */
            body {
              background-color: #f8f9fa !important;
              margin: 0 !important;
              padding: 0 !important;
            }

            header {
              background-color: #4CAF50 !important;
              color: white !important;
              padding: 1rem 2rem !important;
              border-radius: 0.5rem !important;
              margin-bottom: 1.5rem !important;
              display: flex !important;
              align-items: center !important;
              justify-content: space-between !important;
            }

            header h1 {
              margin: 0 !important;
              font-size: 1.5rem !important;
            }

            /* Viewer Styles */
            .viewer-frame {
              width: 100% !important;
              height: 80vh !important;
              border: 2px solid #dee2e6 !important;
              border-radius: 10px !important;
              box-shadow: 0 0 10px rgba(0, 0, 0, 0.08) !important;
              overflow: hidden !important;
            }

            /* PDF Styles */
            body {
              overflow-x: hidden !important;
            }

            #sidebar {
              max-height: 90vh !important;
              overflow-y: auto !important;
              border-right: 1px solid #ddd !important;
            }

            .thumbnail {
              border: 2px solid transparent !important;
              cursor: pointer !important;
              margin-bottom: 10px !important;
            }

            .thumbnail.selected {
              border-color: #007bff !important;
            }

            .pdf-page {
              position: relative !important;
              margin-bottom: 2rem !important;
              text-align: center !important;
            }

            .textLayer span {
              color: transparent !important;
              border-radius: 3px !important;
              padding: 1px !important;
            }

            canvas {
              border: 1px solid #dee2e6 !important;
              box-shadow: 0 0 8px rgba(0, 0, 0, 0.1) !important;
              margin: 0 auto !important;
            }

            .zoom-buttons {
              display: flex !important;
              justify-content: center !important;
              gap: 10px !important;
              margin-bottom: 1rem !important;
            }

            .zoom-buttons button,
            .download-btn {
              background-color: #007bff !important;
              color: white !important;
              border: none !important;
              padding: 10px 20px !important;
              border-radius: 5px !important;
              cursor: pointer !important;
              transition: background-color 0.3s ease !important;
            }

            .zoom-buttons button:hover,
            .download-btn:hover {
              background-color: #0056b3 !important;
            }

            .download-btn {
              font-size: 16px !important;
            }

            .image-preview img {
              border: none !important;
              min-height: 90vh !important;
              width: 100% !important;
              height: auto !important;
            }

            /* Responsiveness */
            @media (max-width: 768px) {
              .zoom-buttons {
                flex-direction: column !important;
                align-items: center !important;
              }

              .download-btn {
                width: 100% !important;
                text-align: center !important;
                margin-top: 10px !important;
              }

              #sidebar {
                display: none !important;
              }

              .col-md-2,
              .col-md-10 {
                flex: 0 0 100% !important;
              }

              .viewer-frame {
                height: 60vh !important;
              }

              .container {
                padding-left: 15px !important;
                padding-right: 15px !important;
              }

              .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
              }
            }
          </style>

          <header>
            <h1>📊 Excel Preview</h1>
            <a href="<?= $file_download ?>" class="btn btn-light btn-sm" download>⬇️ Download Excel</a>
          </header>

          <div class="viewer-frame">
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($file_download) ?>" width="100%" height="100%" frameborder="0"></iframe>
          </div>
        <?php } elseif ($file_store_format == 'pdf') { ?>
          <style>
            /* Global Styles */
            body {
              overflow-x: hidden !important;
            }

            #sidebar {
              max-height: 90vh !important;
              overflow-y: auto !important;
              border-right: 1px solid #ddd !important;
            }

            .thumbnail {
              border: 2px solid transparent !important;
              cursor: pointer !important;
              margin-bottom: 10px !important;
            }

            .thumbnail.selected {
              border-color: #007bff !important;
            }

            .pdf-page {
              position: relative !important;
              margin-bottom: 2rem !important;
              text-align: center !important;
            }

            .textLayer span {
              color: transparent !important;
              border-radius: 3px !important;
              padding: 1px !important;
            }

            canvas {
              border: 1px solid #dee2e6 !important;
              box-shadow: 0 0 8px rgba(0, 0, 0, 0.1) !important;
              margin: 0 auto !important;
            }

            .zoom-buttons {
              display: flex !important;
              justify-content: center !important;
              gap: 10px !important;
              margin-bottom: 1rem !important;
            }

            .zoom-buttons button,
            .download-btn {
              background-color: #007bff !important;
              color: white !important;
              border: none !important;
              padding: 10px 20px !important;
              border-radius: 5px !important;
              cursor: pointer !important;
              transition: background-color 0.3s ease !important;
            }

            .zoom-buttons button:hover,
            .download-btn:hover {
              background-color: #0056b3 !important;
            }

            .download-btn {
              font-size: 16px !important;
            }

            .image-preview img {
              border: none !important;
              min-height: 90vh !important;
              width: 100% !important;
              height: auto !important;
            }

            /* Responsiveness */
            @media (max-width: 768px) {
              .zoom-buttons {
                flex-direction: column !important;
                align-items: center !important;
              }

              .download-btn {
                width: 100% !important;
                text-align: center !important;
                margin-top: 10px !important;
              }

              #sidebar {
                display: none !important;
              }

              .col-md-2,
              .col-md-10 {
                flex: 0 0 100% !important;
              }

              .viewer-frame {
                height: 60vh !important;
              }

              .container {
                padding-left: 15px !important;
                padding-right: 15px !important;
              }

              .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
              }
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
              <div class="zoom-buttons">
                <button class="btn btn-secondary" id="zoom-in">Zoom In 🔍</button>
                <button class="btn btn-secondary" id="zoom-out">Zoom Out 🔎</button>
              </div>
              <a class="btn download-btn" href="<?= $file_download ?>" download target="_blank">📥 Download PDF</a>

              <div id="pdf-container"></div>
            </div>
          </div>

          <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
          <script>
            const url = "<?= $file_download ?>";
            const container = document.getElementById('pdf-container');
            const thumbnails = document.getElementById('thumbnail-list');

            let pdfDoc = null;
            let scale = 1.2;
            let pageCanvases = [];

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            function renderAllPages(pdf) {
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

            function zoomAllPages(inOut) {
              scale += inOut === 'in' ? 0.2 : -0.2;
              container.innerHTML = '';
              thumbnails.innerHTML = '';
              pageCanvases = [];
              renderAllPages(pdfDoc);
            }

            // Load PDF
            pdfjsLib.getDocument(url).promise.then(pdf => {
              pdfDoc = pdf;
              renderAllPages(pdf);
            });

            document.getElementById('zoom-in').addEventListener('click', () => zoomAllPages('in'));
            document.getElementById('zoom-out').addEventListener('click', () => zoomAllPages('out'));
          </script>
        <?php } elseif ($file_store_format == 'png' || $file_store_format == 'jpg' || $file_store_format == 'jpeg') { ?>
          <div class="image-preview">
            <img src="<?php echo $file_download; ?>" width="100%" height="100%" style="border: none; min-height: 90vh;" />
          </div>
        <?php } else { ?>
          <h1>File Tidak bisa dibuka</h1>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
