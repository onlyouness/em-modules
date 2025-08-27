document.addEventListener('DOMContentLoaded', function () {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        item.addEventListener('click', function () {
            const answer = item.nextElementSibling;  // The corresponding faq-answer
            const icon = item.querySelector('.icon-container i'); // The icon inside the faq-item

            // Toggle the 'open' class for smooth transitions
            answer.classList.toggle('open');
            icon.classList.toggle('open');
            item.classList.toggle('open')
        });
    });
});
