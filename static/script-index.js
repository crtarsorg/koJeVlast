var BASE_PATH =  "http://kojenavlasti.rs/";
var BASE_PATH =  window.location.protocol+"//"+window.location.hostname+"/";


var LOKAL = BASE_PATH;
//"http://localhost/koJeVlast/";
var OPSTINE = BASE_PATH + "api/opstine";
//var DATA_PATH = "data/podaciOpstina.json?2";

var FILES_PATH = "partials/"


$("#indikator").load(FILES_PATH + "legenda.html?2")

$.get(FILES_PATH + "modal.html?17", function(data) {
    $("#mainWrapper").append(data);
});

var base_selektor = "g:not([id='granice'])";



//glavni load
$("#mapa").load("srbija.svg?1", function() {

    //$("#mapa").trigger("ucitano", ["Custom", "Event"]);
    var selektor = " #mapa path:not(#KiM path,#granice path),#mapa polygon:not(#KiM polygon,#granice polygon)";

    // var selektorRegion  = "[okrug] > path";

    mouseEvents(selektor);
    //    mouseEventsRegion( selektorRegion )

    //$("#mapa").prepend("<button class='prijavi btn btn-lg btn-danger'>Prijavi promenu</button>")

    $(".prijavi").click(function() {
        //var url = "./posaljitePromenu.html";
        var win = window.open(url, '_blank');
        win.focus();

    })

    var zoom = svgPanZoom("#mapa svg")

    //anotate();
    var popup = "<div class='popup'><span class='popup-close'><i class='fa fa-times' aria-hidden='true'></i></span><p>Pređite mišem preko mape, kliknite na opštinu/okrug pogledajte info o... prijavite promenu</p></div>";
    $("#mapa").prepend(popup);

    closePopup();

    $("#mapa").trigger( "ucitano" );
});


var trenutna_boja = "rgb(155, 227, 220)";


function showModal(opstina) {
    //temp = podaci[random_index];
    opstina_temp = opstina[0]
    naslov = opstina_temp.opstina;
    id = opstina_temp.opid;
    idopstine = + opstina_temp.oidopstine;

    podaciOdborniciOpstina(id); // id opstine
    naslov_modal(naslov);
    info_tab(opstina_temp);

    dokumenta(id);

    //linkovi(idopstine, naslov);
    //send opid
    linkovi(opstina_temp.opid, naslov, false);
    prikazTabovaOpstine(true);

    //var procenti = izracunajProcente( DataStranke.getOdbornici());

    $("#spinner, #fade").toggleClass('hidden');
    $('#modal_id').modal('show');
}


function showModalRegion(naslov, id) {
    naslov_modal(naslov);
    linkovi(id, naslov, true)
    podaciAkteriRegion(+id);

    prikazTabovaOpstine(false);
}

function prikazTabovaOpstine(isOpstina) {
    if(isOpstina){
        $("li a[href='#budzet'], li a[href='#izbori']").parent().show()
    }
    else{
        $("li a[href='#budzet'], li a[href='#izbori']").parent().hide()
    }
}

function linkovi(id, naslov, okrugT) {

    var sufix = "id";
    if(okrugT)
        sufix = "okrug";

    $("#shareLink").attr("href", LOKAL + "opstina.php?"+sufix+"=" + id);
    $("#fbShare").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + LOKAL + "opstina.php?"+sufix+"=" + id);
    $("#twShare").attr("href", "https://twitter.com/intent/tweet?text=" + LOKAL + "opstina.php?"+sufix+"=" + id);
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

    $(".logo").attr("src", putanja_logo + opstina.ologo);

    $("#stanovnici").html(" " + broj_stanovnika.toLocaleString('de-DE'));
    $("#povrs").html(" " + povrsina);

}

function naslov_modal(naslov) {
    $(".modal-title").html(naslov);
}




