<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Promene po akteru</title>

<?php include_once 'partials/head-part.html'; ?>

</head>

<body>

  <?php include 'partials/nav.html'; ?>






<div class="container">

        <h3 style="color:white;">Promene po akteru</h3>
        <p></p>

    <header class="page-header">
        <h1 id="ime">loading...</h1>
    </header>

    <ul class="timeline">
        <!--<li><div class="tldate">Apr 2014</div></li>-->


    </ul>
        <div class="clearfix"></div>
    </div>




    <script src="http://kojenavlasti.rs/admin/js/jquery-1.11.2.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
    $(document).ready(function() {



 $.getJSON("http://kojenavlasti.rs/api/akter/promene/"+<?php echo $_GET['id'];?>, function(json, textStatus) {


//reverse array
json.reverse();


$("#ime").html(json[0].aime+" "+json[0].aprezime);

//console.dir(json);



var prevdate = '';

var prevstranka = '';
var prevfunkcija = '';
var prevmestoFunkcije = '';
var prevkoalicija = '';
var prevvlast = '';

var itemout = [];


for (var i = 0; i < json.length; i++) {

//vars
var inverted = '';
var shownewdate = '';
var datumoddo = '';

var funkcija = '';
var mestoFunkcije = '';
var stranka = '';
var koalicija = '';
var vlast = '';
var opstina = '';
var okrug = '';




//vars checking and cleanup
if(i % 2 === 0 ) { inverted = '';} else {inverted = ' class="timeline-inverted" ';}

if(json[i].funkcija) { funkcija = json[i].funkcija }
if(json[i].pod) {
    datumoddo = 'od '+formatDate(json[i].pod)

    //grupisanje po mesecima
    var thisdate = json[i].pod;
    if(prevdate !== thisdate.substr(0, 7)){
        shownewdate = '<li><div class="tldate">'+formatDateMonth(thisdate)+'</div></li>';
        prevdate = thisdate.substr(0, 7);
    } else {
        shownewdate = '';
    }


}
if(json[i].pdo) { datumoddo += ' do '+formatDate(json[i].pdo) }
if(json[i].fmesto) { mestoFunkcije += json[i].fmesto }
if(json[i].snaziv) { stranka += json[i].snaziv }
if(json[i].knaziv) { koalicija += json[i].knaziv }
if(json[i].pnavlasti==1) { vlast += 'Da' } else if(json[i].pnavlasti==2){ vlast += 'Ne' } else { vlast += 'Nema podataka' }
if(json[i].opstina) { opstina += json[i].opstina }
if(json[i].okrug) { okrug += json[i].okrug }


//datumoddo += ' ///// ' + formatDate(json[i].pod);

var item = shownewdate;
item += '<li '+inverted+' >';
item += '<div class="tl-circ"></div>';
item += '<div class="timeline-panel">';
item += '<div class="tl-heading">';
if(prevfunkcija!=funkcija && i!=0){item += '<u><h4>'+funkcija+'</h4></u>';} else {item += '<h4>'+funkcija+'</h4>';}
item += '<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> '+datumoddo+'</small></p>';
item += '</div>';
item += '<div class="tl-body">';
item += '<p>Mesto funkcije: '+mestoFunkcije+'</p> ';
if(prevstranka!=stranka && i!=0){item += '<u><p>Stranka: '+stranka+'</p></u>';} else {item += '<p>Stranka: '+stranka+'</p>';}
if(prevkoalicija!=koalicija && i!=0){item += '<u><p>Koalicija: '+koalicija+'</p></u>';} else {item += '<p>Koalicija: '+koalicija+'</p>';}
if(prevvlast!=vlast && i!=0){item += '<u><p>Na vlasti: '+vlast+'</p></u>';} else {item += '<p>Na vlasti: '+vlast+'</p>';}
item += '<p>Opština: '+opstina+' ('+okrug+')</p>';
item += '</div>';
item += '</div>';
item += '</li>';
item += '';


//$(".timeline").append(item);
itemout.push(item)


prevstranka=stranka;
prevvlast = vlast;
prevkoalicija=koalicija;
prevfunkcija=funkcija;

}

//push data (itemout) to timeline
console.dir(itemout);
$(".timeline").append(itemout.reverse());

})


function formatDate(date) {
    var monthNames = [
        "Januar", "Februar", "Mart",
        "April", "Maj", "Jun", "Jul",
        "Avgust", "Septembar", "Oktobar",
        "Novembar", "Decembar"
    ];


    var dateParts = date.split("-");
    var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0, 2), dateParts[2].substr(3, 2), dateParts[2].substr(6, 2), dateParts[2].substr(9, 2));

    var day = jsDate.getDate();
    var monthIndex = jsDate.getMonth();
    var year = jsDate.getFullYear();

    return day + '. ' + monthNames[monthIndex] + ' ' + year;
}


