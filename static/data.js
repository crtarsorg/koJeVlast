

var BASE_PATH =  "http://kojenavlasti.rs/";

var stranke_vlast = BASE_PATH + "api/strankaNaVlasti";

var DataStranke = (function() {
    var _data = [];
    var _stranke = [];
    var _Opstine = [];
    var _odbornici = [];
    var _odborniciRegion = [];
    var _regioni = [];
    var _promene = [];

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
        setOdborniciRegion: function(d) {
            _odborniciRegion = d;
        },

        getOdborniciRegion: function() {
            return _odborniciRegion;
        },
        setRegioni: function ( d ) {
            _regioni = d;
        },
        getRegioni: function ( d ) {
           return _regioni;
        },

        setOpstine: function(d) {
            _Opstine = d;
        },

        getOpstine: function() {
            return _Opstine;
        },
        setPromene: function(d) {
            _promene = d;
        },

        getPromene: function() {
            return _promene;
        }
    }
})();




function podaciOdborniciOpstina(idOpstine) {
    $.getJSON( BASE_PATH + "api/akteriPoOpstini/"+idOpstine, function(json, textStatus) {
        $("tbody").empty();

        //ne gledamo izvrsnu vlast
        var funkcija_odbornik = [1,2,3,4,6]
        json1 = json.filter(function(la) {
            return funkcija_odbornik.indexOf( +la.fid  ) >-1
        })

        izracunajProcente(json1);
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

$.getJSON(BASE_PATH +"api/imajuPromene", function(json, textStatus) {

    DataStranke.setPromene(json);

});




function podaciAkteriRegion( id_region ) {

    $.getJSON( BASE_PATH + "api/akteri/poRegionu/"+ +id_region, function(json, textStatus) {

        DataStranke.setOdborniciRegion(json);
        tabelaOdbornikaRegion( json );
        procentiRegion(json);
        $('#modal_id').modal('show')

    });
}

/*$.getJSON(DATA_PATH, function(json, textStatus) {
    Data.set(json);
});*/


$.getJSON(stranke_vlast, function(json, textStatus) {
    DataStranke.set(json)

    var stranke = [];

    json.forEach(function(el, ind){
        stranke = _.union(stranke, el.vlast);
    })
    stranke = _.uniqBy(stranke,"id");
    //uzmi stranke na vlasti

    DataStranke.setStranke(stranke);
    initStranka();
});


/*
1  Budžet 
2  Rezultati izbora 
3  Prinudna uprava 
4  Transfer iz budžeta 
5  Izvršenje budžeta 
*/
function dokumenta(op_id) {

    //http://admin.kojenavlasti.rs/admin/opstine/getDocs/71

    $.get(BASE_PATH + "admin/opstine/getDocs/" + op_id, function(data) {

        data = JSON.parse(data);

        var budz_temp = "";
        var izvr_temp = "";
        var trans_temp = "";
        var prinuda_temp = "";
        var izbori_temp = "";

        for (var i = 0, len = data.length; i < len; i++) {
            if (data[i].opdkat == 1) {
                budz_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/" + data[i].opdfile + "'>" + data[i].opdnaziv + "</a><br/>";
            } else if (data[i].opdkat == 5) {
                izvr_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/" + data[i].opdfile + "'>" + data[i].opdnaziv + "</a><br/>";
            } else if (data[i].opdkat == 4) {
                trans_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/" + data[i].opdfile + "'>" + data[i].opdnaziv + "</a><br/>";
            } else if (data[i].opdkat == 3) {
                prinuda_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/" + data[i].opdfile + "'>" + data[i].opdnaziv + "</a><br/>";
            } else if (data[i].opdkat == 2) {
                izbori_temp += "<a target='_blank' href='http://kojenavlasti.rs/files/docs/" + data[i].opdfile + "'>" + data[i].opdnaziv + "</a><br/>";
            }
        }

        if (budz_temp == "") 
            budz_temp = "<b>Nema dokumenata</b>";
        if (izvr_temp == "") 
            izvr_temp = "<b>Nema dokumenata</b>";
        if (trans_temp == "") 
            trans_temp = "<b>Nema dokumenata</b>";
        if (prinuda_temp == "") 
            prinuda_temp = "<b>Nema dokumenata</b>";
        if(izbori_temp =="")
            izbori_temp = "<b>Nema dokumenata</b>";

        $("#budzet_dokument").html(budz_temp);
        $("#izvrsenje_dokument").html(izvr_temp);
        $("#transfer_dokument").html(trans_temp);
        $("#prinudna_dokument").html(prinuda_temp);
        $("#izbori_dokument").html(izbori_temp);

    });

}