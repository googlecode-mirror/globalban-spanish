/*
	EDIT : This file as been edited by Fantole
	http://www.css-ressource.com

    This file is part of GlobalBan.

    Written by Stefan Jonasson <soynuts@unbuinc.net>
    Copyright 2008 Stefan Jonasson
    
    GlobalBan is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GlobalBan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GlobalBan.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * Returns a new XMLHttpRequest object, or false if this browser
 * doesn't support it
 */
function newXMLHttpRequest() {

  var xmlreq = false;

  if (window.XMLHttpRequest) {
    // Create XMLHttpRequest object in non-Microsoft browsers
    xmlreq = new XMLHttpRequest();

  } else if (window.ActiveXObject) {
    // Create XMLHttpRequest via MS ActiveX
    try {
      // Try to create XMLHttpRequest in later versions
      // of Internet Explorer

      xmlreq = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e1) {
      // Failed to create required ActiveXObject
      try {
        // Try version supported by older versions
        // of Internet Explorer
        xmlreq = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e2) {
        // Unable to create an XMLHttpRequest with ActiveX
      }
    }
  }
  return xmlreq;
}

/*
 * Returns a function that waits for the specified XMLHttpRequest
 * to complete, then passes its XML response
 * to the given handler function.
 * req - The XMLHttpRequest whose state is changing
 * responseXmlHandler - Function to pass the XML response to
 */
function getReadyStateHandler(req, responseXmlHandler, processFile, reqSend, errorCount) {

  // Return an anonymous function that listens to the
  // XMLHttpRequest instance
  return function () {

    // If the request's status is "complete"
    if (req.readyState == 4) {

      if(errorCount == 5) {
        alert("Network connection interrupted. Please try again.");
        return;
      }

      // If we have a disconnect error, retry sending the request
      if(req.status >= 12000) {
        createRequest(responseXmlHandler, processFile, reqSend, errorCount+1);
        return; // No need to continue at this point
      }

      // Check that a successful server response was received
      if (req.status == 200) {
        // Pass the XML payload of the response to the
        // handler function
        responseXmlHandler(req.responseXML);

      } else {
        // An HTTP problem has occurred (usually a 404 or 500)
        //alert("HTTP error: "+req.status);
        alert("Your session has timed out or an error has occured with your request ("+req.status+")");
      }
    }
  }
}

// Create a new XMLHttpRequest and process it
function createRequest(responseFunction, processFile, reqSend, errorCount) {
  // Obtain an XMLHttpRequest instance
  var req = newXMLHttpRequest();

  // Set the handler function to receive callback notifications
  // from the request object
  var handlerFunction = getReadyStateHandler(req, responseFunction, processFile, reqSend, errorCount);
  req.onreadystatechange = handlerFunction;

  // Open an HTTP POST connection to the php file
  // Third parameter specifies request is asynchronous.
  req.open("GET", processFile+reqSend, true);

  // Specify that the body of the request contains form data
  //req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  // Send form encoded data
  req.send(null);
}

/*
 * Change the pending status of the specified ban id.  The status becomes the opposite 
 * of the current value.  The possible active values are 0 (off) and 1 (on) 
 */
function changePendingStatus(id, pending) {
  var processFile = "index.php?page=changePendingStatus&ajax=1";
  var reqSend = "&id="+id+"&pending="+pending;

  createRequest(htmlUpdatePendingStatus, processFile, reqSend, 0);
}

/*
 * This method is called once the changePendingaStatus method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated. 
 */
function htmlUpdatePendingStatus(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var pending = root.getElementsByTagName("update")[0].firstChild.nodeValue;
    var html = "";
    
    if(pending == 1) {
      //html = "<img src=\"images/hourglass.png\"/>";
      document.getElementById("pending:"+id).childNodes[0].childNodes[1].src = "images/hourglass.png";
    } else {
      //html = "<img src=\"images/cross.png\"/>";
      document.getElementById("pending:"+id).childNodes[0].childNodes[1].src = "images/cross.png";
    }
    
    // Change the html/image
    //document.getElementById("pending:"+id).innerHTML = html;
    // Update the onclick function
    document.getElementById("pending:"+id).onclick = function() { changePendingStatus(id, pending); };
  }
}


