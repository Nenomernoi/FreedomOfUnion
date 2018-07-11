<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('DATA_BASE_AVATAR_VERSION', 1528486293);
define('DATA_BASE_INFORMATION_VERSION', 1528486293);
define('DATA_BASE_UNIFORM_VERSION', 1528486293);
define('DATA_BASE_ACHIVIMENTS_VERSION', 1528486293);
define('DATA_BASE_CARDS_VERSION', 1528486293);
define('DATA_BASE_BANNER_VERSION', 1528486293);
define('DATA_BASE_HISTORY_VERSION', 1528486293);

define('VERSION', "version");


define('MAX_ITEM', 20);
define('MAX_ITEM_LIST', 100);

define('MAX_TOWER', 100);
////потом отключить
define('DEFAULT_TOWER',20);
define('DEFAULT_ROV', 15);
////потом отключить
define('DEFAULT_RES', 6);
define('DEFAULT_MINE', 1);

define('EMPTY_CARD', 196);
define('MIN_CARD', 1);
define('MAX_CARD', 195);

define('MAX_CARD_ATACK', 968);
define('MAX_CARD_DEFENCE', 568);

define('PLUS_ONE', 1);
define('MINUS_ONE', -1);
define('PLUS_TWICE', 2);
define('EQUAL_MAX_OR_MIN_TWICE', -2);
define('IF_LESS_PLUS_TWICE_ELSE_PLUS_ONE', -3);
define('IF_LESS_EQUAL_MAX', -4);

define('MINUS_ONE_BONUS', -1);
define('MINUS_TWICE_BONUS', -2);
define('MAX_BONUS_ME', 1);


define('CODE_COMPLITE', 100);
define('CODE_ERROR_SECURITY', 200);
define('CODE_ERROR_METHTOD', 250);
define('CODE_ERROR_AUTH', 300);
define('CODE_ERROR_NOT_FOUND', 400);
define('CODE_ERROR_NOT_FOUND_PARAMS', 500);
define('CODE_ERROR_BUSY_REGISTRATION', 600);
define('CODE_ERROR_NOT_ENOUGHT_MONEY', 700);
define('CODE_ERROR_YOU_IN_GAME', 800);
define('CODE_ERROR_VERIFY', 900);


define('TABLE_II_CARDS', "ii_cards");
define('TABLE_USER', "user");
define('TABLE_SHARE', "share");
define('TABLE_GAMES', "games");
define('TABLE_UNIFORM', "uniform");
define('TABLE_INFO', "info");
define('TABLE_CARDS', "cards");
define('TABLE_BANNER', "banner");
define('TABLE_TURNS', "turns");
define('TABLE_AVATAR', "avatar");
define('TABLE_PLACES', "places");
define('TABLE_PAYMENT', "payment");
define('TABLE_RATING', "raiting");
define('TABLE_ACH', "achiviments");
define('TABLE_ACH_GAME', "game_achiviments");
define('TABLE_HISTORY', "history");
define('DATA', "data");
define('LEVEL', "level");
define('MESSAGE', "message");
define('EXTRA_MESSAGE', "extraMessage");
define('SUCCESS', "success");
define('CARD', "card");
define('PROGRESS', "progress");
define('ATLAS', "atlas");
define('CORRECT', "correct");
define('TIME', "time");
define('LAST_TIME', "lats_time");
define('TURN_COUNT', "turn_count");
define('FRACTION', "fraction");
define('PUSH', "push");

define('PACKET_CHESS', "by.nenomernoi.chess");
define('PACKET_CALC', "by.nenomernoi.calcshop");
define('PACKET_ID_EMPTY', -1);
define('PACKET_ID_CHESS', 0);
define('PACKET_ID_CALC', 1);

define('EN', "en");
define('RU', "ru");

define('PATH_BASE', "https://freedom-or-union.herokuapp.com/");
define('PATH_IMAGE', PATH_BASE."/api/v1/load/images/");


