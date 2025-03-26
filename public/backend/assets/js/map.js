document.addEventListener("DOMContentLoaded", () => {
  const googleApiKey = "AIzaSyDl7ias7CMBPanjqPisVXwhXXVth21Cl5Y";

  const locations = [
    { name: "Eiffel Tower", latitude: 48.8584, longitude: 2.2945 }, // Paris, France
    { name: "Colosseum", latitude: 41.8902, longitude: 12.4922 }, // Rome, Italy
    { name: "Big Ben", latitude: 51.5007, longitude: -0.1246 }, // London, UK
    { name: "Sagrada Familia", latitude: 41.4036, longitude: 2.1744 }, // Barcelona, Spain
    { name: "Brandenburg Gate", latitude: 52.5163, longitude: 13.3777 }, // Berlin, Germany
    { name: "Acropolis of Athens", latitude: 37.9715, longitude: 23.7266 }, // Athens, Greece
    { name: "Charles Bridge", latitude: 50.0865, longitude: 14.4114 }, // Prague, Czech Republic
    { name: "Rialto Bridge", latitude: 45.438, longitude: 12.3358 }, // Venice, Italy
    { name: "St. Peter's Basilica", latitude: 41.9022, longitude: 12.4539 }, // Vatican City
    { name: "Neuschwanstein Castle", latitude: 47.5576, longitude: 10.7498 }, // Bavaria, Germany
    { name: "Palace of Westminster", latitude: 51.4995, longitude: -0.1248 }, // London, UK
    { name: "Amsterdam Canals", latitude: 52.3731, longitude: 4.8926 }, // Amsterdam, Netherlands
    { name: "Louvre Museum", latitude: 48.8606, longitude: 2.3376 }, // Paris, France
    { name: "Mont Saint-Michel", latitude: 48.636, longitude: -1.5115 }, // Normandy, France
    { name: "Edinburgh Castle", latitude: 55.9486, longitude: -3.1999 }, // Edinburgh, Scotland
    { name: "Leaning Tower of Pisa", latitude: 43.7229, longitude: 10.3966 }, // Pisa, Italy
    { name: "Alhambra", latitude: 37.1761, longitude: -3.5881 }, // Granada, Spain
    { name: "Santorini", latitude: 36.3932, longitude: 25.4615 }, // Santorini, Greece
    { name: "Matterhorn", latitude: 45.9763, longitude: 7.6586 }, // Zermatt, Switzerland
    { name: "Schonbrunn Palace", latitude: 48.1849, longitude: 16.3122 }, // Vienna, Austria
  ];

  //   Custom Icon
  const customIcon = L.icon({
    iconUrl: "./assets/images/locationDot.png",
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });

  // Initialize Map
  const map = L.map("map").setView(
    [locations[0].latitude, locations[0].longitude],
    5
    
  );

 

  // Light Mode Map
  L.tileLayer(`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`, {
    maxZoom: 19,
    attribution: "&copy; The Media Vault",
  }).addTo(map);

  // Dark Mode Map
  L.tileLayer(`https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png`, {
    maxZoom: 19,
    attribution: "&copy; The Media Vault",
  }).addTo(map);

 

  // Function to generate Street View Image URL
  function getStreetViewImage(lat, lng, googleApiKey) {
    return `https://maps.googleapis.com/maps/api/streetview?size=600x300&location=${lat},${lng}&key=${googleApiKey}`;
  }

  // Function to fetch Address using Google Geocoding API
  async function fetchAddress(lat, lng) {
    const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${googleApiKey}`;
    const response = await fetch(geocodeUrl);
    const data = await response.json();
    return data.results[0]?.formatted_address || "Address not found";
  }

  // Loop through Locations Array to Add Markers
  locations.forEach(async (location) => {
    const { name, latitude, longitude } = location;

    // Add Marker to Map
    const marker = L.marker([latitude, longitude], { icon: customIcon }).addTo(
      map
    );

    // Fetch Street View Image and Address
    const imageUrl = getStreetViewImage(latitude, longitude, googleApiKey);
    const address = await fetchAddress(latitude, longitude);

    // Popup Content
    const popupContent = `
            <div class="popup-card">
              <img src="${imageUrl}" class="popup-img" alt="Street View" />
              <div class="popup-text">
              
                <div class="popup-header">
                  <h3 class="popup-header-title">${name}</h3>
                  <p class="popup-header-stars">
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="none">
                      <path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400" />
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="none">
                      <path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400" />
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="none">
                      <path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400" />
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="none">
                      <path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400" />
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="none">
                      <path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400" />
                    </svg>
                  </p>
                </div>

                <div class="popup-mid">
                  <div class="popup-mid-rating">
                    <span><svg class="star" xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none"><path d="M9.58154 0.787901C9.71325 0.382525 10.2867 0.382526 10.4185 0.787902L12.3709 6.79678C12.4298 6.97807 12.5987 7.10081 12.7893 7.10081H19.1074C19.5337 7.10081 19.7109 7.64624 19.3661 7.89678L14.2546 11.6105C14.1004 11.7225 14.0359 11.9211 14.0948 12.1024L16.0472 18.1113C16.1789 18.5167 15.7149 18.8538 15.3701 18.6032L10.2586 14.8895C10.1044 14.7775 9.89559 14.7775 9.74137 14.8895L4.62992 18.6032C4.28508 18.8538 3.82111 18.5167 3.95283 18.1113L5.90523 12.1024C5.96413 11.9211 5.8996 11.7225 5.74539 11.6105L0.633933 7.89678C0.289099 7.64624 0.466321 7.10081 0.892559 7.10081H7.21067C7.40129 7.10081 7.57023 6.97807 7.62913 6.79678L9.58154 0.787901Z" fill="#FFB400"/></svg></span>

                    <p class="popup-mid-rating-text">4.9</p>
                  </div>

                  <ul class="popup-mid-ul">
                    <li><span>86</span> views</li>
                    <li><span>36</span> Downloads</li>
                    <li><span>9</span> Collected</li>
                  </ul>
                </div>


                <div class="popup-footer">
                  <span class="popup-footer-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><g clip-path="url(#clip0_307_13604)"><path d="M22.4039 12.3108C22.3563 12.2141 22.3315 12.1078 22.3315 12C22.3315 11.8922 22.3563 11.7858 22.4039 11.6891L23.299 9.85795C23.7974 8.83838 23.4025 7.62316 22.4001 7.09127L20.5996 6.13596C20.5042 6.08574 20.4217 6.01427 20.3583 5.92707C20.295 5.83987 20.2525 5.73927 20.2342 5.63304L19.8821 3.62544C19.686 2.50767 18.652 1.75655 17.5286 1.9155L15.5105 2.20097C15.4038 2.21639 15.295 2.20709 15.1925 2.17377C15.09 2.14045 14.9965 2.08402 14.9193 2.00883L13.4544 0.591712C12.6387 -0.197377 11.3609 -0.197424 10.5453 0.591712L9.08035 2.00897C9.0031 2.08414 8.90961 2.14057 8.8071 2.17388C8.70459 2.20719 8.5958 2.21651 8.48912 2.20111L6.47102 1.91564C5.3472 1.7566 4.31361 2.50781 4.11753 3.62559L3.76541 5.63309C3.74711 5.73932 3.70464 5.83992 3.64129 5.92712C3.57794 6.01433 3.49539 6.08581 3.40002 6.13606L1.59956 7.09136C0.597101 7.6232 0.202228 8.83852 0.700601 9.8581L1.59567 11.6892C1.64332 11.7859 1.6681 11.8923 1.6681 12.0001C1.6681 12.1079 1.64332 12.2142 1.59567 12.3109L0.700554 14.142C0.202182 15.1616 0.597054 16.3768 1.59952 16.9087L3.39998 17.864C3.49535 17.9142 3.57791 17.9857 3.64127 18.0729C3.70463 18.1601 3.74711 18.2607 3.76541 18.3669L4.11753 20.3745C4.29603 21.3921 5.16856 22.1057 6.17135 22.1056C6.27011 22.1056 6.37033 22.0987 6.47107 22.0844L8.48916 21.799C8.59585 21.7835 8.70465 21.7928 8.80717 21.8261C8.90968 21.8595 9.00317 21.9159 9.08039 21.9911L10.5453 23.4082C10.9532 23.8028 11.4764 24.0001 11.9998 24C12.5231 24 13.0466 23.8027 13.4543 23.4082L14.9193 21.9911C15.0773 21.8382 15.2927 21.7684 15.5105 21.799L17.5286 22.0844C18.6525 22.2434 19.686 21.4923 19.8821 20.3745L20.2342 18.367C20.2525 18.2608 20.295 18.1602 20.3584 18.073C20.4217 17.9857 20.5043 17.9143 20.5996 17.864L22.4001 16.9087C23.4025 16.3769 23.7974 15.1615 23.299 14.142L22.4039 12.3108ZM9.23133 5.77099C10.6306 5.77099 11.7691 6.90944 11.7691 8.30875C11.7691 9.70805 10.6306 10.8465 9.23133 10.8465C7.83203 10.8465 6.69358 9.70805 6.69358 8.30875C6.69358 6.90944 7.83203 5.77099 9.23133 5.77099ZM7.92146 17.057C7.78632 17.1922 7.60919 17.2598 7.43209 17.2598C7.255 17.2598 7.07781 17.1922 6.94272 17.057C6.67244 16.7867 6.67244 16.3485 6.94272 16.0782L16.0781 6.94286C16.3483 6.67258 16.7866 6.67258 17.0569 6.94286C17.3272 7.21314 17.3272 7.65137 17.0569 7.92165L7.92146 17.057ZM14.7682 18.229C13.3689 18.229 12.2304 17.0905 12.2304 15.6912C12.2304 14.2919 13.3689 13.1535 14.7682 13.1535C16.1675 13.1535 17.3059 14.2919 17.3059 15.6912C17.3059 17.0905 16.1675 18.229 14.7682 18.229Z" fill="white"/><path d="M14.768 14.5377C14.1319 14.5377 13.6144 15.0551 13.6144 15.6912C13.6144 16.3272 14.1319 16.8447 14.768 16.8447C15.404 16.8447 15.9215 16.3272 15.9215 15.6912C15.9215 15.0551 15.404 14.5377 14.768 14.5377ZM9.23113 7.15515C8.59509 7.15515 8.07764 7.6726 8.07764 8.30864C8.07764 8.94469 8.59509 9.46219 9.23113 9.46219C9.86717 9.46219 10.3847 8.94473 10.3847 8.30864C10.3846 7.67265 9.86717 7.15515 9.23113 7.15515Z" fill="white"/></g><defs><clipPath id="clip0_307_13604"><rect width="24" height="24" fill="white"/></clipPath></defs></svg></span>
                  
                  <p class='popup-footer-text'>Buy and use photo and videos anywher</p>
                </div>


              </div>
            </div>`;

    // Bind Popup to Marker
    marker.bindPopup(popupContent);

    const icon = document.querySelector(".leaflet-marker-icon");
    icon.setAttribute("src", "./assets/images/locationDot.png");
  });



  async function getCoordinates(locationName) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
      locationName
    )}`;
    
    try {
      const response = await fetch(url);
      const data = await response.json();
      
      if (data && data.length > 0) {
        return {
          latitude: parseFloat(data[0].lat),
          longitude: parseFloat(data[0].lon),
        };
      } else {
        throw new Error("Location not found");
      }
    } catch (error) {
      console.error("Error fetching coordinates:", error.message);
      alert("Unable to find the location. Please try again.");
    }
  }
  
  // For Search by Only Name
  document
    .getElementById("searchButton")
    .addEventListener("click", async () => {
      const locationName = document.getElementById("locationInput").value;

      if (locationName) {
        const coordinates = await getCoordinates(locationName);

        if (coordinates) {
          const { latitude, longitude } = coordinates;

          // Update the map view
          map.setView([latitude, longitude], 10);

          // Add a marker to the new location
          L.marker([latitude, longitude])
            .addTo(map)
            .bindPopup(locationName)
            .openPopup();
        }
      } else {
        alert("Please enter a location name.");
      }
    });

  // Handle user input and update the map
  document.getElementById("advancedSearchButton")?.addEventListener("click", async () => {
    const country = document.getElementById("countryInput").value;
    const city = document.getElementById("cityInput").value;
    const area = document.getElementById("areaInput").value;
    const road = document.getElementById("roadInput").value;
    const zip = document.getElementById("zipInput").value;

    // Combine inputs into a single query string
    const query = `${road} ${area} ${city} ${zip} ${country}`.trim();

    if (query) {
      const coordinates = await getCoordinates(query);

      if (coordinates) {
        const { latitude, longitude, display_name } = coordinates;

        // Update the map view
        map.setView([latitude, longitude], 15);

        // Add a marker to the new location
        L.marker([latitude, longitude]).addTo(map).bindPopup(display_name).openPopup();
      }
    } else {
      alert("Please provide at least one location detail.");
    }
  });
});

// Advanced Search Modal JS
document?.addEventListener("DOMContentLoaded", () => {
  let modal = document.querySelector(".advanced--search--modal");
  let openModal = document.querySelector("#map--filter");
  let closeModal = document.querySelector(".advanced--search--close--button");

  openModal?.addEventListener("click", () => {
    modal.classList.toggle("show");
  });
  closeModal?.addEventListener("click", () => {
    modal.classList.remove("show");
  });
});

// Map Floating Menu JS
document?.addEventListener("DOMContentLoaded", () => {
  let menu = document.querySelector(".maps--floating--option--menu");
  let openMenu = document.querySelector(".maps--floating--option");

  openMenu?.addEventListener("click", () => {
    menu?.classList.toggle("show");
  });
});
