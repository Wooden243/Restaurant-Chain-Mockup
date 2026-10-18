<?php
declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

use RestaurantMockup\Models\RestaurantChain;

// A fresh randomized mock dataset is generated on every HTTP page load.
$chains = [];
for ($i = 0; $i < 3; $i++) {
    $chains[] = RestaurantChain::RandomGenerator();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A randomly generated restaurant-chain management mockup.">
    <title>Restaurant Chain Mockup</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<main class="container">
    <header class="site-header">
        <h1>Restaurant Chain Mockup</h1>
        <p>Explore randomly generated chains, their locations, employees and company details.</p>
        <p class="hint">Reload the page to generate a new dataset. Expand each location to inspect its employees.</p>
    </header>
    <?php foreach ($chains as $chain): ?>
        <?= $chain->toHTML() ?>
    <?php endforeach; ?>
    <footer>Demo data only — names, addresses and personnel are fictional.</footer>
</main>
</body>
</html>
