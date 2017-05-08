var LOKAL = BASE_PATH;
//"http://localhost/koJeVlast/";
var OPSTINE = BASE_PATH + "api/opstine";
//var DATA_PATH = "data/podaciOpstina.json?2";

var FILES_PATH = "partials/"


$("#indikator").load(FILES_PATH + "legenda.html")

$.get(FILES_PATH + "modal.html", function(data) {
    $("#mainWrapper").append(data);
});

var base_selektor = "g:not([id='granice'])";


function mouseEvents(selektor) {

    $(selektor)
        .on("mouseover.boja", function() {

            trenutna_boja = $(this).css("fill");
            $(this).css("fill", "orange");

            //treba proslediti id regiona kad je region aktivan
            var od = $(this).attr("opstina");
            var tip = "opstina";

            if (od == undefined) {
                od = $(this).parent().attr("okrug");
                tip = "region";
            }
            //console.log( $(this).parent().attr("okrug") );

            sideDetailsHandlerHover(od, tip);
        });

    $(selektor)
        .on("mouseleave.boja", function() {
            //uzeti koja je trenutna boja 

            $(this).css("fill", trenutna_boja);
            $(".detalji_title").html("")
            $("#detalji table").empty();
            //nedostupni podaci
        });
    //mora da bude i region detalji
    $(selektor).click(sideDetaljiHandlerClick);

}

//glavni load
$("#mapa").load("srbija.svg", function() {

    //$("#mapa").trigger("ucitano", ["Custom", "Event"]);
    var selektor = " #mapa path:not(#KiM path,#granice path),#mapa polygon:not(#KiM polygon,#granice polygon)";

    // var selektorRegion  = "[okrug] > path";

    mouseEvents(selektor);
    //    mouseEventsRegion( selektorRegion )     

    $("#mapa").prepend("<button class='prijavi btn btn-lg btn-danger'>Prijavi promenu</button>")

    $(".prijavi").click(function() {

        var url = "./posaljitePromenu.html";
        var win = window.open(url, '_blank');
        win.focus();

    })

    //anotate();
});


var trenutna_boja = "rgb(155, 227, 220)";

function resetColors(argument) {
    $(base_selektor + ">*").css("fill", "rgb(155, 227, 220)");
}


var sideDetaljiHandlerClick = function(event) {

    if ($('[opstina]').css('display') !== 'none') {
        opstinaDetaljiHandlerClick(event.target.getAttribute("opstina"));
    } else {
        regionDetailHandlerClick(event.target, "Naslov regiona");
    }
}


var regionDetailHandlerHover = function(elem, naslov) {

    var temp = elem; //id

    if (Number.isInteger(+elem)) {
        temp = elem;
    } else if (
        elem !== undefined && elem.tagName !== undefined 
        && 
        (elem.tagName == "path" || elem.tagName == "polygon") ) {

        temp = $(elem).parent().attr("okrug");
    }

    var sumed = podaciRegion(temp);

    if (sumed !== undefined) {
        sideDetails(sumed.naslov, sumed.vlast)
    }

}

var opstinaDetaljiHandlerClick = function(id_opstine) {

    var temp = $(this).attr('opstina');

    if (id_opstine !== undefined && Number.isInteger(+id_opstine)) {
        temp = id_opstine;
    }

    var naslov = "Naslov ";
    var podaci = DataStranke.getOpstine();
    var opstina = podaci.filter(function(el) {
        return +el.oidopstine == temp ;
    })

    var id = 0;

    if (opstina.length == 0) {
        return;
    }

    showModal(opstina);

}

function regionDetailHandlerClick(element, naslov_region) {
    var id = $(element).parent().attr('okrug');

    //sakrij podatke o budzetima i dokumentima
    //uzmi podatke o odbornicima za sve opstine

    //stranke u vlasti, populacija i povrsina
    var podaci = podaciRegion(id);
    podaci.ologo = "bb7a4496cbe2a3b397a38acda978c2a1e4b77f36.png"

    podaciAkteriRegion(id); //ajax zahtev - podaci za tabelu

    info_tab(podaci);

    showModalRegion(podaci.naslov, id);

}


function showModal(opstina) {
    //temp = podaci[random_index];
    opstina_temp = opstina[0]
    naslov = opstina_temp.opstina;
    id = opstina_temp.opid;
    idopstine = opstina_temp.oidopstine;

    podaciOdborniciOpstina(id); // id opstine
    naslov_modal(naslov);
    info_tab(opstina_temp);

    dokumenta(id);

    linkovi(idopstine, naslov);

    //var procenti = izracunajProcente( DataStranke.getOdbornici());

    $('#modal_id').modal('show')
}


function showModalRegion(naslov, id) {
    naslov_modal( naslov );
    podaciAkteriRegion(+id);
}

function linkovi(id, naslov) {
    $("#shareLink").attr("href", LOKAL + "opstina.php?id=" + id + "&naslov=" + naslov);
    $("#fbShare").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + LOKAL + "opstina.php?id=" + id + "&naslov=" + naslov);
    $("#twShare").attr("href", "https://twitter.com/intent/tweet?text=" + LOKAL + "opstina.php?id=" + id + "&naslov=" + naslov);
}

