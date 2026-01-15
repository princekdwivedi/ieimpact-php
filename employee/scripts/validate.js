function isNumeric(string, ignoreWhiteSpace) 
{
	if (string.search) 
	{
		if ((ignoreWhiteSpace && string.search(/[^\d\s]/) != -1) || (!ignoreWhiteSpace && string.search(/\D/) != -1)) return false;
	}
	return true;
}

function isAlphaNumeric(string, ignoreWhiteSpace) 
{
	if (string.search) 
	{
		if ((ignoreWhiteSpace && string.search(/[^\w\s]/) != -1) || (!ignoreWhiteSpace && string.search(/\W/) != -1)) return false;
	}
	return true;
}

function isEmail(address) 
{
	if (address != '' && address.search) 
	{
		if (address.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) return true;
		else return false;
	}
	var domain = address.substring(address.indexOf('@') + 1);
	if (domain.indexOf('.') == -1) return false;
	if (domain.indexOf('.') == 0 || domain.indexOf('.') == domain.length - 1) return false;
	return true;
}

function isValidName(string)
{
	var re = /^([a-zA-Z])+$/;
	if(!string.match(re))
		return false;
	else
		return true;
}
function isURL(argvalue)
{
	if(argvalue.indexOf("http://")<0 && argvalue.indexOf("https://")<0)
	{
		argvalue = "http://" + argvalue
	}

	if (argvalue.indexOf(" ") != -1)
		return false;
	else if (argvalue == "http://")
	    return false;
	else if (argvalue.indexOf("http://") > 0)
		return false;
	argvalue = argvalue.substring(7, argvalue.length);

	if (argvalue.indexOf(".") == -1)
		return false;
	else if (argvalue.indexOf(".") == 0)
	    return false;
	else if (argvalue.charAt(argvalue.length - 1) == ".")
	    return false;
	
	if (argvalue.indexOf("/") != -1) 
	{
		argvalue = argvalue.substring(0, argvalue.indexOf("/"));
		if (argvalue.charAt(argvalue.length - 1) == ".")
		{
			return false;
		}			
	}

	if (argvalue.indexOf(":") != -1) 
	{
		if (argvalue.indexOf(":") == (argvalue.length - 1))
		{
			return false;
		}
	    else if (argvalue.charAt(argvalue.indexOf(":") + 1) == ".")
		{
			return false;
			argvalue = argvalue.substring(0, argvalue.indexOf(":"));
			if (argvalue.charAt(argvalue.length - 1) == ".")
			{
				return false;
			}
		}
	}
	return true;
}

function strtrim(inputString) 
{
	if (typeof inputString != "string") 
	{
		return inputString; 
	}
	var retValue = inputString;
	var ch = retValue.substring(0, 1);
	while (ch == " ") 
	{
		retValue = retValue.substring(1, retValue.length);
		ch = retValue.substring(0, 1);
	}
	ch = retValue.substring(retValue.length-1, retValue.length);
	while (ch == " ")
	{
		retValue = retValue.substring(0, retValue.length-1);
		ch = retValue.substring(retValue.length-1, retValue.length);
	}
	while (retValue.indexOf("  ") != -1)
	{
		retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length); // Again, there are two spaces in each of the strings
	}
	return retValue; 
}

function autotab(original,destination)
{
	if(IgnoreKeys())
		return;
	
	if(document.selection.createRange().text.length != 0)
		return;

	if(original.getAttribute && original.value.length == original.getAttribute("maxlength"))
	{
		destination.focus()
	}
}

function IgnoreKeys()
{
	// Ignore Home and End keys
	if(event.keyCode == 35 || event.keyCode == 36) //home and end
		return true;

	if(event.keyCode == 9) // Shift tab
		return true;
		
	if(event.keyCode == 37 || event.keyCode == 38 || 
		event.keyCode == 39 || event.keyCode == 40) // Arrow keys
		return true;
		
	if(event.keyCode == 46) //Delete
		return true;
		
	if(event.keyCode == 16) // Shift
		return true;				
		 	
	if(event.keyCode == 8) // Back
		return true;				

	return false;			

}
function restrictSpecialChars()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if((k >= 65 && 90 >= k) || (k >= 97 && 122 >= k) || (k == 46))
	{
		return true;
	}
	if((k == 32 || k == 0) || (k >= 48 && 57 >= k) || k == 8)
	{
		return true;
	}
	else
	{
		return false;
	}
}