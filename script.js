/**
 * Alibaba Clone - Frontend Logic
 */
 
document.addEventListener('DOMContentLoaded', () => {
    console.log('Alibaba Clone Initialized');
 
    // Smooth scroll for anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
 
    // Form validation helper
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
 
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#DDDDDD';
                }
            });
 
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
 
    // Product Card Hover Micro-animations (handled by CSS, but can add JS logic here if needed)
});
 
// Utility function to handle "Add to Cart" via AJAX (future enhancement)
function addToCart(productId) {
    console.log(`Adding product ${productId} to cart...`);
    // Placeholder for AJAX call
}
 
