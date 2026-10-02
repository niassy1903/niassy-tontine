document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-toggle-sidebar]')?.addEventListener('click', () => document.querySelector('#sidebar')?.classList.toggle('open'));
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (!window.confirm(element.dataset.confirm || 'Confirmer cette action ?')) event.preventDefault();
        });
    });
});