var BASE_PATH =  "http://kojenavlasti.rs/";

var LOKAL = BASE_PATH;
//"http://localhost/koJeVlast/";
var OPSTINE = BASE_PATH + "api/opstine";
//var DATA_PATH = "data/podaciOpstina.json?2";

var FILES_PATH = "partials/"


$("#indikator").load(FILES_PATH + "legenda.html?2")

$.get(FILES_PATH + "modal.html?3", function(data) {
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

    linkovi(idopstine, naslov);

    //var procenti = izracunajProcente( DataStranke.getOdbornici());

    $("#spinner, #fade").toggleClass('hidden');
    $('#modal_id').modal('show');
}


function showModalRegion(naslov, id) {
    naslov_modal(naslov);
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


function statsOdbornici(podaci) {
    var not_odbornici =
        podaci.filter(function(la) {
            return la.funkcija !== "Odbornik" && la.funkcija !== null
        });

    var vlast =
        podaci.filter(function(la) {
            return la.vlast == 1;
            //"vlast"
        });
    //var unique = podaci.filter( onlyUnique );

    var stranke = _.uniqBy(podaci, 'stranka'); //nesto ne radi sa lodash-om
    var koalicija = _.uniqBy(podaci, 'koalicija');


    $("#brAktera").html( podaci.length );
    $("#brNotOdbornici").html( not_odbornici.length );
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
                    unos = '<a href="#" class="expand" id="'+full.id+'">'+ unos +'</a>'
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

        }, {
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
                return data.datum;
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

                return '<a href="./posaljite-promenu.php?ime='+full.ime + " "+full.prezime+"&opstina="+full.opstina + '" target="_blank">'+
                ' <img src="static/promene.svg" style="width: 22px;"/></a>';
            }

        }

    ]


    //hook for passing params
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

    var imajuPromene = DataStranke.getPromene();

    podaci = podaci.map(function(elem) {
        var promena = imajuPromene.filter(function(el) {
            return el.id == elem.id;
        });

        if (promena.length > 0)
            elem.promena = true;

        return elem;
    })

    var table = $('#tab table').DataTable({
        data: podaci,
        "columnDefs": columns,
        destroy: true,
        "order": [2, "desc"],
        /*"scrollX": true,*/
        initComplete:drawCallbackHandler,
        "language": {
            "search": "Pretražite:",
            "lengthMenu": "Prikazano _MENU_ unosa po strani",
            "zeroRecords": "Nema unosa ",
            "info": "Prikazana strana _PAGE_ od _PAGES_",
            "infoEmpty": "Nema unosa",
            "infoFiltered": "(filtrirano od dostupnih _MAX_ unosa)"
          },

    });



}

var drawCallbackHandler = function (ev) {

        $(".expand").click(function (ev) {
            ev.preventDefault();

            $this = $(ev.target);

            //treba uzeti i id
            var id = $this.attr('id');

            var $dodat = $this.parents('tr').nextAll(".added-"+id)

            if($dodat.length >0){
                $dodat.toggle()
                return;
            }

            //morao bih pitati da li postoji, ako postoji,
            //ne trazi podatke sa servera


            $.getJSON("http://kojenavlasti.rs/api/akter/promene/"+id, function(json, textStatus) {


                //treba proveriti da li se vec nalazi tu neki element
                //ako vec postoji jedan added, onda nemoj da dodajes dalje

                // prvi unos je trenutni, njega ne uzimam
                json.shift();

                //appenduj nesto da se jasno vide  datumi promena
                for (var i = 0; i < json.length; i++) {

                    var $temp = $("<tr class='added added-"+id+"'></tr>");
                    $this.parents('tr').after( $temp );

                    var tempPdo = "";

                    if(json[i].pdo !==null && json[i].pdo!== undefined)
                        tempPdo = " - " + json[i].pdo;


                        /* + json[i].aime +" "
                        + json[i].aprezime +" "*/

                    koalicija = skraceno( json[i].knaziv );
                    stranka = json[i].snaziv == null? "Nepoznata": json[i].snaziv;
                    funkcija = json[i].funkcija  == null? "Nepoznata": json[i].funkcija;
                    vlast = json[i].pnavlasti ==1 ? "vlast": "opozicija";

                    var msg =
                        "<td>" + json[i].aime +" "
                                + json[i].aprezime + "</td>"
                        +"<td>" + stranka + "</td>"
                        +"<td>" + funkcija + "</td>"
                        +"<td title='"+json[i].knaziv+"'>" + koalicija + "</td>"
                        +"<td>"+vlast+"</td>"
                        +'<td><a href="./posaljitePromenu.html" target="_blank">  <img src="static/promene.svg" style="width: 22px;"/></a></td>'
                        +"<td>" + json[i].pod + "<br/>" + tempPdo + "</td>"
                        ;

                        //.find("td")
                        $temp.append(msg);
                }

            });
        })
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