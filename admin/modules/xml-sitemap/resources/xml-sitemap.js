/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

// When the browser is ready...

function check(){
		if(document.getElementById("uploadfiles").value == "" && jQuery( "#upload_file" ).is(":visible") ) {
		var fileMessage = param.file_message;
		   alert(fileMessage);
		   return false;
		}
}
