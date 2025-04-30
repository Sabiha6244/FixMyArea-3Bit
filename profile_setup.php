<?php session_start();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile Setup</title>
    <link rel="stylesheet" href="assets/style/style.css">
    <style>
        body {
            margin: 0;
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background-color: #1e1e1e;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .logo img {
            width: 140px;
        }

        .sidebar nav a {
            display: block;
            padding: 12px 15px;
            color: #bbb;
            text-decoration: none;
            border-radius: 6px;
            margin: 8px 0;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background-color: #27ae60;
            color: white;
        }

        .sidebar .bottom-text {
            text-align: center;
            font-size: 0.85em;
            color: #777;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #181818;
        }

        .profile-section {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .profile-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #27ae60;
        }

        .profile-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 220px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #ccc;
        }

        .form-group select,
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s, background-color 0.3s;
        }

        .form-group select:focus,
        .form-group input[type="text"]:focus,
        .form-group input[type="file"]:focus {
            border-color: #27ae60;
            outline: none;
        }

        .profile-form input[type="submit"] {
            background-color: #27ae60;
            color: white;
            font-size: 1.1em;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            align-self: flex-start;
            transition: background 0.3s;
        }

        .profile-form input[type="submit"]:hover {
            background-color: #219150;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar">
            <div>
                <div class="logo">
                    <img src="assets/images/logo.png" alt="FixMyArea Logo">
                </div>
                <nav>
                    <a href="issues.php">Issues</a>
                    <a href="profile_setup.php" class="active">Profile Setup</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </div>
            <div class="bottom-text">© FixMyArea 2025</div>
        </div>

        <div class="main-content">
            <div class="profile-section">
                <h2>Profile Setup</h2>
                <form class="profile-form" action="save_profile.php" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="division">Division</label>
                            <select id="division" name="division" required>
                                <option value="">Select Division</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="district">District</label>
                            <select id="district" name="district" required>
                                <option value="">Select District</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="city">City Corporation</label>
                            <select id="city" name="city">
                                <option value="">Select City Corporation</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="upazila">Upazila</label>
                            <select id="upazila" name="upazila">
                                <option value="">Select Upazila</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="postcode">Postcode</label>
                                <select id="postcode" name="postcode" required>
                                    <option value="">Select Postcode</option>
                                </select>

                            </div>


                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                        </div>

                        <input type="submit" value="Save Profile">
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript remains the same -->
    <script>
        const divisionSelect = document.getElementById('division');
        const districtSelect = document.getElementById('district');
        const citySelect = document.getElementById('city');
        const upazilaSelect = document.getElementById('upazila');
        const postcodeSelect = document.getElementById('postcode');

        let divisions = [],
            districts = [],
            cities = [],
            upazilas = [],
            postcodes = [];

        async function loadGeoData() {
            try {
                const [divData, distData, cityData, upazilaData, postcodeData] = await Promise.all([
                    fetch('includes/bangladesh_geojson/bd-divisions.json').then(res => res.json()),
                    fetch('includes/bangladesh_geojson/bd-districts.json').then(res => res.json()),
                    fetch('includes/bangladesh_geojson/dhaka-city.json').then(res => res.json()),
                    fetch('includes/bangladesh_geojson/bd-upazilas.json').then(res => res.json()),
                    fetch('includes/bangladesh_geojson/bd-postcodes.json').then(res => res.json())
                ]);

                divisions = divData.divisions;
                districts = distData.districts;
                cities = cityData.dhaka;
                upazilas = upazilaData.upazilas;
                postcodes = postcodeData.postcodes;

                divisions.forEach(div => {
                    const opt = document.createElement('option');
                    opt.value = div.id;
                    opt.textContent = div.name;
                    divisionSelect.appendChild(opt);
                });
            } catch (error) {
                console.error("Error loading geo data:", error);
            }
        }

        divisionSelect.addEventListener('change', () => {
            const divisionId = divisionSelect.value;

            districtSelect.innerHTML = '<option value="">Select District</option>';
            citySelect.innerHTML = '<option value="">Select City Corporation</option>';
            upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
            postcodeSelect.innerHTML = '<option value="">Select Postcode</option>';

            const filteredDistricts = districts.filter(d => d.division_id == divisionId);
            filteredDistricts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist.id;
                opt.textContent = dist.name;

                districtSelect.appendChild(opt);
            });
        });

        districtSelect.addEventListener('change', () => {
            const districtId = districtSelect.value;

            citySelect.innerHTML = '<option value="">Select City Corporation</option>';
            upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
            postcodeSelect.innerHTML = '<option value="">Select Postcode</option>';

            const filteredCities = cities.filter(c => c.district_id == districtId);
            filteredCities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city.name;
                opt.textContent = `${city.city_corporation} - ${city.name}`;
                citySelect.appendChild(opt);
            });

            const filteredUpazilas = upazilas.filter(u => u.district_id == districtId);
            filteredUpazilas.forEach(upz => {
                const opt = document.createElement('option');
                opt.value = upz.name;
                opt.textContent = upz.name;
                upazilaSelect.appendChild(opt);
            });

            const filteredPostcodes = postcodes.filter(p => p.district_id == districtId);
            filteredPostcodes.forEach(pc => {
                const opt = document.createElement('option');
                opt.value = pc.postCode;
                opt.textContent = `${pc.postCode} - ${pc.postOffice}`;
                postcodeSelect.appendChild(opt);
            });
        });

        loadGeoData();
    </script>
</body>

</html>