<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $companyName = $_POST['company-name'] ?? '';
    $location = $_POST['location'] ?? '';
    $fleetSize = $_POST['fleet-size'] ?? '';
    $spocName = $_POST['spoc-name'] ?? '';
    $spocContact = $_POST['spoc-contact'] ?? '';
    $spocEmail = $_POST['spoc-email'] ?? '';
    $solutionRequired = $_POST['solution-required'] ?? '';
    
    // Validate required fields
    if (empty($companyName) || empty($location) || empty($fleetSize) || 
        empty($spocName) || empty($spocContact) || empty($spocEmail) || 
        empty($solutionRequired)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
    
    // Validate email
    if (!filter_var($spocEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Prepare data for CSV
    $timestamp = date('Y-m-d H:i:s');
    $csvData = [
        $timestamp,
        $companyName,
        $location,
        $fleetSize,
        $spocName,
        $spocContact,
        $spocEmail,
        $solutionRequired
    ];
    
    // Save to CSV file
    $csvFile = 'sales_leads.csv';
    $fileExists = file_exists($csvFile);
    
    $handle = fopen($csvFile, 'a');
    
    // Add headers if file doesn't exist
    if (!$fileExists) {
        fputcsv($handle, [
            'Date & Time',
            'Company Name',
            'Location',
            'Fleet Size',
            'SPOC Name',
            'SPOC Contact',
            'SPOC Email',
            'Solution Required'
        ]);
    }
    
    // Add the data
    fputcsv($handle, $csvData);
    fclose($handle);
    
    // Send email notification
    $to = 'info@veyza.in';
    $subject = 'New Lead: ' . $companyName . ' - VEYZA Website';
    
    $emailBody = "
    <html>
    <head>
        <title>New Lead from VEYZA Website</title>
    </head>
    <body>
        <h2>New Company Lead from VEYZA Website</h2>
        <p><strong>A new company has reached out through the website contact form.</strong></p>
        
        <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; max-width: 600px;'>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>Company Name</td>
                <td>" . htmlspecialchars($companyName) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>Location</td>
                <td>" . htmlspecialchars($location) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>Fleet Size</td>
                <td>" . htmlspecialchars($fleetSize) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>SPOC Name</td>
                <td>" . htmlspecialchars($spocName) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>SPOC Contact</td>
                <td>" . htmlspecialchars($spocContact) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>SPOC Email</td>
                <td>" . htmlspecialchars($spocEmail) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>Solution Required</td>
                <td>" . htmlspecialchars($solutionRequired) . "</td>
            </tr>
            <tr>
                <td style='background-color: #4b2aad; color: white; font-weight: bold;'>Submitted On</td>
                <td>" . $timestamp . "</td>
            </tr>
        </table>
        
        <p style='margin-top: 20px;'><strong>Next Steps:</strong></p>
        <ul>
            <li>Review the lead details</li>
            <li>Contact the SPOC within 24 hours</li>
            <li>Update lead status in the Sales Leads dashboard</li>
        </ul>
        
        <p><a href='https://www.veyza.in/sales-leads.php' style='background-color: #4b2aad; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Sales Leads Dashboard</a></p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: VEYZA Website <noreply@veyza.in>' . "\r\n";
    $headers .= 'Reply-To: ' . $spocEmail . "\r\n";
    
    $emailSent = mail($to, $subject, $emailBody, $headers);
    
    // Return response
    if ($emailSent) {
        echo json_encode([
            'success' => true, 
            'message' => 'Data Submitted Successfully! Our team will reach out to you shortly.'
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'Data Submitted Successfully! Our team will reach out to you shortly. (Note: Email notification may have failed)'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>