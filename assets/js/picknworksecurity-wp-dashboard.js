/*
****************************
The dashboard js file
****************************
*/


jQuery(document).ready(function($) {
$(document).ready(function() {

	
/* *************************************************************************************************
 USE THIS FUCNTION TO HIGHLIGHT THE ROW THAT WAS SELECTED FOR DELETE IN EMAIL LIST OR PHONE LIST	
************************************************************************************************** */
$('.selected_checkbox').click(function(){
	if($(this).is(':checked')) {
	  $(this).closest('tr').addClass('removeRow');
	}else{
	   $(this).closest('tr').removeClass('removeRow');
	}

});




/* ******************************************************************************************
 USE THIS FUNCTION TO SELECT ALL CHECKBOX ONCE THE CHECKBOX SELECT ALL CHECKBOX IS CLICKED
****************************************************************************************** */
	$("#selectAll").click(function() {
		$("input[type=checkbox]").prop("checked", $(this).prop("checked"));
	});

	$("input[type=checkbox]").click(function() {
		if (!$(this).prop("checked")) {
			$("#selectAll").prop("checked", false);
		}
	});




/* *********************************** 
 SORT AND SEARCH LIST TABLE BY EMAIL
************************************* */
function pnw_filter_by_email() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchbyemail");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}




/* *****************************************
 SORT AND SEARCH LIST TABLE BY PHONE
****************************************** */ 
function pnw_filter_by_phone() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchbyphone");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}



/* ****************************************
 SORT AND SEARCH LIST TABLE BY GROUP
****************************************** */
function pnw_filter_by_group() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchbygroup");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}





/* ************************************************
 SORT AND SEARCH LOCKLIST TABLE BY METHOD - BEST
************************************************* */
function pnw_filter_by_method() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("pnwsearchbymethod");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}





/* **********************************************
 SORT AND SEARCH LOCKLIST TABLE BY LOCKS - BEST
************************************************* */
function pnw_filter_by_locks() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("pnwsearchbylocks");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}



/* *********************************************
 SORT AND SEARCH MANAGE LOCK LIST TABLE BY IP
************************************************ */
function pnw_filter_by_ipaddress() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("pnwsearchby");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}




/* *********************************************
 SORT AND SEARCH MANAGE LOCK LIST TABLE BY USERNAME
******************************************** */
function pnw_filter_by_username() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("pnwsearchby");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}




/* ****************************************************
 SORT AND SEARCH MANAGE LOCK LIST TABLE BY USERNAME
******************************************************/
function pnw_filter_by_userid() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("pnwsearchby");
  filter = input.value.toUpperCase();
  table = document.getElementById("pnw-listtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}




});
});


