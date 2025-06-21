<?php
// Check if user is authorized (you can add authentication here)
// For now, anyone can access this page - you should add proper authentication

$csvFile = 'sales_leads.csv';
$statusFile = 'lead_status.csv';

// Handle CSV export - MUST be before any HTML output
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    if (file_exists($csvFile)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="veyza_sales_leads_' . date('Y-m-d') . '.csv"');
        readfile($csvFile);
        exit;
    }
}

// Handle Excel export - MUST be before any HTML output
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    if (file_exists($csvFile)) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="veyza_sales_leads_' . date('Y-m-d') . '.xls"');
        
        // Create Excel content
        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        
        // Read and output CSV data
        $file = fopen($csvFile, 'r');
        while (($row = fgetcsv($file)) !== FALSE) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
        fclose($file);
        
        echo '</table>';
        echo '</body></html>';
        exit;
    }
}

// Handle AJAX requests for updating status and notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_status') {
        $leadId = $_POST['lead_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';
        
        // Read existing status data
        $statusData = [];
        if (file_exists($statusFile)) {
            if (($handle = fopen($statusFile, "r")) !== FALSE) {
                $statusHeaders = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $statusData[$data[0]] = array_combine($statusHeaders, $data);
                }
                fclose($handle);
            }
        }
        
        // Update or add status data
        $statusData[$leadId] = [
            'lead_id' => $leadId,
            'status' => $status,
            'notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Write back to file
        $handle = fopen($statusFile, 'w');
        fputcsv($handle, ['lead_id', 'status', 'notes', 'updated_at']);
        foreach ($statusData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        
        echo json_encode(['success' => true]);
        exit;
    }
}

if (!file_exists($csvFile)) {
    $leads = [];
} else {
    $leads = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $headers = fgetcsv($handle); // Read header row
        while (($data = fgetcsv($handle)) !== FALSE) {
            $leads[] = array_combine($headers, $data);
        }
        fclose($handle);
    }
}

// Load status data
$statusData = [];
if (file_exists($statusFile)) {
    if (($handle = fopen($statusFile, "r")) !== FALSE) {
        $statusHeaders = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== FALSE) {
            $statusData[$data[0]] = array_combine($statusHeaders, $data);
        }
        fclose($handle);
    }                        }

                        // Calculate stats
                        $totalLeads = count($leads);
                        $todayLeads = 0;
                        $thisWeekLeads = 0;
                        
                        foreach ($leads as $lead) {
                            $leadDate = strtotime($lead['Date & Time']);
                            $today = strtotime('today');
                            $weekStart = strtotime('monday this week');
                            
                            if ($leadDate >= $today) {
                                $todayLeads++;
                            }
                            if ($leadDate >= $weekStart) {
                                $thisWeekLeads++;
                            }
                        }
                        ?>
<!DOCTYPE HTML>
<html lang="en-US">