function sideDetails(naslov, stranke) {

    var naslov_detalji = "Stranke u vlasti"
    naslov_detalji += " " + naslov;

    $("#detalji table").empty();
    $(".detalji_title").html(naslov_detalji)


    //grupisi nepoznate stranke
    var nepoznate = stranke.filter(function(el) {
        return el.naziv == "Nepoznata";
    })
   

    stranke.sort(function(a, b) {
      return b.odbornika - a.odbornika;
    });
    for (var i = 0; i < stranke.length; i++) {

        //preskakanje
        if (stranke[i].naziv == undefined || stranke[i].naziv == null 
            || stranke[i].naziv == "Nepoznata" /*|| stranke[i].naziv == "Stranka nije na listi"*/)
            continue;

        $("#detalji table").append("<tr><td>" + stranke[i].naziv + "<span class='broj-odbornika'>(" +  stranke[i].odbornika +  ")</span></td></tr>")
    }

    if(nepoznate.length >0){
        var nepoznate_br = nepoznate.reduce(function(a,b){return a.odbornika+b.odbornika});
        if(nepoznate_br > 0 )
            $("#detalji table").append("<tr><td> Nepoznata <span class='broj-odbornika'>(" +  nepoznate_br +  ")</span></td></tr>")
        }
    $("#opis").removeClass('hidden')

}

function tabelaOdbornikaRegion(data) {
    //uraditi mapiranje podataka, a onda ih proslediti na tabela odbornici

    var mapirani = data.map(function(el) {
        var temp = {
            ime: el.aime,
            prezime: el.aprezime,
            stranka: el.snaziv,
            funkcija: el.funkcija,
            koalicija: el.knaziv /*pkoalicija*/ ,
            vlast: el.pnavlasti == 1 ? "vlast" : "opozicija",
            //promena : "",
            datrodj: el.arodjen,
            pol: el.apol,
            opstina: el.opstina

        }
        return temp;
    })

    tabelaOdbornika(mapirani, true);

}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}


var funkcija_odbornik = [1,2,3,4,6]

function statsOdbornici(podaci) {
    var odbornici_yes =
        podaci.filter(function(la) {
            return funkcija_odbornik.indexOf( +la.fid  ) >-1 ;
        });

    var vlast =
        podaci.filter(function(la) {
            return la.vlast == 1 && funkcija_odbornik.indexOf( +la.fid  ) >-1;
            //"vlast"
        });
    //var unique = podaci.filter( onlyUnique );

    var stranke = _.uniqBy(podaci, 'stranka'); //nesto ne radi sa lodash-om
    var koalicija = _.uniqBy(podaci, 'koalicija');


    $("#brAktera").html( podaci.length );
    $("#brOdbornici").html(  odbornici_yes.length );
    $("#brStranka").html( stranke.length );
    $("#brKoalcija").html( koalicija.length );
    $("#brVlasti").html( vlast.length );

    /*var stats_text = " Ukupan broj aktera: " + podaci.length;
    stats_text += "<br/>" + " Ukupan broj aktera koji nisu odbornici: " + not_odbornici.length;
    stats_text += "<br/>" + " Ukupan broj stranka: " + stranke.length;
    stats_text += "<br/>" + " Ukupan broj koalicija: " + koalicija.length;
    stats_text += "<br/>" + " Ukupan broj odbornika na vlasti: " + vlast.length;
    $("#statsOdb").html(stats_text);*/

}


function skraceno( data ) {
    var naziv = data ;

    if( data == null || data == undefined)
        return "nepoznato";

    if(data.length > 50){
        naziv = data.substr(0, 50);

        naziv = naziv.substr(0, naziv.lastIndexOf(" "))
        naziv+= " ... "; //verovatno treba ovde upitnik
    }
    return naziv;
}

