import './bootstrap';

// Override Livewire default session expired alert with custom toast notification
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) { // CSRF token mismatch / session expired
                preventDefault();
                
                // Prevent multiple toasts
                if (document.querySelector('.session-expired-toast')) return;
                
                // Show custom toast notification instead of browser alert
                const toast = document.createElement('div');
                toast.className = 'session-expired-toast fixed top-4 right-4 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl z-[9999] max-w-md';
                toast.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm mb-1">Sesi Anda Telah Berakhir</h3>
                            <p class="text-xs opacity-90 mb-3">Silakan refresh halaman untuk melanjutkan.</p>
                            <button onclick="window.location.reload()" class="bg-white text-red-600 px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-50 transition">
                                Refresh Sekarang
                            </button>
                        </div>
                        <button onclick="this.closest('.session-expired-toast').remove()" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                
                // Add slide-in animation
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'transform 0.3s ease-out';
                document.body.appendChild(toast);
                
                requestAnimationFrame(() => {
                    toast.style.transform = 'translateX(0)';
                });
                
                // Auto-refresh after 8 seconds
                setTimeout(() => window.location.reload(), 8000);
            }
        });
    });
});
