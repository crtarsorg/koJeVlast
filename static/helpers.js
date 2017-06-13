function izracunajProcente(podaci) {

    // nepoznate, null i sve ostalo stavljam u ostale

    var ukupno = podaci.length;

    var grupisane = _.groupBy(podaci, function(el) {
        return el.stranka;
    })

    //izbacivanje nepoznatih stranaka iz liste
    statistike = grupisanjeOstalih( grupisane );


    var sortirani = _.sortBy(statistike, function(el) {
        return el.procenat });
    sortirani = sortirani.reverse();

    //sve stranke sortirane prema broju mandata

    var svi = [];

    //ne pojavljuju se ostali
    //uzeti samo prve tri i ostale
    if (sortirani.length > 3) {
        //slice 4 do kraja 
        //i sve ih saberi
        svi = sortirani.slice(0, 3); //manje ili jednako
        ostali = sortirani.slice(3); //
        //sabrati sve procente
        var ukupnoOstale = 0;

        ostali.forEach(function(el) {
            ukupnoOstale += el.procenat;
        })

        var nadjena1 = _.find(svi, { skracenica: "ostale" });

        //ako je u prve 3
        if (nadjena1 != undefined ) {
             nadjena1.procenat += ukupnoOstale;
            //samo se sabira na postojecu
        }
        else //ako nije u prve tri vec je sabrana kao ostali
            svi.push({ stranka: "Ostale stranke", skracenica: "ostale", procenat: ukupnoOstale });

    }
    else {
        svi = sortirani;
    }
    drawSvg(svi);
}

function grupisanjeOstalih(grupisane) {
    var statistike = []

    Object.keys(grupisane).forEach(function(el) {

        var stranka = grupisane[el];

        var centi = stranka.length;
        //ostali
        //nepoznate i stranke koje nisu navedene transformisati u 'ostali'
        var sk_temp = el.replace("'", "").split(' ').map(function(item) {
            return item[0] }).join('').toLowerCase();

        if (el == "Stranka nije na listi" || 
            el == "null" || el == null || 
            el == "Nepoznata") {

            el = "Ostale stranke";
            sk_temp = "ostale";
        }

        var unos = { stranka: el, skracenica: sk_temp, procenat: centi };

        var nadjena = _.find(statistike, { skracenica: unos.skracenica /*"ostale"*/ });

        if (nadjena != undefined) {
            nadjena.procenat += centi;
            //unos = nadjena;
        } else
        {//prvi put pronadjena
            statistike.push(unos);
        }
    })

    return statistike
}

function agregacija_okrug(opstine) {

    var stranke = DataStranke.get();

    var init = { opop: 0, opov: 0, vlast: [] };

    if (opstine == undefined)
        return;


    //cifre

    var rez = opstine.reduce(function(a, b) {

        var temp = {};
        temp.opop = +a.opop + +b.opop;
        temp.opov = +a.opov + +b.opov;

        var a_stranke =
            stranke.filter(function(c) {
                return c.idopstine == a.oidopstine });
        var b_stranke =
            stranke.filter(function(c) {
                return c.idopstine == b.oidopstine });

        var temp_ar = [];
        var temp_ar2 = []

        if (a_stranke.length !== 0 && a_stranke[0].vlast !== undefined)
            temp_ar = a_stranke[0].vlast
        if (b_stranke.length !== 0 && b_stranke[0].vlast !== undefined) {
            temp_ar2 = b_stranke[0].vlast
        }

        temp.vlast = temp_ar2.concat(temp_ar);

        return temp;
    }, init);

    
    return rez;
}


function podaciRegion(id_region) {

    var podaci_po_okrugu = DataStranke.getOpstine();
    var grupisane = _.groupBy(podaci_po_okrugu, function(el) {
        return el.oidokruga;
    })

    var filterd = (grupisane[+id_region]);
    var sumed = agregacija_okrug(filterd);
    //naziv regiona

    var regioni = DataStranke.getRegioni();
    var la = regioni.filter(function(el) {
        return el.idOkrug == +id_region;
    });

    if(la.length == 0)
        return undefined;

    sumed.naslov = la[0].okrug;

    return sumed;
}