<div class="sidebar">
    <h2>Hospital System</h2>
    <ul>
        <li><a href="/HMS-main/views/nurse/dashboard.php">Dashboard</a></li>

        <li>
            <a href="javascript:void(0);" class="dropdown-btn">Patient Management</a>
            <ul class="dropdown-content">
                <li><a href="/HMS-main/views/nurse/patient.php">My Patients</a></li>
                <li><a href="/HMS-main/views/nurse/inpatient.php">Inpatients</a></li>
                <li><a href="/HMS-main/views/nurse/outpatient.php">Outpatients</a></li>
            </ul>
        </li>

        <li><a href="/HMS-main/views/nurse/department.php">Departments</a></li>
        <li><a href="/HMS-main/views/nurse/doctorschedule.php">Doctor Schedule</a></li>
        <li><a href="/HMS-main/views/nurse/location.php">Location</a></li>
        <li><a href="/HMS-main/views/nurse/emergency.php">Emergency</a></li>
        <li><a href="/HMS-main/auth/logout.php">Logout</a></li>
    </ul>
</div>

<script>
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            const dropdownContent = btn.nextElementSibling;
            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'block';
            }
        });
    });
</script>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #e0f7fa;
        color: #333;
        box-sizing: border-box;
    }
    .sidebar {
        position: fixed;
        width: 175px;
        height: 100vh;
        background-color: #9c335a;
        padding: 20px;
        color: white;
    }
    .sidebar h2 {
        margin-bottom: 20px;
        font-size: 1.5em;
    }
    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }
    .sidebar ul li {
        margin-bottom: 10px;
        position: relative;
    }
    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 8px;
        font-size: 1em;
    }
    .sidebar ul li a:hover {
        background-color: #34495e;
        border-radius: 4px;
    }
    .dropdown-content {
        display: none;
        list-style-type: none;
        padding-left: 10px;
    }
    .dropdown-content li a {
        padding: 6px 8px;
        font-size: 0.85em;
        opacity: 0.85;
        margin-left: 20px;
    }
    .dropdown-btn::after {
        content: " ▼";
        font-size: 0.7em;
    }
    .dropdown-btn.active::after {
        content: " ▲";
    }
    .content {
        margin-left: 220px;
        padding: 20px;
    }
</style>
