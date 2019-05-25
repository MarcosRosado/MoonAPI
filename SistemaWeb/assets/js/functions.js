// volta ao topo da página
function resetPage(){
    document.getElementById('body').scrollIntoView();
}

// fecha o menu lateral no modo mobile
function fecharMenuLateral(){
    if (mobile_menu_visible == 1) {
        $('html').removeClass('nav-open');

        $('.close-layer').remove();
        setTimeout(function() {
            $toggle.removeClass('toggled');
        }, 400);

        mobile_menu_visible = 0;
    }
}

// reseta os timeouts para poupar recursos
function closeTimeouts() {
    // Set a fake timeout to get the highest timeout id
    var highestIntervalId = setInterval(";");
    for (var i = 0 ; i < highestIntervalId ; i++) {
        clearInterval(i);
    }
    var highestTimeoutId = setTimeout(";");
    for (var i = 0 ; i < highestTimeoutId ; i++) {
        clearTimeout(i);
    }
}