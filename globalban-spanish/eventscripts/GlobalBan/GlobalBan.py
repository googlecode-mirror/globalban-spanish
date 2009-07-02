# -*- coding: utf-8 -*-
"""
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

*** DO NOT MODIFY ANYTHING IN THIS SCRIPT UNLESS YOU KNOW WHAT YOU ARE DOING ***

This script allows for easy banning of a user.  Once banned, the user can
be un-banned from the web-application as the ban is stored inside of a mysql
database.  Also, if the user attempts to join the server, they will get auto-kicked
as it will do a check against the mysql database to see if they exist and if
their ban period has yet to expire.  When the user has been banned on once server,
the user will be banned on all servers that are stored within the mysql table
that contains the servers that are running.  The script must be intalled in all
servers and the server ID must match the ID inside of the servers mysql table.


To ban, simply type !banmenu in say mode.  This will first let you select a
ban reason, followed by a ban length, and then finally the user to ban.  The
command is hidden from say and can not be seen by the public and is only
accessible by those found in the clan_db script.


For developers that wish to integrate GlobalBan into their script, please call
the regcmd gb_externalBanUser.  To execute it, do the following from your script:
  es gb_externalBanUser <adminUserId/adminSteamId> <steamIdToBan> <reasonIdOfBan> <lengthOfBan> <timeScale> <nameOfBanned>
The following is an example:
The admin with userid of 432 is banning the user with Steam ID STEAM_0:0:111111 for reason id 1 forever
Timescale can be in minutes, hours, days, weeks, or months
  es gb_externalBanUser 432 "STEAM_0:0:111111" 1 0 minutes myg0t
  es gb_externalBanUser "STEAM_0:0:000001" "STEAM_0:0:111111" 1 0 minutes myg0t

For scripts that automatically ban, you will want to use the following command:
The IP field is an optional field and is not required
  es gb_consoleBanUser <steamIdToBan> <banReasonId> <lengthOfBanInMinutes> <nameOfBanned> <ipOfBanned>
The following is an example:
  gb_consoleBanUser "STEAM_0:0:000000" 1 60 Test
This will ban Test who is identified by steam id STEAM_0:0:000000 for 60 minutes for ban reason #1.

*** Note: You must have quotes around steam ids ***


The following MUST be placed inside of your autoexec.cfg file.
es_load GlobalBan

Requirements:
PHP 4 or 5
MySQL 4.1+ or 5+
EventScripts v2.0+
"""

import es
import playerlib
import os
import urllib2
import keymenulib
import gamethread
import datetime
import time
from configobj import ConfigObj

#plugin information
info = es.AddonInfo()
info.name = "GlobalBan"
info.version = "3.4 RC3"
info.author = "Soynuts"
info.url = "http://www.unbuinc.net"
info.description = "Bans are stored in a mySQL database on a webserver"
info.basename = "GlobalBan"

# Default config values
serverId = 1
websiteAddy = "http://yourdomain.com/banned/"
banAppealMessage = "Banned. Appeal at yourdomain.com"
hashCode = "secretHashCode"
teachAdmins = 1
clanName = "Your Clan Name or Community"
allowAdminBanning = 1
wgetPath = "wget"
pluginMani = 0
pluginSourceMod = 0

# Variables that will be global
playersChecked = {} # Initialize the dictionary of players checked
badAccess = {} # Initialize the dictionary of players trying to access the banmenu

# Debugging (Only turn on if you need it [True or False])
debugMode = False

################################################################################
################################################################################
# DO NOT EDIT BELOW HERE UNLESS YOU KNOW WHAT YOU ARE DOING
################################################################################
################################################################################

