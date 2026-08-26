import './bootstrap';

document.querySelectorAll('[data-review-form]').forEach((form) => {
    const confirmation = form.querySelector('[data-review-confirmation]');
    const submit = form.querySelector('[data-review-submit]');

    if (!confirmation || !submit) return;

    const syncReviewSubmit = () => {
        submit.disabled = !confirmation.checked;
        submit.setAttribute('aria-disabled', String(!confirmation.checked));
    };

    confirmation.addEventListener('change', syncReviewSubmit);
    syncReviewSubmit();
});
