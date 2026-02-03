import $ from "jquery";

document.addEventListener("DOMContentLoaded", function () {
    fetch("/showBrgyUnit")
        .then((response) => response.json())
        .then((data) => {
            let dropdown = document.getElementById("brgy");

            // console.log(data);
            data.forEach((item) => {
                let option = document.createElement("option");
                option.value = item.brgy_unit;
                option.text = item.brgy_unit;
                dropdown.appendChild(option);
            });
        });
    
    
});