################################################################################
# This will load the script up and initialize any threads and values that must
# be created.
################################################################################
def load():
  # Define the global values in this script
  global serverId, websiteAddy, banAppealMessage, hashCode, teachAdmins, clanName, allowAdminBanning, playersChecked, badAccess, wgetPath

  gbanLog('###############################################')
  gbanLog('Global Ban Has Been Loaded')
  gbanLog('###############################################\n')

  # make sure the dictionaries are clear
  playersChecked.clear()
  badAccess.clear()

  # Register the necessary commands for this script
  es.regsaycmd('!banmenu', 'GlobalBan/banReasonList', 'Ban the selected user')
  es.regcmd('gb_externalBanUser', 'GlobalBan/banExternal', 'Allow other scripts to add to the ban list when executed by an admin')
  es.regcmd('gb_consoleBanUser', 'GlobalBan/banFromConsole', 'Allow other scripts to add to the ban list automatically')
  es.regcmd('gb_saveConfig', 'GlobalBan/saveConfig', 'Used to create the GlobalBan.cfg file from the web')
  es.regcmd('gb_loadConfig', 'GlobalBan/loadConfig', 'Used to load the GlobalBan.cfg file')
  es.regcmd('gb_refreshAdmins', 'GlobalBan/refreshAdmins', 'Used to get a new admin list from the web')
  es.regcmd('gb_refreshBanReasons', 'GlobalBan/refreshBanReasons', 'Used to get a new ban reasons from the web')
  es.regcmd('gb_refreshBanLengths', 'GlobalBan/refreshBanLengths', 'Used to get a new ban lengths from the web')

  # Load the config file
  loadConfig()
  
  # If windows, we want the full path to wget.exe
  if os.name == "nt":
    wgetPath = es.getAddonPath('GlobalBan') + "/wget.exe"

  # Get the list of plugins this server is enabled for
  os.system(wgetPath + " -b --quiet -O " + es.getAddonPath('GlobalBan') + "/plugins.cfg -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getPluginConfig&es=1&serverId=" + str(serverId) + "\"")
  gamethread.delayed(10, loadPluginConfig)

  refreshLists()
  gamethread.delayed(10, updateAdminModLists) # Wait 10 seconds before refreshing the admin lists

################################################################################
# This will read the GlobalBan.cfg file and store the values into the script
################################################################################
def loadConfig():
  global serverId, websiteAddy, banAppealMessage, hashCode, teachAdmins, clanName, allowAdminBanning
  
  gbanLog('GBAN: Attempting to load values from GlobalBan.cfg')
  
  # Look for the GlobalBan config file and load the values from it
  if os.path.isfile(es.getAddonPath('GlobalBan') + '/GlobalBan.cfg'):
    cfg = ConfigObj(es.getAddonPath('GlobalBan') + '/GlobalBan.cfg')

    serverId = cfg['serverId']
    websiteAddy = cfg['websiteAddy']
    banAppealMessage = cfg['banAppealMessage']
    hashCode = cfg['hash']
    teachAdmins = int(cfg['teachAdmins'])
    clanName = cfg['clanName']
    allowAdminBanning = cfg['allowAdminBanning']
  else:
    es.dbgmsg(0, 'GlobalBan: Unable to load GlobalBan.cfg! Please ensure it is in the ./GlobalBan/ directory.')
    gbanLog('GBAN: The GlobalBan.cfg file is not set up correctly!!')
    es.msg('#lightgreen', 'The GlobalBan.cfg file is not set up correctly!!')
    es.msg('#lightgreen', 'The GlobalBan.cfg file is not set up correctly!!')
    gbanLog('GBAN: Please execute a "Save Configuration" from the web, or update the file manually')
    es.msg('#lightgreen', 'Please execute a "Save Configuration" from the web, or update the file manually')
    gbanLog('GBAN: Please save from the web again, or update manually')
    
  # If the website addy is the default... then the cfg file did not load
  if websiteAddy == "http://yourdomain.com/banned/":
    gbanLog('GBAN: The GlobalBan.cfg file is not set up correctly!!')
    es.msg('#lightgreen', 'The GlobalBan.cfg file is not set up correctly!!')
    es.msg('#lightgreen', 'The GlobalBan.cfg file is not set up correctly!!')
    gbanLog('GBAN: Please execute a "Save Configuration" from the web, or update the file manually')
    es.msg('#lightgreen', 'Please execute a "Save Configuration" from the web, or update the file manually')
    gbanLog('GBAN: Please save from the web again, or update manually')

################################################################################
# This will read the plugin.cfg file and store the values into the script
################################################################################
def loadPluginConfig():
  global serverId, websiteAddy, pluginMani, pluginSourceMod
  
  gbanLog('GBAN: Attempting to load values from plugins.cfg')
  
  # Look for the Plugin config file and load the values from it
  if os.path.isfile(es.getAddonPath('GlobalBan') + '/plugins.cfg'):
    cfg = ConfigObj(es.getAddonPath('GlobalBan') + '/plugins.cfg')
        
    pluginMani = int(cfg['mani'])
    pluginSourceMod = int(cfg['sourcemod'])
    
    if debugMode:
      gbanLog('GBAN-DEBUG: Mani Enabled? ' + str(pluginMani))
      gbanLog('GBAN-DEBUG: SourceMod Enabled? ' + str(pluginSourceMod))
    
    # Now update the admin lists
    updateAdminModLists()
  
