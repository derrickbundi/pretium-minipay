let mode = 'dark';

function saveLayoutMode(mode) {
    localStorage.setItem('layoutMode', mode);
}
  
function loadLayoutMode() {
    return localStorage.getItem('layoutMode') || 'dark';
}

// document.addEventListener('DOMContentLoaded', function() {
//     const savedMode = loadLayoutMode();
//     document.documentElement.setAttribute('data-layout-mode', savedMode);
// });

function changeLayoutMode(mode) {
    document.documentElement.setAttribute('data-layout-mode', mode);
    saveLayoutMode(mode);
}

console.log("hit here")