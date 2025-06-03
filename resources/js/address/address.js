function resetDropdown(select, defaultText) {
  select.innerHTML = `<option value="">${defaultText}</option>`;
  select.disabled = true;
}

document.addEventListener('DOMContentLoaded', () =>{
    document.getElementById('region').addEventListener('change', function () {
        // console.log('working', this.value);
        let regionCode = this.value;
        let province = document.getElementById('province');
        let city = document.getElementById('city');
        let barangay = document.getElementById('brgy');

        resetDropdown(province, 'Select Province');
        resetDropdown(city, 'Select City');
        resetDropdown(barangay, 'Select Barangay');

        if (!regionCode) return;

        
        fetch(`/get-provinces/${regionCode}`)
            .then(res => res.json())
            .then(data => {
                // console.log('working2');
            data.forEach(p => {
                province.innerHTML += `<option value="${p.code}">${p.name}</option>`;
            });
            province.disabled = false;
            });
    });
    // city
    document.getElementById('province').addEventListener('change', function () {
        let provinceCode = this.value;
        let city = document.getElementById('city');
        let barangay = document.getElementById('brgy');

        resetDropdown(city, 'Select City');
        resetDropdown(barangay, 'Select Barangay');

        if (!provinceCode) return;

        fetch(`/get-cities/${provinceCode}`)
            .then(res => res.json())
            .then(data => {
            data.forEach(c => {
                city.innerHTML += `<option value="${c.code}">${c.name}</option>`;
            });
            city.disabled = false;
            });
    });
    // brgy
    document.getElementById('city').addEventListener('change', function () {
    let cityCode = this.value;
    let barangay = document.getElementById('brgy');

    resetDropdown(barangay, 'Select Barangay');

    if (!cityCode) return;

    fetch(`/get-brgy/${cityCode}`)      
        .then(res => res.json())
        .then(data => {
        data.forEach(b => {
            barangay.innerHTML += `<option value="${b.code}">${b.name}</option>`;
        });
        barangay.disabled = false;
        });
    });
});