define('CODE_COMPLITE_EN', "Successfully");
define('CODE_COMPLITE_RU', "Успешно");
define('CODE_ERROR_SECURITY_EN', "Security error");
define('CODE_ERROR_AUTH_EN', "Password wrong or email");
define('CODE_ERROR_METHOD_EN', "We use GET instead POST or DELETE");
define('CODE_ERROR_METHOD_RU', "Вы используете GET вместо POST or DELETE");
define('CODE_ERROR_AUTH_RU', "Пароль или почта неверны");
define('CODE_ERROR_AUTH_OLD_EN', "Authorization data are outdated");
define('CODE_ERROR_AUTH_OLD_RU', "Данные авторизации устарели");
define('CODE_ERROR_NOT_FOUND_PARAMS_EN', "There are no parameters");
define('CODE_ERROR_NOT_FOUND_PARAMS_RU', "Отсутствуют параметры");
define('CODE_ERROR_NOT_FOUND_EN', "This user isn't registered");
define('CODE_ERROR_NOT_FOUND_RU', "Указанный пользователь не зарегистрирован");
define('CODE_ERROR_NOT_FOUND_GAME_EN', "This game not found");
define('CODE_ERROR_NOT_FOUND_GAME_RU', "Указанная игра не найдена");
define('CODE_ERROR_NOT_FOUND_GARD_EN', "This card not found");
define('CODE_ERROR_NOT_FOUND_GARD_RU', "Указанная карта не найдена");
define('CODE_ERROR_BUSY_REGISTRATION_EN', "This user is registered");
define('CODE_ERROR_BUSY_REGISTRATION_RU', "Указанный пользователь зарегистрирован");
define('CODE_ERROR_BUSY_NAME_EN', "This name is registered");
define('CODE_ERROR_BUSY_NAME_RU', "Указанное имя уже зарегистрировано");
define('CODE_ERROR_NOT_EQUIP_NAME_EN', "Equipment not found");
define('CODE_ERROR_NOT_EQUIP_NAME_RU', "Экипировка не найдена");
define('CODE_ERROR_NOT_ENOUGHT_MONEY_EN', "Not enough money");
define('CODE_ERROR_NOT_ENOUGHT_MONEY_RU', "Не хватает денег");
define('CODE_ERROR_SYNC_EN', "Synchronization failed. I switch over ... ");
define('CODE_ERROR_SYNC_RU', "Ошибка синхронизации. Переподключаюсь...");
define('CODE_ERROR_NOT_ENOUGHT_RESOURSE_EN', "Not enough resourse");
define('CODE_ERROR_NOT_ENOUGHT_RESOURSE_RU', "Не хватает ресурсов");
define('CODE_ERROR_YOU_IN_GAME_EN', "You're already in the game");
define('CODE_ERROR_YOU_IN_GAME_RU', "Вы уже в игре");
define('CODE_ERROR_VERIFY_EN', "Your payment isn't accepted. Maybe he is fake.");
define('CODE_ERROR_VERIFY_RU', "Ваш платеж не прошел проверку подлинности.");

define('CODE_CONNECTED_ENEMY_EN', "Opponent connected");
define('CODE_CONNECTED_ENEMY_RU', "Соперник подключен");

define('CODE_WAIT_ENEMY_EN', "Opponent waiting");
define('CODE_WAIT_ENEMY_RU', "Соперник ждет");

define('CODE_TURN_ENEMY_EN', "Opponent turned");
define('CODE_TURN_ENEMY_RU', "Соперник сделал ход");

define('COINS_WIN', 2);
define('COINS_WIN_BOT', 1);
define('COINS_WIN_LOSE', 1);

define('TOTAL_WIN', 2);
define('TOTAL_WIN_BOT', 1);
define('TOTAL_WIN_LOSE', 1);


define('SOUTH', 1);
define('NORTH', 2);

define('TURN', 0);
define('ESCAPE', 1);

define('HAT', 0);
define('GUN', 1);
define('UNIFORM', 2);
define('RIFFLE', 3);

define('NO_BOT', -1);
define('PLAYER', 0);
define('BOT', 1);

define('GAME_OVER', -1);
define('GAME_PLAY', 0);

define('MIN_TURN', 4);

