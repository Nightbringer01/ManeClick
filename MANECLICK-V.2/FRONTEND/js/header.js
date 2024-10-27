document.addEventListener('DOMContentLoaded', function() {
    // Function to show the modal
    function showModal(event) {
        event.preventDefault(); // Prevent default link behavior
        var modalContainer = document.getElementById('plansModal');
        modalContainer.style.display = 'block'; // Show the modal container
    }

    // Add event listener to the "View Plans" link
    document.getElementById('view-plans').addEventListener('click', showModal);

    // Function to close the modal
    function closeModal() {
        var modalContainer = document.getElementById('plansModal');
        modalContainer.style.display = 'none'; // Hide the modal container
    }

    // Add event listener to the close button inside the modal
    document.querySelector('.close').addEventListener('click', closeModal);
});