################################################################################
# Unset and delete specific variables when the script is unloaded
################################################################################
def unload():
  global playersChecked, badAccess
  
  # Clear the dictionaries
  playersChecked.clear()
  badAccess.clear()
  
  # Delete the dictionaries
  del playersChecked
  del badAccess
  
################################################################################
# Refresh all lists
################################################################################
def refreshLists():
  gamethread.delayed(5, refreshAdmins)
  gamethread.delayed(5, refreshBanReasons)
  gamethread.delayed(5, refreshBanLengths)

################################################################################
# On every map change we want to get the latest admin, ban reason, and ban length
# lists.
################################################################################
def es_map_start(event_var):
  # Get the list of plugins this server is enabled for
  os.system(wgetPath + " -b --quiet -O " + es.getAddonPath('GlobalBan') + "/plugins.cfg -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getPluginConfig&es=1&serverId=" + serverId + "\"")
  gamethread.delayed(10, loadPluginConfig)
  
  refreshLists()
  gamethread.delayed(12, updateAdminModLists)

################################################################################
# On player active (when the player connects to the server), check to see if they
# are allowed to play
################################################################################
def player_activate(event_var):
  if not es.isbot(event_var['userid']):
    # Cannot pass event_var as it is invalid by the time validatePlayer executes
    # We delay the validation for 1 second in case the user's steam id is pending
    gamethread.delayed(1, validatePlayer, event_var['userid'])

################################################################################
# On player disconnect, remove them from the players checked dictionary.
################################################################################
def player_disconnect(event_var):
  global playersChecked, badAccess, debugMode

  if debugMode:
    gbanLog('GBAN-DEBUG: Players Checked Dictionary Size: ' + str(len(playersChecked)))
    gbanLog('GBAN-DEBUG: Bad Acess Dictionary Size: ' + str(len(badAccess)))

  steamId = event_var['es_steamid']
  
  if steamId is not None:
    # Remove the player from the playersChecked dictionary
    if playersChecked.has_key(event_var['es_steamid']):
      del playersChecked[event_var['es_steamid']]
    # Remove the player from the badAccess dictionary
    if badAccess.has_key(event_var['es_steamid']):
      del badAccess[event_var['es_steamid']]
      
  # If it's this large, then we likely have a leak and need to reset these lists
  if len(playersChecked) > 100:
    playersChecked.clear()
    badAccess.clear()

################################################################################
# On admin death, display a message on how to bring up the ban menu
################################################################################
def player_death(event_var):
  if not es.isbot(event_var['userid']):
    if teachAdmins == 1:
      # This tells admins every time they die how to ban a player
      # This is to help them learn the command
      if isMember(event_var['es_steamid']):
        es.tell(event_var['userid'], '#multi', '#greenPara abrir el menu de banear di en el chat: #default!banmenu')

################################################################################
# Determines if the person trying to use this script is an admin
################################################################################
def isMember(steamId):
  if not es.exists('keygroup', 'clanMembers'):
    es.keygroupcreate('clanMembers')
    es.keygroupload('clanMembers', '|GlobalBan')

  memberCheck = es.keygetvalue('clanMembers', steamId, 'member')
  if memberCheck == '1':
    return True
  else:
    return False

