document.addEventListener('DOMContentLoaded', function () {
    const selectors = document.querySelectorAll('.sl3w-option-setting-field._select:not(._select-multi) select');

    selectors.forEach(function (selector) {
        selector.addEventListener('click', function () {
            selector.classList.toggle('active');
        });

        selector.addEventListener('blur', function () {
            selector.classList.remove('active');
        });
    });
});