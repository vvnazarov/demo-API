## Задание
С использованием Symfony Framework, создать API JSON для конвертора валют. Базовая валюта, сумма и валюта котировки задаются в параметрах запроса.

API должна получать данные с http://www.cbr.ru/scripts/XML_daily.asp. 

## Установка
`composer install`

Web server root:  `public/`

## Использование
**GET** /{from}/{to}/{amount}

**E.g.** `http://<host>/USD/RUB/1.23`