/**
 * This method is called to save the new server information.
 */ 
function saveServer(id) {
  var processFile = "index.php?page=saveServerInfo&ajax=1";

  var name = escape(document.getElementById("serverName:"+id).value);
  var ip = document.getElementById("serverIp:"+id).value;
  var port = document.getElementById("serverPort:"+id).value;
  var type = document.getElementById("serverType:"+id).value;
  var rcon = escape(document.getElementById("serverRcon:"+id).value);
  var currentRcon = escape(document.getElementById("currentServerRcon:"+id).value);
  
  var errorFound = false;
  var alertMessage = "";

  // Validate IP
  var regex = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
  if(!ip.match(regex)) {
    alertMessage += "Server IP is invalid.\n\r";
    errorFound = true;
  }
  
  // Validate Port
  var regex = /^\d{1,8}$/;
  if(!port.match(regex)) {
    alertMessage += "Server Port is invalid.\n\r";
    errorFound = true;
  }
  
  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
    return false;
  }
  
  var reqSend = "&id="+id+"&name="+name+"&ip="+ip+"&port="+port+"&rcon="+rcon+"&currentRcon="+currentRcon+"&type="+type;

  createRequest(serverSaved, processFile, reqSend, 0);
}

/**
 * This method is called once the saveServerInfo has recieved its XML response.
 */ 
function serverSaved(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the name we updated
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    // Returns the success message
    var success = root.getElementsByTagName("success")[0].firstChild.nodeValue;
    
    if(success == "true") {
      alert(name+" Saved!");
    } else {
      alert("The current RCON password supplied for " + name + " did not match.  Save Failed!");
    }
  }
}



/**
 * This method is called to save the new ban reason
 */ 
function saveBanReason(id) {
  var processFile = "index.php?page=saveBanReason&ajax=1";

  var reason = document.getElementById("reasonText:"+id).value;
  
  // Convert + to |plus|
  // Convert & to |amp|
  reason = reason.replace(/\+/g, "|plus|"); // + needs to be escaped
  reason = reason.replace(/&/g, "|amp|");

  var reqSend = "&id="+id+"&reason="+reason;

  createRequest(banReasonSaved, processFile, reqSend, 0);
}

/**
 * This method is called once the saveReason has recieved its XML response.
 */ 
function banReasonSaved(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the name we updated
    var reason = root.getElementsByTagName("reason")[0].firstChild.nodeValue;
    
    // Convert |amp| to &
    reason = reason.replace(/|amp|/, "&");
    
    alert(reason+" Saved!");
  }
}


/**
 * This method is called to save user information.
 */ 
function saveUser(id) {
  var processFile = "index.php?page=saveUserInfo&ajax=1";

  var name = escape(document.getElementById("username:"+id).value);
  var email = document.getElementById("userEmail:"+id).value;
  var accessLevel = document.getElementById("userAccessLevel:"+id).value;
  var steamId = document.getElementById("userSteamId:"+id).value;
  
  var errorFound = false;
  var alertMessage = "";

  // Validate Steam ID
  var regex = /^STEAM_[01]:[01]:\d{0,10}$/;
  if(!steamId.match(regex)) {
    alertMessage += "Steam ID is invalid.\n\r";
    errorFound = true;
  }
  
  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
    return false;
  }

  var reqSend = "&id="+id+"&name="+name+"&email="+email+"&accessLevel="+accessLevel+"&steamId="+steamId;

  createRequest(userSaved, processFile, reqSend, 0);
}

/**
 * This method is called once the saveUserInfo has recieved its XML response.
 */ 
function userSaved(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the name we updated
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var error = root.getElementsByTagName("error")[0].firstChild;
        
    if(error == null) {    
      alert(name+" Updated!");
    } else {
      error = root.getElementsByTagName("error")[0].firstChild.nodeValue;
      alert(error);
    }
  }
}

/*
 * Change the active status of the specified IP ban.  The status becomes the opposite
 * of the current value.  The possible active values are 0 (off) and 1 (on)
 */
