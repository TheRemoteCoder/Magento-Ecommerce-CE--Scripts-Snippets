<?php
/**
 * Get profile export from Magento Admin and download it directly.
 * 
 * Export the data first via admin panel before calling the script:
 * System -> Import/Export -> Dataflow -> Profile -> 'Stock Excel'
 *
 * This workaround was required for Magento 1.5.1.0 because /var/export/ 
 * is not publicly accessible and one can't use the /media/ folders anymore.
 */
$file = '/var/export/export_stock_excel.xml';

if (file_exists($file)) {
    header('Content-type:application/octet-stream');
    header('Content-Disposition:attachment; filename="export_stock_excel.xml"');
    echo file_get_contents($file);
}
else {
    header('Content-Type:text/plain, charset=utf-8');
    echo "File not found - Run export profile via admin panel first.";
}