function procentiRegion(data) {

    var mapirani = data.map(function(d) {

        var temp = {
            stranka: d.snaziv || "Nepoznata"
        }

        return temp;
    });

    izracunajProcente(mapirani);
}


function info_tab(opstina) {

    var putanja_logo = "http://kojenavlasti.rs/files/logos/";
    var povrsina = opstina.opov + " km2";
    var broj_stanovnika = opstina.opop;

    $(".logo").attr("src", putanja_logo + opstina.ologo );

    $("#stanovnici").html(" " + broj_stanovnika.toLocaleString('de-DE'));
    $("#povrs").html(" " + povrsina);

}

function naslov_modal(naslov) {
    $(".modal-title").html(naslov);
}


function sideDetailsOpstina(idOpstine) {
    //stranke koje ucestvuju u vlasti 
    var podaciOpstine = DataStranke.get();

    var opstina = podaciOpstine.filter(function(el) {
        return el.idopstine == idOpstine;
    });

    if (opstina == undefined || 
        opstina == null || 
        opstina.length == 0)
        return;

    var stranke = opstina[0].vlast;

    if (stranke == undefined)
        return;

    naslov = opstina[0].opstina

    sideDetails(naslov, stranke);
}


function sideDetailsHandlerHover(id, op_ili_reg) {

    if (op_ili_reg == "opstina") {
        podaci = sideDetailsOpstina(id, "naslov")
    } else {
        regionDetailHandlerHover(id, "Naslov regiona");
    }

}

function sideDetails(naslov, stranke) {

    var naslov_detalji = "Stranke u vlasti"
    naslov_detalji += " " + naslov;

    $("#detalji table").empty();
    $(".detalji_title").html(naslov_detalji)

    for (var i = 0; i < stranke.length; i++) {

        if (stranke[i].naziv == undefined || stranke[i].naziv == null 
            || stranke[i].naziv == "Nepoznata" || stranke[i].naziv == "Stranka nije na listi")
            continue;

        $("#detalji table").append("<tr><td>" + stranke[i].naziv + "</td></tr>")
    }

}

function tabelaOdbornikaRegion(data) {
    //uraditi mapiranje podataka, a onda ih proslediti na tabela odbornici

    var mapirani = data.map(function(el) {
        var temp = {
            ime: el.aime,
            prezime: el.aprezime,
            stranka: el.snaziv,
            funkcija: el.funkcija,
            koalicija: el.knaziv/*pkoalicija*/,
            vlast: el.pnavlasti == 1 ? "vlast" : "opozicija",
            //promena : "",
            datrodj: el.arodjen,
            pol: el.apol,
            opstina: el.opstina

        }
        return temp;
    })

    tabelaOdbornika(mapirani);

}

function onlyUnique(value, index, self) { 
    return self.indexOf(value) === index;
}


function function_name(argument) {
    // body...
}



function sortiranjeOdbornika(data) {

//sve ostalo > odbornik > null || undefined    

    data.sort( function(a, b) {
      return b.funkcija.length - a.funkcija.length;
    })
    return data;
}

function statsOdbornici( podaci ) {
    var not_odbornici = 
        podaci.filter(function(la){
            return la.funkcija !=="Odbornik" && la.funkcija !== null
        });

    var vlast = 
        podaci.filter(function(la){
            return la.vlast =="vlast" 
        });
    //var unique = podaci.filter( onlyUnique );

    var stranke = _.uniqBy( podaci , 'stranka');    //nesto ne radi sa lodash-om
    var koalicija = _.uniqBy( podaci , 'koalicija');

    var stats_text = " Ukupan broj aktera: " + podaci.length;
    stats_text += "<br/>" +  " Ukupan broj aktera koji nisu odbornici: " + not_odbornici.length;
    stats_text += "<br/>" +  " Ukupan broj stranka: " + stranke.length;
    stats_text += "<br/>" +  " Ukupan broj koalicija: " + koalicija.length;
    stats_text += "<br/>" +  " Ukupan broj odbornika na vlasti: " + vlast.length;
    $("#statsOdb").html( stats_text );

}

