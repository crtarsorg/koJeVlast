$("#indikator").load("forma.html")

$.get("modal.html", function(data) {
    $("#mainWrapper").append(data);
});


var base_selektor = "g:not([id='granice'])";

var Data = (function() {
    var _data = [];
    return {
        set: function(d) {
            _data = d;
        },

        get: function() {
            return _data;
        }
    }
})();


// struktura podatka u fajlu po opstini je losa
$.getJSON('data/poOpstiniNew.json?2', function(json, textStatus) {
    Data.set(json);
});

$("#mapa").load("srbija.svg", function() {

    //$("#mapa").trigger("ucitano", ["Custom", "Event"]);
    var selektor = " #mapa path:not(#KiM path,#granice path),#mapa polygon:not(#KiM polygon,#granice polygon)";

    mouseEvents(selektor);

    $("#mapa").prepend("<button class='prijavi btn btn-danger'>Prijavi promenu</button>")

    /*  $("#juzna_i_istocna_srbija_1>g").hide()
      $("#zapadna_backa_2>g").hide()
      */

    $(".prijavi").click(function() {

        var url = "http://163.172.168.118:8055/posaljiPromenu.html";
        var win = window.open(url, '_blank');
        win.focus();

    })
});


var trenutna_boja = "rgb(155, 227, 220)";

function mouseEvents(selektor) {

    $(selektor)
        .on("mouseover.boja", function() {
            trenutna_boja = $(this).css("fill");
            $(this).css("fill", "orange");
        });

    $(selektor)
        .on("mouseleave.boja", function() {
            //uzeti koja je trenutna boja 

            $(this).css("fill", trenutna_boja);
        });


    $(selektor).click(function() {
        var temp = $(this).parent()[0].id

        //uzmi podatke po opsitni
        //potrebi su dobri identifikatori na mapi; 
        //ali i u podacima
        var podaci = Data.get();

        var tempData = Object.keys(podaci);
        random_index = parseInt(Math.random() * tempData.length);

        random_index = tempData[random_index];

        console.log(random_index);
        console.log(tempData.length);

        temp = podaci[random_index];
        popuni_modal(temp);

        drawSvg();

        $('#modal_id').modal('show')

    });

}

function popuni_modal(pod_opstina) {

    //$var_novo = $(".single-row").clone();
    $("tbody").empty();
    var podaci_opstina = pod_opstina.podaci;

    for (var i = 0; i < podaci_opstina.length; i++) {
        var temp = podaci_opstina[i];

        //setuj promenljive
        var jedan_red =
            '<tr class="single-row">' +
            '<td class="row-ime">' + temp.ime_prezime + '</td>' +
            '<td class="row-stranka">' + temp.stranka + '</td>' +
            '<td class="row-funkcija">' + temp.funkcija + '</td>' +
            '<td class="row-koalicija">' + temp.koalicija + '</td>' +
            '<td class="row-vlast">' + temp.u_vlasti + '</td>' +
            '<td class="row-promena">' + '<a href="#" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>' + '</td>' +
            '</tr>';
        $("tbody").append(jedan_red);


    }


}

function resetColors(argument) {
    $(base_selektor + ">*").css("fill", "rgb(155, 227, 220)");
}