<head>
    <title>Sales Leads - VEYZA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link href='https://fonts.googleapis.com/css?family=League+Script%7CPoppins:300,400,500,600,700%7CMontserrat:900'
        rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href='style.css?q=1' />
    <style>
        .sales-leads-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
        }
        
        .leads-table-wrapper {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px 0;
        }
        
        .leads-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .leads-table th {
            background-color: #4b2aad;
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }
        
        .leads-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
            border-left: none;
            border-right: none;
            vertical-align: top;
        }
        
        .leads-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .leads-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .stats-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #4b2aad;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .no-leads {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .export-btn {
            background: linear-gradient(to bottom, #7f4dec 0%, #5c0daa 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(127, 77, 236, 0.4);
        }
        
        .back-btn {
            background: #f8f9fa;
            color: #4b2aad;
            padding: 12px 25px;
            border: 2px solid #4b2aad;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 10px 10px 0;
            font-size: 14px;
            transition: all 0.3s ease;
        }
          .back-btn:hover {
            background: #4b2aad;
            color: white;
        }
        
        .status-select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 12px;
            background: white;
            min-width: 120px;
        }
        
        .notes-textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 12px;
            resize: vertical;
            min-height: 60px;
            max-width: 200px;
        }
        
        .save-btn {
            background: #4b2aad;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 5px;
        }
        
        .save-btn:hover {
            background: #5c0daa;
        }
        
        .status-new { background-color: #e3f2fd; color: #1976d2; }
        .status-contacted { background-color: #fff3e0; color: #f57c00; }
        .status-qualified { background-color: #e8f5e8; color: #388e3c; }
        .status-proposal { background-color: #fce4ec; color: #c2185b; }
        .status-closed { background-color: #f3e5f5; color: #7b1fa2; }
        .status-lost { background-color: #ffebee; color: #d32f2f; }
        
        @media (max-width: 768px) {
            .leads-table-wrapper {
                overflow-x: auto;
            }
            
            .leads-table {
                min-width: 800px;
            }
            
            .stats-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="site-wrapper">
        <div id="content" class="site-content center-relative">
            <div class="section">
                <div class="block content-1070 center-relative section-content">
                    <div class="menu-wrapper center-relative relative sticky-header">
                        <div class="header-logo">
                            <a href="index.html">
                                <img src="images/logo-light-horizontal.png" alt="VEYZA">
                            </a>
                        </div>
                    </div>
                    
                    <!-- Add spacing to prevent logo overlap -->
                    <div style="height: 100px;"></div>
                    
                    <div class="sales-leads-container">
                        <h1 class="entry-title" style="text-align: center; margin-bottom: 20px;">Sales Leads Dashboard</h1>                        <div style="text-align: center; margin-bottom: 30px;">
                            <a href="index.html" class="back-btn">← Back to Home</a>
                            <a href="?export=csv" class="export-btn">📥 Export to CSV</a>
                            <a href="?export=excel" class="export-btn">📊 Export to Excel</a>
                        </div>

                        <?php
                        // Read CSV file and display data
                        if (!file_exists($csvFile)) {
                            $leads = [];
                        } else {
                            $leads = [];
                            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                                $headers = fgetcsv($handle); // Read header row
                                while (($data = fgetcsv($handle)) !== FALSE) {
                                    $leads[] = array_combine($headers, $data);
                                }
                                fclose($handle);
                            }
                        }

                        // Load status data
                        $statusData = [];
                        if (file_exists($statusFile)) {
                            if (($handle = fopen($statusFile, "r")) !== FALSE) {
                                $statusHeaders = fgetcsv($handle);
                                while (($data = fgetcsv($handle)) !== FALSE) {
                                    $statusData[$data[0]] = array_combine($statusHeaders, $data);
                                }
                                fclose($handle);
                            }
                        }                        ?>

                        <!-- Stats Cards -->
                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $totalLeads; ?></div>
                                <div class="stat-label">Total Leads</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $todayLeads; ?></div>
                                <div class="stat-label">Today's Leads</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $thisWeekLeads; ?></div>
                                <div class="stat-label">This Week</div>
                            </div>
                        </div>

                        <!-- Leads Table -->
                        <div class="leads-table-wrapper">
                            <?php if (empty($leads)): ?>
                                <div class="no-leads">
                                    <h3>No leads found</h3>
                                    <p>When companies submit the contact form, their information will appear here.</p>
                                </div>
                            <?php else: ?>
                                <table class="leads-table">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Company Name</th>
                                            <th>Location</th>
                                            <th>Fleet Size</th>
                                            <th>SPOC Name</th>
                                            <th>SPOC Contact</th>
                                            <th>SPOC Email</th>
                                            <th>Solution Required</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Sort leads by date (newest first)
                                        usort($leads, function($a, $b) {
                                            return strtotime($b['Date & Time']) - strtotime($a['Date & Time']);
                                        });
                                        
                                        foreach ($leads as $index => $lead): 
                                            $leadId = md5($lead['Date & Time'] . $lead['SPOC Email']); // Create unique ID
                                            $currentStatus = isset($statusData[$leadId]) ? $statusData[$leadId] : null;
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($lead['Date & Time']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($lead['Company Name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($lead['Location']); ?></td>
                                                <td><?php echo htmlspecialchars($lead['Fleet Size']); ?></td>
                                                <td><?php echo htmlspecialchars($lead['SPOC Name']); ?></td>
                                                <td><?php echo htmlspecialchars($lead['SPOC Contact']); ?></td>
                                                <td><a href="mailto:<?php echo htmlspecialchars($lead['SPOC Email']); ?>" style="color: #4b2aad;"><?php echo htmlspecialchars($lead['SPOC Email']); ?></a></td>
                                                <td><?php echo htmlspecialchars($lead['Solution Required']); ?></td>
                                                <td>
                                                    <select class="status-select" data-lead-id="<?php echo $leadId; ?>">
                                                        <option value="new" <?php echo (!$currentStatus || $currentStatus['status'] === 'new') ? 'selected' : ''; ?>>New</option>
                                                        <option value="contacted" <?php echo ($currentStatus && $currentStatus['status'] === 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                                                        <option value="qualified" <?php echo ($currentStatus && $currentStatus['status'] === 'qualified') ? 'selected' : ''; ?>>Qualified</option>
                                                        <option value="proposal" <?php echo ($currentStatus && $currentStatus['status'] === 'proposal') ? 'selected' : ''; ?>>Proposal Sent</option>
                                                        <option value="closed" <?php echo ($currentStatus && $currentStatus['status'] === 'closed') ? 'selected' : ''; ?>>Closed Won</option>
                                                        <option value="lost" <?php echo ($currentStatus && $currentStatus['status'] === 'lost') ? 'selected' : ''; ?>>Lost</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="notes-textarea" data-lead-id="<?php echo $leadId; ?>" placeholder="Add notes..."><?php echo $currentStatus ? htmlspecialchars($currentStatus['notes']) : ''; ?></textarea>
                                                    <br>
                                                    <button class="save-btn" data-lead-id="<?php echo $leadId; ?>">Save</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="footer-container">
                <div class="footer-left horizontal-align">
                    <img src="images/logo-dark-vertical.png" alt="VEYZA Logo" class="footer-logo">
                    <p class="footer-slogan">Empower Your Journey, Embrace the Future!</p>
                </div>
                <div class="footer-center">
                    <nav>
                        <ul class="footer-links">
                            <li><a href="index.html#about">About</a></li>
                            <li><a href="index.html#services">Services</a></li>
                            <li><a href="index.html#testimonials">Testimonials</a></li>
                            <li><a href="index.html#contact">Contacts</a></li>
                            <li><a href="https://app.veyza.in" target="_blank">Login</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="footer-right">
                    <p><a href="PrivacyPolicyVEYZA.html" style="color: inherit; text-decoration: none;">© All Rights Reserved. VEYZA</a></p>
                </div>
            </div>        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle save button clicks
            $('.save-btn').click(function() {
                var leadId = $(this).data('lead-id');
                var status = $('.status-select[data-lead-id="' + leadId + '"]').val();
                var notes = $('.notes-textarea[data-lead-id="' + leadId + '"]').val();
                
                $.ajax({
                    url: 'sales-leads.php',
                    method: 'POST',
                    data: {
                        action: 'update_status',
                        lead_id: leadId,
                        status: status,
                        notes: notes
                    },
                    success: function(response) {
                        alert('Status and notes updated successfully!');
                    },
                    error: function() {
                        alert('Error updating status and notes. Please try again.');
                    }
                });
            });
            
            // Auto-save on status change
            $('.status-select').change(function() {
                var leadId = $(this).data('lead-id');
                var status = $(this).val();
                var notes = $('.notes-textarea[data-lead-id="' + leadId + '"]').val();
                
                $.ajax({
                    url: 'sales-leads.php',
                    method: 'POST',
                    data: {
                        action: 'update_status',
                        lead_id: leadId,
                        status: status,
                        notes: notes
                    },
                    success: function(response) {
                        // Optional: Show a subtle success indicator
                    }
                });
            });
        });
    </script>
</body>

</html>