<?php
require '../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$conn = new mysqli("localhost", "root", "root", "charles_hms");
$result = $conn->query("SELECT * FROM billing");

$html = '
<h2>Billing Summary Report</h2>
<table border="1" width="100%" cellspacing="0" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Patient Name</th>
        <th>Doctor Fee</th>
        <th>Medicine Cost</th>
        <th>Total Amount</th>
    </tr>';

while ($row = $result->fetch_assoc()) {
    $html .= '
    <tr>
        <td>'.$row['id'].'</td>
        <td>'.$row['patient_name'].'</td>
        <td>'.$row['doctor_fee'].'</td>
        <td>'.$row['medicine_cost'].'</td>
        <td>'.$row['total_amount'].'</td>
    </tr>';
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("billing_summary.pdf");
exit();
?>
