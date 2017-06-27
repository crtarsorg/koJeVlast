

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
            $("#opis").addClass("hidden");
            //nedostupni podaci
        });
    //mora da bude i region detalji
    $(selektor).click(sideDetaljiHandlerClick);

}




var sideDetaljiHandlerClick = function(event) {

// mark current selection
//$('#mapa > svg polygon ').each(function(){
//    $(this).css("fill", "rgb(155, 227, 220)");
//    $(this).removeAttr( "class" )
//});
//
//console.dir($(this));
//$(this).attr("class", "current");



    if ($('[opstina]').css('display') !== 'none') {

		if(event.target.getAttribute("opstina") == null){
			//kosovo
			alert("Podaci za opstine na KiM nisu dostupne jer su iste u statusu prinudne uprave/privremenog organa ")
			return;
		}

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
        elem !== undefined && elem.tagName !== undefined &&
        (elem.tagName == "path" || elem.tagName == "polygon")) {

        temp = $(elem).parent().attr("okrug");
    }

    if(+temp==0){
        //beograd
        sideDetailsOpstina(70106, 200);
        return;
    }
    var sumed = podaciRegion(temp);

    if (sumed !== undefined) {
        sideDetails(sumed.naslov, sumed.vlast)
    }

}




var opstinaDetaljiHandlerClick = function(id_opstine) {
    $("#spinner, #fade").removeClass('hidden');

    var temp = +$(this).attr('opstina');

    if (id_opstine !== undefined && Number.isInteger(+id_opstine)) {
        temp = +id_opstine;
    }

    var naslov = "Naslov ";
    var podaci = DataStranke.getOpstine();
    var opstina = podaci.filter(function(el) {
        return +el.oidopstine == temp;
    })

    var id = 0;

    if (opstina.length == 0) {
        return;
    }

    showModal(opstina);

}

function regionDetailHandlerClick(element, naslov_region) {
    $("#spinner, #fade").removeClass('hidden');
    var id = $(element).parent().attr('okrug');

    //sakrij podatke o budzetima i dokumentima
    //uzmi podatke o odbornicima za sve opstine

    //stranke u vlasti, populacija i povrsina
    var podaci;
    if(+id ==0 ){//beograd
        var podaci = DataStranke.getOpstine();
        var opstina = podaci.filter(function(el) {
            return +el.opid == 200;
        })
        podaci = opstina;
    }
    else{
        podaci = podaciRegion(id);    
    }



    if(+id ==0 ){
        showModal(podaci);
    }

    else{
        podaci.ologo = "bb7a4496cbe2a3b397a38acda978c2a1e4b77f36.png"
        podaciAkteriRegion(id); //ajax zahtev - podaci za tabelu  
        info_tab(podaci);

    //$("#spinner, #fade").toggleClass('hidden'); //ovo treba da se stavi tamo gde se dobavljaju podaci
        showModalRegion(podaci.naslov, id);
    }

   

}




function sideDetailsOpstina(idOpstine, idBeograd) {
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
    var ret = opstina.filter(function(el) {
        return el.id == idBeograd
    });
    if( ret.length > 0){
        stranke = ret[0].vlast;
        opstina = ret;
    }

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