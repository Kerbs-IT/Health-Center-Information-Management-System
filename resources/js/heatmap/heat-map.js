// Heatmap Application
class HealthHeatmap {
    constructor() {
        this.map = null;
        this.heatLayer = null;
        this.cachedData = null;
        this.isOnline = navigator.onLine;

        this.init();
    }

    /**
     * Initialize the application
     */
    init() {
        // Initialize map
        this.initMap();

        // Setup event listeners
        this.setupEventListeners();

        // Load initial data
        this.loadHeatmapData();

        // Setup online/offline detection
        this.setupOnlineDetection();

        // Try to load cached data on startup
        this.loadCachedData();
    }

    /**
     * Initialize Leaflet map
     */
    initMap() {
        // Create map centered on Hugo Perez
        this.map = L.map("map").setView(
            [14.281205011111709, 120.88813802186077],
            10
        );

        // Add OpenStreetMap tile layer (free, no API key needed)
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors",
            maxZoom: 20,
            minZoom: 15,
        }).addTo(this.map);

        // Add scale control
        L.control.scale().addTo(this.map);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Purok filter change
        document
            .getElementById("purok-filter")
            .addEventListener("change", () => {
                this.loadHeatmapData();
            });

        // Case type filter change
        document
            .getElementById("case-type-filter")
            .addEventListener("change", () => {
                this.loadHeatmapData();
            });

        // Refresh button
        document.getElementById("refresh-btn").addEventListener("click", () => {
            this.loadHeatmapData(true);
        });
    }

    /**
     * Setup online/offline detection
     */
    setupOnlineDetection() {
        window.addEventListener("online", () => {
            this.isOnline = true;
            this.updateOnlineStatus(true);
            console.log("Connection restored");
        });

        window.addEventListener("offline", () => {
            this.isOnline = false;
            this.updateOnlineStatus(false);
            console.log("Connection lost - using cached data");
            this.loadCachedData();
        });

        // Set initial status
        this.updateOnlineStatus(this.isOnline);
    }

    /**
     * Update online status indicator
     */
    updateOnlineStatus(isOnline) {
        const indicator = document.getElementById("status-indicator");
        const statusText = document.getElementById("status-text");

        if (isOnline) {
            indicator.classList.remove("offline");
            statusText.textContent = "Online";
        } else {
            indicator.classList.add("offline");
            statusText.textContent = "Offline";
        }
    }

    /**
     * Load heatmap data from server
     */
    async loadHeatmapData(forceRefresh = false) {
        // Read current values from dropdowns FIRST
        const purokElement = document.getElementById("purok-filter");
        const purok = purokElement.value;
        const caseType = document.getElementById("case-type-filter").value;

        // console.log("🔍 Loading data for:", {
        //     purok,
        //     caseType,
        //     forceRefresh,
        //     isOnline: this.isOnline,
        // }); // DEBUG

        // If offline and not forcing refresh, use cached data
        if (!this.isOnline && !forceRefresh) {
            console.log("📦 Loading from cache"); // DEBUG
            this.loadCachedData(purok, caseType); // Pass current filters to cache
            return;
        }

        // Show loading
        this.showLoading(true);

        try {
            const url = `/api/heatmap-data?purok=${encodeURIComponent(
                purok
            )}&case_type=${encodeURIComponent(caseType)}&_=${Date.now()}`;
            // console.log("🌐 Fetching:", url); // DEBUG

            const response = await fetch(url, {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            if (!response.ok) {
                throw new Error("Failed to fetch data");
            }

            const result = await response.json();
            // console.log("✅ Data received:", result); // DEBUG

            if (result.success) {
                // Update map
                this.updateHeatmap(result.data, result.center);

                // Update statistics
                this.updateStatistics(result.stats);

                // Cache data for offline use WITH THE FILTER KEYS
                this.cacheData(result, purok, caseType);

                // Update filter display
                this.updateFilterDisplay(purok, caseType);
            }
        } catch (error) {
            console.error("❌ Error loading heatmap data:", error);

            // Try to load cached data for CURRENT filters
            const cacheKey = `heatmap_${purok}_${caseType}`;
            if (this.cachedData && this.cachedData[cacheKey]) {
                console.log("📦 Using cached data due to error");
                this.loadCachedData(purok, caseType);
            } else {
                alert(
                    "Failed to load heatmap data. Please check your connection."
                );
            }
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Update heatmap layer
     */
    updateHeatmap(data, center) {
        if (this.heatLayer) {
            this.map.removeLayer(this.heatLayer);
        }

        if (data.length < 3) {
            this.showMarkers(data);
            if (center) {
                const zoom = center.zoom || 15;
                this.map.setView([center.lat, center.lng], zoom);
            }
            return;
        }

        // Count patients per purok to determine density
        const purokCounts = {};
        data.forEach((point) => {
            purokCounts[point.purok] = (purokCounts[point.purok] || 0) + 1;
        });

        // Assign intensity based on patient count thresholds
        const heatData = data.map((point) => {
            const count = purokCounts[point.purok];
            const intensity = Math.max(0.01, purokCounts[point.purok] / 100);

            // console.log("count:", count);

            // Density thresholds based on actual patient counts
            // Density thresholds for larger scale
            // if (count <= 500) {
            //     intensity = 0.1; // Green - Low (0-500)
            // } else if (count <= 1000) {
            //     intensity = 0.4; // Yellow - Moderate (501-1000)
            // } else if (count <= 5000) {
            //     intensity = 0.7; // Orange - High (1001-5000)
            // } else {
            //     intensity = 1.0; // Red - Very High (5001-10000)
            // }

            return [point.lat, point.lng, intensity];
        });

        const pointCount = data.length;

        const radius = pointCount < 20 ? 12 : 20;
        const blur = pointCount < 20 ? 8 : 15;

        this.heatLayer = L.heatLayer(heatData, {
            radius: radius,
            blur: blur,
            maxZoom: 17,
            max: 1.0,
            minOpacity: 0.3,
            gradient: {
                0.0: "rgba(0, 255, 0, 0.8)", // stronger green
                0.2: "rgba(173, 255, 47, 0.7)", // light green
                0.5: "rgba(255, 255, 0, 0.7)",
                1.0: "rgba(255, 0, 0, 0.9)",
            },
        }).addTo(this.map);

        if (center) {
            const zoom = center.zoom || 14;
            this.map.setView([center.lat, center.lng], zoom);
        }
    }

    /**
     * Show simple markers for low patient counts
     */
    showMarkers(data) {
        // Remove existing markers
        if (this.markerGroup) {
            this.map.removeLayer(this.markerGroup);
        }

        // Create marker group
        this.markerGroup = L.layerGroup().addTo(this.map);

        // Add marker for each patient
        data.forEach((point) => {
            const marker = L.circleMarker([point.lat, point.lng], {
                radius: 8,
                fillColor: "#3498db",
                color: "#fff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.7,
            }).addTo(this.markerGroup);

            // Add popup if needed
            marker.bindPopup(`
            <strong>${point.purok}</strong><br>
            Patient Location
        `);
        });
    }

    /**
     * Calculate intensity for heatmap
     */
    getIntensity(totalCount) {
        // Normalize intensity based on total patient count
        if (totalCount <= 50) return 0.2;
        if (totalCount <= 100) return 0.3;
        if (totalCount <= 150) return 0.4;
        if (totalCount <= 200) return 0.5;
        if (totalCount <= 250) return 0.6;
        if (totalCount <= 300) return 0.7;
        if (totalCount <= 350) return 0.6;
        if (totalCount <= 400) return 0.6;
        if (totalCount <= 450) return 0.6;
        if (totalCount <= 500) return 0.6;
        if (totalCount <= 550) return 0.6;
        if (totalCount <= 600) return 0.6;
        if (totalCount <= 700) return 0.6;
        return 1.0;
    }

    /**
     * Update statistics display
     */
    updateStatistics(stats) {
        document.getElementById("total-patients").textContent =
            stats.total || 0;
    }

    /**
     * Update filter display text
     */
    updateFilterDisplay(purok, caseType) {
        const purokText = purok === "all" ? "All Areas" : purok;
        const caseTypeText =
            caseType === "all"
                ? "All Types"
                : caseType
                      .replace("-", " ")
                      .replace(/\b\w/g, (l) => l.toUpperCase());

        document.getElementById(
            "current-filter"
        ).textContent = `${purokText}, ${caseTypeText}`;
    }

    /**
     * Cache data to localStorage
     */
    cacheData(data) {
        try {
            const purokElement = document.getElementById("purok-filter");
            const cacheData = {
                data: data,
                timestamp: new Date().toISOString(),
                filters: {
                    purok: purokElement.value,
                    caseType: document.getElementById("case-type-filter").value,
                },
            };

            // Use filter-specific cache key
            const cacheKey = `heatmap_cache_${cacheData.filters.purok}_${cacheData.filters.caseType}`;
            localStorage.setItem(cacheKey, JSON.stringify(cacheData));
            this.cachedData = cacheData;

            // console.log("✅ Data cached successfully for:", cacheData.filters);
        } catch (error) {
            console.error("Error caching data:", error);
        }
    }

    loadCachedData() {
        try {
            // Get CURRENT filter values (don't overwrite them!)
            const purokElement = document.getElementById("purok-filter");
            const currentPurok = purokElement.value;
            const currentCaseType =
                document.getElementById("case-type-filter").value;

            // Load cache for CURRENT filters only
            const cacheKey = `heatmap_cache_${currentPurok}_${currentCaseType}`;
            const cached = localStorage.getItem(cacheKey);

            if (cached) {
                const cacheData = JSON.parse(cached);
                this.cachedData = cacheData;

                // Update map with cached data
                this.updateHeatmap(cacheData.data.data, cacheData.data.center);
                this.updateStatistics(cacheData.data.stats);

                // DON'T restore filter values - keep current ones!
                // Just update the display
                this.updateFilterDisplay(currentPurok, currentCaseType);

                // console.log(
                //     "📦 Loaded cached data from:",
                //     cacheData.timestamp,
                //     "for",
                //     currentPurok
                // );
            } else {
                console.log(
                    "❌ No cached data available for:",
                    currentPurok,
                    currentCaseType
                );
            }
        } catch (error) {
            console.error("Error loading cached data:", error);
        }
    }

    /**
     * Show/hide loading overlay
     */
    showLoading(show) {
        const overlay = document.getElementById("loading-overlay");
        overlay.style.display = show ? "flex" : "none";
    }
}

// Initialize app when DOM is ready
// Initialize app when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    // Clear old cache format (single key)
    localStorage.removeItem("heatmap_cache");

    // Initialize your heatmap class
    const heatmap = new HealthHeatmap();

    // Load data after DOM is ready
    heatmap.loadHeatmapData(true);

    // Add event listeners for filter changes
    const purokFilter = document.getElementById("purok-filter");

    if (purokFilter.tagName === "SELECT") {
        purokFilter.addEventListener("change", () => {
            heatmap.loadHeatmapData(true);
        });
    }

    const caseTypeFilter = document.getElementById("case-type-filter");
    if (caseTypeFilter) {
        caseTypeFilter.addEventListener("change", () => {
            heatmap.loadHeatmapData(true);
        });
    }
});