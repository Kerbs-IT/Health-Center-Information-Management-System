export default function changeLmp(input, expectedDeliveryDate) {
    const date = new Date(input.value);

    // clasify each item
    let currentYear = date.getFullYear();
    let currentMonth = date.getMonth();
    let currentDay = date.getDate();

    if (currentMonth >= 0 && currentMonth <= 2) {
        currentMonth += 9;
        currentDay += 7;
    } else {
        if (currentMonth >= 3 && currentMonth <= 11) {
            currentMonth -= 3;
            currentDay += 7;
            currentYear += 1;
        }
    }

    let expectedDelivery = new Date(currentYear, currentMonth, currentDay);

    const year = expectedDelivery.getFullYear();
    const month = String(expectedDelivery.getMonth() + 1).padStart(2, "0");
    const day = String(expectedDelivery.getDate()).padStart(2, "0");

    const formattedDate = `${year}-${month}-${day}`;

    expectedDeliveryDate.value = formattedDate;
}
