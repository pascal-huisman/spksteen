<?php
$imageDir = __DIR__ . '/images/';
$allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

$images = [];
if (is_dir($imageDir)) {
    foreach (scandir($imageDir) as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExt, true)) {
            $images[] = $file;
        }
    }
}
sort($images);

function makeTitle(string $filename): string {
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = str_replace(['-', '_'], ' ', $name);
    $name = preg_replace('/^\d+\s*/', '', $name); // strip leading "01 " prefix
    return ucwords(mb_strtolower($name));
}

function detectMaterial(string $filename): string {
    $lower = strtolower($filename);
    if (str_contains($lower, 'albast'))  return 'Albast';
    if (str_contains($lower, 'graniet')) return 'Graniet';
    if (str_contains($lower, 'marmer'))  return 'Marmer';
    return 'Speksteen';
}

function cardClass(int $index, string $filename, string $imageDir): string {
    $size = @getimagesize($imageDir . $filename);
    $isLandscape = $size && $size[0] > $size[1];

    // Landscape photos always become square — never show them sideways
    if ($isLandscape) return ' card--square';

    // Portrait photos: playful rhythm of square, tall, and normal portrait
    $pos = $index % 9;
    if ($pos === 2 || $pos === 7) return ' card--square';
    if ($pos === 0 || $pos === 5) return ' card--tall';
    return '';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dith – Spekstenen Beelden | Maassluis</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="nav">
    <a href="index.php" class="nav__logo">Dith</a>
    <ul class="nav__links">
      <li><a href="index.php" class="active">Portfolio</a></li>
      <li><a href="informatie.html">Speksteen</a></li>
      <li><a href="contact.html">Contact</a></li>
    </ul>
    <button class="nav__toggle" aria-label="menu">&#9776;</button>
  </nav>

  <header class="hero">
    <div class="hero__text">
      <span class="hero__eyebrow">Handgesneden uit steen</span>
      <h1 class="hero__title">Beelden die<br><em>spreken</em></h1>
      <p class="hero__sub">Spekstenen sculpturen — organisch, tijdloos, met de hand gemaakt in Maassluis</p>
      <a href="#portfolio" class="btn">Bekijk het werk</a>
    </div>
<?php if (!empty($images)): ?>
    <div class="hero__img-wrap">
      <img src="images/<?= htmlspecialchars($images[array_rand($images)]) ?>" alt="Speksteen beeld" class="hero__featured-img">
    </div>
<?php else: ?>
    <div class="hero__stone-accent"></div>
<?php endif; ?>
  </header>

  <section class="intro">
    <div class="intro__line"></div>
    <p class="intro__text">
      Elk beeld begint als een ruwe steen en eindigt als een verhaal.
      Dith werkt met speksteen, albast en andere natuurstenen —
      materialen die zich laten vormen met geduld en gevoel.
    </p>
  </section>

  <section class="portfolio" id="portfolio">
    <h2 class="section-title">Portfolio</h2>
    <p class="section-sub">Een selectie van recent werk</p>

    <div class="grid">
<?php if (empty($images)): ?>
      <p style="grid-column:1/-1;text-align:center;color:#888;padding:3rem 0;">
        Nog geen foto's geplaatst — zet afbeeldingen in de map <code>images/</code>.
      </p>
<?php else: ?>
<?php foreach ($images as $i => $file): ?>
      <article class="card<?= cardClass($i, $file, $imageDir) ?>">
        <div class="card__img-wrap">
          <img src="images/<?= htmlspecialchars($file) ?>"
               alt="<?= htmlspecialchars(makeTitle($file)) ?>"
               loading="lazy">
        </div>
        <div class="card__info">
          <span class="card__material"><?= detectMaterial($file) ?></span>
          <h3 class="card__title"><?= htmlspecialchars(makeTitle($file)) ?></h3>
        </div>
      </article>
<?php endforeach; ?>
<?php endif; ?>
    </div>
  </section>

  <section class="cta-banner">
    <div class="cta-banner__inner">
      <h2>Geïnteresseerd in een beeld?</h2>
      <p>Elk werk is uniek en te koop. Neem contact op voor beschikbaarheid en prijzen.</p>
      <a href="contact.html" class="btn btn--light">Neem contact op</a>
    </div>
  </section>

  <footer class="footer">
    <div class="footer__inner">
      <span class="footer__name">Dith · Beeldend kunstenaar</span>
      <span class="footer__location">Maassluis, Zuid-Holland</span>
      <nav class="footer__nav">
        <a href="index.php">Portfolio</a>
        <a href="informatie.html">Speksteen</a>
        <a href="contact.html">Contact</a>
      </nav>
      <span class="footer__copy">&copy; 2025 Dith – spekstenenbeelden.nl</span>
    </div>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>
