<?php // Shared site footer for PHP pages — social links come from Settings. ?>
</main>

<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <img src="assets/img/logo.png" alt="PropFirm Conclave Kochi logo">
        <p>India's premier proprietary trading event — by Finfluenze.<br><?= site_esc(site_setting('tagline', 'Connect · Trade · Get Funded')) ?>.</p>
      </div>
      <div class="foot-col">
        <h4>Event</h4>
        <a href="about.php">About</a><a href="speakers.php">Speakers</a><a href="agenda.php">Agenda</a><a href="tickets.php">Tickets</a>
      </div>
      <div class="foot-col">
        <h4>Attend</h4>
        <a href="venue.php">Venue</a><a href="sponsors.php">Sponsors</a><a href="gallery.php">Gallery</a><a href="faq.php">FAQ</a><a href="contact.php">Contact</a>
      </div>
      <div class="foot-col">
        <h4>More</h4>
        <a href="blog/index.php">Blog</a><a href="privacy.php">Privacy Policy</a><a href="terms.php">Terms &amp; Conditions</a>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 Finfluenze · <?= site_esc(site_setting('site_name', 'PropFirm Conclave Kochi')) ?>. All rights reserved.</span>
      <span class="links">
      <?php
      $socials = [
        ['instagram_url', 'Instagram', '<rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.6" cy="6.4" r="1.3" fill="currentColor" stroke="none"/>'],
        ['facebook_url', 'Facebook', '<path d="M15 3h-2.5A3.5 3.5 0 009 6.5V9H6v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>'],
        ['x_url', 'X', '<path d="M4 4l16 16M20 4L4 20"/>'],
        ['youtube_url', 'YouTube', '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10.5 9.5l5 2.5-5 2.5z" fill="currentColor" stroke="none"/>'],
        ['linkedin_url', 'LinkedIn', '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10.5V17M8 7.5v.01M12 17v-3.8a2.2 2.2 0 014.4 0V17"/>'],
      ];
      foreach ($socials as [$key, $label, $paths]):
        $url = site_setting($key);
        if ($url === '') continue; ?>
        <a href="<?= site_esc($url) ?>" target="_blank" rel="noopener" aria-label="Finfluenze on <?= $label ?>" class="soc"><svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8"><?= $paths ?></svg><span><?= $label ?></span></a>
      <?php endforeach; ?>
        <a href="privacy.php">Privacy</a><a href="terms.php">Terms</a><a href="contact.php">Contact</a>
      </span>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
