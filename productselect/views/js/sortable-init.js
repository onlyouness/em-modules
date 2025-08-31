$(document).ready(function () {
    $("#sortable").sortable({
        update: function (event, ui) {
            var order = $(this).sortable("toArray", { attribute: "data-id" });
            console.log(order);
            // updatePositions(order);
        },
    });
    $("#sortable").disableSelection();
    console.log($('#sortable'))
});

function updatePositions(order) {
    const body = document.querySelector("body");
    const ajax_confirmation = document.querySelector("#ajax_confirmation");
    const tokenAdmin = body.dataset.token;
    fetch(`${baseAdminDir}blogs/positions?_token=${tokenAdmin}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json", // Ensure the body is JSON
            "X-CSRF-Token": tokenAdmin, // Include the CSRF token as a header
        },
        body: JSON.stringify({ order }), // Convert the order array to JSON
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            // Access the success field directly
            const message = data.success || "An unknown error occurred.";

            ajax_confirmation.innerHTML = message;
            ajax_confirmation.style.display = "block";
            setTimeout(() => {
                ajax_confirmation.style.display = "none";
            }, 5000);
        })
        .catch((error) => {
            console.error("Error fetching products:", error); // Handle errors
        });
}
});