function changeIpActiveStatus(ip, active) {
  var processFile = "index.php?page=changeIpActiveStatus&ajax=1";
  var reqSend = "&ip="+ip+"&active="+active;

  createRequest(htmlUpdateIpActiveStatus, processFile, reqSend, 0);
}

/*
 * This method is called once the changeIpActiveStatus method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateIpActiveStatus(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var ip = root.getElementsByTagName("ip")[0].firstChild.nodeValue;
    // This returns the updated status
    var active = root.getElementsByTagName("update")[0].firstChild.nodeValue;
    var html = "";

    if(active == 0) {
      //html = "<img src=\"images/cross.png\"/>";
      document.getElementById("active:"+ip).childNodes[1].src = "images/cross.png";
    } else {
      //html = "<img src=\"images/tick.png\"/>";
      document.getElementById("active:"+ip).childNodes[1].src = "images/tick.png";
    }

    // Change the html/image
    //document.getElementById("active:"+ip).innerHTML = html;
    // Update the onclick function
    document.getElementById("active:"+ip).onclick = function() { changeIpActiveStatus(ip, active); };
  }
}

/*
 * Process the admin list and upload it to the specified server
 */
function uploadAdmins(id) {
  var processFile = "index.php?page=saveAdminListToServer&ajax=1";
  var reqSend = "&serverId="+id;
  
  createRequest(htmlUpdateUploadAdmins, processFile, reqSend, 0);
}

/*
 * This method is called once the uploadAdmins method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateUploadAdmins(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var active = root.getElementsByTagName("active")[0].firstChild.nodeValue;
    var html = "";
    if(active == "1") {
      html = "Completed saving admin list to " + name + " <img src=\"images/tick.png\"/>";
    } else {
      html = "Failed to save the admin list to " + name + ".  The server settings may be incorrect or the web server firewall is blocking rcon commands.";
    }

    // Change the html
    document.getElementById("server:"+id).innerHTML = html;
  }
}

/*
 * Process the admin list and upload it to the specified server
 */
function uploadBanReasons(id) {
  var processFile = "index.php?page=saveReasonListToServer&ajax=1";
  var reqSend = "&serverId="+id;

  createRequest(htmlUpdateUploadBanReasons, processFile, reqSend, 0);
}

/*
 * This method is called once the uploadAdmins method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateUploadBanReasons(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var active = root.getElementsByTagName("active")[0].firstChild.nodeValue;
    
    var html = "";
    if(active == "1") {
      html = "Completed saving Ban Reasons to " + name + " <img src=\"images/tick.png\"/>";
    } else {
      html = "Failed to save Ban Reasons to " + name + ".  The server settings may be incorrect or the web server firewall is blocking rcon commands.";
    }

    // Change the html
    document.getElementById("server:"+id).innerHTML = html;
  }
}

/*
 * Process the ban length list and upload it to the specified server
 */
function uploadBanLengths(id) {
  var processFile = "index.php?page=saveLengthListToServer&ajax=1";
  var reqSend = "&serverId="+id;

  createRequest(htmlUpdateUploadBanLengths, processFile, reqSend, 0);
}

/*
 * This method is called once the uploadAdmins method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateUploadBanLengths(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var active = root.getElementsByTagName("active")[0].firstChild.nodeValue;

    var html = "";
    if(active == "1") {
      html = "Completed saving Ban Lengths to " + name + " <img src=\"images/tick.png\"/>";
    } else {
      html = "Failed to save Ban Lengths to " + name + ".  The server settings may be incorrect or the web server firewall is blocking rcon commands.";
    }

    // Change the html
    document.getElementById("server:"+id).innerHTML = html;
  }
}

/*
 * Change the active status of the specified ban id.  The status becomes the opposite
 * of the current value.  The possible active values are 0 (off) and 1 (on)
 */
function changeUserActiveStatus(id, active) {
  var processFile = "index.php?page=changeUserActiveStatus&ajax=1";
  var reqSend = "&id="+id+"&active="+active;

  createRequest(htmlUpdateUserActiveStatus, processFile, reqSend, 0);
}

