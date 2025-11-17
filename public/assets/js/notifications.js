/**
 * ✅ MODERN NOTIFICATION SYSTEM with Enhanced Badge Animation
 */
class NotificationSystem {
    constructor() {
        console.log('🔔 Modern NotificationSystem initialized');
        this.pollingInterval = null;
        this.updateCountTimeout = null;
        this.lastCount = 0; // ✅ Track last count for animation
        this.init();
        this.loadNotifications();
        this.startPolling();
    }

    init() {
        // Event listeners with delegation for better performance
        document.addEventListener('click', this.handleClick.bind(this));
        
        // Keyboard navigation support
        document.addEventListener('keydown', this.handleKeydown.bind(this));
        
        // Handle dropdown clicks
        document.getElementById('markAllRead')?.addEventListener('click', () => {
            this.markAllAsRead();
        });

        document.getElementById('notificationDropdown')?.addEventListener('click', () => {
            this.loadNotifications();
        });
    }

    handleClick(event) {
        const target = event.target.closest('[data-action]');
        if (!target) return;

        const action = target.dataset.action;
        const id = target.dataset.id;

        event.preventDefault();
        event.stopPropagation();

        switch (action) {
            case 'mark-all-read':
                this.markAllAsRead();
                break;
            case 'delete-notification':
                this.deleteNotification(id);
                break;
            case 'show-detail':
                this.showNotificationDetail(id);
                break;
            case 'clear-read':
                this.clearRead();
                break;
            case 'clear-expired':
                this.clearExpired();
                break;
            case 'clear-all':
                this.clearAll();
                break;
            case 'refresh':
                this.loadNotifications();
                this.updateCount();
                break;
        }
    }

    handleKeydown(event) {
        // Escape key closes detail modal
        if (event.key === 'Escape' && document.querySelector('.swal2-container')) {
            Swal.close();
        }
    }

    // ✅ ENHANCED POLLING: Auto refresh with smart intervals
    startPolling() {
        this.updateCount(); // Initial count load

        // Adaptive polling: more frequent when tab is active
        this.pollingInterval = setInterval(() => {
            if (document.visibilityState === 'visible') {
                this.updateCount();
            }
        }, document.visibilityState === 'visible' ? 30000 : 60000);

        // Adjust polling based on page visibility
        document.addEventListener('visibilitychange', () => {
            clearInterval(this.pollingInterval);
            this.startPolling();
        });

        console.log('🔄 Smart auto-refresh polling started');
    }

    // ✅ ENHANCED COUNT: Update notification count with debouncing and animation
    async updateCount() {
        if (this.updateCountTimeout) {
            clearTimeout(this.updateCountTimeout);
        }

        this.updateCountTimeout = setTimeout(async () => {
            try {
                const response = await fetch('/api/mahasiswa/notifications/count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    console.log('🔔 Count updated:', data.count); // Debug log
                    this.updateBadge(data.count);
                } else {
                    console.warn('Count API returned false:', data);
                }
            } catch (error) {
                console.error('Failed to update notification count:', error);
                // Show error state briefly
                this.showCountError();
            }
        }, 100);
    }

    // ✅ ENHANCED BADGE: Smooth badge updates with better animations
    updateBadge(count) {
        const badge = document.getElementById('notificationCount');
        if (!badge) {
            console.error('Badge element not found');
            return;
        }

        const currentCount = this.lastCount;
        const isCountIncreased = count > currentCount;
        
        console.log(`🎯 Badge update: ${currentCount} → ${count}`); // Debug log

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;

            // Show badge if hidden
            if (badge.style.display === 'none' || !badge.style.display) {
                badge.style.display = 'flex';
                badge.style.transform = 'scale(0)';
                badge.style.opacity = '0';
                
                // Trigger reflow
                badge.offsetHeight;
                
                // Animate in
                badge.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                badge.style.transform = 'scale(1)';
                badge.style.opacity = '1';
            }

            // Enhanced pulse animation for new notifications
            if (isCountIncreased && currentCount > 0) {
                badge.style.animation = 'none';
                badge.offsetHeight; // Trigger reflow
                badge.style.animation = 'pulse-badge-enhanced 0.8s ease-out';
                
                // Add bounce effect
                setTimeout(() => {
                    badge.style.animation = 'bounce-notification 0.6s ease-out';
                }, 100);
            }

            // First time badge appears with special animation
            if (currentCount === 0 && count > 0) {
                badge.style.animation = 'badge-appear 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }

        } else {
            // Hide badge with animation
            badge.style.transition = 'all 0.2s ease-in';
            badge.style.transform = 'scale(0)';
            badge.style.opacity = '0';
            
            setTimeout(() => {
                badge.style.display = 'none';
                badge.style.transform = 'scale(1)';
                badge.style.opacity = '1';
            }, 200);
        }

        // Update last count
        this.lastCount = count;

        // Clear animation after completion
        setTimeout(() => {
            badge.style.animation = '';
        }, 1000);
    }

