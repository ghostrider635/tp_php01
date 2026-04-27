</div><!-- /.page-body -->

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> FacturePro – UPC Faculté des Sciences Informatiques</p>
</footer>

</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
(function() {
    var btn     = document.getElementById('hamburger-btn');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.classList.add('menu-open');
        btn.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.classList.remove('menu-open');
        btn.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
    }

    btn.addEventListener('click', function() {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);

    document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
        link.addEventListener('click', closeSidebar);
    });

    // Fermer sidebar si redimensionnement vers desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) closeSidebar();
    });
})();
</script>
</body>
</html>
