/***************************/
//@Author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

$(document).ready(function(){
	//global vars
	var inputMessage = $("#message");
	var loading = $("#loading");
	var messageList = $(".content > ul");
  var updateTime = 0;
	//functions
	function updateShoutbox(){
		//just for the fade effect
		loading.show();
		//send the post to shoutbox.php
		$.ajax({
			type: "POST", url: "shoutbox.php", data: "action=update",
			complete: function(data){
				loading.hide();
				messageList.html(data.responseText);
			}
		});    
	}
	//check if all fields are filled
	function checkForm(){
		if(inputMessage.attr("value"))
			return true;
		else
			return false;
	}

  function autoUpdate() {
    if(updateTime == 0 ) {
      $(document).everyTime(30000, 'autoUpdate', function(i) {
        updateShoutbox();
        updateTime++;
        $("#showupdatetime").html(""+updateTime+"");
        $("#showtimer").html("30");
        if(updateTime == 10) {
          $("#update").val("แสดงข้อความใหม่อีกครั้ง !");
          updateTime = 0;
        }        
      }, 10);
    }
  }
  
  $(document).everyTime(1000, 'timer', function(i) {
    var timer = parseInt($("#showtimer").html()) - 1;    
    if(timer > 60)  {
      $("#showtimer").html("60");   
    } else {
      $("#showtimer").html(timer);
    }
  });
	
	//on submit event
	$("#form").submit(function(){
		if(checkForm()){
			var message = inputMessage.val();
      inputMessage.val("");      
			//we deactivate submit button while sending
			$("#send").attr({ disabled:true, value:"ส่งข้อความ ..." });
			$("#send").blur();
			//send the post to shoutbox.php
			$.ajax({
				type: "POST", url: "shoutbox.php", data: "action=insert&message=" + message,
				complete: function(data){
					updateShoutbox();
					//reactivate the send button
					$("#send").attr({ disabled:false, value:"ส่งข้อความ !" });
				}
			 });
       inputMessage.focus();
		}
		else alert("Please fill all fields!");
		//we prevent the refresh of the page after submitting the form
		return false;
	});

  $("#update").click(function(){
    updateShoutbox();
    if(updateTime == 0) autoUpdate();
    $("#update").val("แสดงข้อความใหม่ !");
  }); 
  
  //Load for the first time the shoutbox data
	updateShoutbox();

  autoUpdate();

});