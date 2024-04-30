document.addEventListener('DOMContentLoaded', function() {
    var accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(function(header) {
        header.addEventListener('click', function(event) {
            var accordionContent = this.nextElementSibling;  // Direct sibling assuming content comes right after header
            var isExpanded = accordionContent.style.display === 'block';

            // Close all open items first
            document.querySelectorAll('.accordion-content').forEach(function(content) {
                content.style.display = 'none';
            });
            document.querySelectorAll('.toggle-icon').forEach(function(icon) {
                icon.classList.remove('minus-icon');
                icon.classList.add('plus-icon');
            });

            // Now handle the clicked item
            if (!isExpanded) {
                accordionContent.style.display = 'block';
                this.querySelector('.toggle-icon').classList.remove('plus-icon');
                this.querySelector('.toggle-icon').classList.add('minus-icon');
            }
        });
    });

    // Initialize any other interactivity, like the breadcrumb effects
    initBreadcrumbs();
});

function toggleAccordion(element) {
    const content = element.parentNode.nextElementSibling;
    if (content.style.display === 'none') {
        content.style.display = 'block';
        element.parentNode.setAttribute('aria-expanded', 'true');
    } else {
        content.style.display = 'none';
        element.parentNode.setAttribute('aria-expanded', 'false');
    }
}


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
