# karma8

Общие рассуждения

* Система должна логгировать исходы функций в удобном формате для дальнешего изучения
* Необходимо провести ряд оптимизаций системы:
  * Необходимо добавить id и primary key в таблицы
  * Необходимо добавить поля created_at, updated_at, deleted_at, чтобы контролировать создание и обновление записей. Также добавить индексы
* Оптимизация подтверждений эмейлов:
  * Если пользователь подтвердил эмейл через почту(кликнул ссылку в велком-письме) нет смысла его подтверждать
  * У домена почтового ящика должны быть MX записи, если их нет - нет смысла проверять email
  * У MX домена должен быть открыт 25 порт. Сортируем серверы в обратном порядке по приоритетам и сканируем 25 порт. Если нет ни одного открытого - значит это нерабочие SMTP серверы.
  * Очевидно, что все выше действия можно совершить при регистрации и вообще не заполнять таблицу с проверками предупреждая пользователя о проблемах с его почтой.
  * У почтового сервера есть логи отправки. Если письмо не дошло - нет смысла проверять его, т.к. очевидно он неверен
* Оптимизация исполнения долгих функций:
  * Кажется явным в данном случае переписать такие медленные функции, но если это невозможно, то их исполнение нужно распараллелить
  * Для распараллеливания php поддерживает системную функцию fork расширения pcntl - находим классы и прикручиваем
  * Перед каждым форком нужно отсоединяться от базы данных, закрывать файловые дескрипторы и сокеты.
  * Очевидно, что функции не жрут процессор, а ждут ответа от некого сервиса, поэтому процессов может быть много
  * Допустим в самом худшем случае нужно проверить все 1 000 000 эмейлов. Каждый проверяется 1 минуту в самом худшем случае. Если запускать 1000 процессов, то проверка займет 16 часов.

Чтобы начать
```
make build up init
```

Чтобы войти в бд
```
make mysql
```

Чтобы запустить отправку эмейлов. Запуск раз в сутки - берется сл 3 дня из базы + 1 день. Если чаще надо - поменять $period.
```
make send_email
```