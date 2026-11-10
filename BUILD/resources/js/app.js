import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Make Swal globally accessible
window.Swal = Swal;

// Custom RHS Heritage SweetAlert Toast Mixin
window.SwalToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Helper functions for easy calling
window.showSuccess = function(title, text = '') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonColor: '#0F3D2E',
        confirmButtonText: 'Selesai',
        customClass: {
            popup: 'rhs-swal-popup',
            title: 'rhs-swal-title',
            confirmButton: 'rhs-swal-confirm'
        }
    });
};

window.showError = function(title, text = '') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonColor: '#C0392B',
        confirmButtonText: 'Tutup',
        customClass: {
            popup: 'rhs-swal-popup',
            title: 'rhs-swal-title',
            confirmButton: 'rhs-swal-confirm'
        }
    });
};

// Global confirm dialog interceptor for forms with data-confirm or confirm()
document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('submit', (e) => {
        const form = e.target;
        const confirmMessage = form.getAttribute('data-confirm') || form.dataset.confirm;

        if (confirmMessage && !form.dataset.confirmed) {
            e.preventDefault();
            const isDanger = form.querySelector('[type="submit"].bg-danger, [type="submit"].text-danger') || 
                             form.action.includes('destroy') || 
                             form.querySelector('input[name="_method"][value="DELETE"]');

            Swal.fire({
                title: 'Konfirmasi Aksi',
                text: confirmMessage,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#C0392B' : '#0F3D2E',
                cancelButtonColor: '#6B6B6B',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rhs-swal-popup',
                    title: 'rhs-swal-title',
                    confirmButton: 'rhs-swal-confirm',
                    cancelButton: 'rhs-swal-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }
    });
});

// Initialize AOS (Animate on Scroll)
function initAOS() {
    AOS.init({
        duration: 650,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50,
        delay: 50,
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAOS);
} else {
    initAOS();
}

window.AOS = AOS;

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: document.documentElement.classList.contains('dark') || 
              localStorage.getItem('theme') === 'dark' || 
              (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

        init() {
            this.apply();
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!('theme' in localStorage)) {
                    this.dark = e.matches;
                    this.apply();
                }
            });
        },

        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            this.apply();
        },

        apply() {
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
});

document.addEventListener('alpine:initialized', () => {
    setTimeout(() => {
        if (window.AOS) {
            window.AOS.refresh();
        }
    }, 150);
});

window.Alpine = Alpine;
Alpine.start();


