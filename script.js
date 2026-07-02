// Password toggle function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

// Menu search and filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const products = document.querySelectorAll('.product-item');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categorySelect.value;
        
        products.forEach(product => {
            const name = product.querySelector('h5').textContent.toLowerCase();
            const category = product.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterProducts);
    categorySelect.addEventListener('change', filterProducts);
});
