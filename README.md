#RW Change Deprecated Userlogins

Dieses Plugin wurde notwendig nach dem Import von Benutzern aus einem andenren System in Wordpress.
Das Plugin beseitigt insbesondere Leerzeichen und Punkte in Benutzernamen, um Konflikte in Wordpress zu vermeiden. 
Damit Benutzer sich trotzdem mit ihren "alten" Benutzernamen anmelden können, werden die in der Tabelle usermeta hinterlegt und bei einem Login überprüft.
Nach einem Login mit dem "alten" Benutzernamen bekommt der Nutzer eine E-Mail, in dem im die Änderung mitgeteilt wird:

---

  Hallo USER NAME,
  
  
  Diese Nachricht erhalten Sie, weil sie sich mit dem Benutzername 'USER NAME' im Netzwerk von rpi-virtuell/reliwerk angemeldet haben.
  Aus technischen Gründen (der Benutzername enthält Punkte, Sonderzeichen oder Leerzeichen) musste dieser geändert werden und heißt nun **'USER-NAME'** Bitte verwenden Sie zur Anmeldung nur noch den geänderten Benutzernamen.
  
  Vielen Dank für dein Verständnis!
  
  Dein rpi-virtuell Technik Teamhttp://about.rpi-virtuell.de 
