        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-cerrar alertas después de 5 segundos
    document.querySelectorAll('.alert').forEach(function(alert) {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-warning')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
</script>
</body>
</html>
