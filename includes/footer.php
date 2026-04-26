</div><!-- /.page-body -->

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> FacturePro – UPC Faculté des Sciences Informatiques</p>
</footer>

</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
var btn     = document.getElementById('hamburger-btn');
var sidebar = document.getElementById('sidebar');
var overlay = document.getElementById('sidebar-overlay');

function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.classList.add('menu-open');
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.classList.remove('menu-open');
}

btn.addEventListener('click', function() {
    if (sidebar.classList.contains('open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

overlay.addEventListener('click', closeSidebar);

var navLinks = document.querySelectorAll('.sidebar-nav a');
for (var i = 0; i < navLinks.length; i++) {
    navLinks[i].addEventListener('click', closeSidebar);
}
</script>
</body>
</html>