################################################################################
# Validates the player agains the mySQL database
################################################################################
def validatePlayer(playerid):
  global websiteAddy, hashCode, serverId, playersChecked, badAccess, wgetPath, debugMode

  playerSteamId = es.getplayersteamid(playerid)
  
  if debugMode:
    gbanLog('GBAN-DEBUG: Checking player ' + playerSteamId)
  
  if playerSteamId is not None:
    # If they are not found, that means they have not been checked yet
    if not playersChecked.has_key(playerSteamId):
      if not es.isbot(playerid):
        playerName = es.getplayername(playerid)

        # Get IP address of the person
        player = playerlib.getPlayer(playerid)
        userIp = player.attributes["address"]
        
        ipAddy, port = userIp.split(":")
        
        if debugMode:
          gbanLog('GBAN: Player address is ' + ipAddy)

        # The webpage to check if the person is banned
        banCheckURL = websiteAddy + 'index.php?page=checkUser&es=1'
        banCheckURL += '&steamId=' + playerSteamId
        banCheckURL += '&sid=' + str(serverId)
        banCheckURL += '&hash=' + hashCode
        banCheckURL += '&name=' + urllib2.quote(playerName)
        banCheckURL += '&ip=' + ipAddy

        gbanLog('GBAN: Validate Player URL:' + banCheckURL)

        os.system(wgetPath + " --delete-after -b --quiet -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + banCheckURL+"\"")
    
        gbanLog('GBAN: ' + es.getplayersteamid(playerid) + ' has been added to the player checked list')
        # Add the player's steam id to the dictionary of checked players
        playersChecked[playerSteamId] = playerSteamId
        badAccess[playerSteamId] = 0 # Set to zero

################################################################################
# This method brings up the ban reason menu, which is displayed when the admin
# invokes the !banmenu command.
################################################################################
def banReasonList():
  global badAccess
  # Get the userid of the person calling this menu
  playerid = es.getcmduserid()
  
  # Member check only needs to be performed on this menu
  if isMember(es.getplayersteamid(playerid)):
    if es.exists('keygroup', 'GlobalBan_Reason'):
      # Open the keygroup that contains all reason codes
      es.keygroupdelete('GlobalBan_Reason')
    es.keygroupload('GlobalBan_Reason', '|GlobalBan')

    # Create keymenu called banReasonMenu for user to select from
    banReasonMenu = keymenulib.create("banReasonMenu", "selectedBanReason", banLengthList, "GlobalBan_Reason", "#keyvalue reasonText", "#key", "Ban Reason List")
    banReasonMenu.send(playerid)
  else:
    es.tell(playerid, '#green', 'No eres aun un admin, registrate en wwww.clanlds.es/baneados para serlo!')
    # Increment the number of attempts
    badAccess[es.getplayersteamid(playerid)] = int(badAccess[es.getplayersteamid(playerid)]) + 1
    if int(badAccess[es.getplayersteamid(playerid)]) > 4:
      # Remove the player from the badAccess dictionary
      if badAccess.has_key(event_var['es_steamid']):
        del badAccess[es.getplayersteamid(playerid)]
      # Kick the player
      es.server.cmd('kickid ' + str(playerid) + ' Has sido expulsado por intentar usar un comando de admin');

################################################################################
# This method brings up the ban length menu, which is displayed after the admin
# selects a ban reason from the ban reason menu
################################################################################
def banLengthList(playerid, selectedBanReason, popupid):
  # Now set the ban reason selected to the correct admin in the admin keylist
  # Stored to the keylist so that it can be "passed" on
  # And to prevent other admins from stomping on each other
  es.keysetvalue('clanMembers', es.getplayersteamid(playerid), 'banReason', selectedBanReason)

  if es.exists('keygroup', 'GlobalBan_Length'):
    # Open the keygroup that contains all ban lengths
    es.keygroupdelete('GlobalBan_Length')
  es.keygroupload('GlobalBan_Length', '|GlobalBan')

  # Create keymenu called banLengthMenu for user to select from
  banLengthMenu = keymenulib.create("banLengthMenu", "selectedBanLength", banPlayerMenu, "GlobalBan_Length", "#keyvalue readable", "#key", "Ban Lengths")
  banLengthMenu.send(playerid)

################################################################################
# This method brings up the playerlist menu, which is displayed after the admin
# selects a ban length from the ban length menu
################################################################################
def banPlayerMenu(playerid, selectedBanLength, popupid):
  # Now set the ban length selected to the correct admin in the admin keylist
  # Stored to the keylist so that it can be "passed" on
  # And to prevent other admins from stomping on each other
  es.keysetvalue('clanMembers', es.getplayersteamid(playerid), 'banLength', selectedBanLength)

  if es.exists('keygroup', 'playerlist'):
    es.server.cmd('es_xkeygroupdelete playerlist')
  es.server.cmd('es_xcreateplayerlist playerlist')

  # Create keymenu called banmenu for admin to select a player from
  banmenu = keymenulib.create("banmenu", "userToBan", banInGame, "playerlist", "#keyvalue name", "#key", "Player List")
  banmenu.send(playerid)

  # Delete the playerlist
  es.server.cmd('es_xkeygroupdelete playerlist')

