<?php 
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'SendMessage') {
    
    $companyName = $_REQUEST['company-name'] ?? '';
    $location = $_REQUEST['location'] ?? '';
    $fleetSize = $_REQUEST['fleet-size'] ?? '';
    $spocName = $_REQUEST['spoc-name'] ?? '';
    $spocContact = $_REQUEST['spoc-contact'] ?? '';
    $spocEmail = $_REQUEST['spoc-email'] ?? '';
    $solutionRequired = $_REQUEST['solution-required'] ?? '';
    
    // Validation
    $errors = [];
    if(empty($companyName)) $errors[] = "Company name is required";
    if(empty($location)) $errors[] = "Location is required";
    if(empty($fleetSize)) $errors[] = "Fleet size is required";
    if(empty($spocName)) $errors[] = "SPOC name is required";
    if(empty($spocContact)) $errors[] = "SPOC contact is required";
    if(empty($spocEmail)) $errors[] = "SPOC email is required";
    if(empty($solutionRequired)) $errors[] = "Solution required is required";
    
    if(!empty($errors)) {
        echo json_encode(['ResponseData' => implode(', ', $errors)]);
        exit;
    }
    
    // Clean and sanitize data
    $companyName = htmlspecialchars(trim($companyName));
    $location = htmlspecialchars(trim($location));
    $fleetSize = htmlspecialchars(trim($fleetSize));
    $spocName = htmlspecialchars(trim($spocName));
    $spocContact = htmlspecialchars(trim($spocContact));
    $spocEmail = htmlspecialchars(trim($spocEmail));
    $solutionRequired = htmlspecialchars(trim($solutionRequired));
    
    // Fleet size display mapping
    $fleetSizeDisplay = [
        'less-than-50' => 'Less than 50',
        '50-100' => '50-100',
        'above-100' => 'Above 100'
    ];
    
    // Solution required display mapping
    $solutionDisplay = [
        'unified-view' => 'Unified View',
        'control-tower-services' => 'Control Tower Services',
        'mobile-app-tracking' => 'Mobile App Tracking Solution'
    ];
    
    // Email message content
    $message = '<h2>New Lead from VEYZA Website</h2>';
    $message .= '<p><strong>Company Name:</strong> ' . $companyName . '</p>';
    $message .= '<p><strong>Location:</strong> ' . $location . '</p>';
    $message .= '<p><strong>Fleet Size:</strong> ' . ($fleetSizeDisplay[$fleetSize] ?? $fleetSize) . '</p>';
    $message .= '<p><strong>SPOC Name:</strong> ' . $spocName . '</p>';
    $message .= '<p><strong>SPOC Contact:</strong> ' . $spocContact . '</p>';
    $message .= '<p><strong>SPOC Email:</strong> ' . $spocEmail . '</p>';
    $message .= '<p><strong>Solution Required:</strong> ' . ($solutionDisplay[$solutionRequired] ?? $solutionRequired) . '</p>';
    $message .= '<p><strong>Date & Time:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    
    // Email settings
    $to = 'info@veyza.in';
    $subject = 'New Company Lead from VEYZA Website - ' . $companyName;
    $headers = "From: " . $spocName . " <" . $spocEmail . "> \r\n";
    $headers .= 'Reply-To: info@veyza.in' . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    
    // Send email
    $send_email = mail($to, $subject, $message, $headers);
    
    // Return success message
    echo json_encode(['ResponseData' => 'Data Submitted, Our Team Will be Reach out to you shortly!']);
}
?>