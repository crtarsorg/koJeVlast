
var BASE_PATH =  "http://163.172.168.118:8055/";
var OPSTINE =  BASE_PATH + "admin/api/opstine";
var DATA_PATH = "data/podaciOpstina.json?2";

var FILES_PATH = "partials/"

var stranke_vlast = "data/dataApi.json";

$("#indikator").load(FILES_PATH +"forma.html")

$.get(FILES_PATH + "modal.html", function(data) {
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


var DataStranke = (function() {
    var _data = [];
    var _stranke = [];

    return {
        set: function(d) {
            _data = d;
        },

        get: function() {
            return _data;
        },

        setStranke: function(d) {
            _stranke = d;
        },

        getStranke: function() {
            return _stranke;
        }
    }
})();


// struktura podatka u fajlu po opstini je losa
$.getJSON(DATA_PATH, function(json, textStatus) {
    Data.set(json);
});

// struktura podatka u fajlu po opstini je losa
$.getJSON(stranke_vlast, function(json, textStatus) {
    DataStranke.set(json)

    var stranke = [];
    
    json.forEach(function(el, ind){stranke = _.union(stranke, el.vlast)})
    //uzmi stranke na vlasti

    DataStranke.setStranke(stranke);
});



//glavni load
$("#mapa").load("srbija.svg", function() {

    //$("#mapa").trigger("ucitano", ["Custom", "Event"]);
    var selektor = " #mapa path:not(#KiM path,#granice path),#mapa polygon:not(#KiM polygon,#granice polygon)";

    mouseEvents(selektor);

    $("#mapa").prepend("<button class='prijavi btn btn-danger'>Prijavi promenu</button>")

    /*  $("#juzna_i_istocna_srbija_1>g").hide()
      $("#zapadna_backa_2>g").hide()
      */

    $(".prijavi").click(function() {

        var url = BASE_PATH + "posaljiPromenu.html";
        var win = window.open(url, '_blank');
        win.focus();

    })

    initOpstine();
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
        var temp = $(this).attr('opstina')
        var naslov = "Naslov ";
      
        //uzmi podatke po opsitni
        //potrebi su dobri identifikatori na mapi; 
        //ali i u podacima
        var podaci = DataStranke.get();
        
        var opstina = podaci.filter(function(el){return el.idopstine == temp})
        if(opstina.length > 0 )
            naslov = opstina[0].opstina;
        //temp = podaci[random_index];
        popuni_modal(podaci, naslov);

        drawSvg();

        $('#modal_id').modal('show')

    });

}

function popuni_modal(odb_opstina, naslov) {

    $(".modal-title").html( naslov );

    //$var_novo = $(".single-row").clone();
    $("tbody").empty();
    var podaci_opstina = odb_opstina;

    for (var i = 0; i < podaci_opstina.length; i++) {
        var temp = podaci_opstina[i];

        //setuj promenljive
        var jedan_red =
            '<tr class="single-row">' +
            '<td class="row-ime">' + temp.ime + " " + temp.prezime + '</td>' +
            '<td class="row-stranka">' + temp.stranka + '</td>' +
            '<td class="row-funkcija">' + temp.funkcija + '</td>' +
            '<td class="row-koalicija">' + temp.datrodj + '</td>' +
            '<td class="row-vlast">' + temp.pol + '</td>' +
            '<td class="row-promena">' + '<a href="#" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>' + '</td>' +
            '</tr>';
        $("tbody").append(jedan_red);


    }


}

function resetColors(argument) {
    $(base_selektor + ">*").css("fill", "rgb(155, 227, 220)");
}



function initOpstine() {

    var ajax = new XMLHttpRequest();
    ajax.open("GET", OPSTINE, true);

    ajax.onload = function() {
        var list = JSON.parse(ajax.responseText).map(function(i) { 
            return {label:i.opstina,value:i.opstina,data:i.oidopstine};      
        //{label:i.opstina/*, value:i.oidopstine*/};
         });
        new Awesomplete(
            document.querySelector("#opstinaSearch"),
            {  list: list           
        });

        $("#opstinaSearch").on("awesomplete-selectcomplete", function (ev, datum) {
            var tekst = ev.originalEvent.text;

            var opstina_temp  =  list.filter(function(el) {return el.value == tekst }); 
            var query = "[opstina='"+opstina_temp[0].data+"']";
            console.log(query);
            $( query).css("fill","red")

        })
    };
    ajax.send();
}
