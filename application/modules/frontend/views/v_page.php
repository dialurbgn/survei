<div class="container py-5 my-3">
	<div class="row">
		<div class="col-lg-12 mb-4 mb-lg-0">
			<h2 class="mb-0 font-weight-bold">
				<?= $title; ?>
			</h2>

			<!-- Tombol Share (buka modal popup) -->
			<div class="mt-3">
				<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
					<i class="fas fa-share-alt"></i> Bagikan
				</button>
			</div>
		</div>
		
		<div class="row mt-3">
			<?= $content_web; ?>
		</div>
	</div>
</div>

<!-- Modal Share -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shareModalLabel">Bagikan Page</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-3">Bagikan ke media sosial:</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()); ?>" target="_blank" class="btn btn-primary">
            <i class="fab fa-facebook-f"></i> Facebook
          </a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()); ?>&text=<?= urlencode($title); ?>" target="_blank" class="btn btn-info text-white">
            <i class="fab fa-twitter"></i> Twitter
          </a>
          <a href="https://wa.me/?text=<?= urlencode($title . ' ' . current_url()); ?>" target="_blank" class="btn btn-success">
            <i class="fab fa-whatsapp"></i> WhatsApp
          </a>
          <a href="https://www.instagram.com/balai_kalibrasi/" target="_blank" class="btn btn-danger">
            <i class="fab fa-instagram"></i> Instagram
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
