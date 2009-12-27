<?php
/*
+ ----------------------------------------------------------------------------+
|     esGlobalBan - Language File.
|
|     $Source: /cvsroot/banned/languages/French/lan_configuration.php,v $
|     $Revision: 1.0 $
|     $Date: 2009/07/02 30:36:39 $
|     $Author: Odonel $
+----------------------------------------------------------------------------+
*/

$LAN_CONFIGURATION_001 = 'Por favor introducca una direccion de email valida.';
$LAN_CONFIGURATION_002 = 'ya esta en la lista.';
$LAN_CONFIGURATION_003 = 'Debes especificar un directorio para las demos.';
$LAN_CONFIGURATION_004 = "Debes especificar un limite de tama&ntilde;o para las demos.";
$LAN_CONFIGURATION_005 = "Debes especificar un mensage para mostrarle en el juego a los baneados.";
$LAN_CONFIGURATION_006 = 'Debes especificar el numero de dias a mantener activo un ban pendiente de revisar.';
$LAN_CONFIGURATION_007 = 'Debes especificar un codigo hash por motivos de seguridad.';
$LAN_CONFIGURATION_008 = 'Debes especificar el prefijo de las tablas SMF.';
$LAN_CONFIGURATION_009 = 'Debes especificar que grupo de SMF tendra privilegios de plenos poderes.';
$LAN_CONFIGURATION_010 = 'Debes especificar que grupo de SMF tendra privilegios de gestores de banes.';
$LAN_CONFIGURATION_011 = 'Debes especificar que grupo de SMF tendra privilegios de admin.';
$LAN_CONFIGURATION_012 = 'Debes especificar que grupo de SMF tendra privilegios de miembro.';
$LAN_CONFIGURATION_013 = 'Debes especificar que grupo de SMF no tendra ningun poder.';
$LAN_CONFIGURATION_014 = 'Debes especificar un codigo de creacion de usuario.';
$LAN_CONFIGURATION_015 = 'Informacion de la Version';
$LAN_CONFIGURATION_016 = 'Version Usandose:';
$LAN_CONFIGURATION_017 = 'Ajustes de la Web';
$LAN_CONFIGURATION_018 = 'Nombre del Sitio';
$LAN_CONFIGURATION_019 = 'Este es el nombre de la web que se mostrara en la barra de titulo de la ventana del navegador de internet.';
$LAN_CONFIGURATION_020 = 'Logo';
$LAN_CONFIGURATION_021 = 'Este debe ser el nombre exacto del fichero del logo que habras guardado en la carpeta de imágenes que quieres mostrar en la cabecera de la web.';
$LAN_CONFIGURATION_022 = 'Version Disponible:';
$LAN_CONFIGURATION_023 = 'Activar Boton al Foro';
$LAN_CONFIGURATION_024 = 'Esto a&ntilde;adira al menu de navegacion un boton con link a tu foro.';
$LAN_CONFIGURATION_025 = 'Si';
$LAN_CONFIGURATION_026 = 'No';
$LAN_CONFIGURATION_027 = 'Direccion del Foro';
$LAN_CONFIGURATION_028 = 'Introducca la URL de su foro si tiene activada esta opcion.';
$LAN_CONFIGURATION_029 = 'Banes por Pagina';
$LAN_CONFIGURATION_030 = 'Esto establece el numero de banes que seran mostrados por cada pagina en el listado.';
$LAN_CONFIGURATION_031 = 'Numero de Links a Paginas';
$LAN_CONFIGURATION_032 = 'El numero de links a mostrar antes y despues de la pagina seleccionada (IE: fijado a 2 veras 1 2 ... 10 11 [12] 13 14 ... 23 24).';
$LAN_CONFIGURATION_033 = 'Directorio para las Demos';
$LAN_CONFIGURATION_034 = 'El directorio relativo a la raiz de la web de baneados. Por defecto esta fijado a la carpeta demos';
$LAN_CONFIGURATION_035 = 'Limite Tama&ntilde;o Demos(MB)';
$LAN_CONFIGURATION_036 = 'El maximo tama&ntilde;o de las demos en MB que pueden ser enviadas. No puede ser superior al limite establecido en el fichero de configuracion php.ini';
$LAN_CONFIGURATION_037 = 'Codigo Registro Nuevo Usuario:';
$LAN_CONFIGURATION_038 = 'Este es el codigo que puedes facilitarle a los futuros miembros/admins para que puedan registrarse ellos solitos que tengas que registrarlos tu.';
$LAN_CONFIGURATION_039 = 'Enviar Email por cada Ban';
$LAN_CONFIGURATION_040 = 'Si se activa, todos los email de la lista de abajo reciviran un email cuando se a&ntilde;ada un nuevo ban.';
$LAN_CONFIGURATION_041 = 'Enviar Email cuando A&ntilde;adan una Demo';
$LAN_CONFIGURATION_042 = 'Si esta activado, todos los emails de la lista de abajo reciviran un email cuando a&ntilde;adan una nueva demo.';
$LAN_CONFIGURATION_043 = 'Email del Enviante';
$LAN_CONFIGURATION_044 = "Este es el email para el \'desde\'  que los demas veran cuando reciban un email de nuevos banes o demos.";
$LAN_CONFIGURATION_045 = 'Emails de los que recibiran notificaciones';
$LAN_CONFIGURATION_046 = 'Las direcciones de email de la gente que tu quieres que sea informada de nuevos banes y demos.';
$LAN_CONFIGURATION_047 = 'A&ntilde;adir >>';
$LAN_CONFIGURATION_048 = '<< Quitar';
$LAN_CONFIGURATION_049 = 'Ajustes de Banes';
$LAN_CONFIGURATION_050 = 'Mensage a Baneados';
$LAN_CONFIGURATION_051 = "El mensage que los usuarios baneados veran cuando intenten conectarse a tus servidores. Usa la variable \'gb_time\' para fijar donde se mostrara el periodo aplicado en el ban, ejemplo: Has sido baneado gb_time. Visita www.tuweb.com/baneados/ para mas informacion.";
$LAN_CONFIGURATION_052 = 'Permitir Banear a otro Admin';
$LAN_CONFIGURATION_053 = 'Activalo para permitir a los admins banear a otro admin.';
$LAN_CONFIGURATION_054 = 'Dias a aplicar un ban pendiente supervision';
$LAN_CONFIGURATION_055 = 'El numero de dias que un Ban en modo pendiente debe aplicarse. Solo se le aplica a los banes de mas de 1 hora de duracion puestos por los Miembros.  El Ban no se diferenciara de un ban Inactivo tras este numero de dias si no se le quita el estado pendiente de supervision.  Fijalo a 0 para dejar Re-Entrar a cualquiera baneado por un Miembro si ha pasado mas de una hora.';
$LAN_CONFIGURATION_056 = 'Quitar el modo pendiente si suben una demo';
$LAN_CONFIGURATION_057 = 'Quitar el estado pendiente de un ban si un Miembro sube una demo para dicho ban.';
$LAN_CONFIGURATION_058 = 'Codigo Hash';
$LAN_CONFIGURATION_059 = 'Este es un codigo secreto usado por el script ES dell HL2 para hablar con esta web cuando se pone un ban o consulta si un player esta baneado. Sirve para prevenir que terceros no autorizados consigan comunicarse con la web y poner un ban.';
$LAN_CONFIGURATION_060 = 'Ense&ntilde;ar a los Admins';
$LAN_CONFIGURATION_061 = "Activelo si desea mostrarles el mensage \'Type !banmenu\' a los admins cuando mueran.  Sirve para recordarles como se banea desde el juego.";
$LAN_CONFIGURATION_062 = 'Ajustes de la Integracion con Foro SMF';
$LAN_CONFIGURATION_063 = 'Activar la Integracion SMF';
$LAN_CONFIGURATION_064 = 'Activelo para integrarla con su foro SMF y usar el gestor de usuarios de SMF en vez del propio de GlobalBan. Las paginas de GlobalBan deben instalarse en una subcarpeta /banned/ dentro de la carpeta de SMF (yoursite.com/Forums/banned).';
$LAN_CONFIGURATION_065 = 'Prefijo de las tablas SMF';
$LAN_CONFIGURATION_066 = 'El prefijo de tus tablas SMF (normalmente smf_ por defecto).';
$LAN_CONFIGURATION_067 = 'Grupo SMF de Super-Usuarios';
$LAN_CONFIGURATION_068 = 'Introducca la ID del grupo que desea asociar a tener pleno acceso en GlobalBan.';
$LAN_CONFIGURATION_069 = 'Grupo SMF de Gestores de Banes';
$LAN_CONFIGURATION_070 = 'Introducca la ID del grupo que desea asociar a tener acceso a editar todos los banes en GlobalBan.';
$LAN_CONFIGURATION_071 = 'Grupo SMF de Admins';
$LAN_CONFIGURATION_072 = 'Introducca la ID del grupo que desea asociar a tener acceso a banear a cualquiera sin requerir supervision del ban en GlobalBan.';
$LAN_CONFIGURATION_073 = 'Grupo SMF de Miembros';
$LAN_CONFIGURATION_074 = "Introducca la ID del grupo que desea asociar a tener acceso a banear a cualquiera, pero todos los banes superiores a 1 hora quedaran en estado pendientes de supervision.  Si el ban no es quitado de estado pendiente pasados el numero de dias especificados arriba, entonces el ban quedara inactivo.";
$LAN_CONFIGURATION_075 = 'Grupo SMF sin Poderes';
$LAN_CONFIGURATION_076 = 'Introducca la ID del grupo que desea asociar al grupo que no tendra poderes para banear y que cedera la gestion de sus antiguos banes al grupo de gestion.';
$LAN_CONFIGURATION_077 = 'Guardar Configuracion';
$LAN_CONFIGURATION_078 = 'Nota: Guardando la configuracion tambien se actualizara el archivo GlobalBan.cfg en todos los servidores de HL2 activos.';
$LAN_CONFIGURATION_079 = 'Acceso Denegado.';
$LAN_CONFIGURATION_080 = '';
$LAN_CONFIGURATION_081 = '';
$LAN_CONFIGURATION_082 = 'Generar';

?>