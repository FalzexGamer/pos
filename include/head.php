<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Print.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/print-js/1.6.0/print.min.js"></script>
    
    <!-- General Styles -->
    <style>
        /* General sidebar styles */
        .sidebar {
            transition: all 0.3s ease;
        }
        .sidebar.collapsed {
            width: 4rem;
        }
        .main-content.expanded {
            margin-left: 4rem;
        }
        
        /* General print styles */
        @media print {
            .no-print {
                display: none !important;
            }
        }
        
        /* General mobile-specific styles */
        @media (max-width: 1023px) {
            .main-content {
                margin-left: 0 !important;
            }
            
            /* Ensure touch targets are large enough */
            button, input, select, textarea {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Prevent horizontal scroll */
            body {
                overflow-x: hidden;
            }
            
            /* Optimize modal for mobile */
            .modal-content {
                margin: 1rem;
                max-height: calc(100vh - 2rem);
            }
            
            /* Fix sidebar for mobile and tablet */
            .sidebar {
                z-index: 10000 !important;
            }
            
            /* Ensure overlay is behind sidebar but above content */
            #sidebar-overlay {
                z-index: 9999 !important;
            }
            
            /* Fix hamburger button visibility */
            .lg\\:hidden {
                display: block !important;
            }
        }
        
        /* General tablet-specific styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .main-content {
                margin-left: 0 !important;
            }
        }
        
        /* General landscape mobile optimization */
        @media (max-width: 768px) and (orientation: landscape) {
            .h-screen {
                height: 100vh;
                min-height: 100vh;
            }
            
            /* Ensure sidebar works in landscape */
            .sidebar {
                z-index: 10000 !important;
            }
            
            /* Make hamburger button more visible in landscape */
            .lg\\:hidden {
                display: block !important;
            }
        }
        
        /* General tablet and mobile landscape */
        @media (max-width: 1023px) and (orientation: landscape) {
            .sidebar {
                z-index: 10000 !important;
            }
        }
        
        /* General mobile viewport height fix */
        :root {
            --vh: 1vh;
        }
        
        .h-screen {
            height: calc(var(--vh, 1vh) * 100);
        }
        
        /* General touch-friendly improvements */
        @media (max-width: 768px) {
            /* Larger touch targets */
            button, input, select, textarea {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Better spacing for mobile */
            .space-y-3 > * + * {
                margin-top: 0.75rem;
            }
            
            .space-y-4 > * + * {
                margin-top: 1rem;
            }
            
            /* Prevent zoom on input focus */
            input, select, textarea {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
