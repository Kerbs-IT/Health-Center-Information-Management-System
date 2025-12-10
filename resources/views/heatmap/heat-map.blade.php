<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/css/patient/record.css',
    'resources/js/record/record.js',
    'resources/css/heatmap/heat_map.css',
    'resources/js/heatmap/heat-map.js'])
    <div class="d-flex vh-100 heatmap">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            <!-- header part -->
            @include('layout.header')
            <!-- header ends here -->
            <main class=" flex-grow-1 py-2 px-4 basic-info" style="overflow-y: auto; min-height: 0;">
                <div class="heatmap-container">
                    <!-- Filters -->
                    <div class="heatmap-filters">
                        <div class="filter-group">
                            @if(Auth::user()->role == 'nurse')
                            <!-- Nurse: Can select any purok -->
                            <label for="purok-filter">Purok/Subdivision:</label>
                            <select id="purok-filter" class="filter-select">
                                <option value="all">All Puroks</option>
                                @foreach($puroks as $purok)
                                <option value="{{ $purok }}">{{ $purok }}</option>
                                @endforeach
                            </select>
                            @else
                            <!-- Staff: Only see their assigned purok -->
                            <label for="purok-filter">Purok/Subdivision:</label>
                            <!-- <select id="purok-filter" class="filter-select">
                                <option value="{{ $handledBrgy }}" selected>{{ $handledBrgy }}</option>
                            </select> -->
                            <input type="text"  class="filter-select" value="{{$handledBrgy}}" disabled>
                            <input type="hidden" id="purok-filter" class="filter-select" value="{{$handledBrgy}}">
                            @endif
                        </div>

                        <div class="filter-group">
                            <label for="case-type-filter">Patient Type:</label>
                            <select id="case-type-filter" class="filter-select">
                                <option value="all">All Types</option>
                                @foreach($caseTypes as $type)
                                <option value="{{ $type }}">{{ ucwords(str_replace('-', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button id="refresh-btn" class="btn-refresh">
                            <span class="refresh-icon">🔄</span> Refresh
                        </button>
                    </div>

                    <!-- Statistics Panel -->
                    <div class="stats-panel">
                        <div class="stat-item">
                            <span class="stat-label">Total Patients:</span>
                            <span class="stat-value" id="total-patients">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Current View:</span>
                            <span class="stat-value" id="current-filter">All Areas, All Types</span>
                        </div>
                        <div class="stat-item online-status">
                            <span class="status-indicator" id="status-indicator"></span>
                            <span class="stat-label" id="status-text">Active</span>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="map" class="map-container"></div>

                    <!-- Legend -->
                    <!-- Legend -->
                    <div class="map-legend">
                        <h3>Patient Density(Per Purok)</h3>
                        <div class="legend-item">
                            <span class="legend-color" style="background: rgba(0, 255, 0, 0.6);"></span>
                            <span>Low (0-500)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: rgba(255, 255, 0, 0.7);"></span>
                            <span>Moderate (501-1K)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: rgba(255, 165, 0, 0.8);"></span>
                            <span>High (1K-5K)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: rgba(255, 0, 0, 0.9);"></span>
                            <span>Very High (5K-10K)</span>
                        </div>
                    </div>


                    <!-- Loading Overlay -->
                    <div id="loading-overlay" class="loading-overlay" style="display: none;">
                        <div class="loading-spinner"></div>
                        <p>Loading map data...</p>
                    </div>
                </div>
            </main>
        </div>

    </div>


    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet.heat Plugin -->
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>


    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('heatmap');

            if (con) {
                con.classList.add('active');
            }
        })

        function showFileName(input) {
            const fileName = input.files.length ? input.files[0].name : "No file chosen";
            document.getElementById("fileName").textContent = fileName;
        }
    </script>
    @endif
</body>

</html>