function updateFormOptions(categoryId) {
  // Example: Show the rename input if a category is selected
  const renameInput = document.querySelector('input[name="new_name"]');
  if (categoryId) {
      renameInput.style.display = 'block';
  } else {
      renameInput.style.display = 'none';
  }
  // Add more conditions to handle other form elements
}

