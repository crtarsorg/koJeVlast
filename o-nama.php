<!DOCTYPE html>
<html lang="en">

<head>

	<title>Ko je na vlasti</title>

	<?php include_once 'partials/head-part.html'; ?>

</head>

<body>

	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><img src="static/icons/logo.svg"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/">Početna</a></li>
                   <!--  <li><a href="partials/uporedjivanje.html">Uporedjivanje opstina</a></li> -->
                    <li><a href="tabela.php">Tabele sa podacima</a></li>
                    <li><a href="statistike.php">Statistike</a></li>
                    <li class="active"><a href="o-nama.php">O nama</a>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="prijavi"><a href="<?php echo 'http://'.$_SERVER["SERVER_NAME"].'/api/posaljitePromenu';?>">Prijavi promenu</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <main>

	<div class="container">
		<div class="o-nama">
			<section>
				<div class="jumbotron">
					<h2>Ko je na vlasti</h2>
					<p>Dobrodošli na portal „Ko je na vlasti“ na kom su predstavljeni podaci o stranačkoj pripadnosti svih lokalnih odbornika, predsednika lokalnih skupština, gradonačelnika i predsednika opština u Srbiji. Dodatno, na portalu se možete informisati o rezultatima lokalnih izbora, budžetskim planovima, izveštajima o izvršenju budžeta, kao i podacima o prinudnoj upravi.</p>
					<p>Za svaku opštinu u Srbiji, korišćenjem pretrage možete pronaći koja je stranka u vlasti a koja u opoziciji, ko su odbornici, kao i da li su i kada odbornici menjali stranačku pripadnost. Svi podaci objavljeni na portalu su već bili javno dostupni ili ih je tim «Ko je na vlasti» prikupio na osnovu zahteva za pristup informacijama od javnog značaja. Nažalost, nismo uspeli da prikupimo podatke za svih 6.737 odbornika u Srbiji i zbog toga smo kod onih odbornika za koje ne znamo stranačku pripadnost označili kao „nepoznato“. Tu nam je potrebna vaša pomoć – da zajedno popunimo nedostajuće podatke i zajedno pratimo da li i u kojoj meri odbornici «preleću» iz stranke u stranku.</p>
				</div>
			</section>
			<section>
				<h2>Ko smo mi i zašto smo pokrenuli portal «Ko je na vlasti»</h2>
				<p>Slobodni i fer izbori su osnov demokratskog sistema. Na lokalnim izborima biramo odbornike koji imaju odgovornost i ovlašćenje da donose odluke u naše ime i za dobrobit svih nas. Svedoci smo pojave «preletanja» odbornika iz stranke u stranku, nakon izbora. Ovu pojavu uglavnom vezujemo za pravljenje skupštinske većine mimo izborne volje građana, tako što izabrani odbornici ili menjaju «stranačku boju», ili se prave neprincipijalne post-izborne koalicije. Mi, građani Srbije, imamo pravo da budemo informisani o tome ko nas predstavlja u lokalnim skupštinama, ko zastupa naše interese, kako lokalna vlast troši pare u ime nas, poreskih obveznika, i da na osnovu svih tih informacija i saznanja na svakim sledećim izborima, donosimo odluku za koga ćemo glasati.
				<br>
				Portal “Ko je na vlasti” je nastao na inicijativu udruženja građana <a href="http://www.crta.rs/">CRTA</a>, koja se bori za odgovorniju i transparentniju vlast u Srbiji u kojoj verujemo da demokratija nema alternativu. CRTA se kroz svoju posmatračku misiju <a href="http://www.gradjaninastrazi.rs/">“Građani na straži”</a> zalaže za fer, slobodne i demokratske izbore. Portal “Ko je na vlasti” je odgovor na pojavu “preletanja” i ima za cilj da prati i informiše javnost ko su preletači, kao prvi korak. Ovaj portal treba da posluži kao izvor za medije da dodatno istraže razloge koji dovode do preletanja na lokalnom nivou.</p>
			</section>
			<section>
				<h2>Zašto je važno da pamtimo ko su „preletači“?</h2>
				<p>Zato što smo svoj glas na izborima poverili ljudima u koje smo imali dovoljno poverenja da će nas najbolje zastupati u lokalnim skupštinama. Zbog uverenja da svojim iskustvom, znanjem i kredibilitetom mogu da doprinesu rešenju problema koji nas tište. Ako od tih stavova i politika odstupe, zatim „prelete“ u neku drugu političku opciju zaboravljajući na obećanja i na naše ukazano poverenje, onda je na nama da dodatno kontrolišemo njihov rad i proveravamo da li je  povereni glas na izborima izneveren i ukraden.
				<br>„Preletači“ moraju da objasne svoje postupke i da povrate poverenje građana. Moramo da pamtimo ko su bili preletači, da pratimo kako su zastupali naše interese i da sledeći put dobro razmislimo ko su ljudi na listama kandidata koje želimo da podržimo.</p>
			</section>
			<section>
				<h2>Šta je posledica preletanja?</h2>
				<p>„Politička korupcija“ na delu – izigrano poverenje i obesmišljeni izbori i izražena volja građana, jer skupštinska većina uz «pozajmljene odbornike» čini prekrojenu skupštinsku većinu u skladu sa nedemokratskim principima. Motivi preletanja nisu do kraja ispitani, ali postoje ozbiljne indicije da nisu iz ideoloških i političkih ubeđenja, već iz materijalnih razloga – koristoljublja.</p>
			</section>
			<section>
				<h2>Kako da se uključite?</h2>
				<p>Prijavite promenu „političkog dresa“ odbornika u vašoj ili susednoj opštini. Potrebno je samo da uradite jednostavnu registraciju <a href="http://kojenavlasti.rs/api/posaljitePromenu">OVDE</a>. Popunjavanjem kratke forme, informacija o promeni političke stranke za naznačenog odbornika će doći do našeg tima i neće biti dostupna trećim licima. Nakon dalje provere i obrade, informaciju ćemo javno objaviti ali bez navođenja vas kao izvora.</p>
				<p>Na osnovu ovih informacija mediji poput <a href="http://www.istinomer.rs/clanak/1662/Mapa-preletaca-na-lokalu">Istinomera će praviti dodatne analize.</a>
				<br>Na ovaj način možete demonstrirati demokratsku odgovornost i ukazati na „preletanja“ odbornika i „promene“ u zastupanju politika i doprineti naporu za zaštitu izbornog prava građana a samim tim i demokratskog poretka naše zemlje.</p>
				<p>Udruženje CRTA ne prikuplja i ne čuva vaše lične podatke koje ostavljate prilikom popunjavanja forme na portalu "Ko je na vlasti". U slučaj ostavljanja vaše e-mail adrese, informacije će biti korišćene jedino u svrhu interne komunikacije između Vas i tima CRTE i neće biti deljena sa trećim licima.</p>
				<p>Zaštita Vaših podataka o ličnosti za nas predstavlja prioritet.</p>
			</section>
		</div>
	</div>

<?php include_once "footer.php"; ?>
