<?php // Footer scripts only ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('click', function(e){
    const link = e.target.closest('#offcanvasSidebar a.nav-link[href]');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href || href === '#' || href.startsWith('javascript')) return;
    e.preventDefault();
    const el = document.getElementById('offcanvasSidebar');
    if (el) {
      const oc = bootstrap.Offcanvas.getOrCreateInstance(el);
      oc.hide();
      setTimeout(()=>{ window.location.href = href; }, 160);
    } else {
      window.location.href = href;
    }
  });
</script>
</body>
</html>
