// Optimized Heatmap Application for Large Datasets (20,000+ patients)
class HealthHeatmap {
    constructor() {
        this.map = null;
        this.heatLayers = {}; // Store multiple heat layers by case type
        this.cachedData = null;
        this.isOnline = navigator.onLine;
        this.zoomListenerAttached = false; // Prevent duplicate zoom listeners
        this.renderTimeout = null; // Debounce rendering

        // Performance configuration
        this.config = {
            maxPointsPerLayer: 1000, // Limit points per layer for performance
            samplingRatio: 0.3, // Sample 30% of data when exceeding max
            minPointsForHeatmap: 3, // Show markers if less than this
            clusterThreshold: 100, // Use clustering if more than this per type
            debounceDelay: 300, // ms to wait before re-rendering on zoom
        };

        // Case type color configuration
        this.caseTypeColors = {
            vaccination: "#2E7D32", // Green
            prenatal: "#E91E63", // Pink
            "senior-citizen": "#FF9800", // Orange
            "tb-dots": "#D32F2F", // Red
            "family-planning": "#1976D2", // Blue
        };

        this.init();
    }

    /**
     * Initialize the application
     */
    init() {
        this.initMap();
        this.setupEventListeners();
        this.setupOnlineDetection();
        this.loadCachedData();
        this.loadHeatmapData();
    }

