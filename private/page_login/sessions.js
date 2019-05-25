window.onload = function () {
    checkSession();
};

function checkSession() {
    $.ajax({
        url: "../private/page_login/session.php",
        type: "get",
        data: {"session": "verificar"},
        success: function (result) {
            if (result == "SessionOnline")
                window.location.href = "../SistemaWeb/index.html";
        }
    })

}