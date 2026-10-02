document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('#sidebar');
    document.querySelector('[data-toggle-sidebar]')?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        document.querySelector('[data-sidebar-overlay]')?.classList.toggle('hidden');
    });
    document.querySelector('[data-sidebar-overlay]')?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        document.querySelector('[data-sidebar-overlay]')?.classList.add('hidden');
    });
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (!window.confirm(element.dataset.confirm || 'Confirmer cette action ?')) event.preventDefault();
        });
    });
    document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[role="alert"]')?.remove());
    });
});