//// (PLAYER - Gamer 1 )-----( ENEMY - Gamer 2)
define('WINNER_LOSSER', 1);

define('WINNER_PARENT', 2);
define('WINNER_CHILD', 3);

define('RESIGN', 4);
define('TIME_OUT', 5);

define('RESIGN_PARENT', 6);
define('TIME_OUT_PARENT', 7);

define('RESIGN_CHILD', 8);
define('TIME_OUT_CHILD', 9);

/////BACK CARD
define('CARD_OFFICER', 1);
define('CARD_INDUSTRY', 2);
define('CARD_UNITS', 3);
define('CARD_BONUS', 4);
define('CARD_RES', 5);
define('CARD_CSA', 6);
define('CARD_USA', 7);
define('CARD_CSA_USA', 8);


define('NO_ADS', 'no_ads');
define('COINS_100', 'coins100');
define('COINS_500', 'coins500');
define('COINS_1000', 'coins1000');
define('COINS_5000', 'coins5000');
define('COINS_10000', 'coins10000');

//// Achiviment
define('GRANT_1', 16);
define('GRANT_2', 14);
define('GRANT_3', 12);
define('GRANT_4', 10);

define('MAC_CL_1', 20);
define('MAC_CL_2', 25);
define('MAC_CL_3', 30);
define('MAC_CL_4', 35);

define('HAUP_1', 30);
define('HAUP_2', 40);
define('HAUP_3', 50);
define('HAUP_4', 60);

define('PETER_1', 30);
define('PETER_2', 40);
define('PETER_3', 50);
define('PETER_4', 70);


///GCM KEY
define('API_ACCESS_KEY', 'AAAA-8PvxC0:APA91bGRCuZS230dVkyfcbxNSAMKG2eShDDql28xTdsZ-JWgrToVmglGksBDdSGYODUX9vF715yttLbWt3qPHcKjK8UFm1sPzY_rMuYtdN9J74qSMT2PIlLNHFVf8cGyFghXz21fl26FeJS_hRe55Ru8xbQ9O1ulGw');

define('DEFAULT_URL', 'https://freedomorunion-be894.firebaseio.com/');
define('DEFAULT_TOKEN', 'sitPcSwi76ozQG2gY05BhBt7mOu7Sw4oVN7UKyFW');
define('GAMES_PATH', 'games');
define('TURNS_PATH', 'turns');
define('ACHIVIMENTS_PATH', 'achievements');

define('PUBLIC_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkv3DcqR5oADlwGXdOXylgYzJo5+O/MM14AzJWr1jY+26kQFTWE2A5mhdD4BT3byadELnDz4nzNsMcfWg4XR0PafPqZYvNpkW4LSIwUO7GbAz5shgPnJYaecdtHGF8rHfj4R7aTiioRQazncS8xM81T1af5QpwYrZoTw4SQFizhRhb+GUzFHDNgusOqIBIrPKVV1oVKUVizMbHRVPVh0Bx5hZ52ZZqE/RKzIUKVQEFuUXK03hVBnAvQ7PWh9TThBxFPF/lM/uXPe5n6KnvWt6ISSB9KPkefZ+QdBUIbeaIsVs1v+I42yx8AVo+kn5GYyU3o056fkjCMdBa1sy1UPVLwIDAQAB');
define('PACKAGE_NAME', 'by.nenomernoi.freedomorunion');

//DEBUG
define('KEY_APP_DEBUG', 'DEEC7131301B66FD88942BE4E43708B4E91B6E44');
//DEBUG WORK
define('KEY_APP_DEBUG_WORK', 'AC067910F6908E915F09559DAA869F67C3D68741');
///RELEASE 
define('KEY_APP_RELEASE', 'E36C24C0D1C405DF8E14A59BD5460F198880D378');
define('KEY_APP_RELEASE_NO_ADS', '06E2761270DC97459482EDE8FB58AD71E050E4A8');
define('KEY_APP_RELEASE_NO_ADS_SECOND', '9070E8E1EF3525D6D789EE718141EAAF6296E6B2');
