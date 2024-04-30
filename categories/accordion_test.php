<!DOCTYPE html>
<html>
<head>
    <title>Category Accordion</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to load categories and their descendants
            function loadCategories() {
                $.ajax({
                    url: 'get_categories.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var categoriesList = $('#categories');
                        categoriesList.empty();

                        // Recursively build the accordion list
                        function buildAccordionList(categories, parentElement) {
                            categories.forEach(function(category) {
                                var listItem = $('<li>').text(category.name);
                                parentElement.append(listItem);

                                if (category.descendants && category.descendants.length > 0) {
                                    var descendantsList = $('<ul>').hide();
                                    listItem.append(descendantsList);
                                    listItem.addClass('has-descendants');
                                    buildAccordionList(category.descendants, descendantsList);
                                }
                            });
                        }

                        buildAccordionList(response, categoriesList);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading categories:', error);
                    }
                });
            }

            // Load categories on page load
            loadCategories();

            // Toggle descendants on click
            $(document).on('click', '.has-descendants', function() {
                $(this).find('ul').first().slideToggle();
            });
        });
    </script>
</head>
<body>
    <h1>Category Accordion</h1>
    <ul id="categories"></ul>
</body>
</html>