function tabelaOdbornika(podaci) {
   
    $( '#tab table ' ).dataTable().api().clear();
    //soritranje, prvo funkcije na vlasti
    
    //$("#modal_id tbody").html( " " );
    
    //unique opstine

    var opstine_temp = "";

    if( podaci[0].opstina == '' )
        opstina_temp = "";

    statsOdbornici(podaci);

    

    //podaci = sortiranjeOdbornika( podaci );

    /*for (var i = 0; i < podaci.length; i++) {
        var temp = podaci[i];

        var stranka_temp = temp.stranka;

        if (stranka_temp == null || stranka_temp == undefined ) stranka_temp = "Nepoznata";
        if (temp.funkcija == null || temp.funkcija == undefined ) temp.funkcija = "Nepoznata";
        if (temp.koalicija == null || temp.koalicija == undefined ) temp.koalicija = "Nepoznata";
        if (temp.vlast == null || temp.vlast == undefined ) temp.vlast = "Nepoznata";

        //ako je datum 01.01 - -izbrisati to

        //setuj promenljive
        var jedan_red =
            '<tr class="single-row">' +
            '<td class="row-ime">' + temp.ime + " " + temp.prezime + '</td>' +
            '<td class="row-stranka">' + stranka_temp + '</td>' +
            '<td class="row-funkcija">' + temp.funkcija + '</td>' +
            '<td class="row-koalicija">' + temp.koalicija + '</td>' +
            '<td class="row-vlast">' + temp.vlast + '</td>' +
            '<td class="row-promena">' + '<a href="./posaljitePromenu.html" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>' + '</td>' +
            '</tr>';
        $("#modal_id tbody").append(jedan_red);


    }*/

    var columns =  [ 
        {
        "targets": 0,
        "data": "ime",
        "render": function ( data, type, full, meta ) {
            if (data == null || data == undefined ) 
                data = "Nepoznata";

                //console.log( "Ime Prezime:" + data );
            return data + " " + full.prezime;
            }

        },
        {
        "targets": 1,
        "data": "stranka",
        "render": function ( data, type, full, meta ) {
            if (data == null || data == undefined ) 
                data = "Nepoznata";

            //console.log( "stranka:" + data );
            return data;
            }

        },
        {
        "targets": 2,
        "data": "funkcija",
        "render": function ( data, type, full, meta ) {
            if (data == null || data == undefined ) 
                data = "Nepoznata";

            //console.log( "funkcija :" + data );
            return data;
            }

        },
        {
        "targets": 3,
        "data": "koalicija",
        "render": function ( data, type, full, meta ) {
            if (data == null || data == undefined ) 
                data = "Nepoznata";
            
            //console.log( "koalicija :" + data );

            return data;
            }

        },
        {
        "targets": 4,
        "data": "vlast",
        "render": function ( data, type, full, meta ) {
            if (data == null || data == undefined ) 
                data = "Nepoznata";

            //console.log( "vlast :" + data );
            return data;
            }

        },
         {
        "targets": 5,
        //"data": "promena",
        "render": function ( data, type, full, meta ) {
            //console.log( "promena :" + data );

            return '<a href="./posaljitePromenu.html" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>';
            }

        },

    ]

    $(".row-promena").click(function(ev) {
        ev.preventDefault();

        var upit = "";
        var kolone = ["ime", "stranka", "funkcija", "koalicija", "vlast", "promena"];

        var podaci = $(this).parent().children();

        podaci.each(function(i, el) {
            upit += kolone[i] + "=" + $(el).text() + "&";
        })

        upit = upit.replace("&promena= &", "");

        window.location.href = "./posaljitePromenu.html" + "?" + upit;

    })
    

    var table = $('#tab table').DataTable(
            {
                data:podaci,
                "columnDefs": columns, 
                destroy:true,
                "order": [ 2, "desc" ],
                 
            }
        );

    //mozda samo treba rucno izbrisati deo sa enumeracijom
    //table.destroy();
    //table.draw();
    /*$('#tab table').DataTable({
        "order": [[ 2, "desc" ]],
         paging: false,
        searching: false
    });*/

}


function initOpstine() {

    var ajax = new XMLHttpRequest();
    ajax.open("GET", OPSTINE, true);

    ajax.onload = function() {
        var list = JSON.parse(ajax.responseText).map(function(i) {
            return { label: i.opstina, value: i.opstina, data: i.oidopstine };
            //{label:i.opstina/*, value:i.oidopstine*/};
        });
        new Awesomplete(
            document.querySelector("#opstinaSearch"), {
                list: list
            });

        $("#opstinaSearch").on("awesomplete-selectcomplete", function(ev, datum) {
            var tekst = ev.originalEvent.text;

            var opstina_temp = list.filter(function(el) {
                return el.value == tekst });
            var query = "[opstina='" + opstina_temp[0].data + "']";

            $(query).css("fill", "red")

        })
    };
    ajax.send();
}

/*stranka na vlasti lista*/
function initStranka( ) {

    //napravis selekt opcije
    var data = DataStranke.getStranke();

    for (var i = 0; i < data.length; i++) {
        if (data[i].naziv == "Nepoznata" || data[i].naziv == null  || data[i].naziv == "null" || data[i].naziv == "Stranka nije na listi")
            continue;
        $("#stranka").append("<option value='" + data[i].id + "'>" + data[i].naziv + "</option>")

    }

}

function initRegioni( ) {
    var data = DataStranke.getOpstine();

    //trebaju mi unique regioni
    var okruzi = data.map(function(el, ind) {
        return { okrug: el.okrug, idOkrug: el.oidokruga }
    })

    var jedinstveni = _.uniqBy(okruzi,"idOkrug");
       
       
    DataStranke.setRegioni(jedinstveni);


    for (var i = 0; i < jedinstveni.length; i++) {
        $("#regioni").append("<option value='" + jedinstveni[i].idOkrug + "'>" + jedinstveni[i].okrug + "</option>")
    }
}
