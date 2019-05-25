

$(document).ready(function () {
    //dashboard
    $("#dashboard").click(function () {
        $("#dashboardLI").addClass("nav-item active");
        $("#cadastroDevicesLi").removeClass("nav-item active");
        closeTimeouts();
        $("#body").load("pages/cadastroPessoal.html");
        fecharMenuLateral();
        resetPage();

    });

    $("#cadastroDevices").click(function () {
        $("#cadastroDevicesLi").addClass("nav-item active");
        $("#dashboardLI").removeClass("nav-item active");
        closeTimeouts();
        $("#body").load("pages/cadastroDevices.html");
        fecharMenuLateral();
        resetPage();
    });


    // faz logout e limpa a sessão
    $("#logout").click(function () {
        $.ajax({
            url:"../private/page_index/logout.php",
            type:"get",
            data:{"session":"logout"},
            success:function (result) {
                if(result == "sucesso"){
                    window.location.href="../LoginPage/index.html";
                }
            }

        })

    });
    
});

