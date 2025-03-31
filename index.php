<?php
// Database connection configuration
require_once 'config/database.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include header
include_once 'includes/header.php';

// Main content
?>

<div class="container mt-4">
    <h1>Inventory Management System</h1>
    
    <!-- Dashboard Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text" id="totalProducts">Loading...</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Items</h5>
                    <p class="card-text" id="lowStock">Loading...</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Categories</h5>
                    <p class="card-text" id="totalCategories">Loading...</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Activities</h5>
                    <p class="card-text" id="recentActivities">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <h2>Quick Actions</h2>
            <div class="btn-group">
                <a href="products/add.php" class="btn btn-primary">Add Product</a>
                <a href="products/index.php" class="btn btn-success">View Products</a>
                <a href="categories/manage.php" class="btn btn-secondary">Manage Categories</a>
                <a href="reports/generate.php" class="btn btn-info">Generate Report</a>
            </div>
        </div>
    </div>
</div>

<script>
// Fetch dashboard data
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/dashboard_data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('API Error:', data.error);
                document.getElementById('totalProducts').textContent = 'Error';
                document.getElementById('lowStock').textContent = 'Error';
                document.getElementById('totalCategories').textContent = 'Error';
                document.getElementById('recentActivities').textContent = 'Error loading data';
                return;
            }
            
            // Update dashboard cards
            document.getElementById('totalProducts').textContent = data.total_products || '0';
            document.getElementById('lowStock').textContent = data.low_stock || '0';
            document.getElementById('totalCategories').textContent = data.total_categories || '0';
            
            // Format recent activities
            let activitiesHtml = '';
            if (!data.recent_activities || data.recent_activities.length === 0) {
                activitiesHtml = 'No recent activities';
            } else {
                activitiesHtml = '<ul class="list-unstyled">';
                data.recent_activities.forEach(activity => {
                    let dateText = 'Recently';
                    if (activity.created_at) {
                        const date = new Date(activity.created_at);
                        if (!isNaN(date.getTime())) {
                            dateText = date.toLocaleDateString();
                        }
                    }
                    activitiesHtml += `<li>${activity.nome} - ${dateText}</li>`;
                });
                activitiesHtml += '</ul>';
            }
            document.getElementById('recentActivities').innerHTML = activitiesHtml;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('totalProducts').textContent = 'Error loading data';
            document.getElementById('lowStock').textContent = 'Error loading data';
            document.getElementById('totalCategories').textContent = 'Error loading data';
            document.getElementById('recentActivities').textContent = 'Error loading data';
        });
});
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>
