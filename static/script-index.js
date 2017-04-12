
var BASE_PATH =  "http://kojenavlasti.rs/";
var LOKAL =  BASE_PATH;
//"http://localhost/koJeVlast/";
var OPSTINE =  BASE_PATH + "api/opstine";
//var DATA_PATH = "data/podaciOpstina.json?2";

var FILES_PATH = "partials/"

var stranke_vlast = BASE_PATH + "api/strankaNaVlasti";

$("#indikator").load(FILES_PATH +"legenda.html")

$.get(FILES_PATH + "modal.html", function(data) {
    $("#mainWrapper").append(data);
});


var base_selektor = "g:not([id='granice'])";


var DataStranke = (function() {
    var _data = [];
    var _stranke = [];
    var _Opstine = [];
    var _odbornici = [];

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

        setOdbornici: function(d) {
            _odbornici = d;
        },

        getOdbornici: function() {
            return _odbornici;
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
    $.getJSON( BASE_PATH + "api/akteriPoOpstini/"+idOpstine, function(json, textStatus) {
        $("tbody").empty();

        izracunajProcente(json);
        DataStranke.setOdbornici(json);

        tabelaOdbornika(json)
    });
}



// struktura podatka u fajlu po opstini je losa
$.getJSON(BASE_PATH +"api/opstine", function(json, textStatus) {
    DataStranke.setOpstine(json);
    initOpstine();
    initRegioni();
});

/*$.getJSON(DATA_PATH, function(json, textStatus) {
    Data.set(json);
});*/


$.getJSON(stranke_vlast, function(json, textStatus) {
    DataStranke.set(json)

    var stranke = [];
    
    json.forEach(function(el, ind){stranke = _.union(stranke, el.vlast)})
    //uzmi stranke na vlasti

    DataStranke.setStranke(stranke);
    initStranka();
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

    
});


var trenutna_boja = "rgb(155, 227, 220)";


var sideDetaljiHandler = function ( event ) {
   
    if( $('[opstina]').css('display') !== 'none' ){
        opstinaDetaljiHandler( event.target.getAttribute("opstina") );        
    }
    else {
        regionDetailHandler( event.target );
    }
}

var regionDetailHandler = function ( elem ) {
    var temp = $( elem ).parent().attr("okrug");

    //okrug
    //alert( "Okrug" + temp );  

    var podaci_po_okrugu = DataStranke.getOpstine();
    var grupisane = _.groupBy(podaci_po_okrugu, function (el) {
       return el.oidokruga;
    })

    var filterd = (grupisane[+temp]);
    agregacija_okrug(filterd)

    //agregacija - izracunavanje svih parametara
    
}


function agregacija_okrug( opstine ) {
    
    var stranke = DataStranke.get();

    

    var init = {opop:0,opov:0,vlast:[]};

    var rez  = opstine.reduce(function ( a, b) {
        var temp = {};
        temp.opop = +a.opop + +b.opop;
        temp.opov = +a.opov + +b.opov;

        var a_stranke =
            stranke.filter(function(c){return c.idopstine == a.oidopstine});
        var b_stranke =
            stranke.filter(function(c){return c.idopstine == b.oidopstine});    

        var temp_ar = [];
        var temp_ar2 = []
            
            if( a_stranke.length !== 0 && a_stranke[0].vlast !== undefined) 
                temp_ar = a_stranke[0].vlast
            if( b_stranke .length !== 0 && b_stranke[0].vlast !== undefined){
                temp_ar2 = b_stranke[0].vlast
            }
        //za neke opstine nema podataka
        //    

        temp.vlast = temp_ar2.concat( temp_ar ) ;

        //broj - count
        //opop
        //opov
        

        //i trebaju mi podaci o strankama na vlasti u tim opstinama
        //za svaku opstinu
        return temp;
    } , init);

    console.log( rez );
}

var opstinaDetaljiHandler = function( id_opstine) {
        
        var temp = $(this).attr('opstina');

        if(id_opstine !== undefined && Number.isInteger(+id_opstine)){
            temp = id_opstine;
        }

        var naslov = "Naslov ";
        var podaci = DataStranke.getOpstine();        
        var opstina = podaci.filter(function(el){return +el.oidopstine == temp})
        
        
        var id = 0;

        if(opstina.length == 0 ){
            return;
        }

        //temp = podaci[random_index];
        opstina_temp = opstina[0]
        naslov = opstina_temp.opstina;
        id = opstina_temp.opid;
        idopstine = opstina_temp.oidopstine;
                
        podaciOdborniciOpstina( id );// id opstine
        naslov_modal( naslov );
        info_tab( opstina_temp );

        dokumenta( id );

        linkovi(idopstine, naslov);

        //var procenti = izracunajProcente( DataStranke.getOdbornici());

        

        $('#modal_id').modal('show')

    }

function linkovi( id, naslov) {
        $("#shareLink").attr("href", LOKAL + "opstina.php?id=" + id + "&naslov="+naslov);
        $("#fbShare").attr("href", "https://www.facebook.com/sharer/sharer.php?u="+LOKAL + "opstina.php?id=" + id + "&naslov="+naslov);
        $("#twShare").attr("href", "https://twitter.com/intent/tweet?text="+LOKAL + "opstina.php?id=" + id + "&naslov="+naslov);
    }    

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


        //mora da bude i region detalji
    $(selektor).click( sideDetaljiHandler  );

}

function izracunajProcente( podaci ) {
   //console.log('izracunavanje');     
   //console.log( podaci );     

   // nepoznate, null i sve ostalo stavljam u ostale

   var ukupno = podaci.length;

   var grupisane = _.groupBy(podaci, function (el) {
       return el.stranka;
   })

   var statistike = []

   Object.keys( grupisane ).forEach(function(el) {

        var centi = el.length ;
        //ostali
        //nepoznate i stranke koje nisu navedene transformisati u 'ostali'
        var sk_temp = el.replace("'","").split(' ').map(function(item){return item[0]}).join('').toLowerCase();

        if(el == "Stranka nije na listi" || el ==null || el =="Nepoznata") 
            {
                el = "Ostale stranke";
                sk_temp = "ostale";
            }

        var unos = {stranka: el ,skracenica: sk_temp, procenat: centi };    

        var nadjena = _.find(statistike, {skracenica:"ostale"});

        if(nadjena != undefined)
        {
            nadjena.procenat+= centi;
            //unos = nadjena;
        }
        else 
            statistike.push( unos );         
   })


   var sortirani = _.sortBy(statistike, function(el){return el.procenat});
   sortirani = sortirani.reverse();

   var svi = [];
   //uzeti samo prve tri i ostale
   if(sortirani.length > 4){
        //slice 4 do kraja 
        //i sve ih saberi
        svi = sortirani.slice(0,3);
        ostali = sortirani.slice(4);
        //sabrati sve procente
        var ukupno = 0;

        ostali.forEach(function(el) {
            ukupno+=el.procenat;
        })

        svi.push({stranka: "Ostale stranke" ,skracenica: "ostale", procenat: ukupno }    );

   }
   else
        svi = sortirani;

   //console.log( svi );


   drawSvg( svi );
}

function info_tab( opstina ) {

    var putanja_logo = "http://kojenavlasti.rs/files/logos/";
    
    $(".logo").attr("src", putanja_logo + opstina.ologo);

    var povrsina = opstina.opov + " km2";

    var broj_stanovnika = opstina.opop ;

    $("#stanovnici").html( " " +  broj_stanovnika.toLocaleString('de-DE') );
    $("#povrs").html( " " + povrsina );

}

function dokumenta( op_id ) {

    //http://admin.kojenavlasti.rs/admin/opstine/getDocs/71
    
    $.get( BASE_PATH + "admin/opstine/getDocs/"+ op_id, function( data ) {
      
      //$( ".result" ).html( data );
        //console.log( JSON.parse( data )  );
          //alert( "Load was performed." );
          data = JSON.parse(data);

          var budz_temp = "";
          var izvr_temp = "";
          var trans_temp = "";
          var prinuda_temp = "";

          for (var i = 0,  len = data.length; i < len ; i++) {
              if( data[i].opdkat == 1 ){
                    budz_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/"+data[i].opdfile+"'>"+data[i].opdnaziv+"</a><br/>";
              } 
              else if( data[i].opdkat == 5 ){
                    izvr_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/"+data[i].opdfile+"'>"+data[i].opdnaziv+"</a><br/>";    
              }
              else if( data[i].opdkat == 4 ){
                    trans_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/"+data[i].opdfile+"'>"+data[i].opdnaziv+"</a><br/>";
              }
              else if( data[i].opdkat == 2 ){
                    prinuda_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/"+data[i].opdfile+"'>"+data[i].opdnaziv+"</a><br/>";
              }
          }

          //rezultati izbora

        if(budz_temp =="") budz_temp = "<b>Nema dokumenata</b>";
        if(izvr_temp =="") izvr_temp = "<b>Nema dokumenata</b>";
        if(trans_temp =="") trans_temp = "<b>Nema dokumenata</b>";
        if(trans_temp =="") trans_temp = "<b>Nema dokumenata</b>";  

        $("#budzet_dokument").html( budz_temp );
        $("#izvrsenje_dokument").html( izvr_temp );
        $("#transfer_dokument").html( trans_temp );
        $("#prinudna_dokument").html( prinuda_temp );

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

    if(stranke == undefined) 
        return;

    naslov_detalji += " " + opstina[0].opstina
    $(".detalji_title").html(naslov_detalji)

    for (var i = 0; i < stranke.length; i++) {
        
        if(stranke[i] == undefined 
        || stranke[i] ==null 
        || stranke[i] =="Nepoznata"
        || stranke[i] =="Stranka nije na listi") 
            continue;
        $("#detalji table").append("<tr><td>"+stranke[i]+"</td></tr>")
    }
    
}

function tabelaOdbornika(podaci) {


    //ovde bi trebali ubaciti data tables 
    //$var_novo = $(".single-row").clone();
   

    for (var i = 0; i < podaci.length; i++) {
        var temp = podaci[i];

        var stranka_temp = temp.stranka;
        if(stranka_temp == null ) stranka_temp = "Nepoznata";
        //setuj promenljive
        var jedan_red =
            '<tr class="single-row">' +
            '<td class="row-ime">' + temp.ime + " " + temp.prezime + '</td>' +
            '<td class="row-stranka">' + stranka_temp + '</td>' +
            '<td class="row-funkcija">' + temp.funkcija + '</td>' +
            '<td class="row-koalicija">' + temp.datrodj + '</td>' +
            '<td class="row-vlast">' + temp.pol + '</td>' +
            '<td class="row-promena">' + '<a href="./posaljitePromenu.html" target="_blank"> <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></a>' + '</td>' +
            '</tr>';
        $("#modal_id tbody").append(jedan_red);


    }

    $(".row-promena").click(function(ev) {
        ev.preventDefault();

        var upit = "";
        var kolone = ["ime","stranka","funkcija","koalicija","vlast","promena"];

        var podaci =  $(this).parent().children();

        podaci.each(function (i,el) {
            upit += kolone[i] + "="+$(el).text()+"&";
        })

        upit = upit.replace("&promena= &","");
        
        window.location.href =  "./posaljitePromenu.html" +"?"+ upit;
        
    })

}


function resetColors(argument) {
    $(base_selektor + ">*").css("fill", "rgb(155, 227, 220)");
}



function initOpstine() {

    var ajax = new XMLHttpRequest();
    ajax.open("GET", OPSTINE, true);

    ajax.onload = function() {
        var list = JSON.parse(ajax.responseText).map(function(i) { 
            return { label:i.opstina, value:i.opstina, data:i.oidopstine };      
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


/*stranka na vlasti lista*/
function initStranka(argument) {


    //napravis selekt opcije
    var data = DataStranke.getStranke();

    for (var i = 0; i < data.length; i++) {
        if(data[i] == "Nepoznata" || data[i] == null || data[i] == "Stranka nije na listi")
                continue;
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

    
    var jedinstveni = _.uniq(okruzi, function(e) {return e.okrug} );

   // console.log(jedinstveni);

    for (var i = 0; i < jedinstveni.length; i++) {
        $("#regioni").append("<option value='"+jedinstveni[i].idOkrug+"'>"+jedinstveni[i].okrug+"</option>")        
    }
}

