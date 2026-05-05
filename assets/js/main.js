// MUST Hostel Allocation System - Main JS

document.addEventListener('DOMContentLoaded', function () {

    // ===== Mobile Sidebar Toggle =====
    const sidebar = document.getElementById('sidebar');

    // Create hamburger if on mobile
    if (window.innerWidth <= 768) {
        const ham = document.createElement('button');
        ham.innerHTML = '☰';
        ham.style.cssText = 'position:fixed;top:14px;left:14px;z-index:200;background:var(--navy);color:white;border:none;border-radius:6px;padding:8px 12px;font-size:18px;cursor:pointer';
        document.body.appendChild(ham);

        ham.addEventListener('click', function () {
            sidebar?.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (sidebar?.classList.contains('open') && !sidebar.contains(e.target) && e.target !== ham) {
                sidebar.classList.remove('open');
            }
        });
    }

    // ===== Auto-dismiss flash alerts =====
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });  

    // ===== Confirm delete buttons =====
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ===== Table row clickable =====
    document.querySelectorAll('tr[data-href]').forEach(function (row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
            window.location.href = this.dataset.href;
        });
    });

    // ===== File upload preview =====
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const sizeKB = Math.round(file.size / 1024);
            let hint = this.nextElementSibling;
            if (hint && hint.classList.contains('form-hint')) {
                hint.textContent = '✅ Selected: ' + file.name + ' (' + sizeKB + ' KB)';
                hint.style.color = 'var(--green)';
            }
        });
    });

    // ===== Search input auto-submit on clear =====
    document.querySelectorAll('input[name="search"]').forEach(function (input) {
        input.addEventListener('keyup', function (e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.closest('form').submit();
            }
        });
    });

});