/*
 * This method is called once the changeActiveStatus method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateUserActiveStatus(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var active = root.getElementsByTagName("update")[0].firstChild.nodeValue;
    var html = "";

    if(active == 0) {
      //html = "<img src=\"images/cross.png\"/>";
      document.getElementById("active:"+id).childNodes[1].childNodes[1].src = "images/cross.png";
    } else {
      //html = "<img src=\"images/tick.png\"/>";
      document.getElementById("active:"+id).childNodes[1].childNodes[1].src = "images/tick.png";
    }

    // Change the html/image
    //document.getElementById("active:"+id).innerHTML = html;
    // Update the onclick function
    document.getElementById("active:"+id).onclick = function() { changeUserActiveStatus(id, active); };
  }
}


/*
 * Change the pending status of the specified ban id.  The status becomes the opposite
 * of the current value.  The possible active values are 0 (off) and 1 (on)
 */
function changeBadNameFilter(id, filter) {
  var processFile = "index.php?page=changeBadNameFilter&ajax=1";
  var reqSend = "&id="+id+"&filter="+filter;

  createRequest(htmlUpdateBadNameFilter, processFile, reqSend, 0);
}

/*
 * This method is called once the changeBadNameFilter method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateBadNameFilter(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var filter = root.getElementsByTagName("update")[0].firstChild.nodeValue;
    var html = "";

    if(filter == 1) {
      //html = "<img src=\"images/tick.png\" style=\"cursor:pointer;\"/>";
      document.getElementById("filter:"+id).childNodes[1].src = "images/tick.png";
    } else {
      //html = "<img src=\"images/cross.png\" style=\"cursor:pointer;\"/>";
      document.getElementById("filter:"+id).childNodes[1].src = "images/cross.png";
    }

    // Change the html/image
    //document.getElementById("filter:"+id).innerHTML = html;
    // Update the onclick function
    document.getElementById("filter:"+id).onclick = function() { changeBadNameFilter(id, filter); };
  }
}


/*
 * Change the pending status of the specified ban id.  The status becomes the opposite
 * of the current value.  The possible active values are 0 (off) and 1 (on)
 */
function changeBadNameKick(id, kick) {
  var processFile = "index.php?page=changeBadNameKick&ajax=1";
  var reqSend = "&id="+id+"&kick="+kick;

  createRequest(htmlUpdateBadNameKick, processFile, reqSend, 0);
}

/*
 * This method is called once the changeBadNameKick method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateBadNameKick(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var kick = root.getElementsByTagName("update")[0].firstChild.nodeValue;
    var html = "";

    if(kick == 1) {
      //html = "<img src=\"images/tick.png\" style=\"cursor:pointer;\"/>";
      document.getElementById("kick:"+id).childNodes[1].src = "images/tick.png";
    } else {
      //html = "<img src=\"images/cross.png\" style=\"cursor:pointer;\"/>";
      document.getElementById("kick:"+id).childNodes[1].src = "images/cross.png";
    }

    // Change the html/image
    //document.getElementById("kick:"+id).innerHTML = html;
    // Update the onclick function
    document.getElementById("kick:"+id).onclick = function() { changeBadNameKick(id, kick); };
  }
}



/*
 * Process the server list and upload the updated config information
 */
function saveServerConfig(id) {
  var processFile = "index.php?page=saveConfigToServer&ajax=1";
  var reqSend = "&serverId="+id;

  createRequest(htmlUpdateUploadConfig, processFile, reqSend, 0);
}

/*
 * This method is called once the saveServerConfig method has recieved its
 * xml response.  With this xml, the appropriate html areas are updated.
 */
function htmlUpdateUploadConfig(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root "submission" element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the updated status
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var success = root.getElementsByTagName("success")[0].firstChild.nodeValue;
    var port = root.getElementsByTagName("port")[0].firstChild.nodeValue;
    var html = "";
    if(success == "1") {
      html = "Completed saving configuration to " + name + " <img src=\"images/tick.png\"/>";
    } else {
      html = "Failed to save the configuration to " + name + ".  The server settings may be incorrect or the web server firewall is blocking outgoing tcp/udp port " + port + " required for sending rcon commands.";
    }

    // Change the html
    document.getElementById("server:"+id).innerHTML = html;
  }
}

