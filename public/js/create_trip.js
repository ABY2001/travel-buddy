document.addEventListener("DOMContentLoaded", function () {
    const createModal = document.getElementById("createModal");
    const groupModal = document.getElementById("groupModal");
    const openCreateModalBtn = document.getElementById("openCreateModal");
    const openGroupModalBtn = document.getElementById("openGroupModal");
    const closeCreateModalBtn = document.getElementById("closeCreateModal");
    const closeGroupModalBtn = document.getElementById("closeGroupModal");

    console.log("Script loaded");

    // Open modals
    if (openCreateModalBtn) {
        openCreateModalBtn.addEventListener("click", () => {
            console.log("Create button clicked");
            if (createModal) createModal.style.display = "flex";
        });
    }

    if (openGroupModalBtn) {
        openGroupModalBtn.addEventListener("click", () => {
            console.log("Group button clicked");
            if (groupModal) groupModal.style.display = "flex";
        });
    }

    // Close modals
    if (closeCreateModalBtn) {
        closeCreateModalBtn.addEventListener("click", () => {
            console.log("Close create clicked");
            if (createModal) createModal.style.display = "none";
        });
    }

    if (closeGroupModalBtn) {
        closeGroupModalBtn.addEventListener("click", () => {
            console.log("Close group clicked");
            if (groupModal) groupModal.style.display = "none";
        });
    }

    // Close modal if clicking outside
    window.addEventListener("click", (event) => {
        if (event.target === createModal) {
            console.log("Clicked outside create modal");
            createModal.style.display = "none";
        }
        if (event.target === groupModal) {
            console.log("Clicked outside group modal");
            groupModal.style.display = "none";
        }
    });
});