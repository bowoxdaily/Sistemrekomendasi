$(document).ready(function() {
    // Load activities when page loads
    loadActivities();
    
    // Handle filter change
    $('#activity-filter').on('change', function() {
        loadActivities($(this).val());
    });
    
    // Function to load activities via AJAX
    function loadActivities(filter = 'all') {
        $('#activity-container').html(`
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        
        $.ajax({
            url: _baseURL + 'api/student/activities',
            type: 'GET',
            data: { filter: filter },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderActivities(response.data);
                } else {
                    showErrorMessage('Terjadi kesalahan saat memuat aktivitas.');
                }
            },
            error: function(xhr) {
                showErrorMessage('Tidak dapat memuat data aktivitas. Silakan coba lagi nanti.');
                console.error('Error loading activities:', xhr);
            }
        });
    }
    
    // Function to render activities
    function renderActivities(activities) {
        const container = $('#activity-container');
        container.empty();
        
        if (activities.length === 0) {
            container.html(`
                <div class="text-center py-4">
                    <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">Belum ada aktivitas tercatat</p>
                </div>
            `);
            return;
        }
        
        activities.forEach(function(activity) {
            const actionButton = activity.action_url ? 
                `<a href="${activity.action_url}" class="btn btn-sm btn-outline-primary mt-2">${activity.action_text}</a>` : '';
            
            const activityItem = `
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="${getActivityIconClass(activity.type)}"></i>
                        <div class="activity-line"></div>
                    </div>
                    <div class="activity-content">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="activity-title mb-0">${activity.title}</h6>
                            <small class="text-muted activity-time">${formatTimeAgo(activity.timestamp)}</small>
                        </div>
                        <p class="activity-text mb-0 text-muted">${activity.description}</p>
                        ${actionButton}
                    </div>
                </div>
            `;
            
            container.append(activityItem);
        });
    }
    
    // Function to show error message
    function showErrorMessage(message) {
        $('#activity-container').html(`
            <div class="alert alert-warning">
                <i class="mdi mdi-alert-circle-outline mr-2"></i>
                ${message}
            </div>
        `);
    }
    
    // Get appropriate icon class based on activity type
    function getActivityIconClass(type) {
        switch(type) {
            case 'questionnaire':
                return 'mdi mdi-clipboard-text text-info';
            case 'recommendation':
                return 'mdi mdi-lightbulb-outline text-warning';
            case 'profile':
                return 'mdi mdi-account-edit text-success';
            case 'login':
                return 'mdi mdi-login text-primary';
            default:
                return 'mdi mdi-bell-outline text-secondary';
        }
    }
    
    // Format timestamp to relative time
    function formatTimeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diffMs = now - time;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHours = Math.floor(diffMin / 60);
        const diffDays = Math.floor(diffHours / 24);
        
        if (diffSec < 60) {
            return 'baru saja';
        } else if (diffMin < 60) {
            return `${diffMin} menit yang lalu`;
        } else if (diffHours < 24) {
            return `${diffHours} jam yang lalu`;
        } else if (diffDays < 7) {
            return `${diffDays} hari yang lalu`;
        } else {
            // Format as date for older activities
            return new Date(timestamp).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    }
});
