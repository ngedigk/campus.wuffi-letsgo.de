function toggleDropdown() {
    document.getElementById('profileContainer').classList.toggle('active');
}

window.onclick = function(event) {
    if (!event.target.closest('.profile-icon')) {
        const container = document.getElementById('profileContainer');
        if (container && container.classList.contains('active')) {
            container.classList.remove('active');
        }
    }
}