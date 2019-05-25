var myVar;

// esconde o loader após a página ter sido carregada, com um delay de 1s
function loader() {
    changeData();
    myVar = setTimeout(showPage, 2500);

}

function changeData(){
    $.ajax({
        url:"../private/page_index/session.php",
        type:"get",
        data:{"session":"verificar"},
        success:function (result) {
            console.log(result);
            const response = jQuery.parseJSON(result);
            if(response["status"] == "online" ) {
                document.getElementById("userLogin").innerHTML = "Bem vindo "+response["nomeUsuario"];
            }
            else{
                logout();
                window.location = "../LoginPage/index.html";
            }
        }
    })
}
function logout(){
    $.ajax({
        url:"../private/page_index/logout.php",
        type:"get",
        data:{"session":"logout"},
        success:function (result) {
            if(result == "sucesso"){
                window.location.href="../LoginPage/index.html";
            }
        }

    });
}

function showPage() {
    document.getElementById("loader-wrapper").style.display = "none";
    document.getElementById("main-panel").style.display = "block";
    document.getElementById("main-panel").removeAttribute("hidden");

}