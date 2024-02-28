<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Localization file for User
 */

$langArr = [
    'approve' => [
        'genitiveСase' => [
            'phone' => 'телефона',
            'email' => 'email',
        ],
        'Code for approve {codeType}' => 'Код для подтверждения {codeType}',
        'Code' => 'Код',
        'Dear' => 'Уважаемый',
        'Your approval code on {siteName}: <b>{code}</b>' => 'Ваш код подтверждения на {siteName}: <b>{code}</b>',
        '{team} team' => 'Команда {team}',
    ],
    'errors' => [
        'User avatar load error' => 'Ошибка загрузки аватара пользователя',
        'User avatar save error' => 'Ошибка сохранения аватара пользователя',
        ''
    ],
    'fields' => [
        'id' => 'ID',
        'email' => 'Email',
        'phone' => 'Телефон',
        'telegram' => 'Telegram',
        'language' => 'Язык',
        'name' => 'Имя',
        'nikname' => 'Ник',
        'status' => 'Статус',
        'role' => 'Роль',
        'rating' => 'Рейтинг',
        'avatar' => 'Аватар',
        'email_approve_code' => 'Код подтверждения email',
        'email_approved' => 'Email подтвержден',
        'email_send_time' => 'Время отправки email',
        'phone_approve_code' => 'Код подтверждения телефона',
        'phone_approved' => 'Телефон подтвержден',
        'phone_send_time' => 'Время отправки телефона',
        'created_time' => 'Время создания',
        'sex' => 'Пол',
        'contacts' => 'Контакты',
        'password' => 'Пароль',
    ],
    'statuses' => [
        'created' => 'Создан',
        'active' => 'Активен',
        'deleted' => 'Удален',
    ],
    'roles' => [
        'user' => 'Пользователь',
        'moderator' => 'Модератор',
        'admin' => 'Админ',
    ],
    'sexes' => [
        'male' => 'Мужской',
        'female' => 'Женский',
        'other' => 'Другой',
    ],
];