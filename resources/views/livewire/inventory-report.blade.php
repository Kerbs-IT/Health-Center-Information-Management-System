<div class="min-vh-100">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8 mx-2 mt-2 shadow p-1">
        <div class="bg-white shadow rounded-sm p-3 border-1 border-pink-500">
            <div class="flex align-items-center justify-around">
                <h1 class="text-7xl">126</h1>
            <i class="bi bi-prescription text-7xl text-green-900 bg-green-500/75 p-3 rounded-xl"> </i>
            </div>
            <h5 class="text-center mt-3 fs-4">Total Medicines</h5>
        </div>
        <div class="bg-white shadow rounded-sm p-3 border-1 border-pink-500">
            <div class="flex align-items-center justify-around">
                <h1 class="text-7xl">126</h1>
            <i class="bi bi-prescription text-7xl text-green-900 bg-green-500/75 p-3 rounded-xl"> </i>
            </div>
            <h5 class="text-center mt-3 fs-4">Total Vaccines</h5>
        </div>
        <div class="bg-white shadow rounded-sm p-3 border-1 border-pink-500">
            <div class="flex align-items-center justify-around">
                <h1 class="text-7xl">126</h1>
            <i class="bi bi-prescription text-7xl text-green-900 bg-green-500/75 p-3 rounded-xl"> </i>
            </div>
            <h5 class="text-center mt-3 fs-4">Total Vaccines</h5>
        </div>
        <div class="bg-white shadow rounded-sm p-3 border-1 border-pink-500">
            <div class="flex align-items-center justify-around">
                <h1 class="text-7xl">126</h1>
            <i class="bi bi-prescription text-7xl text-green-900 bg-green-500/75 p-3 rounded-xl"> </i>
            </div>
            <h5 class="text-center mt-3 fs-4">Low Stock</h5>
        </div>
        <div class="bg-white shadow rounded-sm p-3 border-1 border-pink-500">
            <div class="flex align-items-center justify-around">
                <h1 class="text-7xl">126</h1>
            <i class="bi bi-prescription text-7xl text-green-900 bg-green-500/75 p-3 rounded-xl"> </i>
            </div>
            <h5 class="text-center mt-3 fs-4">Expiring Soon</h5>
        </div>
    </div>
    <!-- ANALYTICS SECTION -->
    <div class="mt-3 px-3">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Visual Analytics</h2>

        <!-- GRID FOR 2 CHARTS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-3">

            <!-- MEDICINES VS VACCINES BAR CHART -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold mb-3 text-pink-700">Medicine vs Vaccine Count</h3>
                <canvas id="barChart"></canvas>
            </div>

            <!-- MONTHLY USAGE LINE CHART -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold mb-3 text-pink-700">Monthly Consumption Trend</h3>
                <canvas id="lineChart"></canvas>
            </div>

        </div>

        <!-- NEXT GRID SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">

            <!-- STOCK DISTRIBUTION PIE CHART -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold mb-3 text-pink-700">Stock Level Distribution</h3>
                <canvas id="pieChart"></canvas>
            </div>

            <!-- EXPIRING VS NOT EXPIRING DOUGHNUT -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold mb-3 text-pink-700">Expiring Items Overview</h3>
                <canvas id="doughnutChart"></canvas>
            </div>

        </div>
    </div>

</div>
