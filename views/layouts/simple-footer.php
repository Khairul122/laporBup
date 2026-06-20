<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <?php
            $current_year = date('Y');
            echo "© $current_year  Kabupaten Mandailing Natal ALL Rights Reserved";
            ?>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        
        const cards = document.querySelectorAll('.menu-item, .info-card, .menu-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = this.classList.contains('menu-item') ?
                    'translateY(-8px) scale(1.02)' : 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
</body>
</html>