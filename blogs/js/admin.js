// document.addEventListener('DOMContentLoaded', () => {

//     document.querySelectorAll('.category-select').forEach(select => {
//         const body = document.querySelector("body");
//         const tokenAdmin = body.dataset.token;  // Get token from body
//         select.addEventListener('change', function () {
//             const categoryId = this.value;
//             console.log(`Selected Category ID: ${categoryId}`);

//             const productSelect = this.closest('.sections_container').querySelector('.product-select'); // Get the product select element

//             // Fetch products based on selected category
//             fetch(`${baseAdminDir}blogs/products?category_id=${categoryId}&_token=${tokenAdmin}`)
//                 .then(response => {
//                     if (!response.ok) {
//                         throw new Error(`HTTP error! Status: ${response.status}`);
//                     }
//                     return response.json(); // Parse the JSON response
//                 })
//                 .then(data => {
//                     productSelect.innerHTML = ''; // Clear current options

//                     // Populate the products dropdown with new options
//                     Object.entries(data).forEach(([id, name]) => {
//                         const option = document.createElement('option');
//                         option.value = name;  // Set product ID as the value
//                         option.textContent = id;  // Set product name as the label
//                         productSelect.appendChild(option); // Append new option
//                     });

//                     console.log(data)
//                 })
//                 .catch(error => {
//                     console.error('Error fetching products:', error);  // Handle any errors
//                 });
//         });
//     });

// });

// document.addEventListener('DOMContentLoaded', () => {
//     // Select all category dropdowns
//     document.querySelectorAll('.category-select').forEach(select => {
//         // Get the CSRF token from the hidden input field
//         const csrfToken = document.getElementById('section__token').value;
//         const body = document.querySelector("body");
//         const tokenAdmin = body.dataset.token;  // Get token from body

//         select.addEventListener('change', function () {
//             const categoryId = this.value; // Get the selected category ID
//             console.log(`Selected Category ID: ${categoryId}`);

//             // Find the associated product dropdown within the same section container
//             const sectionContainer = this.closest('#section');
//             const productSelect = sectionContainer.querySelector('.product-select');

//             // Ensure the baseAdminDir is defined
//             // const baseAdminDir = document.body.dataset.baseAdminDir || '/admin/'; // Replace with actual base admin directory if known

//             // Fetch products for the selected category
//             fetch(`${baseAdminDir}blogs/products?category_id=${categoryId}&_token=${tokenAdmin}`)
//                 .then(response => {
//                     if (!response.ok) {
//                         throw new Error(`HTTP error! Status: ${response.status}`);
//                     }
//                     return response.json(); // Parse JSON response
//                 })
//                 .then(data => {
//                     productSelect.innerHTML = ''; // Clear current options

//                     // Populate product dropdown with options
//                     Object.entries(data).forEach(([id, name]) => {
//                         const option = document.createElement('option');
//                         option.value = name;  // Set product ID as the value
//                         option.textContent = id;  // Set product name as the label
//                         productSelect.appendChild(option); // Append new option
//                     });

//                     console.log(data); // Debugging output
//                 })
//                 .catch(error => {
//                     console.error('Error fetching products:', error); // Handle errors
//                 });
//         });
//     });
// });

document.addEventListener("DOMContentLoaded", () => {
  // Select all category dropdowns
  const form = document.querySelector('form[name="section"]');
  document.querySelectorAll(".category-select").forEach((select) => {
    // Get the CSRF token from the hidden input field
    const csrfToken = document.getElementById("section__token").value;
    const body = document.querySelector("body");
    const tokenAdmin = body.dataset.token; // Get token from body

    select.addEventListener("change", function () {
      const categoryId = this.value; // Get the selected category ID
      console.log(`Selected Category ID: ${categoryId}`);

      // Find the associated product dropdown within the same section container
      const sectionContainer = this.closest("form"); // Select the closest form
      const productSelect = sectionContainer.querySelector(".product-select");

      // Ensure the baseAdminDir is defined (you may need to set this value in the HTML if necessary)
      // const baseAdminDir = document.body.dataset.baseAdminDir || '/admin/';

      // Fetch products for the selected category
      fetch(
        `${baseAdminDir}blogs/products?category_id=${categoryId}&_token=${tokenAdmin}`
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json(); // Parse JSON response
        })
        .then((data) => {
          productSelect.innerHTML = ""; // Clear current options

          // Populate product dropdown with options
          Object.entries(data).forEach(([id, name]) => {
            const option = document.createElement("option");
            option.value = name; // Use product ID as the value
            option.textContent = id; // Use product name as the label
            productSelect.appendChild(option); // Append new option
          });

          console.log(data); // Debugging output
          form.addEventListener("submit", function (event) {
            const productSelect = form.querySelector(".product-select");
            const selectedProducts = Array.from(
              productSelect.selectedOptions
            ).map((option) => option.value);

            // If no products are selected, prevent form submission and show an error message
            if (selectedProducts.length === 0) {
              event.preventDefault();
              alert("Please select at least one product.");
            } else {
              // Set the products field as an array of selected values
              productSelect.value = selectedProducts;
            }
            console.log(selectedProducts);
          });
        })
        .catch((error) => {
          console.error("Error fetching products:", error); // Handle errors
        });
    });
  });

  $(function () {
    $("#sortable").sortable({
      update: function (event, ui) {
        var order = $(this).sortable("toArray", { attribute: "data-id" });
        console.log(order);
        updatePositions(order);
      },
    });
    $("#sortable").disableSelection();
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