    // ✅ NEW: Show error state for count
    showCountError() {
        const badge = document.getElementById('notificationCount');
        if (badge && badge.style.display !== 'none') {
            badge.style.animation = 'shake-error 0.5s ease-in-out';
            setTimeout(() => {
                badge.style.animation = '';
            }, 500);
        }
    }

    // ✅ LOAD: Enhanced loading with error recovery
    async loadNotifications() {
        const elements = {
            loading: document.getElementById('notificationLoading'),
            list: document.getElementById('notificationList'),
            empty: document.getElementById('notificationEmpty'),
            subtitle: document.getElementById('notificationSubtitle')
        };

        // Validate required elements
        const missingElements = Object.entries(elements)
            .filter(([key, element]) => !element)
            .map(([key]) => key);

        if (missingElements.length > 0) {
            console.error('Required elements not found:', missingElements);
            return;
        }

        this.showLoading(true);
        elements.list.innerHTML = '';
        elements.empty.style.display = 'none';

        try {
            const response = await fetch('/api/mahasiswa/notifications', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.renderNotifications(data.data);
                elements.subtitle.textContent = `${data.data.length} notifikasi`;
            } else {
                this.showEmptyState();
                elements.subtitle.textContent = 'Tidak ada notifikasi';
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showErrorState(error.message);
        } finally {
            this.showLoading(false);
        }
    }

    // ✅ LOADING: Improved loading states
    showLoading(show) {
        const loading = document.getElementById('notificationLoading');
        if (loading) {
            loading.style.display = show ? 'block' : 'none';
        }
    }

    showEmptyState() {
        const empty = document.getElementById('notificationEmpty');
        if (empty) {
            empty.style.display = 'block';
        }
    }

    showErrorState(message) {
        const list = document.getElementById('notificationList');
        if (list) {
            list.innerHTML = `
                <div class="notification-error-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div class="error-text">Gagal memuat notifikasi</div>
                    <button class="btn-refresh" data-action="refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                        Coba Lagi
                    </button>
                </div>
            `;
        }
    }

    // ✅ RENDER: Optimized rendering with virtual scrolling preparation
    renderNotifications(notifications) {
        const list = document.getElementById('notificationList');

        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();

        notifications.forEach(notif => {
            const element = this.createNotificationElement(notif);
            fragment.appendChild(element);
        });

        list.appendChild(fragment);
    }

    createNotificationElement(notif) {
        const div = document.createElement('div');
        div.className = `notification-item ${!notif.is_read ? 'unread' : ''}`;
        div.setAttribute('data-id', notif.id_notifikasi);
        div.setAttribute('role', 'listitem');
        div.setAttribute('tabindex', '0');
        div.setAttribute('aria-label', `Notifikasi: ${notif.judul}`);

        div.innerHTML = `
            <div class="d-flex align-items-start gap-3">
                <div class="notification-icon-wrapper ${this.getIconClass(notif.kategori)}">
                    <i class="bi ${this.getIcon(notif.kategori)}" aria-hidden="true"></i>
                </div>
                <div class="notification-content" 
                     data-action="show-detail" 
                     data-id="${notif.id_notifikasi}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="notification-main-title">${this.escapeHtml(notif.judul)}</h6>
                        ${notif.is_important ? '<i class="bi bi-star-fill notification-important-icon" aria-label="Penting"></i>' : ''}
                    </div>
                    <p class="notification-message">${this.escapeHtml(this.truncateText(notif.pesan, 80))}</p>
                    <span class="notification-time">${this.escapeHtml(notif.time_ago)}</span>
                </div>
                <div class="notification-actions-column">
                    ${!notif.is_read ? '<div class="notification-dot" aria-label="Belum dibaca"></div>' : '<div style="height: 8px;"></div>'}
                    <button class="btn-delete-notification" 
                            data-action="delete-notification"
                            data-id="${notif.id_notifikasi}"
                            title="Hapus notifikasi"
                            aria-label="Hapus notifikasi">
                        <i class="bi bi-x" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        `;

        return div;
    }

    // ✅ SECURITY: HTML escaping
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ✅ ICONS: Category-based styling
    getIconClass(kategori) {
        const iconClasses = {
            'lamaran': 'icon-lamaran',
            'magang': 'icon-magang',
            'sistem': 'icon-sistem',
            'pengumuman': 'icon-pengumuman',
            'evaluasi': 'icon-evaluasi',
            'deadline': 'icon-deadline'
        };
        return iconClasses[kategori] || 'icon-sistem';
    }

    getIcon(kategori) {
        const icons = {
            'lamaran': 'bi-file-earmark-text',
            'magang': 'bi-briefcase',
            'sistem': 'bi-gear',
            'pengumuman': 'bi-megaphone',
            'evaluasi': 'bi-clipboard-check',
            'deadline': 'bi-alarm'
        };
        return icons[kategori] || 'bi-bell';
    }

    // ✅ ACTIONS: Enhanced user actions with optimistic updates
    async markAsRead(id) {
        // Optimistic update
        const item = document.querySelector(`[data-id="${id}"]`);
        if (item) {
            item.classList.remove('unread');
            const dot = item.querySelector('.notification-dot');
            if (dot) {
                dot.style.opacity = '0';
                setTimeout(() => {
                    dot.style.height = '8px';
                    dot.innerHTML = '';
                }, 200);
            }
        }

        try {
            const response = await fetch(`/api/mahasiswa/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateCount(); // ✅ Update count after marking as read
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error marking as read:', error);
            // Revert optimistic update
            if (item) {
                item.classList.add('unread');
            }
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/api/mahasiswa/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.loadNotifications();
                this.updateCount(); // ✅ Update count after marking all as read
                this.showToast(`${data.updated_count} notifikasi ditandai dibaca`, 'success');
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
            this.showToast('Gagal menandai semua sebagai dibaca', 'error');
        }
    }

    async deleteNotification(id) {
        try {
            const response = await fetch(`/api/mahasiswa/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                // Smooth removal animation
                const item = document.querySelector(`[data-id="${id}"]`);
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        item.remove();
                        this.checkEmptyState();
                    }, 300);
                }

                this.updateCount(); // ✅ Update count after deletion
                this.showToast('Notifikasi dihapus', 'success');
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
            this.showToast('Gagal menghapus notifikasi', 'error');
        }
    }

    checkEmptyState() {
        const remainingItems = document.querySelectorAll('.notification-item');
        if (remainingItems.length === 0) {
            this.showEmptyState();
            document.getElementById('notificationSubtitle').textContent = 'Tidak ada notifikasi';
        }
    }

    // ✅ CLEAR ACTIONS: Bulk operations with confirmation
    async clearRead() {
        const result = await this.showConfirmation(
            'Hapus Notifikasi Terbaca?',
            'Semua notifikasi yang sudah dibaca akan dihapus permanen.',
            'warning',
            '#3B82F6'
        );

        if (result.isConfirmed) {
            await this.performClearAction('/api/mahasiswa/notifications/read', 'notifikasi terbaca');
        }
    }

    async clearExpired() {
        const result = await this.showConfirmation(
            'Hapus Notifikasi Kedaluwarsa?',
            'Notifikasi yang lebih dari 30 hari akan dihapus permanen.',
            'warning',
            '#F59E0B'
        );

        if (result.isConfirmed) {
            await this.performClearAction('/api/mahasiswa/notifications/expired', 'notifikasi kedaluwarsa');
        }
    }

    async clearAll() {
        const result = await this.showConfirmation(
            'Hapus Semua Notifikasi?',
            'Semua notifikasi akan dihapus permanen dan tidak bisa dikembalikan.',
            'warning',
            '#EF4444'
        );

        if (result.isConfirmed) {
            await this.performClearAction('/api/mahasiswa/notifications', 'notifikasi');
        }
    }

    async performClearAction(url, type) {
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.loadNotifications();
                this.updateCount(); // ✅ Update count after clearing
                this.showToast(`${data.deleted_count} ${type} dihapus`, 'success');
            }
        } catch (error) {
            console.error(`Error clearing ${type}:`, error);
            this.showToast(`Gagal menghapus ${type}`, 'error');
        }
    }

    // ✅ UI HELPERS: Consistent UI feedback
    showConfirmation(title, text, icon, confirmButtonColor) {
        return Swal.fire({
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonColor,
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3',
                confirmButton: 'px-4 py-2',
                cancelButton: 'px-4 py-2'
            }
        });
    }

    showToast(message, type = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        const iconMap = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Toast.fire({
            icon: iconMap[type] || 'info',
            title: message
        });
    }

    // ✅ DETAIL MODAL: Enhanced notification detail view
    async showNotificationDetail(id) {
        try {
            Swal.fire({
                title: 'Memuat Detail...',
                allowOutsideClick: false,
                allowEscapeKey: true,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            const response = await fetch(`/api/mahasiswa/notifications/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                await this.renderNotificationDetail(data.data);

                if (!data.data.is_read) {
                    this.markAsRead(id);
                }
            } else {
                throw new Error(data.message || 'Gagal memuat detail notifikasi');
            }

        } catch (error) {
            console.error('Error loading notification detail:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Gagal memuat detail notifikasi',
                icon: 'error',
                customClass: { popup: 'rounded-3' }
            });
        }
    }

    // ✅ RENDER NOTIFICATION DETAIL: Enhanced modal dengan UI yang rapi
    async renderNotificationDetail(notification) {
        const categoryClass = `category-${notification.kategori}`;
        const iconClass = this.getIconClass(notification.kategori);
        const icon = notification.icon || this.getIcon(notification.kategori);
        
        // Generate status badges
        const statusBadges = this.generateStatusBadges(notification);
        
        // Generate metadata section
        const metadataSection = this.generateMetadataSection(notification);
        
        // Generate action buttons based on category and data
        const actionButtons = this.generateActionButtons(notification, notification.data_terkait);

        const html = `
            <div class="notification-detail-modal ${categoryClass}">
                <!-- Header Section -->
                <div class="notification-detail-header">
                    <div class="notification-detail-header-pattern"></div>
                    <div class="notification-detail-header-content">
                        <div class="d-flex align-items-start gap-4">
                            <div class="notification-detail-icon-large ${iconClass}">
                                <i class="bi ${icon}"></i>
                            </div>
                            <div class="notification-detail-title-section flex-1">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <h3 class="notification-detail-main-title">
                                        ${this.escapeHtml(notification.judul)}
                                    </h3>
                                    <div class="notification-detail-badges">
                                        ${statusBadges}
                                    </div>
                                </div>
                                <div class="notification-detail-meta">
                                    <span class="notification-detail-category">
                                        <i class="bi bi-tag-fill"></i>
                                        ${this.getCategoryName(notification.kategori)}
                                    </span>
                                    <span class="notification-detail-jenis ${notification.badge_class}">
                                        ${this.getJenisName(notification.jenis)}
                                    </span>
                                    <span class="notification-detail-time">
                                        <i class="bi bi-clock"></i>
                                        ${notification.time_ago}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="notification-detail-content">
                    <!-- Message -->
                    <div class="notification-detail-message-section">
                        <h5 class="notification-detail-section-title">
                            <i class="bi bi-chat-text"></i>
                            Pesan
                        </h5>
                        <div class="notification-detail-message">
                            ${this.formatMessage(notification.pesan)}
                        </div>
                    </div>
                    
                    <!-- Metadata -->
                    ${metadataSection}
                    
                    <!-- Additional Data -->
                    ${notification.data_terkait ? this.renderAdditionalData(notification.data_terkait, notification.kategori) : ''}
                    
                    <!-- Timeline Info -->
                    <div class="notification-detail-timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="bi bi-plus-circle text-success"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>Dibuat:</strong> ${notification.formatted_date}
                            </div>
                        </div>
                        ${notification.is_read ? `
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-check-circle text-primary"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>Dibaca:</strong> Sudah dibaca
                                </div>
                            </div>
                        ` : ''}
                        ${notification.expired_at ? `
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-alarm ${notification.is_expired ? 'text-danger' : 'text-warning'}"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>${notification.is_expired ? 'Kedaluwarsa:' : 'Akan kedaluwarsa:'}</strong> 
                                    ${this.formatDate(notification.expired_at)}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Actions Section -->
                <div class="notification-detail-actions">
                    <div class="notification-status-indicator">
                        <div class="notification-status-dot ${notification.is_read ? 'status-read' : 'status-unread'}"></div>
                        <span class="notification-status-text">
                            ${notification.is_read ? 'Sudah dibaca' : 'Belum dibaca'}
                        </span>
                    </div>
                    <div class="notification-action-buttons">
                        ${actionButtons}
                        <button class="notification-action-btn notification-action-secondary" onclick="Swal.close()" title="Tutup modal">
                            <i class="bi bi-x-lg"></i>
                            <span>Tutup</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        await Swal.fire({
            html: html,
            showConfirmButton: false,
            showCloseButton: false,
            customClass: {
                popup: 'notification-detail-modal-container',
                htmlContainer: 'p-0'
            },
            width: 'auto',
            padding: 0,
            background: 'transparent',
            backdrop: 'rgba(0, 0, 0, 0.4)'
        });
    }

    // ✅ GENERATE STATUS BADGES: Create status badges
    generateStatusBadges(notification) {
        let badges = '';
        
        if (notification.is_important) {
            badges += `
                <span class="notification-badge notification-badge-important">
                    <i class="bi bi-star-fill"></i>
                    Penting
                </span>
            `;
        }
        
        if (notification.is_expired) {
            badges += `
                <span class="notification-badge notification-badge-expired">
                    <i class="bi bi-clock-history"></i>
                    Kedaluwarsa
                </span>
            `;
        }
        
        return badges;
    }

    // ✅ GENERATE METADATA SECTION: Create metadata display
    generateMetadataSection(notification) {
        return `
            <div class="notification-detail-metadata-section">
                <h5 class="notification-detail-section-title">
                    <i class="bi bi-info-circle"></i>
                    Informasi Detail
                </h5>
                <div class="notification-metadata-grid">
                    <div class="metadata-item">
                        <span class="metadata-label">ID Notifikasi:</span>
                        <span class="metadata-value">#${notification.id_notifikasi}</span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Kategori:</span>
                        <span class="metadata-value">${this.getCategoryName(notification.kategori)}</span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Jenis:</span>
                        <span class="metadata-value">${this.getJenisName(notification.jenis)}</span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Status:</span>
                        <span class="metadata-value">${notification.is_read ? 'Sudah dibaca' : 'Belum dibaca'}</span>
                    </div>
                    ${notification.expired_at ? `
                        <div class="metadata-item">
                            <span class="metadata-label">Kedaluwarsa:</span>
                            <span class="metadata-value">${this.formatDate(notification.expired_at)}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // ✅ GENERATE ACTION BUTTONS: Context-based buttons
    generateActionButtons(notification, additionalData) {
        let buttons = '';

        // Action buttons based on category
        switch (notification.kategori) {
            case 'lamaran':
                if (additionalData?.lamaran_id) {
                    buttons += `
                        <a href="/mahasiswa/lamaran" class="notification-action-btn notification-action-primary">
                            <i class="bi bi-eye"></i>
                            <span>Lihat Lamaran</span>
                        </a>
                    `;
                }
                if (additionalData?.action === 'submitted') {
                    buttons += `
                        <a href="/mahasiswa/lowongan" class="notification-action-btn notification-action-secondary">
                            <i class="bi bi-search"></i>
                            <span>Cari Lowongan Lain</span>
                        </a>
                    `;
                }
                break;

            case 'magang':
                buttons += `
                    <a href="/mahasiswa/logaktivitas" class="notification-action-btn notification-action-primary">
                        <i class="bi bi-journal-text"></i>
                        <span>Buka Logbook</span>
                    </a>
                `;
                break;

            case 'evaluasi':
                buttons += `
                    <a href="/mahasiswa/evaluasi" class="notification-action-btn notification-action-primary">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Isi Evaluasi</span>
                    </a>
                `;
                break;

            case 'deadline':
                if (additionalData?.deadline) {
                    buttons += `
                        <a href="/mahasiswa/evaluasi" class="notification-action-btn notification-action-primary">
                            <i class="bi bi-clock-history"></i>
                            <span>Selesaikan Sekarang</span>
                        </a>
                    `;
                }
                break;

            case 'sistem':
                if (additionalData?.completion && additionalData.completion < 80) {
                    buttons += `
                        <a href="/mahasiswa/profile" class="notification-action-btn notification-action-primary">
                            <i class="bi bi-person-gear"></i>
                            <span>Lengkapi Profile</span>
                        </a>
                    `;
                }
                break;

            case 'pengumuman':
                if (additionalData?.action_url) {
                    buttons += `
                        <a href="${additionalData.action_url}" class="notification-action-btn notification-action-primary">
                            <i class="bi bi-arrow-right"></i>
                            <span>Lihat Detail</span>
                        </a>
                    `;
                }
                break;
        }

        // Always add delete button
        buttons += `
            <button class="notification-action-btn notification-action-secondary" 
                    onclick="notificationSystem.deleteNotificationFromDetail(${notification.id_notifikasi})" title="Hapus notifikasi">
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </button>
        `;

        return buttons;
    }

    // ✅ ENHANCED ADDITIONAL DATA RENDERING
    renderAdditionalData(data, category) {
        if (!data || typeof data !== 'object') return '';

        const dataItems = [];

        // Category-specific data rendering dengan data dari database
        switch (category) {
            case 'lamaran':
                if (data.action) dataItems.push(['Status Lamaran', this.getActionText(data.action)]);
                if (data.lamaran_id) dataItems.push(['ID Lamaran', `#${data.lamaran_id}`]);
                if (data.perusahaan) dataItems.push(['Perusahaan', data.perusahaan]);
                if (data.posisi) dataItems.push(['Posisi', data.posisi]);
                if (data.lowongan_id) dataItems.push(['ID Lowongan', `#${data.lowongan_id}`]);
                if (data.documents_count) dataItems.push(['Jumlah Dokumen', `${data.documents_count} dokumen`]);
                if (data.document_types) dataItems.push(['Jenis Dokumen', data.document_types.join(', ')]);
                break;

            case 'magang':
                if (data.perusahaan) dataItems.push(['Perusahaan', data.perusahaan]);
                if (data.posisi) dataItems.push(['Posisi', data.posisi]);
                if (data.magang_id) dataItems.push(['ID Magang', `#${data.magang_id}`]);
                if (data.id_magang) dataItems.push(['ID Magang', `#${data.id_magang}`]);
                if (data.tgl_mulai) dataItems.push(['Tanggal Mulai', this.formatDate(data.tgl_mulai)]);
                if (data.tgl_selesai) dataItems.push(['Tanggal Selesai', this.formatDate(data.tgl_selesai)]);
                if (data.durasi_hari) dataItems.push(['Durasi', `${data.durasi_hari} hari`]);
                if (data.completion_type) dataItems.push(['Jenis Penyelesaian', this.getCompletionTypeText(data.completion_type)]);
                if (data.days_expired) dataItems.push(['Hari Kedaluwarsa', `${data.days_expired} hari`]);
                break;

            case 'evaluasi':
                if (data.deadline) dataItems.push(['Deadline', this.formatDate(data.deadline)]);
                if (data.jenis_evaluasi) dataItems.push(['Jenis Evaluasi', data.jenis_evaluasi]);
                break;

            case 'sistem':
                if (data.completion !== undefined) dataItems.push(['Kelengkapan Profile', `${data.completion}%`]);
                break;

            case 'deadline':
                if (data.deadline) dataItems.push(['Deadline', this.formatDate(data.deadline)]);
                if (data.jenis_evaluasi) dataItems.push(['Jenis', data.jenis_evaluasi]);
                break;

            case 'pengumuman':
                if (data.action_url) dataItems.push(['Link Terkait', data.action_url]);
                break;

            default:
                // Generic data rendering
                Object.entries(data).forEach(([key, value]) => {
                    if (key !== 'action_url' && value !== null && value !== undefined && key !== 'document_types') {
                        dataItems.push([this.formatKey(key), Array.isArray(value) ? value.join(', ') : value]);
                    }
                });
        }

        if (dataItems.length === 0) return '';

        return `
            <div class="notification-detail-data-section">
                <h5 class="notification-detail-section-title">
                    <i class="bi bi-database"></i>
                    Data Terkait
                </h5>
                <div class="notification-detail-data-content">
                    ${dataItems.map(([label, value]) => `
                        <div class="notification-data-item">
                            <span class="notification-data-label">${label}:</span>
                            <span class="notification-data-value">${value}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    // ✅ DELETE FROM DETAIL: Delete from modal
    async deleteNotificationFromDetail(id) {
        const result = await Swal.fire({
            title: 'Hapus Notifikasi?',
            text: 'Notifikasi ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-3' }
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/api/mahasiswa/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.close(); // Close detail modal
                    this.loadNotifications(); // Refresh notification list
                    this.updateCount(); // ✅ Update count after deletion
                    this.showToast('Notifikasi berhasil dihapus', 'success');
                } else {
                    throw new Error(data.message || 'Gagal menghapus notifikasi');
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal menghapus notifikasi',
                    icon: 'error',
                    customClass: { popup: 'rounded-3' }
                });
            }
        }
    }

    // ✅ HELPER METHODS: Complete utility functions
    truncateText(text, length) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }

    getCategoryName(kategori) {
        const names = {
            'lamaran': 'Lamaran',
            'magang': 'Magang', 
            'sistem': 'Sistem',
            'pengumuman': 'Pengumuman',
            'evaluasi': 'Evaluasi',
            'deadline': 'Deadline'
        };
        return names[kategori] || 'Notifikasi';
    }

    // ✅ FIXED: Added missing getJenisName method
    getJenisName(jenis) {
        const jenisNames = {
            'info': 'Informasi',
            'success': 'Sukses',
            'warning': 'Peringatan',
            'danger': 'Bahaya'
        };
        return jenisNames[jenis] || jenis;
    }

    getActionText(action) {
        const actions = {
            'submitted': 'Lamaran Dikirim',
            'accepted': 'Lamaran Diterima',
            'rejected': 'Lamaran Ditolak',
            'cancelled': 'Lamaran Dibatalkan'
        };
        return actions[action] || action;
    }

    getCompletionTypeText(type) {
        const types = {
            'auto_completed': 'Diselesaikan Otomatis',
            'manual_completed': 'Diselesaikan Manual',
            'forced_completed': 'Diselesaikan Paksa'
        };
        return types[type] || type;
    }

    formatKey(key) {
        return key.replace(/_/g, ' ')
                  .split(' ')
                  .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                  .join(' ');
    }

    // ✅ FIXED: Added missing formatMessage method
    formatMessage(message) {
        // Convert line breaks and basic formatting
        return message
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }

    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    // ✅ CLEANUP: Destroy polling on page unload
    destroy() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            console.log('🛑 NotificationSystem polling stopped');
        }
        if (this.updateCountTimeout) {
            clearTimeout(this.updateCountTimeout);
        }
    }
}

// ✅ INITIALIZATION: Single instance with proper cleanup
document.addEventListener('DOMContentLoaded', function () {
    // Cleanup existing instance
    if (window.notificationSystem) {
        window.notificationSystem.destroy();
    }
    
    // Initialize new instance
    window.notificationSystem = new NotificationSystem();
});

// ✅ CLEANUP: On page unload
window.addEventListener('beforeunload', function () {
    if (window.notificationSystem) {
        window.notificationSystem.destroy();
    }
});