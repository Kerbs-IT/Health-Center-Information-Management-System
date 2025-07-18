function resetDropdown(select, defaultText) {
  select.innerHTML = `<option value="" dissabled >${defaultText}</option>`;
    select.disabled = true;
}

document.addEventListener('DOMContentLoaded', () =>{
    document.getElementById('region').addEventListener('change', function () {
        // console.log('working', this.value);
        let regionCode = this.value;
        let province = document.getElementById('province');
        let city = document.getElementById('city');
        let barangay = document.getElementById('brgy');

        let selectedProvinceId = document.getElementById('province').dataset.selected;

        resetDropdown(province, 'Select Province');
        resetDropdown(city, 'Select City');
        resetDropdown(barangay, 'Select Barangay');

        if (!regionCode) return;

        
        fetch(`/get-provinces/${regionCode}`)
            .then(res => res.json())
            .then(data => {
                // console.log('working2');
            data.forEach(p => {
                let selected = (p.code === selectedProvinceId) ? 'selected' : '';
                province.innerHTML += `<option value="${p.code}"  ${selected} >${p.name}</option>`;
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
                city.innerHTML += `<option value="${c.code}" >${c.name}</option>`;
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

// display all the province,cities, brgy
let province = document.getElementById('province');
let city = document.getElementById('city');
let barangay = document.getElementById('brgy');
let region = document.getElementById('region');
let regionCode = region.value;

export async function loadAddress(province, city, barangay, region, regionCode) {
    const selectedProvinceId = province.dataset.selected;
    const selectedCityId = city.dataset.selected;
    const selectedBrgyId = barangay.dataset.selected;
    const selectedRegionId = region.dataset.selected;

    // Clear existing options first
    region.innerHTML = '<option hidden dissabled value="">Select Region</option>';
    province.innerHTML = '<option hidden dissabled value="" >Select Province</option>';
    city.innerHTML = '<option hidden dissabled value="" >Select City/Municipality</option>';
    barangay.innerHTML = '<option hidden dissabled  value="">Select Barangay</option>';

    try {
        // Fetch all region options
        const regionRes = await fetch('/get-regions');
        const regionData = await regionRes.json();

        regionData.region.forEach(r => {
            const selected = r.code == selectedRegionId ? 'selected' : '';
            region.innerHTML += `<option value="${r.code}" ${selected}>${r.name}</option>`;
        });

        // Only load provinces, cities, and barangays if regionCode is provided
        if (regionCode) {
            const [provinceRes, cityRes, brgyRes] = await Promise.all([
                fetch(`/get-provinces/${regionCode}`),
                selectedProvinceId ? fetch(`/get-cities/${selectedProvinceId}`) : Promise.resolve({ json: async () => [] }),
                selectedCityId ? fetch(`/get-brgy/${selectedCityId}`) : Promise.resolve({ json: async () => [] })
            ]);

            const [provinceData, cityData, brgyData] = await Promise.all([
                provinceRes.json(),
                cityRes.json(),
                brgyRes.json()
            ]);

            // Provinces
            provinceData.forEach(p => {
                const selected = p.code == selectedProvinceId ? 'selected' : '';
                province.innerHTML += `<option value="${p.code}" ${selected}>${p.name}</option>`;
            });
            province.disabled = false;

            // Cities
            cityData.forEach(c => {
                const selected = c.code == selectedCityId ? 'selected' : '';
                city.innerHTML += `<option value="${c.code}" ${selected}>${c.name}</option>`;
            });
            city.disabled = false;

            // Barangays
            brgyData.forEach(b => {
                const selected = b.code == selectedBrgyId ? 'selected' : '';
                barangay.innerHTML += `<option value="${b.code}" ${selected}>${b.name}</option>`;
            });
            barangay.disabled = false;
        }

    } catch (err) {
        console.error('Error loading address data:', err);
    }
}

loadAddress(province,city,barangay,region,regionCode);

