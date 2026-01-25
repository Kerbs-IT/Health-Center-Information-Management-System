import jQuery from "jquery";
import momentLib from "moment";

// ✅ Import daterangepicker directly (no dynamic import needed)
import "daterangepicker";
import "daterangepicker/daterangepicker.css";

// Set globals for daterangepicker
if (typeof window !== "undefined") {
    window.jQuery = jQuery;
    window.$ = jQuery;
    window.moment = momentLib;
}

const moment = momentLib;

let patientDistributionDateRange = {
    start: moment().subtract(6, 'months').startOf('month'),
    end: moment().endOf('month'),
};

document.addEventListener("DOMContentLoaded", async (e) => {
    try {
        const response = await fetch("/dashboard/info", {
            headers: {
                accept: "application/json",
            },
        });

        if (!response.ok) {
            console.log("Errors:", response.status);
            return;
        }

        const data = await response.json();

        // initialize the data
        const overallPatientElement = document.getElementById(
            "overall-patient-counts"
        );
        const vaccinationPatientElement =
            document.getElementById("vaccination-count");
        const prenatalPatientElement =
            document.getElementById("prenatal-count");
        const tbDotsPatientElement = document.getElementById("tb-dots-count");
        const seniorCitizenElement = document.getElementById(
            "senior-citizen-count"
        );
        const familyPlanningElement = document.getElementById(
            "family-planning-count"
        );
        // provide the values
        overallPatientElement.innerHTML = data.overallPatients ?? 0;
        vaccinationPatientElement.innerHTML = data.vaccinationCount ?? 0;
        prenatalPatientElement.innerHTML = data.prenatalCount ?? 0;
        tbDotsPatientElement.innerHTML = data.tbDotsCount ?? 0;
        seniorCitizenElement.innerHTML = data.seniorCitizenCount ?? 0;
        familyPlanningElement.innerHTML = data.familyPlanningCount ?? 0;

        // console.log(data.baseData);
    } catch (error) {
        console.error("Error");
    }
});

initDateRangePicker()
function initDateRangePicker() {
    const pickerConfig = {
        opens: "left",
        drops: "down",
        showDropdowns: true,
        autoApply: false,
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
    // initiliaze
    reloadDistribution();

    const patientDistributionDate = $("#patientDistributionDateRange");
    if (patientDistributionDate.length) {
        patientDistributionDate.daterangepicker(
            {
                ...pickerConfig,
                startDate: patientDistributionDateRange.start,
                endDate: patientDistributionDateRange.end,
            },
            function (start, end, label) {
                patientDistributionDateRange = { start, end };
                reloadDistribution();
            },
        );
    } else {
        console.warn("⚠ Bar chart date range input not found");
    }

}

async function reloadDistribution() {
    
    try {
        const startDate = patientDistributionDateRange.start.format('YYYY-MM-DD');
        const endDate = patientDistributionDateRange.end.format("YYYY-MM-DD");
        const response = await fetch(
            `/health-worker/area-patient-distribution?startDate=${startDate}&endDate=${endDate}`,
            {
                headers: {
                    accept: "Application/json",
                },
            },
        );

        const data = await response.json();

        if (data.data.assigned_area) {
            document.getElementById(
                "dashboard-healthworker-assigned-area",
            ).textContent = data.data.assigned_area;
        }

        // Update each service type count
        Object.entries(data.data).forEach(([key, value]) => {
            if (key !== "assigned_area") {
                const element = document.querySelector(
                    `.patient-distribution-item.${key}`,
                );
                if (element) {
                    const h5 = element.querySelector("h5");
                    if (h5) {
                        h5.textContent = value;
                    }
                }
            }
        });

    } catch (error) {
        console.log("Error in fetching record:", error);
    }
}
