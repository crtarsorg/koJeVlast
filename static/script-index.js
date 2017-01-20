
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
    var _Opstine = [];

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
        },

        setOpstine: function(d) {
            _Opstine = d;
        },

        getOpstine: function() {
            return _Opstine;
        }
    }
})();



function podaciOdborniciOpstina(idOpstine) {
    $.getJSON(BASE_PATH + "admin/api/akteriPoOpstini/"+idOpstine, function(json, textStatus) {
        $("tbody").empty();

        tabelaOdbornika(json)
    });
}



// struktura podatka u fajlu po opstini je losa
$.getJSON(BASE_PATH +"admin/api/opstine", function(json, textStatus) {
    DataStranke.setOpstine(json);
});

$.getJSON(DATA_PATH, function(json, textStatus) {
    Data.set(json);
});


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

    $("#mapa").prepend("<button class='prijavi btn btn-lg btn-danger'>Prijavi promenu</button>")

    /*  $("#juzna_i_istocna_srbija_1>g").hide()
      $("#zapadna_backa_2>g").hide()
      */

    $(".prijavi").click(function() {

        var url =  "./posaljitePromenu.html";
        var win = window.open(url, '_blank');
        win.focus();

    })

    initOpstine();
    initStranka();
    initRegioni();
});


var trenutna_boja = "rgb(155, 227, 220)";

function mouseEvents(selektor) {

    $(selektor)
        .on("mouseover.boja", function() {
            trenutna_boja = $(this).css("fill");
            $(this).css("fill", "orange");

            sideDetails($(this).attr("opstina"));
        });

    $(selektor)
        .on("mouseleave.boja", function() {
            //uzeti koja je trenutna boja 

            $(this).css("fill", trenutna_boja);
             $(".detalji_title").html("")
        });


    $(selektor).click(function() {
        var temp = $(this).attr('opstina')
        var naslov = "Naslov ";
        
        var odbornici = Data.get();
        var podaci = DataStranke.getOpstine();
        
        var opstina = podaci.filter(function(el){return el.oidopstine == temp})
        
        
        var id = 0;
        if(opstina.length > 0 ){
            naslov = opstina[0].opstina;
            id = opstina[0].opid;
        }
        //temp = podaci[random_index];
                
        podaciOdborniciOpstina( id );// id opstine
        naslov_modal( naslov);

        drawSvg();

        $('#modal_id').modal('show')

    });

}

function naslov_modal(naslov) {
    $(".modal-title").html( naslov );    
    
}


function sideDetails(idOpstine) {

    var naslov_detalji = "Stranke u vlasti"


    $("#detalji table").empty();
    //stranke koje ucestvuju u vlasti 
    var podaciOpstine = DataStranke.get();

    var opstina = podaciOpstine.filter(function(el) {
        return el.idopstine == idOpstine ;
    });

    if(opstina == undefined 
        || opstina ==null 
        || opstina.length==0) 
            return;

    var stranke = opstina[0].vlast;

    if(stranke ==undefined) 
        return;

    naslov_detalji += " " + opstina[0].opstina
    $(".detalji_title").html(naslov_detalji)

    for (var i = 0; i < stranke.length; i++) {
        
        $("#detalji table").append("<tr><td>"+stranke[i]+"</td></tr>")
    }
    
}

function tabelaOdbornika(podaci) {


    //ovde bi trebali ubaciti data tables 
    //$var_novo = $(".single-row").clone();
   

    for (var i = 0; i < podaci.length; i++) {
        var temp = podaci[i];

        //setuj promenljive
        var jedan_red =
            '<tr class="single-row">' +
            '<td class="row-ime">' + temp.ime + " " + temp.prezime + '</td>' +
            '<td class="row-stranka">' + temp.stranka + '</td>' +
            '<td class="row-funkcija">' + temp.funkcija + '</td>' +
            '<td class="row-koalicija">' + temp.datrodj + '</td>' +
            '<td class="row-vlast">' + temp.pol + '</td>' +
            '<td class="row-promena">' + '<a href="./posaljitePromenu.html" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>' + '</td>' +
            '</tr>';
        $(".modal tbody").append(jedan_red);


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
            
            $( query).css("fill","red")

        })
    };
    ajax.send();
}



function initStranka(argument) {
    //napravis selekt opcije
    var data = DataStranke.getStranke();

    for (var i = 0; i < data.length; i++) {
        $("#stranka").append("<option value='"+data[i]+"'>"+data[i]+"</option>")
        
    }

    //
}

function initRegioni(argument) {
    var data = DataStranke.getOpstine();

    //trebaju mi unique regioni
    var okruzi = data.map(function (el, ind) {
        return {okrug:el.okrug, idOkrug:el.oidokruga}
    })

    
    var jedinstveni = _.uniqBy(okruzi, 'okrug' )

    for (var i = 0; i < jedinstveni.length; i++) {
        $("#regioni").append("<option value='"+jedinstveni[i].idOkrug+"'>"+jedinstveni[i].okrug+"</option>")        
    }
}