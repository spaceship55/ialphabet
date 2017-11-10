
/*Adding required attribute to email field*/


window.onload = function() {

//Fix for Quizzes and Survey
document.querySelectorAll('.mlwEmail')[0].value == "";
document.querySelectorAll('.mlwEmail')[0].required =true;
document.getElementsByTagName("input")[1].required = true;
// console.log("Email is required now");


//Fix for 5 questions
var k;
for(k=0;k<document.getElementsByName("email").length;k++){
	document.getElementsByName("email")[k].required =true;

}

document.getElementsByClassName("qsm-submit-btn")[0].onclick = function() {
   if(document.querySelectorAll('.mlwEmail')[0].value == ""){
	// document.getElementsByClassName("qsm-submit-btn")[0].disabled = true;
	alert("Email is required");
	return false;
	}     
}

}



