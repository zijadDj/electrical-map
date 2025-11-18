<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrical Infrastructure Management System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        #map {
            height: 100vh;
            width: 100%;
            z-index: 1;
        }
        
        .sidebar {
            z-index: 1000;
        }
        
        .leaflet-popup-content {
            font-family: 'Inter', sans-serif;
            min-width: 320px;
            max-width: 400px;
        }
        
        @media (max-width: 480px) {
            .leaflet-popup-content {
                min-width: 280px;
                max-width: 90vw;
            }
        }
        
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .leaflet-popup-tip {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .custom-marker-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        .status-read { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .status-not_read { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .status-season { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">

<!-- Sidebar -->
<div class="sidebar fixed left-0 top-0 h-full w-80 bg-white shadow-2xl transform transition-transform duration-300 slide-in -translate-x-full lg:translate-x-0">
    <div class="h-full flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-bolt text-2xl"></i>
                    <h1 class="text-xl font-bold">Electrical Map</h1>
                </div>
                <button id="toggleSidebar" class="lg:hidden hover:bg-white/20 p-2 rounded-lg transition">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <p class="text-sm opacity-90">Infrastructure Management System</p>
        </div>
        
        <!-- Search Section -->
        <div class="p-6 border-b">
            <div class="relative">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search electrical boxes..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                >
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="p-6 border-b">
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4">Statistics</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <span id="readCount" class="text-2xl font-bold text-green-700">0</span>
                    </div>
                    <p class="text-sm text-green-600 mt-1">Read</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center justify-between">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        <span id="not_readCount" class="text-2xl font-bold text-red-700">0</span>
                    </div>
                    <p class="text-sm text-red-600 mt-1">Not Read</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <div class="flex items-center justify-between">
                        <i class="fas fa-tools text-amber-600 text-xl"></i>
                        <span id="seasonCount" class="text-2xl font-bold text-amber-700">0</span>
                    </div>
                    <p class="text-sm text-amber-600 mt-1">Season</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                        <span id="totalCount" class="text-2xl font-bold text-blue-700">0</span>
                    </div>
                    <p class="text-sm text-blue-600 mt-1">Total</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="p-6 border-b">
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4">Filters</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" id="filterRead" checked class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                    <span class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm">Read Boxes</span>
                    </span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" id="filterNot_read" checked class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                    <span class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm">Not Read Boxes</span>
                    </span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" id="filterSeason" checked class="w-4 h-4 text-amber-600 rounded focus:ring-amber-500">
                    <span class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                        <span class="text-sm">Season Boxes</span>
                    </span>
                </label>
            </div>
        </div>
        
        <!-- Results List -->
        <div class="flex-1 overflow-y-auto p-6">
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4">Search Results</h3>
            <div id="resultsList" class="space-y-3">
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    Start searching to see results
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Menu Toggle -->
<button id="mobileMenuToggle" class="lg:hidden fixed top-4 left-4 z-50 bg-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
    <i class="fas fa-bars text-gray-700"></i>
</button>

<!-- Map Container -->
<div id="map"></div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 shadow-2xl">
        <div class="flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Loading electrical boxes...</span>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
    // Global variables
    let map;
    let markers = L.markerClusterGroup();
    let allBoxes = [];
    let filteredBoxes = [];
    
    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        fetchBoxes();
        setupEventListeners();
    });
    
    // Initialize the map
    function initializeMap() {
        map = L.map('map').setView([42.5, 19.3], 12);
        
        // Add OpenStreetMap tiles with better styling
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add the marker cluster group to the map
        map.addLayer(markers);
    }
    
    // Fetch boxes from API
    async function fetchBoxes() {
        try {
            const response = await fetch('/api/boxes');
            const data = await response.json();
            allBoxes = data;
            filteredBoxes = [...allBoxes];
            
            // Hide loading overlay
            document.getElementById('loadingOverlay').style.display = 'none';
            
            // Update statistics
            updateStatistics();
            
            // Add markers to map
            addMarkersToMap();
            
        } catch (error) {
            console.error('Error fetching boxes:', error);
            document.getElementById('loadingOverlay').innerHTML = `
                <div class="bg-white rounded-lg p-8 shadow-2xl">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                        <p class="text-gray-700">Failed to load electrical boxes</p>
                        <button onclick="location.reload()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Retry
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    // Add markers to the map
    function addMarkersToMap() {
        // Clear existing markers
        markers.clearLayers();
        
        filteredBoxes.forEach(box => {
            if (box.latitude && box.longitude) {
                const statusClass = getStatusClass(box.status);
                const icon = L.divIcon({
                    className: `custom-marker-icon ${statusClass}`,
                    html: `<i class="fas fa-bolt text-white text-xs"></i>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                
                const marker = L.marker([box.latitude, box.longitude], { icon })
                    .bindPopup(createPopupContent(box));
                
                markers.addLayer(marker);
            }
        });
    }
    
    // Create popup content
    function createPopupContent(box) {
        const statusBadge = getStatusBadge(box.status);
        return `
            <div class="p-5 min-w-[320px] max-w-[400px]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-xl text-gray-800">${box.code}</h3>
                    ${statusBadge}
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                        <span class="text-gray-600">Latitude: ${box.latitude.toFixed(6)}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-compass text-gray-400 w-5"></i>
                        <span class="text-gray-600">Longitude: ${box.longitude.toFixed(6)}</span>
                    </div>
                </div>
                <div class="mt-5 pt-4 border-t flex space-x-3">
                    <button onclick="centerOnBox(${box.latitude}, ${box.longitude})" class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-crosshairs mr-2"></i> Center
                    </button>
                    <button onclick="getDirections(${box.latitude}, ${box.longitude})" class="flex-1 bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-green-700 transition font-medium">
                        <i class="fas fa-directions mr-2"></i> Directions
                    </button>
                </div>
            </div>
        `;
    }
    
    // Get status CSS class
    function getStatusClass(status) {
        switch(status?.toLowerCase()) {
            case 'read': return 'status-read';
            case 'not_read': return 'status-not_read';
            case 'season': return 'status-season';
            default: return 'status-read';
        }
    }
    
    // Get status badge HTML
    function getStatusBadge(status) {
        const statusClass = getStatusClass(status);
        const statusText = status || 'Unknown';
        return `<span class="px-2 py-1 rounded-full text-xs font-semibold text-white ${statusClass}">${statusText}</span>`;
    }
    
    // Update statistics
    function updateStatistics() {
        const stats = {
            read: allBoxes.filter(b => b.status?.toLowerCase() === 'read').length,
            not_read: allBoxes.filter(b => b.status?.toLowerCase() === 'not_read').length,
            season: allBoxes.filter(b => b.status?.toLowerCase() === 'season').length,
            total: allBoxes.length
        };
        
        document.getElementById('readCount').textContent = stats.read;
        document.getElementById('not_readCount').textContent = stats.not_read;
        document.getElementById('seasonCount').textContent = stats.season;
        document.getElementById('totalCount').textContent = stats.total;
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', handleSearch);
        
        // Filter checkboxes
        ['filterRead', 'filterNot_read', 'filterSeason'].forEach(id => {
            document.getElementById(id).addEventListener('change', applyFilters);
        });
        
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        
        function toggleSidebarMenu() {
            sidebar.classList.toggle('-translate-x-full');
        }
        
        mobileToggle.addEventListener('click', toggleSidebarMenu);
        toggleSidebar.addEventListener('click', toggleSidebarMenu);
    }
    
    // Handle search
    function handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        if (searchTerm === '') {
            filteredBoxes = [...allBoxes];
            document.getElementById('resultsList').innerHTML = `
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    Start searching to see results
                </div>
            `;
        } else {
            filteredBoxes = allBoxes.filter(box => 
                box.code?.toLowerCase().includes(searchTerm) ||
                box.status?.toLowerCase().includes(searchTerm)
            );
            updateResultsList(filteredBoxes);
        }
        
        applyFilters();
    }
    
    // Apply filters
    function applyFilters() {
        const readFilter = document.getElementById('filterRead').checked;
        const not_readFilter = document.getElementById('filterNot_read').checked;
        const seasonFilter = document.getElementById('filterSeason').checked;
        
        let finalFiltered = filteredBoxes.filter(box => {
            const status = box.status?.toLowerCase();
            return (status === 'read' && readFilter) ||
                   (status === 'not_read' && not_readFilter) ||
                   (status === 'season' && seasonFilter) ||
                   (!status && readFilter);
        });
        
        // Update markers
        markers.clearLayers();
        finalFiltered.forEach(box => {
            if (box.latitude && box.longitude) {
                const statusClass = getStatusClass(box.status);
                const icon = L.divIcon({
                    className: `custom-marker-icon ${statusClass}`,
                    html: `<i class="fas fa-bolt text-white text-xs"></i>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                
                const marker = L.marker([box.latitude, box.longitude], { icon })
                    .bindPopup(createPopupContent(box));
                
                markers.addLayer(marker);
            }
        });
        
        // Update results list
        updateResultsList(finalFiltered);
    }
    
    // Update results list
    function updateResultsList(boxes) {
        const resultsList = document.getElementById('resultsList');
        
        if (boxes.length === 0) {
            resultsList.innerHTML = `
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                    No results found
                </div>
            `;
            return;
        }
        
        resultsList.innerHTML = boxes.slice(0, 10).map(box => `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="centerOnBox(${box.latitude}, ${box.longitude})">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-800">${box.code}</h4>
                    ${getStatusBadge(box.status)}
                </div>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    ${box.latitude.toFixed(4)}, ${box.longitude.toFixed(4)}
                </div>
            </div>
        `).join('');
    }
    
    // Center map on specific box
    function centerOnBox(lat, lng) {
        map.setView([lat, lng], 16);
        
        // Open the popup for this marker
        markers.eachLayer(layer => {
            const pos = layer.getLatLng();
            if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
                layer.openPopup();
            }
        });
    }
    
    // Get directions (opens in Google Maps)
    function getDirections(lat, lng) {
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
    }
</script>

</body>
</html>
