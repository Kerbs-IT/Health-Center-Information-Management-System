// ✅ Import daterangepicker directly (no dynamic import needed)
import "daterangepicker";
import "daterangepicker/daterangepicker.css";
import momentLib from "moment";

// Set globals for daterangepicker
if (typeof window !== "undefined") {
    window.jQuery = jQuery;
    window.$ = jQuery;
    window.moment = momentLib;
}

const moment = momentLib;

// Livewire Records Date Range state
let recordsDateRange = {
    start: moment().subtract(6, "months").startOf("month"),
    end: moment(),
};
// Initialize date range pickers
function initDateRangePickers() {
    const pickerConfig = {
        opens: "left",
        drops: "down",
        showDropdowns: true,
        autoApply: true, // Auto-apply for instant updates
        showCustomRangeLabel: true,
        alwaysShowCalendars: true,
        locale: {
            format: "MMM D, YYYY",
            separator: " - ",
            applyLabel: "Apply",
            cancelLabel: "Cancel",
            fromLabel: "From",
            toLabel: "To",
            customRangeLabel: "Custom Range",
            weekLabel: "W",
            daysOfWeek: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            monthNames: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            firstDay: 0,
        },
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [
                moment().subtract(1, "days"),
                moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
            "Last 3 Months": [
                moment().subtract(3, "months").startOf("month"),
                moment().endOf("month"),
            ],
            "Last 6 Months": [
                moment().subtract(6, "months").startOf("month"),
                moment().endOf("month"),
            ],
            "This Year": [moment().startOf("year"), moment().endOf("year")],
            "Last Year": [
                moment().subtract(1, "year").startOf("year"),
                moment().subtract(1, "year").endOf("year"),
            ],
        },
    };

    // Records Date Range
    const recordsRangeInput = $("#dateRange");
    if (recordsRangeInput.length) {
        recordsRangeInput.daterangepicker(
            {
                ...pickerConfig,
                startDate: recordsDateRange.start,
                endDate: recordsDateRange.end,
            },
            function (start, end, label) {
                recordsDateRange = { start, end };

                // Format dates for Livewire (YYYY-MM-DD)
                const startDate = start.format("YYYY-MM-DD");
                const endDate = end.format("YYYY-MM-DD");

                // Dispatch event to Livewire component
                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("dateRangeChanged", [startDate, endDate]);
                    // console.log("✅ Event dispatched successfully"); // Add this
                } else {
                    console.error("❌ Livewire not found!"); // Add this
                }

                // console.log(
                //     "📅 Records date range updated:",
                //     startDate,
                //     "to",
                //     endDate,
                // );
            },
        );

        // console.log("✅ Records date range picker initialized");
    } else {
        console.warn("⚠ Records date range input not found");
    }
}

// Initialize on Livewire load
document.addEventListener("livewire:initialized", () => {
    // console.log("🔥 Livewire initialized - setting up date pickers");
    initDateRangePickers();
});

// Reinitialize after Livewire updates (if needed)
document.addEventListener("livewire:update", () => {
    // console.log("OWKINGGG KABAAAA");
    const recordsRangeInput = $("#dateRange");
    if (
        recordsRangeInput.length &&
        !recordsRangeInput.data("daterangepicker")
    ) {
        // console.log(
        //     "🔄 Reinitializing records date picker after Livewire update",
        // );
        initDateRangePickers();
    }
});

const dateRange = document.getElementById("dateRange");
