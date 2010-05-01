<?php
/*
+ ----------------------------------------------------------------------------+
|     esGlobalBan - Language File.
|
|     $Source: /cvsroot/banned/languages/Russian/lan_configuration.php,v $
|     $Revision: 1.0 $
|     $Date: 2009/07/02 30:36:39 $
|     $Author: Odonel $
+----------------------------------------------------------------------------+
*/

$LAN_CONFIGURATION_001 = 'Введите допустимый email адрес.';
$LAN_CONFIGURATION_002 = 'уже в списке';
$LAN_CONFIGURATION_003 = 'Задайте дерикторию для файлов демо.';
$LAN_CONFIGURATION_004 = "Задайте максимальный размер файла демо.";
$LAN_CONFIGURATION_005 = "Задайте сообщение о бане.";
$LAN_CONFIGURATION_006 = 'Задайте количество дней, которое находится в бане игрок, ожидающий рассмотрения бана.';
$LAN_CONFIGURATION_007 = 'Задайте хэш код безопасности.';
$LAN_CONFIGURATION_008 = 'Задайте префикс SMF базы данных.';
$LAN_CONFIGURATION_009 = 'Задайте SMF группу с полными правами.';
$LAN_CONFIGURATION_010 = 'Задайте SMF группу с правами модерирования банов.';
$LAN_CONFIGURATION_011 = 'Задайте SMF группу с админскими правами.';
$LAN_CONFIGURATION_012 = 'Задайте SMF группу с пользовательскими правами.';
$LAN_CONFIGURATION_013 = 'Задайте SMF группу без каких-либо прав.';
$LAN_CONFIGURATION_014 = 'Задайте регистрационный код.';
$LAN_CONFIGURATION_015 = 'Версия';
$LAN_CONFIGURATION_016 = 'Установленная версия:';
$LAN_CONFIGURATION_017 = 'Настройки сайта';
$LAN_CONFIGURATION_018 = 'Название сайта';
$LAN_CONFIGURATION_019 = 'Это название вашего сайта, которое будет отображаться в заголовке веб-браузера.';
$LAN_CONFIGURATION_020 = 'Лого';
$LAN_CONFIGURATION_021 = 'Точное имя файла с логотипом, находящегося в папке с изображениями. (отображается в верхней части страницы)';
$LAN_CONFIGURATION_022 = 'Официальная версия:';
$LAN_CONFIGURATION_023 = 'Активировать ссылку на форум';
$LAN_CONFIGURATION_024 = 'В меню навигации добавится ссылка на ваш форум.';
$LAN_CONFIGURATION_025 = 'Да';
$LAN_CONFIGURATION_026 = 'Нет';
$LAN_CONFIGURATION_027 = 'Адрес форума';
$LAN_CONFIGURATION_028 = 'URL вашего форума';
$LAN_CONFIGURATION_029 = 'Количество банов на страницу';
$LAN_CONFIGURATION_030 = 'Количество банов, отображаемых на одной странице списка банов.';
$LAN_CONFIGURATION_031 = 'Количество активных ссылок на страницы';
$LAN_CONFIGURATION_032 = "Количество ссылок на другие страницы, которые будут видны до и после выбранной страницы (Пример: если установлено на \'2\' вы увидите \'1 2 ... 10 11 [12] 13 14 ... 23 24\').";
$LAN_CONFIGURATION_033 = 'Директория файлов демо';
$LAN_CONFIGURATION_034 = 'Папка на веб-сервере в которой будут храниться заливаемые файлы демо. Папке необходимо присвоить CMOD 777.  Стандартная папка - demos, в корне вашего веб-сайта.';
$LAN_CONFIGURATION_035 = 'Максимальный размер файла демо(MB)';
$LAN_CONFIGURATION_036 = 'Максимальный размер заливаемого файла демо в мегабайтах.  Не может быть больше значения установшенного в файле настроек веб-сервера php.ini.';
$LAN_CONFIGURATION_037 = 'Регистрационный код';
$LAN_CONFIGURATION_038 = 'Код, необходимый для регистрации участниками собственного аккаунта в системе.';
$LAN_CONFIGURATION_039 = 'Высылать Email оповещение при новом бане';
$LAN_CONFIGURATION_040 = 'Если да, то все email адреса из списка будут получать уведомления о новом бане.';
$LAN_CONFIGURATION_041 = 'Высылать Email оповещение при добавление нового файла демо';
$LAN_CONFIGURATION_042 = 'Если да, то все email адреса из списка будут получать уведомления о новых демках.';
$LAN_CONFIGURATION_043 = 'Email адрес отправителя';
$LAN_CONFIGURATION_044 = "Этот адрес будет виден в графе \'от\' при получении письма о новом бане или демке.";
$LAN_CONFIGURATION_045 = 'Email адреса для получение уведомлений';
$LAN_CONFIGURATION_046 = 'Еmail адреса людей, которые будут получать уведомления о добавленных банах или демках.';
$LAN_CONFIGURATION_047 = 'Добавить >>';
$LAN_CONFIGURATION_048 = '<< Удалить';
$LAN_CONFIGURATION_049 = 'Настройки бана';
$LAN_CONFIGURATION_050 = 'Сообщение о бане';
$LAN_CONFIGURATION_051 = "Сообщение, которое увидит забаненный игрок при коннекте к серверу. Используйте переменные \'gb_time\' -длительностть бана и \'gb_reason\' - причина, пример: Вы забанены. Длительность: gb_time. Причина: gb_reason. Подробности на yoursite.ru";
$LAN_CONFIGURATION_052 = 'Бан админа';
$LAN_CONFIGURATION_053 = 'Возможность админа забанить другого админа.';
$LAN_CONFIGURATION_054 = 'Ожидание рассмотрения бана';
$LAN_CONFIGURATION_055 = 'Количество дней, которое находится в бане игрок, ожидающий рассмотрения бана.  Применятся только к банам поставленным пользователем с момента которого прошел час. Если бан не будет рассмотрен после заданого кол-ва дней, то он станет неактивен и забаненный сможет зайти на сервер.  Установите на \'0\' чтобы позволить забаненному пользователем игроку зайти в игру мгновенно.';
$LAN_CONFIGURATION_056 = 'Убрать рассмотрение при добавлении демки';
$LAN_CONFIGURATION_057 = 'Убрать статус рассмотрения бана, если была залита демка.';
$LAN_CONFIGURATION_058 = 'Хэш код безопасности';
$LAN_CONFIGURATION_059 = 'Код безопасности, который использует ES скрипт для общения с веб-сервером.';
$LAN_CONFIGURATION_060 = 'Обучение админа';
$LAN_CONFIGURATION_061 = "Выставите \"Да\" если хотите чтобы в игре после смерти админа ему выводилось сообщение \'Type !banmenu\', напоминающее о том как вызвать меню бана.";
$LAN_CONFIGURATION_062 = 'Настройка SMF Интеграции';
$LAN_CONFIGURATION_063 = 'Включить SMF интеграцию';
$LAN_CONFIGURATION_064 = 'Включите для интеграции в систему SMF и использования пользовательских групп SMF.  Префикс SMF таблиц должен начинаться с smf_.  GlobalBan необходимо установить в подкаталог папки Forums (yoursite.com/Forums/banned).';
$LAN_CONFIGURATION_065 = 'Префикс SMF таблицы в базе данных';
$LAN_CONFIGURATION_066 = 'Префикс ваших SMF таблицы  (стандартно - smf_).';
$LAN_CONFIGURATION_067 = 'SMF группа супер-пользователей';
$LAN_CONFIGURATION_068 = 'Введите ID SMF группы, которая  будет обладать полными правами доступа к сайту.';
$LAN_CONFIGURATION_069 = 'SMF группа модераторов';
$LAN_CONFIGURATION_070 = 'Введите ID SMF группы, которая  будет обладать правами модерирования всех банов.';
$LAN_CONFIGURATION_071 = 'SMF группа админов';
$LAN_CONFIGURATION_072 = 'Введите ID SMF группы, которая  будет обладать правами добавления банов без ограничений и их редактированием.';
$LAN_CONFIGURATION_073 = 'SMF группа пользователей';
$LAN_CONFIGURATION_074 = "Введите ID SMF группы, которая  будет обладать правами добавления банов, но все их баны длительностью больше часа будут отправлены на рассмотрение вышестоящим админам.";
$LAN_CONFIGURATION_075 = 'SMF группы без прав';
$LAN_CONFIGURATION_076 = 'Введите ID SMF группы, которая  не будет обладать правами добавления банов.';
$LAN_CONFIGURATION_077 = 'Сохранить настройки';
$LAN_CONFIGURATION_078 = 'Внимание: Сохранение настроек обновит GlobalBan.cfg на всех активных серверах.';
$LAN_CONFIGURATION_079 = 'Доступ запрещен.';
$LAN_CONFIGURATION_080 = "Файл config/class.Config.php не может быть изменён. Проверьте права на изменение данного файла на веб-сервере.";
$LAN_CONFIGURATION_081 = '';
$LAN_CONFIGURATION_082 = 'Сгенерировать';
$LAN_CONFIGURATION_083 = 'Активировать ссылку на веб-портал';
$LAN_CONFIGURATION_084 = 'В меню навигации добавится ссылка на ваш веб-портал.';
$LAN_CONFIGURATION_085 = 'Адрес веб-портала';
$LAN_CONFIGURATION_086 = 'URL вашего форума. Пример: http://www.yourdomain.com';
$LAN_CONFIGURATION_087 = 'Активировать ссылку HLstatsX';
$LAN_CONFIGURATION_088 = 'К каждому Steam_ID из списка банов будет добавлена ссылка на поиск в вашей статистике HLstatsX Community Edition (http://www.hlxcommunity.com/).';
$LAN_CONFIGURATION_089 = 'HlstatsX адрес';
$LAN_CONFIGURATION_090 = 'URL вашей HlstatsX статистики. Пример: http://www.yourdomain.com/HlstatsX/';
$LAN_CONFIGURATION_091 = 'Язык';
$LAN_CONFIGURATION_092 = 'Язык, который будет установлен стандартным для вашего сайта. Также используется для отправки сообщений на игровом сервере.';
$LAN_CONFIGURATION_093 = 'Настройка e107 интеграции';
$LAN_CONFIGURATION_094 = 'Включить автопост e107';
$LAN_CONFIGURATION_095 = 'Если у вас установлен e107 форум GlobalBan автоматически создает новый пост на форуме о каждом добавленом бане.';
$LAN_CONFIGURATION_096 = 'e107 адрес';
$LAN_CONFIGURATION_097 = "URL вашей e107 веб-системы. Пример: \'http://www.your_e107_domain.com/\'";
$LAN_CONFIGURATION_098 = "e107 хост базы данных";
$LAN_CONFIGURATION_099 = "Стандартно - localhost.";
$LAN_CONFIGURATION_100 = "e107 префикс таблицы";
$LAN_CONFIGURATION_101 = "Префикс вашей e107 таблицы (стандартно - \'e107_\').";
$LAN_CONFIGURATION_102 = "Пользователь базы данных";
$LAN_CONFIGURATION_103 = "MySQL пользователь с доступом к базе данных системы e107.";
$LAN_CONFIGURATION_104 = "e107 Имя пользователя";
$LAN_CONFIGURATION_105 = "Зарегистрированный пользователь e107, который будет являться автором создаваемых постов.";
$LAN_CONFIGURATION_106 = "Пароль базы данных";
$LAN_CONFIGURATION_107 = "Пароль пользователя MySQL с доступом к базе данных системы e107.";
$LAN_CONFIGURATION_108 = "ID категории форума";
$LAN_CONFIGURATION_109 = "Пример: если ссылка на категорию форума выглядит как \'http://www.youre107.com/e107_plugins/forum/forum_viewforum.php?19\' то выставите значение \'19\'";
$LAN_CONFIGURATION_110 = "Имя базы данных";
$LAN_CONFIGURATION_111 = "Имя базы данных MySQL вашей системы e107.";
$LAN_CONFIGURATION_112 = 'Предупреждать о ранее забаненном игроке';
$LAN_CONFIGURATION_113 = 'Дает возможность задать кому выводить сообщение при коннекте ранее забаненного игрока (срок бана истёк) на сервер.';
$LAN_CONFIGURATION_114 = 'Каждого игрока';
$LAN_CONFIGURATION_115 = 'Админа и ранее забаненного';
$LAN_CONFIGURATION_116 = 'Только админа (чат)';
$LAN_CONFIGURATION_117 = 'Только ранее забаненного (HUD Панель)';
$LAN_CONFIGURATION_118 = 'Никого';
$LAN_CONFIGURATION_119 = "Английский";
$LAN_CONFIGURATION_120 = "Испанский";
$LAN_CONFIGURATION_121 = "Французский";
$LAN_CONFIGURATION_122 = "Русский";
$LAN_CONFIGURATION_123 = "Only Warrnings Ex-Banned Min Length";
$LAN_CONFIGURATION_124 = "Allows you to select required Min Ban Lenght to advise in game about this Ex-Banned when he joins.";
?>