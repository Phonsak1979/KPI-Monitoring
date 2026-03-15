<?php
$files = [
    'd:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\head.blade.php',
    'd:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\script.blade.php',
    'd:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\sidebar.blade.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Replace href="dist/..." or href="plugins/..."
        $content = preg_replace('/href="(dist\/|plugins\/)([^"]+)"/', 'href="{{ asset(\'$1$2\') }}"', $content);

        // Replace src="dist/..." or src="plugins/..."
        $content = preg_replace('/src="(dist\/|plugins\/)([^"]+)"/', 'src="{{ asset(\'$1$2\') }}"', $content);

        file_put_contents($file, $content);
        echo "Processed $file\n";
    } else {
        echo "File not found: $file\n";
    }
}
