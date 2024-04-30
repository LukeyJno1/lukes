$currentCategoryId = isset($categoryId) ? $categoryId : null;

function confirmDeletion() {
    return confirm('Are you sure you want to delete this category and all associated data?');
}