// ADD FANTOLE
function saveServerGroup(id) {
  var processFile = "index.php?page=updateServerGroups&ajax=1";
  
  var name = escape(document.getElementById("groupName:"+id).value);
  var description = document.getElementById("groupDescription:"+id).value;
  
  var errorFound = false;
  var alertMessage = "";

  // Validate Name
  if(name == "") {
    alertMessage += "Name is empty.\n\r";
    errorFound = true;
  }
  
  // Validate Descrition
  if(description == "") {
    alertMessage += "Description is empty.\n\r";
    errorFound = true;
  }  
  
  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
	document.location.reload();return(false)
    return false;
  }
  
  var reqSend = "&id="+id+"&name="+name+"&description="+description;

  createRequest(serverSavedGroup, processFile, reqSend, 0);
}

function serverSavedGroup(xml) {

  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we updated
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the name we updated
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
	// This returns the descrition we updated
    // Returns the success message
    var success = root.getElementsByTagName("success")[0].firstChild.nodeValue;
    
    if(success == "true") {
      alert(name+" Saved!");
    } else {
      alert("The current Description for " + name + " did not match.  Save Failed!");
    }
  }
}

function deleteVerify(id, name) {
  if(confirm("Do you really want to delete "+name+"?")) {
  var processFile = "index.php?page=deleteServerGroups&ajax=1";
  
  var name = escape(document.getElementById("groupName:"+id).value);
  //var description = document.getElementById("groupDescription:"+id).value;
  
  var errorFound = false;
  var alertMessage = "";

  // Validate ID
  if(id == "") {
    alertMessage += "The name doesn't exist.\n\r";
    errorFound = true;
  }
  
  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
	document.location.reload();return(false)
    return false;
  }
  
  var reqSend = "&id="+id+"&name="+name;

  createRequest(serverDeleteGroup, processFile, reqSend, 0);
}}

function serverDeleteGroup(xml) {
  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we deleted
    var id = root.getElementsByTagName("id")[0].firstChild.nodeValue;
    // This returns the name we deleted
    var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    // Returns the success message
    var success = root.getElementsByTagName("success")[0].firstChild.nodeValue;
    
    if(success == "true") {
      alert(name+" Deleted!");
	  document.location.href="index.php?page=manageServerGroups&adminPage=1"
    } else {
      alert("The current Server Group " + name + " did not match.  Delete Failed!");
    }
  }
}







// ADD

function deleteVerify2(server_group_id , admin_id, name) {
  if(confirm("Do you really want to delete "+name+"?")) {
  var processFile = "index.php?page=deleteServerAdmin&ajax=1";
  
  //var name = escape(document.getElementById("deleteUser:"+id).value);
  //var description = document.getElementById("groupDescription:"+id).value;
  
  var errorFound = false;
  var alertMessage = "";

  // Validate ID
  if(server_group_id == "") {
    alertMessage += "The name doesn't exist.\n\r";
    errorFound = true;
  }
  
  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
	document.location.reload();return(false)
    return false;
  }
  
  var reqSend = "&server_group_id="+server_group_id+"&admin_id="+admin_id+"&name="+name;

  createRequest(serverDeleteUser, processFile, reqSend, 0);
}}

function serverDeleteUser(xml) {
  // Make sure we have an XML object
  if(xml === null) {
    return; // Do nothing
  }

  // Get the root element from the document
  var root = xml.getElementsByTagName("root")[0];
  if(root !== null) {
    // This returns the id we deleted
    var server_group_id = root.getElementsByTagName("server_group_id")[0].firstChild.nodeValue;
    // This returns the name we deleted
    var admin_id = root.getElementsByTagName("admin_id")[0].firstChild.nodeValue;
    // Returns the success message
	var name = root.getElementsByTagName("name")[0].firstChild.nodeValue;
    var success = root.getElementsByTagName("success")[0].firstChild.nodeValue;
    
    if(success == "true") {
      alert(name+" Deleted!");
	  document.location.href="index.php?page=manageServerAdmins&adminPage=1&serverGroupId="+server_group_id
    } else {
      alert("The current Server Group ID = " + name + " did not match.  Delete Failed!");
    }
  }
}