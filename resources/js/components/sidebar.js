/**
 * Sidebar drawer component — Mobile navigation panel
 */
export default function sidebar() {
    return {
        open: false,

        toggle() {
            this.open = !this.open;
            this._updateBodyScroll();
        },

        close() {
            this.open = false;
            this._updateBodyScroll();
        },

        _updateBodyScroll() {
            document.body.style.overflow = this.open ? 'hidden' : '';
        },

        init() {
            // Tutup sidebar saat Escape ditekan
            this.$watch('open', (val) => {
                if (val) {
                    document.addEventListener('keydown', this._onKeydown = (e) => {
                        if (e.key === 'Escape') this.close();
                    });
                } else {
                    document.removeEventListener('keydown', this._onKeydown);
                }
            });
        },
    };
}
