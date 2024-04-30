// ajax-category-creation.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-category-form'); // Make sure this matches the ID of your form
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);

        fetch('/categories/ajax/create_category_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Redirect to the new category page using the slug and ID
                // Make sure the path matches your actual URL structure and .htaccess rewrite rule
                window.location.href = '/categories/category_pages/' + encodeURIComponent(data.slug) + '?id=' + encodeURIComponent(data.categoryId);
            } else {
                // Handle error, show error message to the user
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            // Handle network errors or issues with the request
            console.error('Fetch Error:', error);
        });
    });
});