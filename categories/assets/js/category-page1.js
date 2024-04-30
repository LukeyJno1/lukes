document.addEventListener('DOMContentLoaded', function() {
    var accordionHeaders = document.querySelectorAll('.accordion-header');

    // It looks like you intended to use `accordionIcons`, but it wasn't declared anywhere.
    // Assuming `accordionIcons` should select some elements within your accordion headers, you might need something like this:
    // Note: You'll need to adjust the selector based on your actual HTML structure.
    var accordionIcons = document.querySelectorAll('.accordion-header .toggle-icon');

    accordionHeaders.forEach(function(header) {
        header.addEventListener('click', function(event) {
            event.preventDefault();
            var accordionItem = this.parentElement;
            var accordionContent = accordionItem.querySelector('.accordion-content');
            var isExpanded = accordionContent.style.display === 'block';

            // Close all accordion items
            accordionHeaders.forEach(function(otherHeader) {
                var otherItem = otherHeader.parentElement;
                var otherContent = otherItem.querySelector('.accordion-content');
                otherContent.style.display = 'none';
                otherHeader.querySelector('.toggle-icon').classList.remove('minus-icon');
                otherHeader.querySelector('.toggle-icon').classList.add('plus-icon');
            });

            if (!isExpanded) {
                accordionContent.style.display = 'block';
                this.querySelector('.toggle-icon').classList.remove('plus-icon');
                this.querySelector('.toggle-icon').classList.add('minus-icon');
            }
        });
    });

    accordionIcons.forEach(function(icon) {
        icon.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent the accordion from toggling when the icon is clicked

            // Get the .accordion-content element that is the next sibling of the parent of the icon
            var accordionContent = this.parentNode.nextElementSibling;

            // Check if the accordion is currently expanded or not
            var isExpanded = accordionContent.style.display === 'block';

            // If expanded, hide it and switch to plus icon, else show it and switch to minus icon
            if (isExpanded) {
                accordionContent.style.display = 'none';
                this.classList.remove('minus-icon');
                this.classList.add('plus-icon');
            } else {
                accordionContent.style.display = 'block';
                this.classList.remove('plus-icon');
                this.classList.add('minus-icon');
            }
        });
    });

    // Category links should only navigate to the category page and not toggle the accordion
    var categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            // No additional action needed here unless you want to explicitly use event.preventDefault();
        });
    });

    // Initialize the breadcrumbs functionality
    initBreadcrumbs();
});

function initBreadcrumbs() {
    var breadcrumbs = document.querySelector('.breadcrumb-trail');
    if (breadcrumbs) {
        var breadcrumbLinks = breadcrumbs.querySelectorAll('a');
        breadcrumbLinks.forEach(function(link) {
            link.addEventListener('mouseover', function() {
                this.style.textDecoration = 'underline';
            });
            link.addEventListener('mouseout', function() {
                this.style.textDecoration = 'none';
            });
        });
    }
}
