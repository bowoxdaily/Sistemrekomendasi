$(document).ready(function() {
    // Load recent activities
    loadActivities();
    
    // Handle filter change
    $('#activity-filter').change(function() {
        loadActivities($(this).val());
    });
    
    // Function to load activities
    function loadActivities(filter = 'all') {
        $.ajax({
            url: _baseURL + 'api/student/activities',
            type: 'GET',
            data: { filter: filter },
            success: function(response) {
                renderActivities(response.data);
            },
            error: function() {
                $('#activity-container').html(
                    '<div class="alert alert-warning">Gagal memuat aktivitas. Silakan coba lagi nanti.</div>'
                );
            }
        });
    }
    
    // Function to render activities
    function renderActivities(activities) {
        const container = $('#activity-container');
        container.empty();
        
        if (activities.length === 0) {
            container.html('<div class="text-center py-4 text-muted">Belum ada aktivitas</div>');
            return;
        }
        
        const template = $('#activity-template').html();
        
        activities.forEach(function(activity) {
            let item = template
                .replace('{{title}}', activity.title)
                .replace('{{time}}', formatTime(activity.timestamp))
                .replace('{{description}}', activity.description)
                .replace(/\{\{hasAction\}\}/g, activity.action_url ? '' : '<!--')
                .replace(/\{\{\/hasAction\}\}/g, activity.action_url ? '' : '-->')
                .replace('{{actionUrl}}', activity.action_url || '#')
                .replace('{{actionText}}', activity.action_text || 'Lihat');
                
            // Set the appropriate icon based on activity type
            item = item.replace('mdi-circle', getActivityIcon(activity.type));
                
            container.append(item);
        });
    }
    
    // Get appropriate icon for activity type
    function getActivityIcon(type) {
        switch(type) {
            case 'questionnaire':
                return 'mdi mdi-clipboard-text text-info';
            case 'recommendation':
                return 'mdi mdi-lightbulb-outline text-warning';
            case 'profile':
                return 'mdi mdi-account-edit text-success';
            case 'login':
                return 'mdi mdi-login-variant text-primary';
            default:
                return 'mdi mdi-bell-outline text-secondary';
        }
    }
    
    // Format timestamp to relative time
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffMinutes < 1) return 'Baru saja';
        if (diffMinutes < 60) return `${diffMinutes} menit yang lalu`;
        
        const diffHours = Math.floor(diffMinutes / 60);
        if (diffHours < 24) return `${diffHours} jam yang lalu`;
        
        const diffDays = Math.floor(diffHours / 24);
        if (diffDays < 7) return `${diffDays} hari yang lalu`;
        
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }
});
