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

    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-tip {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .custom-marker-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .user-location-marker {
            background: transparent !important;
            border: none !important;
        }

        .status-read {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .status-not_read {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .status-season {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .leaflet-routing-container {
            position: fixed !important;
            top: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 3000 !important;
            max-height: 50vh;
            max-width: 90vw;
            width: 400px;
            overflow-y: auto;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            margin: 0 !important;
            font-family: 'Inter', sans-serif !important;
            background-color: white;
        }

        /* Hide routing container on very small screens if needed or adjust */
        @media (max-width: 480px) {
            .leaflet-routing-container {
                width: 90vw;
                max-width: 90vw;
                top: 10px !important;
            }
        }

        @media (max-width: 1023px) {

            /* Make search results larger on mobile */
            #resultsList>div {
                padding: 1rem !important;
                margin-bottom: 0.75rem !important;
            }

            #resultsList h4 {
                font-size: 1.1rem !important;
                margin-bottom: 0.5rem !important;
            }

            #resultsList .text-sm {
                font-size: 1rem !important;
                margin-bottom: 0.5rem !important;
            }

            #resultsList .text-xs {
                font-size: 0.9rem !important;
            }

            /* Make the status badge larger */
            #resultsList .px-2 {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                padding-top: 0.25rem !important;
                padding-bottom: 0.25rem !important;
                font-size: 0.9rem !important;
            }

            /* Ensure the search input is large enough for touch */
            #searchInput {
                height: 3rem !important;
                font-size: 1.1rem !important;
                padding-left: 3rem !important;
            }

            .fa-search {
                left: 1rem !important;
                font-size: 1.2rem !important;
            }

            /* Make the search type dropdown larger */
            #searchType {
                height: 3rem !important;
                font-size: 1rem !important;
                padding-left: 0.75rem !important;
            }
        }



        #addBoxForm input[type="number"]::-webkit-inner-spin-button,
        #addBoxForm input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        #addBoxForm input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">

    <!-- Sidebar -->
    <div
        class="sidebar fixed left-0 top-0 h-full w-80 bg-white shadow-2xl transform transition-transform duration-300 slide-in -translate-x-full lg:translate-x-0">
        <div class="h-full flex flex-col">
            <!-- Header - Made more compact -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 lg:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-bolt text-xl lg:text-2xl"></i>
                        <h1 class="text-lg lg:text-xl font-bold">Electrical Map</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="toggleSidebar" class="lg:hidden hover:bg-white/20 p-1 rounded transition">
                            <i class="fas fa-bars text-white"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Section - Made more compact -->
            <div class="p-3 lg:p-4 border-b">
                <div class="flex space-x-2 items-stretch">
                    <div class="relative flex-1">
                        <input type="text" id="searchInput" placeholder="Search..."
                            class="w-full h-10 lg:h-auto pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-sm">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <select id="searchType"
                        class="w-28 lg:w-36 border border-gray-300 rounded-lg px-2 py-1 lg:py-2 text-xs lg:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="code">Box Code</option>
                        <option value="name">Consumer Name</option>
                        <option value="number">Consumer #</option>
                    </select>
                </div>
            </div>

            <!-- Statistics - Made more compact -->
            <div class="p-3 lg:p-4 border-b">
                <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Stats</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-green-50 p-2 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            <span id="readCount" class="text-lg font-bold text-green-700">0</span>
                        </div>
                        <p class="text-xs text-green-600">Read</p>
                    </div>
                    <div class="bg-red-50 p-2 rounded-lg border border-red-200">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-times-circle text-red-600 text-sm"></i>
                            <span id="not_readCount" class="text-lg font-bold text-red-700">0</span>
                        </div>
                        <p class="text-xs text-red-600">Not Read</p>
                    </div>
                    <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-tools text-amber-600 text-sm"></i>
                            <span id="seasonCount" class="text-lg font-bold text-amber-700">0</span>
                        </div>
                        <p class="text-xs text-amber-600">Season</p>
                    </div>
                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                            <span id="totalCount" class="text-lg font-bold text-blue-700">0</span>
                        </div>
                        <p class="text-xs text-blue-600">Total</p>
                    </div>
                </div>
            </div>

            <!-- Filters - Made more compact -->
            <div class="p-3 lg:p-4 border-b">
                <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Filters</h3>
                <div class="space-y-1">
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded text-sm">
                        <input type="checkbox" id="filterRead" checked
                            class="w-3.5 h-3.5 text-green-600 rounded focus:ring-green-500">
                        <span class="flex items-center space-x-1">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                            <span>Read</span>
                        </span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded text-sm">
                        <input type="checkbox" id="filterNot_read" checked
                            class="w-3.5 h-3.5 text-red-600 rounded focus:ring-red-500">
                        <span class="flex items-center space-x-1">
                            <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                            <span>Not Read</span>
                        </span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded text-sm">
                        <input type="checkbox" id="filterSeason" checked
                            class="w-3.5 h-3.5 text-amber-600 rounded focus:ring-amber-500">
                        <span class="flex items-center space-x-1">
                            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                            <span>Season</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Results List - Takes remaining space -->
            <div class="flex-1 overflow-y-auto p-3 lg:p-4">
                <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Results</h3>
                <div id="resultsList" class="space-y-2">
                    <div class="text-gray-500 text-sm text-center py-4">
                        <i class="fas fa-search text-3xl mb-2 block opacity-30"></i>
                        <div class="text-sm">Search for boxes</div>
                    </div>
                </div>
            </div>

            <!-- Profile Section - Made more compact -->
            <div class="p-2 border-t bg-gray-50">
                @auth
                    <button onclick="openProfileModal()"
                        class="w-full flex items-center justify-center space-x-2 bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition text-sm">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        <span>Profile</span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full text-center bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition text-sm">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuToggle"
        class="lg:hidden fixed top-24 left-4 z-50 bg-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
        <i class="fas fa-bars text-gray-700"></i>
    </button>

    <!-- User Location Button -->
    <button id="locateUser"
        class="fixed bottom-8 right-4 z-50 bg-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-shadow group">
        <i class="fas fa-crosshairs text-blue-600 group-hover:text-blue-700"></i>
    </button>

    @auth
        <!-- Add Box Button -->
        <button onclick="openAddBoxModal()"
            class="fixed top-24 right-4 z-50 bg-blue-600 text-white p-3 rounded-lg shadow-lg hover:bg-blue-700 transition-all hover:scale-110 active:scale-95 group">
            <i class="fas fa-plus text-xl"></i>
        </button>
    @endauth

    <!-- Clear Route Button -->
    <button id="clearRouteBtn" onclick="clearRoute()"
        class="fixed bottom-24 right-4 z-50 bg-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-shadow group hidden text-red-600 hover:text-red-700">
        <i class="fas fa-times-circle"></i>
        <span class="ml-2 font-medium text-sm">Clear Route</span>
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

    <!-- Profile Modal -->
    @auth
        <div id="profileModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[2000] hidden flex items-center justify-center">
            <div
                class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all scale-100">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white relative">
                    <button onclick="closeProfileModal()"
                        class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full backdrop-blur-sm">
                            <i class="fas fa-user text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-blue-100">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Account Status</span>
                            <span
                                class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Active</span>
                        </div>
                        <!-- Logout Form -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center space-x-2 bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-lg hover:bg-red-100 transition">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    <!-- Edit Box Modal -->
    @auth
        <div id="editBoxModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[2000] hidden items-center justify-center">
            <div
                class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all scale-100">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 p-6 text-white relative">
                    <button onclick="closeEditBoxModal()"
                        class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full backdrop-blur-sm">
                            <i class="fas fa-edit text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Edit Box</h2>
                            <p class="text-green-100">Update box details</p>
                        </div>
                    </div>
                </div>

                <form id="editBoxForm" onsubmit="handleBoxUpdate(event)" class="p-6 space-y-4">
                    <input type="hidden" id="editBoxId">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Code -->
                        <div class="space-y-1">
                            <label for="editBoxCode" class="text-sm font-medium text-gray-700">Box Code *</label>
                            <input type="text" id="editBoxCode" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                placeholder="e.g. B-001">
                        </div>

                        <!-- Status -->
                        <div class="space-y-1">
                            <label for="editBoxStatus" class="text-sm font-medium text-gray-700">Status</label>
                            <select id="editBoxStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition">
                                <option value="not_read">Not Read</option>
                                <option value="read">Read</option>
                                <option value="season">Season</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Latitude -->
                        <div class="space-y-1">
                            <label for="editBoxLat" class="text-sm font-medium text-gray-700">Latitude *</label>
                            <input type="number" id="editBoxLat" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                placeholder="42.xxx">
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-1">
                            <label for="editBoxLng" class="text-sm font-medium text-gray-700">Longitude *</label>
                            <input type="number" id="editBoxLng" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                placeholder="19.xxx">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Consumer Name -->
                        <div class="space-y-1">
                            <label for="editBoxConsumerName" class="text-sm font-medium text-gray-700">Consumer Name</label>
                            <input type="text" id="editBoxConsumerName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                placeholder="John Doe">
                        </div>

                        <!-- Consumer Number -->
                        <div class="space-y-1">
                            <label for="editBoxConsumerNumber" class="text-sm font-medium text-gray-700">Consumer
                                Number</label>
                            <input type="text" id="editBoxConsumerNumber"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                placeholder="123456">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 pt-4 border-t">
                        <button type="button" onclick="closeEditBoxModal()"
                            class="flex-1 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-green-700 transition font-medium">
                            <i class="fas fa-save mr-2"></i> Update Box
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth

    <!-- Add Box Modal -->
    @auth
        <div id="addBoxModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[2000] hidden items-center justify-center">
            <div
                class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all scale-100">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white relative">
                    <button onclick="closeAddBoxModal()"
                        class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full backdrop-blur-sm">
                            <i class="fas fa-cube text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Add New Box</h2>
                            <p class="text-blue-100">Enter box details below</p>
                        </div>
                    </div>
                </div>

                <form id="addBoxForm" onsubmit="handleBoxSubmit(event)" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Code -->
                        <div class="space-y-1">
                            <label for="boxCode" class="text-sm font-medium text-gray-700">Box Code *</label>
                            <input type="text" id="boxCode" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="e.g. B-001">
                        </div>

                        <!-- Status -->
                        <div class="space-y-1">
                            <label for="boxStatus" class="text-sm font-medium text-gray-700">Status</label>
                            <select id="boxStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                <option value="not_read" selected>Not Read</option>
                                <option value="read">Read</option>
                                <option value="season">Season</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Latitude -->
                        <div class="space-y-1">
                            <label for="boxLat" class="text-sm font-medium text-gray-700">Latitude *</label>
                            <input type="number" id="boxLat" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="42.xxx">
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-1">
                            <label for="boxLng" class="text-sm font-medium text-gray-700">Longitude *</label>
                            <input type="number" id="boxLng" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="19.xxx">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Consumer Name -->
                        <div class="space-y-1">
                            <label for="boxConsumerName" class="text-sm font-medium text-gray-700">Consumer Name</label>
                            <input type="text" id="boxConsumerName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="John Doe">
                        </div>

                        <!-- Consumer Number -->
                        <div class="space-y-1">
                            <label for="boxConsumerNumber" class="text-sm font-medium text-gray-700">Consumer Number</label>
                            <input type="text" id="boxConsumerNumber"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="123456">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 pt-4 border-t">
                        <button type="button" onclick="useCurrentLocation()"
                            class="flex-1 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition font-medium">
                            <i class="fas fa-location-arrow mr-2"></i> Use My Location
                        </button>
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-save mr-2"></i> Save Box
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth



    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <!-- Leaflet Routing Machine JS -->
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        // Global variables
        let map;
        let markers = L.markerClusterGroup({
            maxClusterRadius: 80,
            disableClusteringAtZoom: 17,
            spiderfyOnMaxZoom: true
        });
        let allBoxes = [];
        let filteredBoxes = [];
        let userLocationMarker = null;
        let userLocationCircle = null;
        let routingControl = null;

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            initializeMap();
            fetchBoxes();
            setupEventListeners();
        });

        // Initialize the map
        function initializeMap() {
            map = L.map('map').setView([42.5, 19.3], 12);

            // Add OpenStreetMap tiles with better styling
            L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiemlqYWQtZGoiLCJhIjoiY21rbWljeXdwMGRodDNkcGhycXZuNmwzcyJ9.vjU3Deyw3qOPG0Wg_RM7bQ', {
                tileSize: 512,
                zoomOffset: -1,
                attribution: '© <a href="https://www.mapbox.com/">Mapbox</a> © OpenStreetMap',
                maxZoom: 22
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
                    ${box.nameOfConsumer ? `
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user text-gray-400 w-5"></i>
                        <span class="text-gray-600">${box.nameOfConsumer}</span>
                    </div>` : ''}
                    ${box.numberOfConsumer ? `
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-hashtag text-gray-400 w-5"></i>
                        <span class="text-gray-600">Number of Consumer: ${box.numberOfConsumer}</span>
                    </div>` : ''}
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
                @auth
                    <div class="mt-3">
                         <button onclick="openEditBoxModal(${JSON.stringify(box).replace(/"/g, '&quot;')})" class="w-full bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition font-medium">
                            <i class="fas fa-edit mr-2"></i> Edit Box
                        </button>
                    </div>
                @endauth
            </div>
        `;
        }

        // Get status CSS class
        function getStatusClass(status) {
            switch (status?.toLowerCase()) {
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

            // User location button
            const locateUserBtn = document.getElementById('locateUser');
            locateUserBtn.addEventListener('click', locateUser);
        }

        // Handle search
        function handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const searchType = document.getElementById('searchType').value;

            if (searchTerm === '') {
                // Show all markers when search is cleared
                filteredBoxes = [...allBoxes];
                updateResultsList(filteredBoxes);

                // Clear all markers and re-add them to show all
                markers.clearLayers();
                addMarkersToMap();
                return;
            }

            // Rest of your existing search logic
            filteredBoxes = allBoxes.filter(box => {
                switch (searchType) {
                    case 'name':
                        return box.nameOfConsumer?.toLowerCase().includes(searchTerm);
                    case 'number':
                        return box.numberOfConsumer?.toString().includes(searchTerm);
                    case 'code':
                    default:
                        return box.code?.toLowerCase().includes(searchTerm);
                }
            });

            updateResultsList(filteredBoxes);
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
            const searchType = document.getElementById('searchType').value;

            if (boxes.length === 0) {
                resultsList.innerHTML = `
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                    No results found
                </div>`;
                return;
            }

            resultsList.innerHTML = boxes.slice(0, 10).map(box => {
                let title, subtitle = '';

                switch (searchType) {
                    case 'name':
                        title = box.nameOfConsumer || 'No name';
                        subtitle = `Box: ${box.code}`;
                        break;
                    case 'number':
                        title = `Consumer #${box.numberOfConsumer || 'N/A'}`;
                        subtitle = `Box: ${box.code}${box.nameOfConsumer ? ` • ${box.nameOfConsumer}` : ''}`;
                        break;
                    case 'code':
                    default:
                        title = box.code;
                        subtitle = box.nameOfConsumer || '';
                }

                return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                     onclick="centerOnBox(${box.latitude}, ${box.longitude})">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-semibold text-gray-800 truncate">${title}</h4>
                        ${getStatusBadge(box.status)}
                    </div>
                    ${subtitle ? `<div class="text-sm text-gray-600 mb-1 truncate">${subtitle}</div>` : ''}
                    <div class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        ${box.latitude.toFixed(4)}, ${box.longitude.toFixed(4)}
                    </div>
                </div>`;
            }).join('');
        }

        // Center map on specific box
        function centerOnBox(lat, lng) {
            const sidebar = document.querySelector('.sidebar');
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
            }

            const targetLatLng = L.latLng(lat, lng);

            // Close any existing popup first
            map.closePopup();

            // First, find the exact marker and store its reference
            let targetMarker = null;
            let closestDistance = Infinity;

            markers.eachLayer(layer => {
                const pos = layer.getLatLng();
                const distance = targetLatLng.distanceTo(pos);

                // Find the closest marker within a reasonable distance (10 meters)
                if (distance < 10 && distance < closestDistance) {
                    closestDistance = distance;
                    targetMarker = layer;
                }
            });

            if (!targetMarker) {
                console.log('No marker found at the given coordinates');
                return;
            }

            // Store the original popup content
            const popupContent = targetMarker.getPopup()?.getContent() || createPopupContent(targetMarker.options.boxData);

            // Create a new popup with the same content
            const popup = L.popup()
                .setLatLng(targetLatLng)
                .setContent(popupContent);

            // Fly to the location
            map.flyTo(targetLatLng, 18, {
                duration: 1,
                easeLinearity: 0.25,
                onEnd: function () {
                    // Small delay to ensure the map has settled
                    setTimeout(() => {
                        // Open the popup directly
                        popup.openOn(map);

                        // Force a small pan to ensure the popup is fully visible
                        setTimeout(() => {
                            map.panBy([0, -50], {
                                duration: 0.3,
                                easeLinearity: 0.25
                            });
                        }, 100);
                    }, 100);
                }
            });
        }


        // Get directions (in-app routing)
        function getDirections(destLat, destLng) {
            // Check if we have user location
            if (!userLocationMarker) {
                showNotification('Please locate yourself first', 'info');
                locateUser();
                // Wait for location to be found (simple approach: retry after a delay or let user click again)
                // For better UX, we could pass a callback to locateUser, but for now we'll just prompt.
                return;
            }

            const userLat = userLocationMarker.getLatLng().lat;
            const userLng = userLocationMarker.getLatLng().lng;

            calculateRoute(userLat, userLng, destLat, destLng);

            // Close popup to see the route better
            map.closePopup();
        }

        // Calculate and display route
        function calculateRoute(startLat, startLng, endLat, endLng) {
            // Clear existing route
            clearRoute();

            // Create new route
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(startLat, startLng),
                    L.latLng(endLat, endLng)
                ],
                routeWhileDragging: false,
                showAlternatives: false,
                fitSelectedRoutes: true,
                lineOptions: {
                    styles: [{ color: '#3b82f6', opacity: 0.7, weight: 5 }]
                },
                createMarker: function () { return null; } // Don't create default markers
            }).addTo(map);

            // Show clear button and hide menu button
            document.getElementById('clearRouteBtn').classList.remove('hidden');
            document.getElementById('mobileMenuToggle').classList.add('hidden');
        }

        // Clear current route
        function clearRoute() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            document.getElementById('clearRouteBtn').classList.add('hidden');
            document.getElementById('mobileMenuToggle').classList.remove('hidden');
        }

        // Locate user on the map
        function locateUser() {
            const locateBtn = document.getElementById('locateUser');

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                showNotification('Geolocation is not supported by your browser', 'error');
                return;
            }

            // Show loading state
            locateBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-600"></i>';
            locateBtn.disabled = true;

            // Get current position
            navigator.geolocation.getCurrentPosition(
                // Success callback
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    // Remove previous user location markers
                    if (userLocationMarker) {
                        map.removeLayer(userLocationMarker);
                    }
                    if (userLocationCircle) {
                        map.removeLayer(userLocationCircle);
                    }

                    // Create user location marker
                    const userIcon = L.divIcon({
                        className: 'user-location-marker',
                        html: '<div class="relative"><div class="absolute inset-0 bg-blue-500 rounded-full animate-ping"></div><div class="relative bg-blue-600 rounded-full w-4 h-4 border-2 border-white shadow-lg"></div></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });

                    userLocationMarker = L.marker([lat, lng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup(`
                        <div class="p-3">
                            <h4 class="font-semibold text-gray-800 mb-2">Your Location</h4>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Accuracy: ±${accuracy.toFixed(0)}m
                            </p>
                        </div>
                    `);

                    // Add accuracy circle
                    userLocationCircle = L.circle([lat, lng], {
                        radius: accuracy,
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1,
                        color: '#3b82f6',
                        weight: 2,
                        opacity: 0.3
                    }).addTo(map);

                    // Center map on user location
                    map.setView([lat, lng], 15);

                    // Open popup
                    userLocationMarker.openPopup();

                    // Reset button
                    locateBtn.innerHTML = '<i class="fas fa-crosshairs text-blue-600 group-hover:text-blue-700"></i>';
                    locateBtn.disabled = false;

                    showNotification('Location found successfully!', 'success');
                },
                // Error callback
                function (error) {
                    // Reset button
                    locateBtn.innerHTML = '<i class="fas fa-crosshairs text-blue-600 group-hover:text-blue-700"></i>';
                    locateBtn.disabled = false;

                    // Handle different error types
                    let errorMessage = 'Unable to retrieve your location';

                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enable location permissions.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                        case error.UNKNOWN_ERROR:
                            errorMessage = 'An unknown error occurred.';
                            break;
                    }

                    showNotification(errorMessage, 'error');
                },
                // Options
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000 // Accept cached position up to 1 minute old
                }
            );
        }

        // Show notification message
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotification = document.querySelector('.location-notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `location-notification fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

            // Set color based on type
            switch (type) {
                case 'success':
                    notification.className += ' bg-green-500 text-white';
                    break;
                case 'error':
                    notification.className += ' bg-red-500 text-white';
                    break;
                default:
                    notification.className += ' bg-blue-500 text-white';
            }

            notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;

            // Add to page
            document.body.appendChild(notification);

            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Profile Modal Functions
        function openProfileModal() {
            document.getElementById('profileModal').classList.remove('hidden');
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('profileModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        // Add Box Modal Functions
        const addBoxModal = document.getElementById('addBoxModal');

        function openAddBoxModal() {
            if (addBoxModal) {
                addBoxModal.classList.remove('hidden');
                addBoxModal.classList.add('flex');
            }
        }

        function closeAddBoxModal() {
            if (addBoxModal) {
                addBoxModal.classList.add('hidden');
                addBoxModal.classList.remove('flex');
                document.getElementById('addBoxForm').reset();
            }
        }

        // Close Add Box modal when clicking outside
        addBoxModal?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeAddBoxModal();
            }
        });

        // Use current location for new box
        function useCurrentLocation() {
            if (!navigator.geolocation) {
                showNotification('Geolocation is not supported by your browser', 'error');
                return;
            }

            showNotification('Getting location...', 'info');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('boxLat').value = position.coords.latitude.toFixed(6);
                    document.getElementById('boxLng').value = position.coords.longitude.toFixed(6);
                    showNotification('Location applied!', 'success');
                },
                (error) => {
                    showNotification('Could not get location', 'error');
                }
            );
        }

        async function handleBoxSubmit(event) {
            event.preventDefault();

            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

            const formData = {
                code: document.getElementById('boxCode').value,
                latitude: document.getElementById('boxLat').value,
                longitude: document.getElementById('boxLng').value,
                nameOfConsumer: document.getElementById('boxConsumerName').value || null,
                numberOfConsumer: document.getElementById('boxConsumerNumber').value || null,
                status: document.getElementById('boxStatus').value
            };

            try {
                const response = await fetch('/api/boxes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token if needed, or if API route is excluded from CSRF
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    showNotification('Box added successfully!', 'success');
                    closeAddBoxModal();

                    // Refresh data
                    await fetchBoxes();

                    // Center on new box
                    if (data.latitude && data.longitude) {
                        map.setView([data.latitude, data.longitude], 18);
                    }
                } else {
                    // Error
                    const errorMessage = data.message || 'Failed to add box';
                    showNotification(errorMessage, 'error');
                    console.error('Server Error:', data);
                }
            } catch (error) {
                console.error('Network Error:', error);
                showNotification('Network error occurred', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }




        // Edit Box Modal Functions
        const editBoxModal = document.getElementById('editBoxModal');

        function openEditBoxModal(box) {
            if (editBoxModal) {
                // Populate form fields
                document.getElementById('editBoxId').value = box.id;
                document.getElementById('editBoxCode').value = box.code;
                document.getElementById('editBoxLat').value = box.latitude;
                document.getElementById('editBoxLng').value = box.longitude;
                document.getElementById('editBoxConsumerName').value = box.nameOfConsumer || '';
                document.getElementById('editBoxConsumerNumber').value = box.numberOfConsumer || '';
                document.getElementById('editBoxStatus').value = box.status?.toLowerCase() || 'not_read';

                editBoxModal.classList.remove('hidden');
                editBoxModal.classList.add('flex');
            }
        }

        function closeEditBoxModal() {
            if (editBoxModal) {
                editBoxModal.classList.add('hidden');
                editBoxModal.classList.remove('flex');
                document.getElementById('editBoxForm').reset();
            }
        }

        // Close Edit Box modal when clicking outside
        editBoxModal?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditBoxModal();
            }
        });

        async function handleBoxUpdate(event) {
            event.preventDefault();

            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';

            const boxId = document.getElementById('editBoxId').value;
            const formData = {
                code: document.getElementById('editBoxCode').value,
                latitude: document.getElementById('editBoxLat').value,
                longitude: document.getElementById('editBoxLng').value,
                nameOfConsumer: document.getElementById('editBoxConsumerName').value || null,
                numberOfConsumer: document.getElementById('editBoxConsumerNumber').value || null,
                status: document.getElementById('editBoxStatus').value
            };

            try {
                const response = await fetch(`/api/boxes/${boxId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification('Box updated successfully!', 'success');
                    closeEditBoxModal();
                    await fetchBoxes(); // Refresh markers
                } else {
                    const errorMessage = data.message || 'Failed to update box';
                    showNotification(errorMessage, 'error');
                    console.error('Server Error:', data);
                }
            } catch (error) {
                console.error('Network Error:', error);
                showNotification('Network error occurred', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }
    </script>

</body>

</html>