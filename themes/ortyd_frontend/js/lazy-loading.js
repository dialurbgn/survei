/**
 * Advanced Lazy Loading Script
 * Mendukung gambar, iframe, dan elemen lainnya
 */

class LazyLoader {
    constructor(options = {}) {
        this.options = {
            // Threshold untuk mulai loading (dalam pixel)
            rootMargin: options.rootMargin || '50px',
            // Persentase elemen yang harus terlihat sebelum loading
            threshold: options.threshold || 0.1,
            // Selector untuk elemen yang akan di-lazy load
            selector: options.selector || '[data-lazy]',
            // Class yang ditambahkan saat loading
            loadingClass: options.loadingClass || 'lazy-loading',
            // Class yang ditambahkan saat sudah loaded
            loadedClass: options.loadedClass || 'lazy-loaded',
            // Class untuk error
            errorClass: options.errorClass || 'lazy-error',
            // Placeholder image
            placeholder: options.placeholder || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E',
            // Fade-in animation duration
            fadeInDuration: options.fadeInDuration || 300
        };

        this.observer = null;
        this.init();
    }

    init() {
        // Cek dukungan Intersection Observer
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            // Fallback untuk browser lama
            this.fallbackLoad();
        }

        // Setup CSS untuk animasi
        this.setupCSS();
    }

    setupCSS() {
        if (!document.getElementById('lazy-loading-styles')) {
            const style = document.createElement('style');
            style.id = 'lazy-loading-styles';
            style.textContent = `
                [data-lazy] {
                    opacity: 0;
                    transition: opacity ${this.options.fadeInDuration}ms ease-in-out;
                }
                
                .${this.options.loadingClass} {
                    opacity: 0.5;
                    position: relative;
                }
                
                .${this.options.loadingClass}::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 20px;
                    height: 20px;
                    margin: -10px 0 0 -10px;
                    border: 2px solid #f3f3f3;
                    border-top: 2px solid #007bff;
                    border-radius: 50%;
                    animation: lazy-spin 1s linear infinite;
                    z-index: 1;
                }
                
                .${this.options.loadedClass} {
                    opacity: 1;
                }
                
                .${this.options.errorClass} {
                    opacity: 0.5;
                    background-color: #f8f9fa;
                    border: 1px dashed #dee2e6;
                }
                
                @keyframes lazy-spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                
                /* Skeleton loading untuk gambar */
                .lazy-skeleton {
                    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                    background-size: 200% 100%;
                    animation: lazy-skeleton 1.5s infinite;
                }
                
                @keyframes lazy-skeleton {
                    0% { background-position: 200% 0; }
                    100% { background-position: -200% 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    setupIntersectionObserver() {
        const config = {
            rootMargin: this.options.rootMargin,
            threshold: this.options.threshold
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadElement(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, config);

        this.observeElements();
    }

    observeElements() {
        const elements = document.querySelectorAll(this.options.selector);
        elements.forEach(element => {
            // Set placeholder jika belum ada
            this.setPlaceholder(element);
            this.observer.observe(element);
        });
    }

    setPlaceholder(element) {
        if (element.tagName === 'IMG' && !element.src) {
            element.src = this.options.placeholder;
            element.classList.add('lazy-skeleton');
        }
    }

    loadElement(element) {
        element.classList.add(this.options.loadingClass);
        element.classList.remove('lazy-skeleton');

        if (element.tagName === 'IMG') {
            this.loadImage(element);
        } else if (element.tagName === 'IFRAME') {
            this.loadIframe(element);
        } else if (element.dataset.lazyBg) {
            this.loadBackgroundImage(element);
        } else {
            this.loadGenericElement(element);
        }
    }

    loadImage(img) {
        const src = img.dataset.lazy || img.dataset.src;
        const srcset = img.dataset.lazySrcset || img.dataset.srcset;

        if (!src && !srcset) {
            this.handleError(img);
            return;
        }

        // Preload image
        const imageLoader = new Image();
        
        imageLoader.onload = () => {
            // Update src dan srcset
            if (src) img.src = src;
            if (srcset) img.srcset = srcset;
            
            this.handleSuccess(img);
        };
        
        imageLoader.onerror = () => {
            this.handleError(img);
        };
        
        // Mulai loading
        imageLoader.src = src;
        if (srcset) imageLoader.srcset = srcset;
    }

    loadIframe(iframe) {
        const src = iframe.dataset.lazy || iframe.dataset.src;
        
        if (!src) {
            this.handleError(iframe);
            return;
        }

        iframe.onload = () => {
            this.handleSuccess(iframe);
        };
        
        iframe.onerror = () => {
            this.handleError(iframe);
        };
        
        iframe.src = src;
    }

    loadBackgroundImage(element) {
        const bgImage = element.dataset.lazyBg;
        
        if (!bgImage) {
            this.handleError(element);
            return;
        }

        // Preload background image
        const imageLoader = new Image();
        
        imageLoader.onload = () => {
            element.style.backgroundImage = `url(${bgImage})`;
            this.handleSuccess(element);
        };
        
        imageLoader.onerror = () => {
            this.handleError(element);
        };
        
        imageLoader.src = bgImage;
    }

    loadGenericElement(element) {
        // Untuk elemen lain, langsung tampilkan
        this.handleSuccess(element);
    }

    handleSuccess(element) {
        element.classList.remove(this.options.loadingClass);
        element.classList.add(this.options.loadedClass);
        
        // Hapus data attributes yang tidak diperlukan
        delete element.dataset.lazy;
        delete element.dataset.lazySrcset;
        delete element.dataset.lazyBg;
        
        // Trigger custom event
        element.dispatchEvent(new CustomEvent('lazyloaded', {
            detail: { element }
        }));
    }

    handleError(element) {
        element.classList.remove(this.options.loadingClass);
        element.classList.add(this.options.errorClass);
        
        // Trigger custom event
        element.dispatchEvent(new CustomEvent('lazyerror', {
            detail: { element }
        }));
    }

    fallbackLoad() {
        // Fallback untuk browser yang tidak mendukung Intersection Observer
        const elements = document.querySelectorAll(this.options.selector);
        elements.forEach(element => {
            this.loadElement(element);
        });
    }

    // Method untuk menambah elemen baru yang perlu di-lazy load
    observe(element) {
        if (this.observer) {
            this.setPlaceholder(element);
            this.observer.observe(element);
        } else {
            this.loadElement(element);
        }
    }

    // Method untuk berhenti mengamati elemen
    unobserve(element) {
        if (this.observer) {
            this.observer.unobserve(element);
        }
    }

    // Method untuk me-refresh semua elemen
    refresh() {
        if (this.observer) {
            this.observer.disconnect();
            this.observeElements();
        }
    }

    // Method untuk destroy
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Auto-initialize jika DOM sudah ready
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi lazy loader dengan konfigurasi default
    window.lazyLoader = new LazyLoader({
        rootMargin: '100px',
        threshold: 0.1,
        selector: 'img[data-lazy], iframe[data-lazy], [data-lazy-bg]',
        fadeInDuration: 500
    });

    // Event listener untuk konten yang dimuat secara dinamis
    document.addEventListener('lazyloaded', function(e) {
        console.log('Element loaded:', e.detail.element);
    });

    document.addEventListener('lazyerror', function(e) {
        console.warn('Element failed to load:', e.detail.element);
    });
});

// Utility functions untuk kemudahan penggunaan

/**
 * Konversi gambar existing menjadi lazy loading
 */
function convertToLazyLoading() {
    const images = document.querySelectorAll('img:not([data-lazy]):not([data-converted])');
    
    images.forEach(img => {
        if (img.src && img.src !== window.lazyLoader.options.placeholder) {
            img.dataset.lazy = img.src;
            img.src = window.lazyLoader.options.placeholder;
            img.dataset.converted = 'true';
            
            if (img.srcset) {
                img.dataset.lazySrcset = img.srcset;
                img.removeAttribute('srcset');
            }
            
            window.lazyLoader.observe(img);
        }
    });
}

/**
 * Lazy load untuk background images
 */
function setupBackgroundLazyLoading() {
    const elements = document.querySelectorAll('[data-bg-lazy]:not([data-converted])');
    
    elements.forEach(element => {
        element.dataset.lazyBg = element.dataset.bgLazy;
        element.dataset.lazy = 'true';
        element.dataset.converted = 'true';
        window.lazyLoader.observe(element);
    });
}

/**
 * Setup lazy loading untuk carousel/slider
 */
function setupCarouselLazyLoading() {
    // Untuk Owl Carousel
    $('.owl-carousel').on('changed.owl.carousel', function(event) {
        const items = $(this).find('.owl-item.active img[data-lazy]');
        items.each(function() {
            window.lazyLoader.observe(this);
        });
    });
    
    // Untuk Slick Slider
    $('.slick-slider').on('afterChange', function(event, slick, currentSlide) {
        const items = $(this).find('.slick-active img[data-lazy]');
        items.each(function() {
            window.lazyLoader.observe(this);
        });
    });
}

// Auto-convert existing images setelah lazy loader ready
window.addEventListener('load', function() {
    // Delay sedikit untuk memastikan semua asset loaded
    setTimeout(() => {
        convertToLazyLoading();
        setupBackgroundLazyLoading();
        setupCarouselLazyLoading();
    }, 1000);
});

// Export untuk penggunaan module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyLoader;
}