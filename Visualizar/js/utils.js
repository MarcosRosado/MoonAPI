"use strict";

function findGetParameter(parameterName) {
  var result = null,
      tmp = [];
  location.search
      .substr(1)
      .split("&")
      .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
      });
  return result;
}


// executa a função de alterar a data de exibição dos dados
function reloadPageWithDate(){
  let shareid = findGetParameter("shareId");
  var DataUpdate = document.getElementById("dataUpdate").value;
  console.log(DataUpdate);
  let timestampDate = Date.parse(DataUpdate);
  timestampDate = timestampDate/1000;
  timestampDate = timestampDate + 26*60*60 + 59*60; //adiciona 3h e 59 minutos para chegar proximo da meia noite
  window.location.href = "http://35.245.96.146/MoonProject/Visualizar/?shareId="+shareid+"&timestamp="+timestampDate;
}