    /**
     * Initialize Leaflet map with optimized settings
     */
    initMap() {
        this.map = L.map("map", {
            preferCanvas: true, // Use Canvas for better performance
            zoomControl: true,
            attributionControl: true,
        }).setView([14.281205011111709, 120.88813802186077], 10);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors",
            maxZoom: 20,
            minZoom: 15,
        }).addTo(this.map);

        L.control.scale().addTo(this.map);

        // Setup single zoom listener with debouncing
        this.setupZoomListener();
    }

    /**
     * Setup debounced zoom listener (single instance)
     */
    setupZoomListener() {
        if (this.zoomListenerAttached) return;

        this.map.on("zoomend", () => {
            // Debounce zoom events to prevent excessive re-renders
            if (this.renderTimeout) {
                clearTimeout(this.renderTimeout);
            }

            this.renderTimeout = setTimeout(() => {
                this.updateHeatmapRadius();
            }, this.config.debounceDelay);
        });

        this.zoomListenerAttached = true;
    }

    /**
     * Update heatmap radius/blur based on zoom (optimized)
     */
    updateHeatmapRadius() {
        const zoom = this.map.getZoom();
        const radius = zoom >= 17 ? 15 : zoom >= 16 ? 12 : 10;
        const blur = zoom >= 17 ? 6 : zoom >= 16 ? 4 : 3;

        // Update all existing layers
        Object.values(this.heatLayers).forEach((layer) => {
            layer.setOptions({
                radius: radius,
                blur: blur,
            });
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        const purokFilter = document.getElementById("purok-filter");
        const caseTypeFilter = document.getElementById("case-type-filter");
        const refreshBtn = document.getElementById("refresh-btn");

        if (purokFilter) {
            purokFilter.addEventListener("change", () =>
                this.loadHeatmapData(true),
            );
        }

        if (caseTypeFilter) {
            caseTypeFilter.addEventListener("change", () =>
                this.loadHeatmapData(true),
            );
        }

        if (refreshBtn) {
            refreshBtn.addEventListener("click", () =>
                this.loadHeatmapData(true),
            );
        }
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

        this.updateOnlineStatus(this.isOnline);
    }

    /**
     * Update online status indicator
     */
    updateOnlineStatus(isOnline) {
        const indicator = document.getElementById("status-indicator");
        const statusText = document.getElementById("status-text");

        if (indicator && statusText) {
            if (isOnline) {
                indicator.classList.remove("offline");
                statusText.textContent = "Online";
            } else {
                indicator.classList.add("offline");
                statusText.textContent = "Offline";
            }
        }
    }

    /**
     * Load heatmap data from server
     */
    async loadHeatmapData(forceRefresh = false) {
        const purokElement = document.getElementById("purok-filter");
        const caseTypeElement = document.getElementById("case-type-filter");

        if (!purokElement || !caseTypeElement) return;

        const purok = purokElement.value;
        const caseType = caseTypeElement.value;

        // Use cached data if offline
        if (!this.isOnline && !forceRefresh) {
            console.log("Loading from cache");
            this.loadCachedData();
            return;
        }

        this.showLoading(true);

        try {
            const url = `/api/heatmap-data?purok=${encodeURIComponent(
                purok,
            )}&case_type=${encodeURIComponent(caseType)}&_=${Date.now()}`;

            const response = await fetch(url, {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    )?.content,
                },
            });

            if (!response.ok) {
                throw new Error("Failed to fetch data");
            }

            const result = await response.json();

            if (result.success) {
                console.log(
                    `Loaded ${result.data.length} data points for ${caseType}`,
                );

                // Update map
                this.updateHeatmap(result.data, result.center, caseType);

                // Update statistics
                this.updateStatistics(result.stats);

                // Cache data
                this.cacheData(result, purok, caseType);

                // Update filter display
                this.updateFilterDisplay(purok, caseType);
            }
        } catch (error) {
            console.error("Error loading heatmap data:", error);
            this.loadCachedData();
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Update heatmap with optimized rendering
     */
    updateHeatmap(data, center, selectedCaseType) {
        console.time("heatmap-render");

        // Clear existing layers
        this.clearHeatLayers();

        if (this.markerGroup) {
            this.map.removeLayer(this.markerGroup);
            this.markerGroup = null;
        }

        // Handle small datasets with markers
        if (data.length < this.config.minPointsForHeatmap) {
            this.showMarkers(data);
            if (center) {
                this.map.setView([center.lat, center.lng], center.zoom || 15);
            }
            console.timeEnd("heatmap-render");
            return;
        }

        // Group data by case type
        const groupedData = this.groupDataByCaseType(data);

        // Render layers
        if (selectedCaseType !== "all") {
            // Single case type
            const caseData = groupedData[selectedCaseType] || [];
            this.createOptimizedHeatLayer(caseData, selectedCaseType);
        } else {
            // All case types - render each separately
            Object.keys(groupedData).forEach((caseType) => {
                this.createOptimizedHeatLayer(groupedData[caseType], caseType);
            });
        }

        // Set map view
        if (center) {
            this.map.setView([center.lat, center.lng], center.zoom || 14);
        }

        console.timeEnd("heatmap-render");
    }

    /**
     * Create optimized heat layer with data sampling
     */
    createOptimizedHeatLayer(data, caseType) {
        if (!data || data.length === 0) return;

        // Sample data if exceeds threshold
        let processedData = data;
        if (data.length > this.config.maxPointsPerLayer) {
            processedData = this.sampleData(
                data,
                this.config.maxPointsPerLayer,
            );
            console.log(
                `Sampled ${caseType}: ${data.length} → ${processedData.length} points`,
            );
        }

        const baseColor = this.caseTypeColors[caseType] || "#808080";
        const rgb = this.hexToRgb(baseColor);

        // Calculate point density for intensity
        const densityMap = this.calculateDensity(processedData);

        // Create heat data with intensity
        const heatData = processedData.map((point) => {
            const key = `${point.lat.toFixed(5)}_${point.lng.toFixed(5)}`; // Reduce precision for grouping
            const density = densityMap[key] || 1;

            // Calculate intensity based on density
            let intensity = 0.7;
            if (density > 20) intensity = 1.0;
            else if (density > 10) intensity = 0.9;
            else if (density > 5) intensity = 0.8;

            return [point.lat, point.lng, intensity];
        });

        // Determine optimal radius and blur
        const zoom = this.map.getZoom();
        const radius = zoom >= 17 ? 15 : zoom >= 16 ? 12 : 10;
        const blur = zoom >= 17 ? 6 : zoom >= 16 ? 4 : 3;

        // Create gradient
        const gradient = {
            0.0: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0)`,
            0.3: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.7)`,
            0.5: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.85)`,
            0.7: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.95)`,
            1.0: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 1.0)`,
        };

        // Create heat layer
        const heatLayer = L.heatLayer(heatData, {
            radius: radius,
            blur: blur,
            maxZoom: 18,
            minZoom: 15,
            max: 1.0,
            minOpacity: 0.5,
            gradient: gradient,
        }).addTo(this.map);

        // Store reference
        this.heatLayers[caseType] = heatLayer;
    }

    /**
     * Calculate density map for points
     */
    calculateDensity(data) {
        const densityMap = {};

        data.forEach((point) => {
            const key = `${point.lat.toFixed(5)}_${point.lng.toFixed(5)}`;
            densityMap[key] = (densityMap[key] || 0) + 1;
        });

        return densityMap;
    }

    /**
     * Sample data points for performance
     * Uses stratified sampling to maintain distribution
     */
    sampleData(data, maxPoints) {
        if (data.length <= maxPoints) return data;

        // Use systematic sampling for even distribution
        const step = data.length / maxPoints;
        const sampled = [];

        for (let i = 0; i < data.length; i += step) {
            sampled.push(data[Math.floor(i)]);
        }

        return sampled.slice(0, maxPoints);
    }

    /**
     * Group data points by case type
     */
    groupDataByCaseType(data) {
        const grouped = {};

        data.forEach((point) => {
            const caseType = point.case_type;
            if (!grouped[caseType]) {
                grouped[caseType] = [];
            }
            grouped[caseType].push(point);
        });

        return grouped;
    }

    /**
     * Clear all heat layers efficiently
     */
    clearHeatLayers() {
        Object.values(this.heatLayers).forEach((layer) => {
            if (this.map.hasLayer(layer)) {
                this.map.removeLayer(layer);
            }
        });
        this.heatLayers = {};
    }

    /**
     * Convert hex color to RGB
     */
    hexToRgb(hex) {
        hex = hex.replace("#", "");
        return {
            r: parseInt(hex.substring(0, 2), 16),
            g: parseInt(hex.substring(2, 4), 16),
            b: parseInt(hex.substring(4, 6), 16),
        };
    }

    /**
     * Show markers for small datasets
     */
    showMarkers(data) {
        if (this.markerGroup) {
            this.map.removeLayer(this.markerGroup);
        }

        this.markerGroup = L.layerGroup().addTo(this.map);

        data.forEach((point) => {
            const color = this.caseTypeColors[point.case_type] || "#808080";

            const marker = L.circleMarker([point.lat, point.lng], {
                radius: 8,
                fillColor: color,
                color: "#fff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8,
            }).addTo(this.markerGroup);

            const caseTypeDisplay = point.case_type
                .replace(/-/g, " ")
                .replace(/\b\w/g, (l) => l.toUpperCase());

            marker.bindPopup(`
                <strong>${point.purok}</strong><br>
                ${caseTypeDisplay}<br>
                Patient Location
            `);
        });
    }

    /**
     * Update statistics display
     */
    updateStatistics(stats) {
        const totalElement = document.getElementById("total-patients");
        if (totalElement) {
            totalElement.textContent = stats.total || 0;
        }

        if (stats.by_case_type) {
            console.log("Statistics by case type:", stats.by_case_type);
        }
    }

    /**
     * Update filter display text
     */
    updateFilterDisplay(purok, caseType) {
        const currentFilterElement = document.getElementById("current-filter");
        const statusIndicator = document.getElementById("status-indicator");
        const statusText = document.getElementById("status-text");

        if (!currentFilterElement) return;

        const purokText = purok === "all" ? "All Areas" : purok;
        const caseTypeText =
            caseType === "all"
                ? "All Types"
                : caseType
                      .replace(/-/g, " ")
                      .replace(/\b\w/g, (l) => l.toUpperCase());

        currentFilterElement.textContent = `${purokText}`;
        statusText.textContent = caseTypeText;
        statusIndicator.style.backgroundColor = caseType == 'all'? '' :this.caseTypeColors[caseType];
    }

    /**
     * Cache data to localStorage with compression
     */
    cacheData(data, purok, caseType) {
        try {
            const cacheData = {
                data: data,
                timestamp: new Date().toISOString(),
                filters: { purok, caseType },
            };

            const cacheKey = `heatmap_cache_${purok}_${caseType}`;

            // Limit cache size - only cache if reasonable
            const dataStr = JSON.stringify(cacheData);
            if (dataStr.length < 5 * 1024 * 1024) {
                // < 5MB
                localStorage.setItem(cacheKey, dataStr);
                this.cachedData = cacheData;
            } else {
                console.warn("Data too large to cache:", dataStr.length);
            }
        } catch (error) {
            console.error("Error caching data:", error);
            // Clear old caches if quota exceeded
            if (error.name === "QuotaExceededError") {
                this.clearOldCaches();
            }
        }
    }

    /**
     * Clear old cache entries
     */
    clearOldCaches() {
        const keys = Object.keys(localStorage);
        keys.forEach((key) => {
            if (key.startsWith("heatmap_cache_")) {
                localStorage.removeItem(key);
            }
        });
    }

    /**
     * Load cached data from localStorage
     */
    loadCachedData() {
        try {
            const purokElement = document.getElementById("purok-filter");
            const caseTypeElement = document.getElementById("case-type-filter");

            if (!purokElement || !caseTypeElement) return;

            const currentPurok = purokElement.value;
            const currentCaseType = caseTypeElement.value;

            const cacheKey = `heatmap_cache_${currentPurok}_${currentCaseType}`;
            const cached = localStorage.getItem(cacheKey);

            if (cached) {
                const cacheData = JSON.parse(cached);
                this.cachedData = cacheData;

                this.updateHeatmap(
                    cacheData.data.data,
                    cacheData.data.center,
                    currentCaseType,
                );
                this.updateStatistics(cacheData.data.stats);
                this.updateFilterDisplay(currentPurok, currentCaseType);

                console.log("Loaded from cache:", cacheData.timestamp);
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
        if (overlay) {
            overlay.style.display = show ? "flex" : "none";
        }
    }
}

// Initialize app when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    // Clear old cache format
    localStorage.removeItem("heatmap_cache");

    // Initialize heatmap
    const heatmap = new HealthHeatmap();

    // Store globally for debugging
    window.heatmap = heatmap;
});

// Map legend interaction
document.addEventListener("click", (e) => {
    const mapElement = e.target.closest(".map-legend");
    if (!mapElement) return;

    const legendContent = document.querySelector(".map-legend-content");
    const iElement = document.querySelector(".map-legend i");

    if (!legendContent || !iElement) return;

    if (iElement.classList.contains("fa-info")) {
        legendContent.classList.add("show");
        iElement.classList.add("icon-rotate");
        iElement.classList.remove("fa-info");
        iElement.classList.add("fa-x");
        legendContent.style.transform = "scale(1)";
    } else if (iElement.classList.contains("fa-x")) {
        legendContent.classList.remove("show");
        iElement.classList.remove("icon-rotate");
        iElement.classList.remove("fa-x");
        iElement.classList.add("fa-info");
    }
});