################################################################################
# This is the ban method used by this script
################################################################################
def banInGame(playerid, userToBan, popupid):
  callerSteamId = es.getplayersteamid(playerid)

  banReason = es.keygetvalue('clanMembers', callerSteamId, 'banReason')
  banLength = es.keygetvalue('clanMembers', callerSteamId, 'banLength')

  # No point banning if the selected person is really a bot
  if not es.isbot(userToBan):
    # Get the player's Steam ID that is being banned
    bannedSteamId = es.getplayersteamid(userToBan)

    # Get IP address of the person being banned
    player = playerlib.getPlayer(userToBan)
    bannedUserIp = player.attributes["address"]

    # Get the name of the player being banned
    nameOfBannedPlayer = es.getplayername(userToBan)

    # When timescale is set to "i" (for ignore) that means the length is actually the length ID
    banUser(playerid, bannedSteamId, banReason, banLength, "i", nameOfBannedPlayer, bannedUserIp)
  else:
    es.tell(callerId, '#green', 'No puedes banear a un bot')

################################################################################
# This method takes in 5 arguments and is used by external scripts wishing to tap
# into GlobalBan.  This is used if an admin is executing a command from another script.
################################################################################
def banExternal():
  callerId = es.getargv(1) # The admin's steam ID
  bannedSteamId = es.getargv(2)
  banReason = es.getargv(3)
  banLength = es.getargv(4)
  timeScale = es.getargv(5)
  nameOfBanned = es.getargv(6)

  banUser(callerId, bannedSteamId, banReason, banLength, timeScale, nameOfBanned)

################################################################################
# This method takes in 5 arguments and is used by external scripts wishing to tap
# into GlobalBan.  This is used if the script is executing an automatic ban.
################################################################################
def banFromConsole():
  bannedSteamId = es.getargv(1)
  banReason = es.getargv(2)
  banLength = int(es.getargv(3)) # This is in minutes
  nameOfBanned = es.getargv(4)
  ipOfBanned = es.getargv(5)
  
  # Convert minutes to the appropriate length and timescale
  # At a later date this will be removed as all lengths will eventually be in minutes  
  if banLength < 60:
    timeScale = "minutes"
  elif banLength >= 60 and banLength < 1440: # between 1 hour and 24 hours
    timeScale = "hours"
    banLength = banLength / 60
  elif banLength >= 1440 and banLength < 10080: # between 1 day and 7 days
    timeScale = "days"
    banLength = banLength / 1440
  elif banLength >= 10080 and banLength < 40320: # between 1 week and 4 weeks
    timeScale = "weeks"
    banLength = banLength / 10080
  elif banLength >= 40320:
    timeScale = "months"
    banLength = banLength / 40320
    
  banUser(-1, bannedSteamId, banReason, str(banLength), timeScale, nameOfBanned, ipOfBanned)
  
