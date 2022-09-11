function refreshPage() {
	
	if (document.getElementById('searchId')) {
		document.getElementById('searchId').value = "";
	}
	if (document.getElementById('searchUsername')) {
		document.getElementById('searchUsername').value = "";
	}
	if (document.getElementById('searchEmail')) {
		document.getElementById('searchEmail').value = "";
	}

	document.forms[formUser].submit();
}