<!DOCTYPE html>
<html>
<head>
    <title>Lab Procedures</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Lab Procedures</h2>

    <!-- Add New -->
    <form method="POST" action="/labprocedure/store" class="mb-4">
        <div class="row g-2">
            <div class="col"><input type="number" name="PatientID" class="form-control" placeholder="Patient ID" required></div>
            <div class="col"><input type="number" name="DoctorID" class="form-control" placeholder="Doctor ID" required></div>
            <div class="col"><input type="datetime-local" name="TestDate" class="form-control" required></div>
            <div class="col"><input type="text" name="Result" class="form-control" placeholder="Result" required></div>
            <div class="col"><input type="date" name="DateReleased" class="form-control" required></div>
            <div class="col"><button class="btn btn-primary" type="submit">Add</button></div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Test Date</th><th>Result</th><th>Released</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($procedures as $p): ?>
                <tr>
                    <td><?= $p->LabProcedureID ?></td>
                    <td><?= $p->PatientID ?></td>
                    <td><?= $p->DoctorID ?></td>
                    <td><?= $p->TestDate ?></td>
                    <td><?= $p->Result ?></td>
                    <td><?= $p->DateReleased ?></td>
                    <td><a href="/labprocedure/edit/<?= $p->LabProcedureID ?>" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form -->
    <?php if (isset($editProcedure)): ?>
        <hr>
        <h4>Edit Lab Procedure</h4>
        <form method="POST" action="/labprocedure/update/<?= $editProcedure->LabProcedureID ?>">
            <div class="row g-2">
                <div class="col"><input type="number" name="PatientID" class="form-control" value="<?= $editProcedure->PatientID ?>"></div>
                <div class="col"><input type="number" name="DoctorID" class="form-control" value="<?= $editProcedure->DoctorID ?>"></div>
                <div class="col"><input type="datetime-local" name="TestDate" class="form-control" value="<?= $editProcedure->TestDate ?>"></div>
                <div class="col"><input type="text" name="Result" class="form-control" value="<?= $editProcedure->Result ?>"></div>
                <div class="col"><input type="date" name="DateReleased" class="form-control" value="<?= $editProcedure->DateReleased ?>"></div>
                <div class="col"><button class="btn btn-success" type="submit">Update</button></div>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
