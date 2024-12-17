document.querySelector("form").addEventListener("submit", function(e) {
    let rating = document.querySelector('input[name="rating"]:checked');
    let comment = document.querySelector('#comment').value;

    // Check if rating is selected
    if (!rating) {
        alert("Please select a rating.");
        e.preventDefault(); // Prevent form submission
        return;
    }

    // Check if comment is provided
    if (comment.trim() === "") {
        alert("Please provide a comment.");
        e.preventDefault(); // Prevent form submission
        return;
    }
});
