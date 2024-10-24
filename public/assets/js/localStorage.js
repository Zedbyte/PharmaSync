(function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.classList.add(savedTheme);
    } else {
        document.documentElement.classList.add('light'); // default theme
    }
})();