function formatDateMonth(date) {
    var monthNames = [
        "Januar", "Februar", "Mart",
        "April", "Maj", "Jun", "Jul",
        "Avgust", "Septembar", "Oktobar",
        "Novembar", "Decembar"
    ];


    var dateParts = date.split("-");
    var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0, 2), dateParts[2].substr(3, 2), dateParts[2].substr(6, 2), dateParts[2].substr(9, 2));

    var day = jsDate.getDate();
    var monthIndex = jsDate.getMonth();
    var year = jsDate.getFullYear();

    return monthNames[monthIndex] + ' ' + year;
}



    });


    </script>

    <?php //include_once "footer.php"; ?>


<style>
u{
    color:#d9534f;
    text-decoration: none;
    border-bottom: 3px solid #d9534f;
    display:block;
}

body {
    background: #333 !important;
}

img { border: 0; max-width: 100%; }

.page-header h1 {
    font-size: 3.26em;
    text-align: center;
    color: #efefef;
    text-shadow: 1px 1px 0 #000;
}

/** timeline box structure **/
.timeline {
    list-style: none;
    padding: 20px 0 20px;
    position: relative;
}

.timeline:before {
    top: 0;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 3px;
    background-color: #eee;
    left: 50%;
    margin-left: -1.5px;
}

.tldate {
    display: block;
    width: 200px;
    background: #414141;
    border: 3px solid #212121;
    color: #ededed;
    margin: 0 auto;
    padding: 3px 0;
    font-weight: bold;
    text-align: center;
    -webkit-box-shadow: 0 0 11px rgba(0,0,0,0.35);
}

.timeline li {
    margin-bottom: 25px;
    position: relative;
}

.timeline li:before, .timeline li:after {
    content: " ";
    display: table;
}
.timeline li:after {
    clear: both;
}
.timeline li:before, .timeline li:after {
    content: " ";
    display: table;
}

/** timeline panels **/
.timeline li .timeline-panel {
    width: 46%;
    float: left;
    background: #fff;
    border: 1px solid #d4d4d4;
    padding: 20px;
    position: relative;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
    -moz-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
}

/** panel arrows **/
.timeline li .timeline-panel:before {
    position: absolute;
    top: 26px;
    right: -15px;
    display: inline-block;
    border-top: 15px solid transparent;
    border-left: 15px solid #ccc;
    border-right: 0 solid #ccc;
    border-bottom: 15px solid transparent;
    content: " ";
}

.timeline li .timeline-panel:after {
    position: absolute;
    top: 27px;
    right: -14px;
    display: inline-block;
    border-top: 14px solid transparent;
    border-left: 14px solid #fff;
    border-right: 0 solid #fff;
    border-bottom: 14px solid transparent;
    content: " ";
}
.timeline li .timeline-panel.noarrow:before, .timeline li .timeline-panel.noarrow:after {
    top:0;
    right:0;
    display: none;
    border: 0;
}

.timeline li.timeline-inverted .timeline-panel {
    float: right;
}

.timeline li.timeline-inverted .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
}

.timeline li.timeline-inverted .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
}


/** timeline circle icons **/
.timeline li .tl-circ {
    position: absolute;
    top: 23px;
    left: 50%;
    text-align: center;
    background: #6a8db3;
    color: #fff;
    width: 35px;
    height: 35px;
    line-height: 35px;
    margin-left: -16px;
    border: 3px solid #90acc7;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
    z-index: 99999;
}


/** timeline content **/

.tl-heading h4 {
    margin: 0;
    color: #c25b4e;
}

.tl-body p, .tl-body ul {
    margin-bottom: 0;
}

.tl-body > p + p {
    margin-top: 5px;
}

/** media queries **/
@media (max-width: 991px) {
    .timeline li .timeline-panel {
        width: 44%;
    }
}

@media (max-width: 700px) {
    .page-header h1 { font-size: 1.8em; }

    ul.timeline:before {
        left: 40px;
    }

    .tldate { width: 140px; }

    ul.timeline li .timeline-panel {
        width: calc(100% - 90px);
        width: -moz-calc(100% - 90px);
        width: -webkit-calc(100% - 90px);
    }

    ul.timeline li .tl-circ {
        top: 22px;
        left: 22px;
        margin-left: 0;

    }
    ul.timeline > li > .tldate {
        margin: 0;
    }

    ul.timeline > li > .timeline-panel {
        float: right;
    }

    ul.timeline > li > .timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }

    ul.timeline > li > .timeline-panel:after {
        border-left-width: 0;
        border-right-width: 14px;
        left: -14px;
        right: auto;
    }
}

</style>



    </main>
    </body>
</html>