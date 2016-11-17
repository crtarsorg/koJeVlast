[
  '{{repeat(500, 700)}}',
  {
    "oid": '{{index()}}',
    "titula": '{{random("prof. dr", "prim. dr", "mr", "dr" ,"doc." , "sci. med." ,"" , "" )}}',
    "srednje_slovo" : '{{random("b.","r.", "sp.","st."," " , " "," " , " "," " , " "," " , " " )}}',
    "ime_prezime": function (tags) {
      var ime = ["arpad","bajro","balint","balša","biljana","blaža","bogdan","borisav","borka","boško","božidar","branka","branko","čedomir","dalibor","danica","danijela","darko","desanka","dijana","dragoljub","dragomir","dubravka","dubravko","dušica","duško","elvira","enis","fatmir","gordana","gorica","igor","ivica","jadranka","jahja","josip","jovica","jovo","katarina","konstantin","krsto","ljibuška","ljubica","ljubinko","ljubiša","ljupka","marijan","marina","marinika","marjana","meho","mihailo","miladin","miletić","milija","milimir","milisav","miljan","miloš","milosav","milutin","miodrag","mira","mirko","miroljub","miroslava","momčilo","momo","nada","neđo","nemanja","novica","ognjen","olena","ostoja","predrag","radmilo","radovan","ratko","sanda","saša","srbislav","srđan","sreto","stanija","stanislava","stefana","studenka","svetlana","tanja","tatjana","tijana","velimir","vera","vjerica","vladan","vlado","zdravko","željko","žika","zoltan"];
      var prezime = ["aleksić", "arsenović", "arsić", "atlagić", "babić", "bačevac", "bajatović", "barišić", "bauer", "bečić", "belačić", "bićanin", "blagojević", "blažić", "bogatinović", "bogdanović", "bogosavljević bošković", "bojanić", "borković", "bošković", "božić", "božović", "broz", "bukvić", "bulatović", "čabradi", "čabraja", "čanak", "cokić", "čolaković", "čomić", "ćorilić", "čotrić", "damjanović", "davidovac", "delić", "đokić", "dragišić", "drecun", "đukanović", "đurić", "đurišić", "đurović", "fehratović", "filipović", "filipovski", "fremond", "gajić", "gavrilović", "gegić", "grubor", "grujić", "hasani", "imamović", "ivković", "jančić", "jankov", "janošević", "jelenković", "jerkov", "ješić", "jevđić", "jevtić", "jevtović vukojičić", "jojić", "jokić", "jolović", "jović", "jugović", "karanac", "komlenski", "kompirović", "konstantinović", "korać", "kosanić", "kovač", "krasić", "krivokapić", "krlić", "lakatoš", "laketić", "lapčević", "lazanski", "lazić", "linta", "ljubenović", "lukić", "macura", "mačužić", "majkić", "maletić", "malušić", "mandić", "manojlović", "maraš", "marinković", "marjanović", "markićević", "martinović", "matić", "mićić", "mićin", "mićunović", "mihailović vacić", "mihajlovska", "mijailović", "miladinović", "milekić", "milenković", "miletić", "milić", "milićević", "milojević", "milojičić", "mirčić", "mitrović", "mrdaković todorović", "mrkonjić", "nikolić pavlović", "nikolić vukajlović", "nogo", "ognjanović", "omerović", "orlić", "ostojić", "pajtić", "palalić", "pantić pilja", "pantović", "papuga", "parezanović", "pastor", "pek", "perić", "pešić", "petković", "petronijević", "radeta", "radičević", "radojičić", "radulović", "rakić", "rančić", "rašković ivić", "repac", "rističević", "ružić", "šarović", "savić", "savkić", "šešelj", "ševarlić", "šormaz", "stamenković", "stanković đuričić", "stanojević", "stević", "stojadinović", "stojiljković", "stojković", "stojmirović", "stošić ", "šulkić", "sušec", "tarbuk", "tepić", "tomašević damnjanović", "tomić", "torbica", "turk", "veljković", "vesović", "videnović", "vlahović", "vučković", "vujadinović", "vujić", "vujić obradović", "vukadinović", "vukomanović", "zagrađanin", "žarić kovačević", "zeljug", "žigmanov", "živković", "zukorlić"];
    
      var curentIme = parseInt( Math.random() * ime.length ) ;
      var curentprezime = parseInt( Math.random() * prezime.length ) ;

      return ime[curentIme] + " " + prezime[curentprezime]  ;
    },
    "stranka": '{{random("sns", "sps", "ds", "nvm" ,"dss" , "sds" ,"js" , "nss" )}}',
    "funkcija": '{{random("pomocnik gradonacelnika", "odbornik", "gradonacelnik", "predesdnik skupstine" ,"upravnik" , "sekretar"  )}}',
    "koalicija": "sns i ostali",
    "opstina": "bor",
    "datod": "19/3/2016",
    "datdo": "19/12/2016",
    "u_vlasti": "da",
    "kolona_dodatna": "dodatna"
  }
]

