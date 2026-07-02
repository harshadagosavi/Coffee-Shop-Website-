<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$sql = "SELECT o.*, u.username, u.email, u.mobile FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Order not found");
}

$order = $result->fetch_assoc();

// Get order items
$sql_items = "SELECT oi.*, p.name FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// Check if TCPDF exists, otherwise use simple method
$use_tcpdf = file_exists('tcpdf/tcpdf.php') || file_exists('TCPDF/tcpdf.php');

if ($use_tcpdf) {
    // Method 1: Using TCPDF library (Professional)
    generateInvoiceWithTCPDF($order, $items_result, $order_id);
} else {
    // Method 2: Simple HTML to PDF download
    generateSimpleInvoice($order, $items_result, $order_id);
}

exit();

// ============================================
// Function to generate invoice with TCPDF
// ============================================
function generateInvoiceWithTCPDF($order, $items_result, $order_id) {
    // Include TCPDF library
    require_once('tcpdf/tcpdf.php'); // Adjust path as needed
    
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Coffee Shop');
    $pdf->SetAuthor('Coffee Shop');
    $pdf->SetTitle('Invoice #' . $order_id);
    $pdf->SetSubject('Invoice');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 10);
    
    // HTML content
    $html = '
    <style>
        .invoice-header {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .invoice-details {
            margin: 20px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .invoice-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .invoice-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
    
    <div class="invoice-header">
        <h1>INVOICE</h1>
    </div>
    
    <div class="company-info">
        <h3>Coffee Shop</h3>
        <p>123 Coffee Street, City, State, PIN</p>
        <p>Phone: +91 1234567890 | Email: info@coffeeshop.com</p>
    </div>
    
    <div class="invoice-details">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Invoice To:</strong><br>
                    ' . htmlspecialchars($order['username']) . '<br>
                    ' . htmlspecialchars($order['email']) . '<br>
                    ' . htmlspecialchars($order['mobile']) . '
                </td>
                <td width="50%" style="text-align:right;">
                    <strong>Invoice Details:</strong><br>
                    Invoice #: ' . $order_id . '<br>
                    Date: ' . date('d/m/Y', strtotime($order['order_date'])) . '<br>
                    Payment: ' . ucfirst($order['payment_method']) . '<br>
                    Status: ' . ucfirst($order['status']) . '
                </td>
            </tr>
        </table>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th width="60%">Product</th>
                <th width="15%">Quantity</th>
                <th width="15%">Price</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>';
    
    $total = 0;
    $items_result->data_seek(0);
    while ($row = $items_result->fetch_assoc()) {
        $subtotal = $row['quantity'] * $row['price'];
        $total += $subtotal;
        $html .= '
            <tr>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . $row['quantity'] . '</td>
                <td>₹' . number_format($row['price'], 2) . '</td>
                <td>₹' . number_format($subtotal, 2) . '</td>
            </tr>';
    }
    
    $html .= '
        </tbody>
    </table>
    
    <div class="total-section">
        <h3>Total Amount: ₹' . number_format($total, 2) . '</h3>
    </div>
    
    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Coffee Shop - Your favorite coffee destination</p>
        <p>This is a computer generated invoice. No signature required.</p>
    </div>';
    
    // Write HTML content
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Output PDF
    $pdf->Output('invoice_' . $order_id . '.pdf', 'D'); // 'D' for download
}

// ============================================
// Function to generate simple invoice (no TCPDF)
// ============================================
function generateSimpleInvoice($order, $items_result, $order_id) {
    // Calculate total
    $total = 0;
    $items_result->data_seek(0);
    while ($row = $items_result->fetch_assoc()) {
        $total += $row['quantity'] * $row['price'];
    }
    
    // Create HTML content
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Invoice #' . $order_id . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 40px;
                color: #333;
            }
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                position: relative;
            }
            .back-button-container {
                margin-bottom: 20px;
                text-align: left;
            }
            .back-button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #667eea;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                text-decoration: none;
                transition: background 0.3s;
            }
            .back-button:hover {
                background: #5a67d8;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #667eea;
            }
            .header h1 {
                color: #667eea;
                margin: 0;
            }
            .company-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .invoice-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }
            .invoice-details {
                width: 48%;
            }
            .customer-details {
                width: 48%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            table th {
                background: #667eea;
                color: white;
                padding: 12px;
                text-align: left;
            }
            table td {
                padding: 12px;
                border-bottom: 1px solid #ddd;
            }
            table tr:nth-child(even) {
                background: #f8f9fa;
            }
            .total-row {
                background: #e8f4ff !important;
                font-weight: bold;
            }
            .footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                color: #666;
                font-size: 14px;
            }
            .print-button {
                text-align: center;
                margin-top: 20px;
            }
            .print-button button {
                background: #667eea;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin: 0 5px;
            }
            .print-button button:hover {
                background: #5a67d8;
            }
            @media print {
                .back-button-container {
                    display: none;
                }
                .print-button {
                    display: none;
                }
                .invoice-container {
                    border: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Back Button Container -->
            <div class="back-button-container">
                <a href="profile.php" class="back-button">
                    <span>&larr;</span> Back to Profile
                </a>
            </div>
            
            <div class="header">
                <h1>COFFEE SHOP</h1>
                <p>123 Coffee Street, City, State, PIN</p>
                <p>Phone: +91 1234567890 | Email: info@coffeeshop.com</p>
            </div>
            
            <div class="company-info">
                <h2 style="color: #667eea; margin: 0 0 10px 0;">TAX INVOICE</h2>
            </div>
            
            <div class="invoice-info">
                <div class="customer-details">
                    <h3>Customer Details:</h3>
                    <p><strong>Name:</strong> ' . htmlspecialchars($order['username']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($order['mobile']) . '</p>
                </div>
                
                <div class="invoice-details">
                    <h3>Invoice Details:</h3>
                    <p><strong>Invoice #:</strong> ' . $order_id . '</p>
                    <p><strong>Date:</strong> ' . date('d/m/Y', strtotime($order['order_date'])) . '</p>
                    <p><strong>Payment Method:</strong> ' . ucfirst($order['payment_method']) . '</p>
                    <p><strong>Status:</strong> ' . ucfirst($order['status']) . '</p>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
    
    $counter = 1;
    $items_result->data_seek(0);
    while ($row = $items_result->fetch_assoc()) {
        $subtotal = $row['quantity'] * $row['price'];
        $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . htmlspecialchars($row['name']) . '</td>
                        <td>' . $row['quantity'] . '</td>
                        <td>₹' . number_format($row['price'], 2) . '</td>
                        <td>₹' . number_format($subtotal, 2) . '</td>
                    </tr>';
    }
    
    $html .= '
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;"><strong>GRAND TOTAL:</strong></td>
                        <td><strong>₹' . number_format($total, 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="footer">
                <p><strong>Thank you for your purchase!</strong></p>
                <p>Coffee Shop - Your favorite coffee destination</p>
                <p>This is a computer generated invoice. No signature required.</p>
                <p>For any queries, contact: harshadagosavi006@gmail.com | +91 8849184854</p>
            </div>
            
            <div class="print-button">
                <button onclick="window.print()">🖨️ Print Invoice</button>
                <button onclick="downloadAsPDF()">📥 Download as PDF</button>
            </div>
        </div>
        
        <script>
            function downloadAsPDF() {
                // This will trigger the browser\'s print to PDF functionality
                window.print();
            }
        </script>
    </body>
    </html>';
    
    // For simple method, just output HTML that user can print as PDF
    echo $html;
}
?>