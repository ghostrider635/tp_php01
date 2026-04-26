</div><!-- /.page-body -->

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> FacturePro – UPC Faculté des Sciences Informatiques</p>
</footer>

</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
(function(){
    var btn     = document.getElementById('hamburger-btn');
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    function close(){ sidebar.classList.remove('open'); overlay.classList.remove('active'); }
    if(btn){ btn.addEventListener('click', function(){ sidebar.classList.toggle('open'); overlay.classList.toggle('active'); }); }
    if(overlay){ overlay.addEventListener('click', close); }
    document.querySelectorAll('.sidebar-nav a').forEach(function(a){ a.addEventListener('click', close); });
})();
</script>
</body>
</html>
