# GigaChat PHP SDK

[![Latest Stable Version](https://poser.pugx.org/zullusa/gigachat-php-sdk/v?style=for-the-badge)](https://packagist.org/packages/zullusa/gigachat-php-sdk) [![Total Downloads](https://poser.pugx.org/zullusa/gigachat-php-sdk/downloads?style=for-the-badge)](https://packagist.org/packages/zullusa/gigachat-php-sdk) [![Latest Unstable Version](https://poser.pugx.org/zullusa/gigachat-php-sdk/v/unstable?style=for-the-badge)](https://packagist.org/packages/zullusa/gigachat-php-sdk) [![License](https://poser.pugx.org/zullusa/gigachat-php-sdk/license?style=for-the-badge)](https://packagist.org/packages/zullusa/gigachat-php-sdk) [![PHP Version Require](https://poser.pugx.org/zullusa/gigachat-php-sdk/require/php?style=for-the-badge)](https://packagist.org/packages/zullusa/gigachat-php-sdk)
PHP API SDK для [GigaChat](https://developers.sber.ru/docs/ru/gigachat/overview/).

## Установка

Установите последнюю версию

```bash
$ composer require zullusa/gigachat-php-sdk
```

## Требования

PHP >= 8.0

## Как использовать

```php

<?php

require 'vendor/autoload.php';

use zullusa\GigaChat\GigaChat;
use zullusa\GigaChat\GigaChatDialog;
use zullusa\GigaChat\GigaChatOAuth;
use zullusa\GigaChat\Type\Message;
use zullusa\GigaChat\Type\Model;

// https://gu-st.ru/content/Other/doc/russiantrustedca.pem
$cert = __DIR__ . '/russiantrustedca.pem';

const GIGACHAT_API_KEY = '';

$gigaChat = GigaChat::withApiKey(
    GIGACHAT_API_KEY,
    $cert
);

// Пример отправки сообщения
$messages = [
    new Message(
        'Когда уже ИИ захватит этот мир?'
    ),
];
$completion = $gigaChat->chatCompletions($messages);

foreach ($completion->getChoices() as $choice) {
    echo $choice->getMessage()->getContent();
    echo $choice->getMessage()->getRole();
}

// Пример для работы с GigaChat в режиме диалога
$dialog = new GigaChatDialog($gigaChat);
$questionMessage = new Message('Когда уже ИИ захватит этот мир?');
$answerMessage = $dialog->getAnswer($questionMessage);

$questionMessage = new Message('Как ИИ изменятся в будущем?');
$answerMessage = $dialog->getAnswer($questionMessage);

// Сброс истории диалога
$dialog->reset();


// Получить список доступных моделей
$models = $gigaChat->getModels();
foreach ($models as $model) {
    echo $model->getId();
    echo $model->getObject();
    echo $model->getOwnedBy();
}

// Посчитать кол-во токенов для строки
$tokensCount = $gigaChat->tokensCount(Model::ID_GIGACHAT_LATEST, 'Когда уже ИИ захватит этот мир?');
echo $tokensCount->getObject();
echo $tokensCount->getTokens();
echo $tokensCount->getCharacters();

// Скачивание файла
$stream = $gigaChat->getFile('file_id');
file_put_contents('file_name.jpg', $stream);

// Создать векторные представления
$embeddings = $gigaChat->getEmbeddings(['1234']);

```
