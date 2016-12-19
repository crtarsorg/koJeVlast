$(function() {

	//ucitaj podatke po koaliciji
	//napraviti eksterno podatke grupisane po koalicijama
	poKoaliciji();
})

function poKoaliciji(argument) {
	$("#koalicija").change(function(event) {
		var koal = event.target.value;

		//console.log(koal);
		//treba mapirati id-eve svg-a sa nazivima opstina

		//filtriraj skup podataka
		var temp = Data.get();
		
		var arr = Object.keys(temp).map(function (key) { return temp[key]; });
		
		var filt = arr.filter(function(el) {
			
			var ukupno = el.length;

			var broj = el.filter(function (el1) {
				return el1.koalicija == koal;
			}).length;

			return (broj/ukupno) > 0.5; 
			
		})
		//prodji kroz sve filtrirane i ofarbaj ih
		console.log(filt);
		debugger;
		
		//oboj mapu prema filtriranim opstinama 
		   //debugger;
		//console.log(podaci);   
	 	//random oboj opstine
	 	
	});
}


function bojenje(argument) {
	
}

function srafiranje(argument) {
	
}