################################################################################
# This method will send the user's information to the website to be added to the
# ban database and it will kick the user from the server.
################################################################################
def banUser(callerId, bannedSteamId, banReason, banLength, timeScale, nameOfBanned="", ipOfBanned=""):
  global websiteAddy, hashCode, serverId, wgetPath, debugMode

  # Get the steam ID of the admin
  if callerId > 0:
    callerSteamId = es.getplayersteamid(callerId)

    # Get the name of the admin banning
    adminName = es.getplayername(callerId)
  else:
    callerSteamId = "-1"
    adminName = "Console"
  
  # If it is a none type, that means callerId is a steam id or it is -1, signifying a console/auto ban
  if callerSteamId is None:
    callerSteamId = callerId
    adminName = ''
  
  if ipOfBanned == "":
    ipAddy = ""
  else:
    ipAddy, port = ipOfBanned.split(":")

  # Make sure the one being banned is not a member
  # Unless the flag to allow the banning of admins is set
  if allowAdminBanning == "1" or not isMember(bannedSteamId):
    # Build the URL for processing the ban
    banUserURL = websiteAddy + 'index.php?page=processServerBan&es=1'
    banUserURL += '&steamId=' + bannedSteamId
    banUserURL += '&len=' + banLength
    banUserURL += '&ts=' + timeScale
    banUserURL += '&r=' + banReason
    banUserURL += '&b=' + callerSteamId
    banUserURL += '&sid=' + str(serverId)
    banUserURL += '&hash=' + hashCode
    banUserURL += '&name=' + urllib2.quote(nameOfBanned)
    banUserURL += '&ip=' + ipAddy

    os.system(wgetPath + " --delete-after -b --quiet -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + banUserURL+"\"")

    # Log the URL used for banning
    gbanLog('GBAN: Ban User URL:' + banUserURL)

    # Add them to the banned_users.cfg file for 1 minute to prevent instant rejoin
    es.server.cmd('banid ' +  str(5) + ' ' + bannedSteamId)

    # Write the ban list
    es.server.cmd('writeid')
    
    if debugMode:
      gbanLog('GBAN-DEBUG: ' + bannedSteamId + ' added to local ban list for 5 minute')

    # Use the player object to kick the user with a ban appeal message
    # es.server.cmd('kickid "' + bannedSteamId + '" ' + banAppealMessage);
    
    if debugMode:
      gbanLog('GBAN-DEBUG: ' + bannedSteamId + ' has been kicked')
      gbanLog('GBAN-DEBUG: Banned by ' + callerSteamId)

    # Tell the server that the player has been banned and log it
    # es.msg('#lightgreen', nameOfBanned + ' ha sido baneado de todos los servidores del clan ' + clanName + '!')
    gbanLog('GBAN: ' + nameOfBanned + ' has been banned from ALL ' + clanName + ' servers!')
    gbanLog('GBAN: STEAM ID Banned: ' + bannedSteamId)
  else:
    es.tell(callerId, '#green', 'No puedes Banear a otro admin')

################################################################################
# This block recieves data from the web app and creates the GlobalBan.cfg file
# This may need to be split up if rcon can not send this much data or done a different way
################################################################################
def saveConfig():
  global serverId, websiteAddy, banAppealMessage, hashCode, teachAdmins, clanName, allowAdminBanning
  # Arguments
  p_serverId = es.getargv(1)
  p_websiteAddy = es.getargv(2)
  p_banAppealMessage = es.getargv(3)
  p_hash = es.getargv(4)
  p_teachAdmins = es.getargv(5)
  p_clanName = es.getargv(6)
  p_allowAdminBanning = es.getargv(7)

  serverId = p_serverId
  websiteAddy = p_websiteAddy
  banAppealMessage = p_banAppealMessage
  hashCode = p_hash
  teachAdmins = int(p_teachAdmins)
  clanName = p_clanName
  allowAdminBanning = p_allowAdminBanning

  # Open the GlobalBan.cfg file for writing
  cfg = open(es.getAddonPath('GlobalBan') + '/GlobalBan.cfg',"w")

  # Write the data to the cfg file
  cfg.write("serverId = " + p_serverId)
  cfg.write("\n")
  cfg.write("websiteAddy = \"" + p_websiteAddy + "\"")
  cfg.write("\n")
  cfg.write("banAppealMessage = \"" + p_banAppealMessage + "\"")
  cfg.write("\n")
  cfg.write("hash = \"" + p_hash + "\"")
  cfg.write("\n")
  cfg.write("teachAdmins = " + p_teachAdmins)
  cfg.write("\n")
  cfg.write("clanName = \"" + p_clanName + "\"")
  cfg.write("\n")
  cfg.write("allowAdminBanning = " + p_allowAdminBanning)

  # Close the file
  cfg.close()

################################################################################
# This will cause the admin list to be refreshed and synced with the MySQL DB
################################################################################
def refreshAdmins():
  global websiteAddy, serverId
  os.system(wgetPath + " -b --quiet -O " + es.getAddonPath('GlobalBan') + "/es_clanMembers_db.txt -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getAdminList&es=1&serverId="+serverId+"\"")
  gbanLog('GBAN: Updating Admin List')
  gamethread.delayed(10, reloadAdminKeyGroup)
  
################################################################################
# This will reload the admin keygroup
################################################################################
def reloadAdminKeyGroup():
  if es.exists('keygroup', 'clanMembers'):
    es.keygroupdelete('clanMembers')
  es.keygroupload('clanMembers', '|GlobalBan')
  