function tabelaOdbornika(podaci, region) {

    $('#tab table ').dataTable().api().clear();

    var opstine_temp = "";



    if (podaci[0].opstina == '')
        opstina_temp = "";

    statsOdbornici(podaci);

    if(region){
        $("#opstina-col").html("Opština")
    }
    else{
        $("#opstina-col").html("Datum")
    }

    var columns = [{
            "targets": 0,
            "data": "ime",
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined)
                    data = "Nepoznata";
                var unos = data + " " + full.prezime;
                if(full.promena){
                    unos = '<a href="http://kojenavlasti.rs/promeneAkter.php?id='+full.id+'"  target="_blank" class="expandOld" id="'+full.id+'">'+ unos +'</a>'
                }
                //console.log( "Ime Prezime:" + data );
                return unos;
            }

        }, {
            "targets": 1,
            "data": "stranka",
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined)
                    data = "Nepoznata";

                //console.log( "stranka:" + data );
                return data;
            }

        }, {
            "targets": 2,
            "data": "funkcija",
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined)
                    data = "Nepoznata";

                //console.log( "funkcija :" + data );
                return data;
            }

        },

         {
            "targets": 3,
            "data": "koalicija",
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined)
                    data = "Bez koalicije";

                //bez koalicije
                var naziv = data;
                naziv = skraceno( data );

                return  "<span title='"+data+"'>"+naziv+"</span>";
            }

        }, {
            "targets": 4,
            "data": "vlast",
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined || +data == 0)
                    return  "Nepoznata";
                if(+data == 1) return "vlast";
                if(+data == 2) return "opozicija";

                //console.log( "vlast :" + data );
                return data;
            }

        },  {
            "targets": 5,
            "data": function(data, type, full, meta){
                if(region ) return data.opstina;
                return new Date(data.datum).toLocaleDateString('sr');
            },
            "render": function(data, type, full, meta) {
                //console.log( "promena :" + data );

                return data;
            }

        },
        {
            "targets": 6,
            //"data": "promena",
            "render": function(data, type, full, meta) {
                //console.log( "promena :" + data );

                return '<a href="./api/posaljitePromenu?ime='+full.ime + " "+full.prezime+"&opstina="+full.opstina + '" target="_blank">'+
                ' <img src="static/promene.svg" style="width: 22px;"/></a>';
            }

        },
         {
            "targets": 7,
            "data": "fid",
            "visible": false,
            "render": function(data, type, full, meta) {
                if (data == null || data == undefined)
                    data = 0;

                //console.log( "funkcija :" + data );
                return data;
            }

        }

    ]


    //hook for passing params
    $(".row-promena").click(function(ev) {
        ev.preventDefault();

        var upit = "";
        var kolone = ["ime", "stranka", "funkcija", "koalicija", "vlast", "promena","fid"];

        var podaci = $(this).parent().children();

        podaci.each(function(i, el) {
            upit += kolone[i] + "=" + $(el).text() + "&";
        })

        upit = upit.replace("&promena= &", "");

        window.location.href = "./posaljitePromenu.html" + "?" + upit;

    })

    var imajuPromene = DataStranke.getPromene();

    podaci = podaci.map(function(elem) {
        var promena = imajuPromene.filter(function(el) {
            return el.id == elem.id;
        });

        if (promena.length > 0)
            elem.promena = true;

        return elem;
    })

    podaci =  podaci.sort(function(a,b){return b.fid - a.fid})

    var table = $('#tab table').DataTable({
        data: podaci,
        "columnDefs": columns,
        destroy: true,
        "order": [[7, "desc"], [0, "asc"]],
        /*"scrollX": true,*/
        initComplete:drawCallbackHandler,
        "language": jezik,

    });



}

var drawCallbackHandler = function (ev) {

    }


function initOpstine() {

    var ajax = new XMLHttpRequest();
    ajax.open("GET", OPSTINE, true);

    ajax.onload = function() {
        var list = JSON.parse(ajax.responseText).map(function(i) {
            if(+i.oidopstine == 79014) return "";
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
                return el.value == tekst
            });
            var query = "[opstina='" + opstina_temp[0].data + "']";

            $(query).css("fill", "red")

        })
    };
    ajax.send();
}

/*stranka na vlasti lista*/
function initStranka() {

    //napravis selekt opcije
    var data = DataStranke.getStranke();

    for (var i = 0; i < data.length; i++) {
        if (data[i].naziv == "Nepoznata" || data[i].naziv == null || data[i].naziv == "null" || data[i].naziv == "Stranka nije na listi" || data[i].id ==200)
            continue;
        $("#stranka").append("<option value='" + data[i].id + "'>" + data[i].naziv + "</option>")

    }

}

function initRegioni() {
    var data = DataStranke.getOpstine();

    //trebaju mi unique regioni
    var okruzi = data.map(function(el, ind) {
        return { okrug: el.okrug, idOkrug: el.oidokruga }
    })

    var jedinstveni = _.uniqBy(okruzi, "idOkrug");


    DataStranke.setRegioni(jedinstveni);


    for (var i = 0; i < jedinstveni.length; i++) {
        $("#regioni").append("<option value='" + jedinstveni[i].idOkrug + "'>" + jedinstveni[i].okrug + "</option>")
    }
}