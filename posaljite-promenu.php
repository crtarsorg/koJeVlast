<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pošaljite promenu</title>

<?php include_once 'partials/head-part.html'; ?>

</head>

<body>

    <?php
        include_once 'partials/nav.html';
    ?>

    <div class="container">
        <h3>Pošalji promenu</h3>
        <p>Koristeći ovu formu možete poslati zahtev za promenu određene informacije <b>ukoliko smatrate da ona nije istinita</b>.</p>
        <p>Prilikom slanja zahteva molimo Vas da obavezno obratite pažnju na sledeće</p>
        <p>
            <ul>
                <li>Obavezno ostavite <b>e-mail adresu</b> ukoliko želite da Vas obavestimo o uspešnoj promeni</li>
                <li>Obavezno navedite <b>osobu ili stranku</b> na koju se promena odnosi kao i <b>opštinu</b> na kojoj je potrebno izvršiti promenu</li>
                <li>Obavezno navedite link ili mesto na kom možemo da verifikujemo promenu. <b>Bez verifikacije nijedna promena neće biti izvršena</b>.</li>
            </ul>
            <b>Zahvaljujemo Vam na vašoj posvećenosti.</b>
        </p>
        <div id="form-alerts"></div>
        <div id="userfrosting-alerts"></div>
        <form name="posaljiPromenuForm" method="post" id="posaljiPromenuForm" action="">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <b>Vaš predlog je u vezi sa (unesite ime osobe ili stranke)*</b>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                            <input type="text" class="form-control" id="akter" name="akter" autocomplete="off" value="Petar" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <b>Na opštini? (unesite opštinu na kojoj deluje osoba ili stranka)* </b>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                            <input type="text" class="form-control" id="opstina" name="opstina" autocomplete="off" value="Mladenovac" placeholder="Opština">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <b>Vaša e-mail adresa*</b>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                            <input type="text" class="form-control" id="email" name="email" autocomplete="off" value="trt@mrt.com" placeholder="E-mail">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <b>Promena koju ste primetili (npr. Članovi i odbornici prokupačkog SDS napustili stranku i formirali grupu građana od 05.12.2016 godine)*</b>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                            <textarea rows="5" type="text" class="form-control" id="promena" name="promena" value="" placeholder="Promena">Članovi i odbornici prokupačkog SDS napustili stranku i formirali grupu građana od 05.12.2016 godine</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <b>Mesto / link / publikacija na kome se mogu potvrditi Vaše tvrdnje</b>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                            <input type="text" class="form-control" id="potvrda" name="potvrda" autocomplete="off" value="google.rs" placeholder="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="vert-pad">
                        <button id="editAkter" type="submit" class="btn btn-block btn-md btn-primary ">
                            <i class="fa fa-edit"></i>Pošalji promenu
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="5bce0da3797b538d67896c312e9a98cfd60e9afd2e84bb66aaf0ec2b725c0fb647916734d66d2eaf773171a9c7562189f10e9a76bb0bd1cdeab08f4caf3804a9">
        </form>
        <div class="clearfix"></div>
    </div>
    <script src="http://kojenavlasti.rs/admin/js/jquery-1.11.2.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
    $(document).ready(function() {

        parametri()


        $("#posaljiPromenuForm").submit(function(e) {

            $.ajax({
                type: "POST",
                url: "http://kojenavlasti.rs/api/predlozitePromenu",
                data: $("#posaljiPromenuForm").serialize(),
                success: function(data) {
                    //refresh changes after adding or updating
                    $("#userfrosting-alerts").fadeOut(200, function() {
                        $('#userfrosting-alerts').html(data);
                        $("#userfrosting-alerts").fadeIn(500, function() {
                            //$("#userfrosting-alerts").fadeOut(5000);
                        });
                    });



                }
            });

            e.preventDefault();
        });


    });

    function parametri(argument) {
        var queryDict = {}

        //parsiranje get parametara, ako ih ima
        location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]})

        $("#akter").val( decodeURIComponent( queryDict['ime'] )  );
        $("#opstina").val( decodeURIComponent( queryDict['opstina'] )  );

    }
    </script>

    <?php include_once "footer.php"; ?>