################################################################################
# This will cause the ban reason list to be refreshed and synced with the MySQL DB
################################################################################
def refreshBanReasons():
  global websiteAddy
  os.system(wgetPath + " -b --quiet -O " + es.getAddonPath('GlobalBan') + "/es_GlobalBan_Reason_db.txt -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getBanReasonList&es=1\"")
  gbanLog('GBAN: Updating Ban Reason List')
  gamethread.delayed(10, reloadBanReasonList)

################################################################################
# This will reload the ban reason keygroup
################################################################################
def reloadBanReasonList():
  if es.exists('keygroup', 'GlobalBan_Reason'):
    es.keygroupdelete('GlobalBan_Reason')
  es.keygroupload('GlobalBan_Reason', '|GlobalBan')

################################################################################
# This will cause the ban length list to be refreshed and synced with the MySQL DB
################################################################################
def refreshBanLengths():
  global websiteAddy
  os.system(wgetPath + " -b --quiet -O " + es.getAddonPath('GlobalBan') + "/es_GlobalBan_Length_db.txt -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getBanLengthList&es=1\"")
  gbanLog('GBAN: Updating Ban Length List')
  gamethread.delayed(10, reloadBanLengthList)

################################################################################
# This will reload the ban length keygroup
################################################################################
def reloadBanLengthList():
  if es.exists('keygroup', 'GlobalBan_Length'):
    es.keygroupdelete('GlobalBan_Length')
  es.keygroupload('GlobalBan_Length', '|GlobalBan')

################################################################################
# This will update the admin lists for the various admin mods that may be installed on the server.
# Doing a file detection makes things a bit easier and more flexible for future mod additions without the need of having to add
# more configuration values.
# If the file comes back as empty, do NOT overwrite the existing one.
################################################################################
def updateAdminModLists():
  global serverId
  gbanPath = es.getAddonPath('GlobalBan')
  # Strip out eventscripts/addons/GlobalBan = 29 characters 
  basePath = gbanPath[0:len(gbanPath)-29]
  
  # Determine what admin mods exist by looking for the admin files
  # If the admin mod files do not exist, then the mod is not installed
  
  # Mani Detection
  # See if cfg/mani_admin_plugin/clients.txt exists
  if pluginMani == 1:
    maniPath = basePath + "cfg/mani_admin_plugin/"
    os.system(wgetPath + " -b --quiet -O " + maniPath + "clients.txt -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getManiList&es=1&serverId=" + serverId + "\"")
    os.system(wgetPath + " -b --quiet -O " + maniPath + "reserveslots.txt -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getManiReservedSlots&es=1&serverId=" + serverId + "\"")
    # Execute mani command to refresh admin list
    gamethread.delayed(10, reloadMani);
    gbanLog('GBAN: Updating Mani clients.txt file.')

  # SourceMod Detection
  # See if addons/soucemod/configs/admin_simple.ini exists
  if pluginSourceMod == 1:
    sourceModPath = basePath + "addons/sourcemod/configs/"
    os.system(wgetPath + " -b --quiet -O " + sourceModPath + "admin_groups.cfg -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getSourceModGroups&es=1&serverId=" + serverId + "\"")
    os.system(wgetPath + " -b --quiet -O " + sourceModPath + "admins_simple.ini -a " + es.getAddonPath('GlobalBan') + "/gban.log \"" + websiteAddy + "index.php?page=getSourceModList&es=1&serverId=" + serverId + "\"")
    # Execute sourcemod command to refresh admin list
    gamethread.delayed(10, reloadSourceMod);
    gbanLog('GBAN: Updating SourceMod admins_simple.ini and admin_group.cfg files.')

################################################################################
# This will force the mani admin list to reload after the new clients.txt file has been downloaded
################################################################################
def reloadMani():
  es.server.cmd('ma_reloadclients')

################################################################################
# This will force the sourcemod admin list to reload after the new admins_simple.ini and admin_group.cfg files have been downloaded
################################################################################
def reloadSourceMod():
  es.server.cmd('sm_reloadadmins')
  
################################################################################
# This method will log messages to the gban.log file.
################################################################################
def gbanLog(message):
  now = datetime.datetime.now()
  f = open(es.getAddonPath('GlobalBan') + '/logs/gban-'+now.strftime("%Y-%m-%d")+'.log','a')
  f.write(now.strftime("%Y-%m-%d %H:%M:%S") + ': ')
  f.write(message + '\n')
  f.close()
  # write the message to the regular game log file
  es.log(message)
